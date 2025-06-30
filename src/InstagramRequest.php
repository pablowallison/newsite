<?php
/**
 * InstagramRequest
 * 
 * @version    1.0
 * @package    Web
 * @author     Pablo Wallison
 * @copyright  Copyright (c) 2006 Concretiza Construções e Imoveis Ltda. (http://www.concretizaimoveis.com.br)
 * @license    http://www.concretizaimoveis.com.br/license
 * 
 */

 namespace App;

 use Exception;
 
 class InstagramRequest {
    

    public function get(string $url, array $query = [], int $timeout = 10): string
    {
        $ch = curl_init();
        $url .= (str_contains($url, '?') ? '&' : '?') . http_build_query($query);
        curl_setopt_array($ch, [
            CURLOPT_URL            => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => $timeout,
            CURLOPT_SSL_VERIFYPEER => false,
        ]);

        $response = curl_exec($ch);
        if ($response === false) {
            throw new \RuntimeException('cURL error: ' . curl_error($ch));
        }
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        if ($code >= 400) {
            throw new \RuntimeException("HTTP $code – resposta: $response");
        }
        return $response;
    }

 }