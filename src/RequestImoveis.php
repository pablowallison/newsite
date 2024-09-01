<?php

namespace App;

use Exception;

class RequestImoveis {
    
    public function request($url, $method = 'GET', $data = null, $authorization = null) {
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
        if ($authorization) {
            $headers[] = "Authorization: $authorization";
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
    

    public function loadAll() {
        try {
            // Parâmetros de filtro
            /*$body = [
                'limit' => '3',
                'order' => 'nome',
                'direction' => 'desc'
            ];*/

            // URL da API que você quer acessar
            $location = 'https://painel.concretizaconstrucoes.com/rest.php?class=ImoveisRestService&method=LoadAll';

            // Chave de autorização no formato Basic
            $authorization = 'Basic 9fbbb2c765d1d5d12c1e3582a9329108c4ed9a96b199ffab6700a413869c';

            // Fazendo a requisição à API
            return $this->request($location, 'GET', null, $authorization);
        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage();
            return null;
        }
    }

    public function load($id) {
        try {
            // Verifica se o ID foi fornecido
            if (empty($id)) {
                throw new Exception("O ID é necessário para carregar o imóvel.");
            }
    
            // URL da API para carregar um imóvel específico
            $location = 'https://painel.concretizaconstrucoes.com/rest.php?class=ImoveisRestService&method=load';
    
            // Adiciona o ID como um parâmetro na URL
            $location .= '&id=' . urlencode($id);
    
            // Chave de autorização no formato Basic
            $authorization = 'Basic 9fbbb2c765d1d5d12c1e3582a9329108c4ed9a96b199ffab6700a413869c';
    
            // Fazendo a requisição à API
            $retorno = $this->request($location, 'GET', null, $authorization);
    
            // Retorna o resultado da requisição
            return $retorno;
        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage();
            return null;
        }
    }
    
}


/*

classe que funciona
class RequestImoveis{
    public function request($url, $method = 'GET', $data = null, $authorization = null)
{
    $options = [
        'http' => [
            'method'  => $method,
            'header'  => "Content-type: application/x-www-form-urlencoded\r\n"
                        . ($authorization ? "Authorization: $authorization\r\n" : ""),
            'content' => $data ? http_build_query($data) : ''
        ],
        'ssl' => [
        'cafile' => '/etc/ssl/certs/cacert.pem',
        'verify_peer' => true,
        'verify_peer_name' => true,
        ],
    ];

    $context  = stream_context_create($options);
    $result = file_get_contents($url, false, $context);
    
    if ($result === FALSE) {
        throw new Exception("Error Processing Request to $url");
    }

    return json_decode($result, true);
}

    public function loadAll(){
        try
    {

        // Parâmetros de filtro
        $body['limit']     = '3';
        $body['order']     = 'nome';
        $body['direction'] = 'desc';

        // URL da API que você quer acessar
        $location = 'https://painel.concretizaconstrucoes.com/rest.php?class=ImoveisRestService&method=LoadAll';

        // Chave de autorização no formato Basic
        $authorization = 'Basic 9fbbb2c765d1d5d12c1e3582a9329108c4ed9a96b199ffab6700a413869c';

        // Fazendo a requisição à API
        $retorno = $this->request($location, 'GET', $body, $authorization);
        $arrayRetorno = json_decode(json_encode($retorno), true);

        // Exibindo a resposta
        //var_dump($retorno);
        return $arrayRetorno;
    }
    catch (Exception $e)
    {
        echo 'Error: ' . $e->getMessage();
    }
    }
}
*/

?>