<?php
/**
 * LeadsService
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
 
 class ChatGuruService {
    
    private $account_id = "6759da76ce4e3d13aa054c85";
    private $phone_id = "6759e3f8cd7de174b9bcb327";
    private $apiKey = "HDZ5BC83WPVTUHB89ZOVIHSC6U52ITV9JH51F3GUU6VT65DGARY370JPQEWCP0PI";

    public function  cadastrarChat ($param){
    
    // URL da API que você quer acessar
    $location = 'https://s22.chatguru.app/api/v1';

    $periodoTrabalho = $param['data']['periodo_trabalho'] == 1 ? 'Sim' : 'Não';
    $dependente      = $param['data']['dependente']        == 1 ? 'Sim' : 'Não';

    $payload  = [
        'action'       => 'chat_add',
        'name'         => $param['data']['nome'],
        'text'         => "Olá, você gostaria de simular as condições de financiamento,
                        com os dados abaixo: 
                        Nome completo: {$param['data']['nome']}, 
                        Telefone: {$param['data']['telefone']},
                        Renda: {$param['data']['renda_familiar']}.
                        CPF: {$param['data']['documento']},
                        Data de nascimento: {$param['data']['data_nascimento']},
                        Estado civil, {$param['data']['estado_civil']},
                        Mais de 3 anos de carteira assinada: {$periodoTrabalho},
                        Filho menor ou dependente: {$dependente}",
        'key'          => $this->apiKey,
        'account_id'   => $this->account_id,
        'phone_id'     => $this->phone_id,
        'chat_number'  => $param['data']['chatNumber'],
    ];
    
    //var_dump($payload);

    $data = new \App\ChatGuruRequestData();
        $result = $data->guruRequest($location, 'POST', $payload);
        return $result;

        //var_dump($result);
    }

 }