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

class LogosPartnersService {
    
    public function loadAll(){
         
        try {
            
            // URL da API que você quer acessar
            $location = 'https://painel.concretizaconstrucoes.com/rest.php?class=LogosPartnersRestService&method=loadAll';

            // Fazendo a requisição à API
            $data = new \App\RequestData();
            $result = $data->request($location, 'GET', null);
            //var_dump($resultTipoImovel);
            return $result;
        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage();
            return null;
        }
    }

}
