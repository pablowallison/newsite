<?php

namespace App;

use App\ChatGuruConfig;
use App\HttpClientInterface;
use App\MessageBuilderInterface;

class ChatGuruLeadService
{
    public function __construct(
        private ChatGuruConfig $cfg,
        private HttpClientInterface $http,
        private MessageBuilderInterface $builder
    ) {}

    public function cadastrarLead(array $dadosForm): array
    {
       var_dump($this->cfg->accountId);
        $payload = [
            'action'      => 'chat_add',
            'name'        => $dadosForm['full_name'],
            'text'        => $this->builder->build($dadosForm),
            'key'         => $this->cfg->apiKey,
            'account_id'  => $this->cfg->accountId,
            'phone_id'    => $this->cfg->phoneId,
            'chat_number' => $dadosForm['phone_number']
        ];

        return $this->http->post($this->cfg->baseUrl, $payload);
    }
}