<?php
/**
 * GoogleMapsService
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

class GoogleMapsService{

    private $apiKey;
    
    public function __construct() {
        // Carrega o arquivo de configuração
        $config = parse_ini_file('config/config.ini', true);

        // Carrega a chave de autorização a partir do arquivo
        $this->apiKey = $config['api']['google_api_key'];
    }

    public function loadCoord($param){
        //var_dump($param); 
        
        //var_dump($endereco);   
        //exit;
        
        try{
            $endereco = $param['data']['0']['logradouro'] . ', ' . $param['data']['0']['num'] . ', Bairro ' . $param['data']['0']['bairro'] . ', ' . $param['data']['0']['cidade'] . ', ' . $param['data']['0']['uf']; 
            $enderecoCodificado = urlencode($endereco);
            

        // Monta a URL da requisição
        $url = "https://maps.googleapis.com/maps/api/geocode/json?address={$enderecoCodificado}&key={$this->apiKey}";
        
        // Inicializa o cURL
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        // Executa a requisição
        $resposta = curl_exec($ch);

        // Verifica se ocorreu algum erro
        if(curl_errno($ch)){
            echo 'Erro na requisição: ' . curl_error($ch);
            exit;
        }

        curl_close($ch);

        // Decodifica a resposta JSON
        $dados = json_decode($resposta, true);
        
        //var_dump($dados);
        if($dados['status'] == 'OK'){
            // Extrai as coordenadas (latitude e longitude)
            $latitude = $dados['results'][0]['geometry']['location']['lat'];
            $longitude = $dados['results'][0]['geometry']['location']['lng'];
            
            $coordenadas = [
                'latitude' => $latitude,
                'longitude' => $longitude
            ];

        } else {
            echo "Erro ao geocodificar o endereço: " . $dados['status'];
        }

        return $coordenadas;

        } catch (Exception $e){
            echo "Error" . $e->getMessage();
            return NULL;
        }
    }

}

?>