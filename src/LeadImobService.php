<?php
/**
 * LeadImobService
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
 
 class LeadImobService {
    
    public static function lead ($param){
    
    // URL da API que você quer acessar
    $location = 'https://painel.concretizaconstrucoes.com/rest.php?class=LeadImobRestService&method=Store';

    $data = new \App\RequestData();
        $result = $data->requisicao($location, 'POST', $param);
        return $result;
    }

 }