<?php

namespace App;

interface HttpClientInterface {

    public function post(string $url, array $data): array;
    
}