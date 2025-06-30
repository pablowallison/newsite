<?php
/**
 * InstagramApi
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
 
 class InstagramApi {
    

    private const BASE = 'https://graph.instagram.com/';

    public function __construct(
        private InstagramRequest $http,
        private string     $token,
        private string     $fields,
    ){}

    /**
     * Retorna as últimas publicações do perfil
     * @param int $limit quantidade de itens (máx. 25 por request)
     */
    public function fetchFeed(int $limit = 12): array
    {
        $response = $this->http->get(self::BASE . 'me/media', [
            'fields'       => $this->fields,
            'access_token' => $this->token,
            'limit'        => $limit,
        ]);
        return json_decode($response, true, flags: JSON_THROW_ON_ERROR)['data'] ?? [];
    }

 }