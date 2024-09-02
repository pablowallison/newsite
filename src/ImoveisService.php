<?php

namespace App;

class ImoveisService {
    
    private $cacheFile = './cache/imoveis.json'; // Caminho para o arquivo de cache
    private $cacheTTL = 3600; // Tempo de vida do cache em segundos (exemplo: 1 hora)

    public function loadAll(){

        // Verifica se o cache existe e não está expirado
        if (file_exists($this->cacheFile) && (time() - filemtime($this->cacheFile) < $this->cacheTTL)) {
            // Carrega dados do cache
            $imoveisComImagens = json_decode(file_get_contents($this->cacheFile), true);
            return $imoveisComImagens;
        }

        $imoveis = new \App\RequestImoveis;
        $result = $imoveis->loadAll();

        $urlBase = 'https://painel.concretizaconstrucoes.com/';  
        $diretorio = 'imagens/imobiliaria/imoveis/'; // Caminho absoluto
    
        // Verifica se o diretório base existe, caso contrário, cria
        if (!is_dir($diretorio)) {
            mkdir($diretorio, 0755, true);
        }
    
        $imoveisComImagens = []; // Inicializando o array para armazenar imóveis com imagens
    
        foreach ($result['data'] as $imovel) {
            
            foreach ($imovel['imagens'] as &$imagem) {
                
                $array_path_imagens = explode('/', $imagem['imagem']);
    
                // Verificação da existência do índice 2 para criação do subdiretório
                if (isset($array_path_imagens[2])) {
                    $subdir = $diretorio . $array_path_imagens[2];
                    if (!is_dir($subdir)) {
                        mkdir($subdir, 0755, true);
                    }
                } else {
                    // Tratamento de erro: diretório não foi identificado corretamente
                    continue;
                }
                
                // Configurações de contexto para ignorar verificação SSL
                $options = [
                    "ssl" => [
                        "verify_peer" => false,
                        "verify_peer_name" => false,
                    ],
                ];
            
                $context = stream_context_create($options);
    
                // Obtém o nome do arquivo original
                $nomeArquivoOriginal = basename($imagem['imagem']);
                
                // Codifica a URL para download
                $imagemUrl = $urlBase . str_replace(' ', '%20', ltrim($imagem['imagem'], '/')); 
    
                // Define o caminho para salvar a imagem localmente
                $caminhoSalvar = $subdir . '/' . $nomeArquivoOriginal;
    
                try {
                    // Download e salvamento da imagem
                    $imagemConteudo = file_get_contents($imagemUrl, false, $context);
                    if ($imagemConteudo !== false) {
                        file_put_contents($caminhoSalvar, $imagemConteudo);
                    } else {
                        // Tratamento de erro: Falha ao baixar a imagem
                        continue;
                    }
                } catch (Exception $e) {
                    // Tratamento de exceção: Falha ao salvar a imagem
                    continue;
                }
    
                // Atualiza o caminho da imagem para o Twig (caminho relativo para a web)
                $imagem['imagem'] = $subdir . '/' . $nomeArquivoOriginal;
            }
    
            // Verifica se o imóvel está ativo e formata o preço
            if ($imovel['status'] == 1) {
                $imovel['preco'] = number_format($imovel['preco'], 2, ',', '.');
                $imoveisComImagens[] = $imovel;
            }
        }

        // Salva os dados processados no cache
        file_put_contents($this->cacheFile, json_encode($imoveisComImagens));

        return $imoveisComImagens;
    }
    
}