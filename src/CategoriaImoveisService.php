<?php
/**
 * CategoriaImoveisService
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

class CategoriaImoveisService {
    
    private $location = "https://painel.concretizaconstrucoes.com/rest.php?class=CategoriaImoveisRestService";

    public function loadAll(){
         
        try {
            
            //Método da API a ser chamado 
            $method = "loadAll";

            //Monta o ENDPOINT da API a ser usado
            $location = $this->location . "&method=" . $method;
            
            // Fazendo a requisição à API
            $data = new \App\RequestData();
            $result = $data->request($location, 'GET', null);
            
            return $result;

        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage();
            return null;
        } 
    }

}
