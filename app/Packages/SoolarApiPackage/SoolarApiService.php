<?php

namespace App\Packages\SoolarApiPackage;

use App\Packages\SoolarApiPackage\Enums\ProductCategoriesEnum;
use App\Packages\SoolarApiPackage\Enums\WarehouseEnum;
use DOMDocument;
use DOMXPath;
use GuzzleHttp\Client;
use GuzzleHttp\Cookie\FileCookieJar;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\File;

class SoolarApiService
{
    private const BASE_URL = 'https://www.soollar.com.br/';
    private const LOGIN_URI = 'login_check';
    private const LOGIN_PAGE_URI = 'customer/login';
    private Client $client;

    public function __construct()
    {
        $this->client = $this->getClient();
    }

    public function listProducts()
    {
        try {
            return $this->getProduct(ProductCategoriesEnum::MODULO);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()], 500);
        }
    }

    private function getProduct(ProductCategoriesEnum $category): JsonResponse
    {
        $url = self::BASE_URL
            . "loja/"
            . WarehouseEnum::FEIRA_DE_SANTANA_BA->value
            . "/produto/listar/"
            . $category->value;

        $html = $this->fetchProductPageWithAutoLogin($url);

        file_put_contents(storage_path('app/soolar_product_page.html'), $html);

        $products = $this->parseProductsFromHtml($html);

        return response()->json([
            'total' => count($products),
            'produtos' => $products,
        ]);
    }

    private function fetchProductPageWithAutoLogin(string $url): string
    {
        if (!$this->checkIfLoggedIn()) {
            $this->login();

            if (!$this->checkIfLoggedIn()) {
                throw new \Exception("A tentativa de login falhou. A sessão continua inativa. Verifique 'login_response.html' e suas credenciais.");
            }
        }

        $response = $this->client->get($url);
        return (string) $response->getBody();
    }

    private function parseProductsFromHtml(string $html): array
    {
        libxml_use_internal_errors(true);
        $dom = new DOMDocument();
        $dom->loadHTML($html);
        $xpath = new DOMXPath($dom);

        $products = [];
        $itens = $xpath->query("//div[contains(@class, 'variant')]");

        foreach ($itens as $li) {
            $doc = new DOMXPath($li->ownerDocument);
            $nomeNode = $doc->query(".//a[contains(@class, 'variant-product-name')]", $li)->item(0);
            $precoNode = $doc->query(".//p[contains(@class, 'variant-final-price')] | .//div[contains(@class, 'price')] | .//span[contains(@class, 'price')]", $li)->item(0);

            $nome = $nomeNode ? trim($nomeNode->textContent) : null;
            $preco = $precoNode ? trim($precoNode->textContent) : 'Indisponível';

            if (str_contains($preco, 'VEJA O NOSSO PREÇO')) {
                $preco = 'Login falhou, preço não disponível.';
            }

            if ($nome) {
                $products[] = [
                    'nome' => $nome,
                    'preco' => $preco,
                ];
            }
        }
        return $products;
    }

    /**
     * FUNÇÃO DE LOGIN ATUALIZADA
     * Removida a busca por CSRF token. Agora ela imita o cURL.
     */
    private function login(): void
    {
        // 1. Visita a página de login primeiro. Isso é crucial para o servidor
        // nos dar um cookie de sessão (FLEXYSESSID) válido.
        $this->client->get(self::BASE_URL . self::LOGIN_PAGE_URI);

        // 2. Prepara os dados do formulário (apenas usuário e senha).
        $credentials = $this->getCredentials();
        $formParams = [
            '_username' => $credentials['username'],
            '_password' => $credentials['password'],
        ];

        // 3. Envia a requisição POST para realizar o login.
        $response = $this->client->post(self::BASE_URL . self::LOGIN_URI, [
            'form_params' => $formParams,
            'allow_redirects' => true,
            'headers' => [
                'Referer' => self::BASE_URL . self::LOGIN_PAGE_URI,
                'Origin' => 'https://www.soollar.com.br',
            ],
        ]);

        // 4. Salva a resposta para depuração.
        file_put_contents(storage_path('app/login_response.html'), (string) $response->getBody());
    }

    private function checkIfLoggedIn(): bool
    {
        $response = $this->client->get(self::BASE_URL . 'customer/profile');
        $html = (string) $response->getBody();
        file_put_contents(storage_path('app/debug_profile.html'), $html);
        return str_contains($html, 'Sair') || str_contains($html, 'Meus Pedidos');
    }

    private function getCredentials(): array
    {
        $path = base_path('app/Packages/SoolarApiPackage/security/credentials.json');
        if (!File::exists($path)) {
            throw new \Exception("Arquivo de credenciais não encontrado em: {$path}");
        }
        $content = File::get($path);
        $data = json_decode($content, true);
        if (!$data || !isset($data['username'], $data['password'])) {
            throw new \Exception("Credenciais malformadas ou incompletas no arquivo JSON.");
        }
        return $data;
    }

    private function getClient(): Client
    {
        $cookiePath = storage_path('app/soolar_cookie.json');
        if (!File::isDirectory(dirname($cookiePath))) {
            File::makeDirectory(dirname($cookiePath), 0755, true, true);
        }
        $cookieJar = new FileCookieJar($cookiePath, true);

        return new Client([
            'cookies' => $cookieJar,
            'allow_redirects' => true,
            'headers' => [
                'User-Agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36',
            ],
            'timeout' => 30.0,
        ]);
    }
}
