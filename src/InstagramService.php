<?php
/**
 * InstagramService
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
 
 class InstagramService {
    

    public function __construct(
        private InstagramApi $api,
        private InstagramFileCache    $cache,
        private int          $ttl
    ){}

    public function get(int $limit = 12): array
    {
        return $this->cache->remember('ig_feed', $this->ttl, fn () => $this->transform($this->api->fetchFeed($limit)));
    }

    private function transform(array $raw): array
    {
        return array_map(static function (array $i) {
            return [
                'url'   => $i['media_url'],
                'link'  => $i['permalink'],
                'alt'   => mb_substr($i['caption'] ?? '', 0, 120),
                'type'  => $i['media_type'],
                'thumb' => $i['thumbnail_url'] ?? null,
                'time'  => $i['timestamp'],
            ];
        }, $raw);
    }

 }