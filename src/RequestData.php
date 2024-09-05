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
  
}

?>