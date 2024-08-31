<?php
require_once 'lib/RequestImoveis.php';

echo "olá";

$imoveis = new RequestImoveis;
$imovel = $imoveis->getAll();
var_dump($imovel);

?>