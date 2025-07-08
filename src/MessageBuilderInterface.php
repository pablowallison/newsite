<?php

namespace App;

interface MessageBuilderInterface
{
    public function build(array $dadosForm): string;
}