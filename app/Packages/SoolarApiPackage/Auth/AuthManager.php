<?php

namespace App\Packages\SoolarApiPackage\Auth;

use GuzzleHttp\Client;
use GuzzleHttp\Cookie\FileCookieJar;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Illuminate\Support\Facades\File;

class AuthManager
{
    private const BASE_URL = 'https://www.soollar.com.br';
    private const LOGIN_PAGE_URI = '/customer/login';
    private const LOGIN_URI = '/login_check';

    private Client $client;
    public function __construct()
    {
        $this->client = $this->getClient();
    }

    public function fetchPageAndHandleLogin(string $url): string
    {
        $response = $this->client->get($url);
        $html = (string) $response->getBody();

        if (str_contains($html, 'Acesse a sua conta') || str_contains($html, '_username')) {
            $this->login();

            $response = $this->client->get($url);
            $html = (string) $response->getBody();
        }

        return $html;
    }

    private function login(): void
    {
        $response = $this->client->get(self::BASE_URL . self::LOGIN_PAGE_URI);
        $html = (string) $response->getBody();

        preg_match('/name="_csrf_token"\s+value="([^"]+)"/', $html, $matches);
        $soollarCsrf = $matches[1] ?? null;

        $credentials = $this->getCredentials();

        $formParams = [
            '_username' => $credentials['username'],
            '_password' => $credentials['password'],
            '_csrf_token' => $soollarCsrf,
        ];

        $this->client->post(self::BASE_URL . self::LOGIN_URI, [
            'form_params' => $formParams,
            'allow_redirects' => true,
            'headers' => [
                'Referer' => self::BASE_URL . self::LOGIN_PAGE_URI,
                'Origin' => self::BASE_URL,
            ],
        ]);
    }

    private function getCredentials(): array
    {
        $path = base_path('app/Packages/SoolarApiPackage/security/credentials.json');
        if (!File::exists($path)) {
            throw new \Exception("Credentials file not found at: {$path}");
        }
        $content = File::get($path);
        $data = json_decode($content, true);
        if (!$data || !isset($data['username'], $data['password'])) {
            throw new \Exception("Malformed or incomplete credentials in JSON file.");
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

        $stack = HandlerStack::create();

        $stack->push(Middleware::retry(
            function ($retries, Request $request, Response $response = null, $exception = null) {
                // Tenta de novo até 3 vezes
                if ($retries >= 2) {
                    return false;
                }
                if ($response && $response->getStatusCode() >= 500) {
                    return true;
                }
                if ($exception instanceof \GuzzleHttp\Exception\ConnectException) {
                    return true;
                }
                return false;
            },
            function ($retries) {
                return 1000 * pow(2, $retries); // Espera 2s, depois 4s
            }
        ));

        return new Client([
            'handler' => $stack, // Usa o handler com o middleware
            'cookies' => $cookieJar,
            'allow_redirects' => true,
            'headers' => [
                'User-Agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36',
            ],
            'timeout' => 30.0,
        ]);
    }
}
