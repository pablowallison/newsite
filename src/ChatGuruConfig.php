<?php
namespace App;

final class ChatGuruConfig
{
    public function __construct(
        public readonly string $accountId,
        public readonly string $phoneId,
        public readonly string $apiKey,
        public readonly string $baseUrl = 'https://s22.chatguru.app/api/v1'
    ) {var_dump($accountId);}
}