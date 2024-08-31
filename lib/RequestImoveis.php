<?php
class RequestImoveis{
    public function request($url, $method = 'GET', $data = null, $authorization = null)
{
    $options = [
        'http' => [
            'method'  => $method,
            'header'  => "Content-type: application/x-www-form-urlencoded\r\n"
                        . ($authorization ? "Authorization: $authorization\r\n" : ""),
            'content' => $data ? http_build_query($data) : ''
        ]
    ];

    $context  = stream_context_create($options);
    $result = file_get_contents($url, false, $context);
    
    if ($result === FALSE) {
        throw new Exception("Error Processing Request to $url");
    }

    return json_decode($result, true);
}

    public function getAll(){
        try
    {

        // Parâmetros de filtro
        $body['limit']     = '3';
        $body['order']     = 'nome';
        $body['direction'] = 'desc';

        // URL da API que você quer acessar
        $location = 'painel.concretizaconstrucoes.com/rest.php?class=ImoveisRestService&method=LoadAll';

        // Chave de autorização no formato Basic
        $authorization = 'Basic 9fbbb2c765d1d5d12c1e3582a9329108c4ed9a96b199ffab6700a413869c';

        // Fazendo a requisição à API
        $retorno = $this->request($location, 'GET', $body, $authorization);

        // Exibindo a resposta
        var_dump($retorno);
    }
    catch (Exception $e)
    {
        echo 'Error: ' . $e->getMessage();
    }
    }
}
