<?php

namespace App;

use Exception;

class ChatGuruRequestData {
    
    private $authorization;
    private $key;

    // Construtor para receber a chave de autorização
    public function __construct() {
        // Carrega o arquivo de configuração
        $config = parse_ini_file('config/config.ini', true);

        // Carrega a chave de autorização a partir do arquivo
        $this->key = $config['api']['guru_api_key'];
    }

    public function guruRequest(
        string $url,
        string $method = 'POST',
        array|string|null $data   = null   // só array faz sentido aqui
    ) {
        $ch = curl_init();
    
        // Inclui automaticamente a sua key, caso a API exija no corpo
        if (is_array($data)) {
            $data += ['key' => $this->key];
        }
    
        // Transforma o array em query-string (x-www-form-urlencoded)
        $body = http_build_query($data ?? []);
    
        // Configura o método
        if (strtoupper($method) === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
        } else {
            $url .= '?' . $body;
        }
    
        // Nenhum cabeçalho adicional
        curl_setopt_array($ch, [
            CURLOPT_URL            => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,   // mantenha true em produção
            CURLOPT_TIMEOUT        => 30,
        ]);
    
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    
        if (curl_errno($ch)) {
            throw new \Exception("cURL error: " . curl_error($ch));
        }
        curl_close($ch);
    
        if ($httpCode >= 400) {
            throw new \Exception("HTTP $httpCode | Resposta: $response");
        }
    
        // Se a API sempre devolve JSON:
        $decoded = json_decode($response, true);
        //var_dump($decoded);
        return json_last_error() === JSON_ERROR_NONE ? $decoded : $response;
    }
    
  
}


?>