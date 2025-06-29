<?php

if (!file_exists('config/config.ini'))
{
    die('Application configuration file not found');
}

$config = parse_ini_file('config/config.ini');
// Definir as constantes baseadas na configuração carregada

date_default_timezone_set($config['timezone']);
