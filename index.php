<?php
require_once 'vendor/autoload.php';
use \App\RequestImoveis;

$imoveis = new RequestImoveis;
$result = $imoveis->loadAll();

foreach ($result['data'] as $imovel) {
    var_dump($imovel['imagens']);
}

?>