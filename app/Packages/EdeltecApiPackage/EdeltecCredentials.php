<?php

namespace App\Packages\EdeltecApiPackage;

use App\Packages\Exceptions\GetBearerTokenMaxAttempsException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class EdeltecCredentials
{
    private string $user;
    private string $password;
    private Client $client;
    public $bearerToken;
    public int $lastUpdateTimeToken;

    public function __construct()
    {
        $this->user = env('EDELTEC_API_KEY');
        $this->password = env('EDELTEC_API_SECRET');
        $this->client = new Client();
        $this->bearerToken = null;
    }

    /** @throws GetBearerTokenMaxAttempsException|GuzzleException */
    public function setOrRenewApiToken(int $attemps = 1): void
    {
        if ($attemps > 3) {
            throw new GetBearerTokenMaxAttempsException('Número de tentativas excedeu o limite!');
        }

        $headers = ['Content-Type' => 'application/json'];
        $jsonBody = [
            "apiKey" => $this->user,
            "secret" => $this->password
        ];

        try {

            $response = $this->client->post(EdeltecApiHelper::BASE_API_URL . '/api-access/token', [
                'headers' => $headers,
                'json' => $jsonBody,
            ]);

            $this->bearerToken = $response->getBody()->getContents();
            $this->lastUpdateTimeToken = time();

        } catch (\Exception) {
            $this->setOrRenewApiToken($attemps++);
        }
    }
}
