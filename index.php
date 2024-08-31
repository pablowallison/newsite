<?php
require_once 'vendor/autoload.php';


$imoveis = new \App\RequestImoveis;
$result = $imoveis->loadAll();

//var_dump($result);

foreach ($result['data'] as $imovel) {
    var_dump($imovel['imagens']);
}

?>