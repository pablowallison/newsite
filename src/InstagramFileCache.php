<?php
/**
 * InstagramFileCache
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
 
 class InstagramFileCache {
    

    public function __construct(private string $dir)
    {
        if (!is_dir($dir) && !mkdir($dir, 0777, true) && !is_dir($dir)) {
            throw new \RuntimeException("Não foi possível criar diretório de cache: $dir");
        }
    }

    public function remember(string $key, int $ttl, callable $callback): array
    {
        $path = $this->dir . '/' . md5($key) . '.json';
        if (file_exists($path) && (time() - filemtime($path) < $ttl)) {
            return json_decode(file_get_contents($path), true, flags: JSON_THROW_ON_ERROR);
        }
        $data = $callback();
        file_put_contents($path, json_encode($data, JSON_THROW_ON_ERROR));
        return $data;
    }

 }