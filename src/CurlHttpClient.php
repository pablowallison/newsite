<?php

namespace App;

use App\HttpClientInterface;

class CurlHttpClient implements HttpClientInterface
{
    public function post(string $url, array $data): array
    {
        var_dump($url);
        // implementa chamada CURL básica
        $ch = curl_init($url);
        
        
        curl_setopt_array($ch, [
            CURLOPT_POST       => true,
            CURLOPT_POSTFIELDS => http_build_query($data),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_HTTPHEADER => ['application/x-www-form-urlencoded'],
        ]);
        $response = curl_exec($ch);
        var_dump($response);
        curl_close($ch);

        if ($response === false) {
            throw new \RuntimeException('Erro de conexão com ChatGuru');
        }
        return json_decode($response, true);
    }
}