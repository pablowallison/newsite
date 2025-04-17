<?php
/**
 * GoogleMapsService
 * 
 * @version    1.0
 * @package    Web
 */

namespace App;

use Exception;

class MapsService {

    private $apiKey;
    
    public function __construct() {
        // Carrega o arquivo de configuração
        $config = parse_ini_file('config/config.ini', true);
    }

    public function loadCoord($param) {
        try {
            // Monta o endereço
            $endereco = $param['data'][0]['logradouro'] 
                      . ', ' . $param['data'][0]['num'] 
                      . ', ' . $param['data'][0]['bairro'] 
                      . ', ' . $param['data'][0]['cidade'] 
                      . ', ' . $param['data'][0]['uf'];

            // Codifica o endereço para URL
            $enderecoCodificado = urlencode($endereco);
    
            // Monta a URL da requisição para Nominatim (sem usar a key do Google)
            $url = "https://nominatim.openstreetmap.org/search?format=json&q={$enderecoCodificado}";
            
            // Inicializa o cURL
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

            // Define um User-Agent para não ser bloqueado pelo Nominatim
            curl_setopt($ch, CURLOPT_USERAGENT, 'SeuSite/1.0 (email@seudominio.com)');

            // Executa a requisição
            $resposta = curl_exec($ch);

            // Verifica se ocorreu algum erro no cURL
            if (curl_errno($ch)) {
                echo 'Erro na requisição: ' . curl_error($ch);
                exit;
            }

            curl_close($ch);

            // Decodifica a resposta JSON (será um array de objetos)
            $dados = json_decode($resposta, true);

            // Verifica se a resposta não está vazia e se o primeiro resultado existe
            if (isset($dados[0])) {
                // Extrai as coordenadas (latitude e longitude) de Nominatim
                $latitude = $dados[0]['lat'];
                $longitude = $dados[0]['lon'];

                $coordenadas = [
                    'latitude' => $latitude,
                    'longitude' => $longitude
                ];

                return $coordenadas;
            } else {
                // Nenhum resultado retornado pela API do Nominatim
                //echo "Nenhum resultado encontrado para o endereço informado.";
                return null;
            }

        } catch (Exception $e) {
            echo "Erro: " . $e->getMessage();
            return null;
        }
    }

}
?>
