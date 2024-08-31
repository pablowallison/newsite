<?php
require_once 'lib/RequestImoveis.php';

echo "olá";

$imoveis = RequestImoveis::getAll();
var_dump($imoveis);

?>