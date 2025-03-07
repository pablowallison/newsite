<?php
/**
 * TipoImoveisService
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

class TipoImoveisService {
    
    public function load(){
         
        try {
            
            // URL da API que você quer acessar
            $location = 'https://painel.concretizaconstrucoes.com/rest.php?class=TipoImoveisRestService&method=loadAll';

            // Fazendo a requisição à API
            $data = new \App\RequestData();
            $resultTipoImovel = $data->request($location, 'GET', null);
            //var_dump($resultTipoImovel);
            return $resultTipoImovel;
        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage();
            return null;
        }
    }

}
