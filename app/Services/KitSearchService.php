<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Http;

class KitSearchService
{
    public function kitSearch($kwp, $roof, $tension)
    {
        $url = env('KITS_URL') . 'getInventoryKitsByParams/';
//        $token = $this->warehouseLogin();

        $request = Http::get($url . $kwp . '/' . $roof . '/' . $tension)->body();

        return json_decode($request, true);
    }

    private function warehouseLogin() {
        $url = env('KITS_URL') . 'auth/login';

        $email = env('LOGIN_EMAIL');
        $password = env('LOGIN_PASSWORD');

        $response = Http::post($url, ['email' => $email, 'password' => $password]);

        return json_decode($response, true)['access_token'];
    }

    public function getKitByUuid($uuid)
    {
        $url = env('KITS_URL') . 'getInventoryKitByCode/' . $uuid;
        $response = Http::get($url)->body();

        return json_decode($response, true)[0];
    }
}
