<?php

namespace App;

use Exception;

class RequestData {
    
    private $authorization;

    // Construtor para receber a chave de autorização
    public function __construct() {
        // Carrega o arquivo de configuração
        $config = parse_ini_file('config/config.ini', true);

        // Carrega a chave de autorização a partir do arquivo
        $this->authorization = $config['api']['rest_key'];
    }

    public function request($url, $method = 'GET', $data = null) {
        $ch = curl_init();
        
        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data ? http_build_query($data) : '');
        } elseif ($method === 'GET' && !empty($data)) {
            $url .= '?' . http_build_query($data);
        }
    
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $headers = ["Content-type: application/x-www-form-urlencoded"];
        if ($this->authorization) {
            $headers[] = "Authorization: $this->authorization";
        }
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    
        $result = curl_exec($ch);
    
        if (curl_errno($ch)) {
            $error = curl_error($ch);
            curl_close($ch);
            throw new Exception("cURL error: $error");
        }
    
        curl_close($ch);
    
        $decodedResult = json_decode($result, true);
        if ($decodedResult === null) {
            throw new Exception("Error decoding JSON response: $result");
        }
    
        return $decodedResult;
    }

    public function requisicao($url, $method = 'POST', $data = null, $headers = []) {
        $ch = curl_init();
    
        // Configuração do método
        switch (strtoupper($method)) {
            case 'POST':
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, is_array($data) ? http_build_query($data) : $data);
                break;
            case 'GET':
                if (!empty($data)) {
                    $url .= '?' . http_build_query($data);
                }
                break;
            case 'PUT':
            case 'DELETE':
            case 'PATCH':
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
                if (!empty($data)) {
                    curl_setopt($ch, CURLOPT_POSTFIELDS, is_array($data) ? json_encode($data) : $data);
                }
                $headers[] = 'Content-Type: application/json';
                break;
        }
    
        // Configuração geral
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    
        // Cabeçalhos
        $defaultHeaders = ["Content-Type: application/x-www-form-urlencoded"];
        if ($this->authorization) {
            $defaultHeaders[] = "Authorization: $this->authorization";
        }
        curl_setopt($ch, CURLOPT_HTTPHEADER, array_merge($defaultHeaders, $headers));
    
        // Executar requisição
        $result = curl_exec($ch);
    
        // Tratamento de erro do cURL
        if (curl_errno($ch)) {
            $error = curl_error($ch);
            curl_close($ch);
            throw new Exception("cURL error: $error");
        }
    
        curl_close($ch);
    
        // Tenta decodificar JSON
        $decodedResult = json_decode($result, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception("Erro ao decodificar JSON: " . json_last_error_msg() . " | Resposta: " . $result);
        }
    
        return $decodedResult;
    }
  
}


?>