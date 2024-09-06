<?php

namespace App;

use Exception;

class ImoveisService {
    
    private $cacheFile = './cache/imoveis.json'; // Caminho para o arquivo de cache
    private $cacheTTL = 3600; // Tempo de vida do cache em segundos (exemplo: 1 hora)

    public function loadAll($param = null) {
        try {

            // Parâmetros de filtro
            $body = [
                'limit' => isset($param['limit']) ? $param['limit'] : '3',
                'order' => isset($param['order']) ? $param['order'] : 'id',
                'direction' => isset($param['direction']) ? $param['direction'] : 'desc'
            ];

            // Verifica se existem filtros
            if (!empty($param['filters'])) {
                foreach ($param['filters'] as $index => $filter) {
                    if (isset($filter[0], $filter[1], $filter[2])) {
                        // Prepara o filtro com os índices corretos
                        $body["filters[{$index}][0]"] = $filter[0];  // Campo
                        $body["filters[{$index}][1]"] = $filter[1];  // Operador
                        $body["filters[{$index}][2]"] = $filter[2];  // Valor
                    }
                }
            }
            //var_dump($body);
            // URL da API que você quer acessar
            $location = 'https://painel.concretizaconstrucoes.com/rest.php?class=ImoveisRestService&method=LoadAll';
            
            // Cria a URL com query string
            $query = http_build_query($body);
            $urlWithQuery = $location . '&' . $query; // Concatena a query string à URL

            $data = new \App\RequestData();
            $result = $data->request($urlWithQuery, 'GET', null);
            //var_dump($result);
            return $result;

        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage();
            return null;
        }
    }

    public function loadAllProperty($param = null){

        
        // Verifica se o cache existe e não está expirado
        if (file_exists($this->cacheFile) && (time() - filemtime($this->cacheFile) < $this->cacheTTL)) {
            // Carrega dados do cache
            $imoveisComImagens = json_decode(file_get_contents($this->cacheFile), true);
            return $imoveisComImagens;
        }

        $result = $this->loadAll($param);

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
        var_dump($imoveisComImagens);

        return $imoveisComImagens;
        /*if (empty($result['data']) || $result['data'] === $imoveisComImagens) {
            // Retorna os dados do cache, pois os dados não mudaram
            return $imoveisComImagens;
        }else{
        }*/

    }

    public function load($id) {
        try {
            // Verifica se o ID foi fornecido
            if (empty($id)) {
                throw new Exception("O ID é necessário para carregar o imóvel.");
            }
    
            // URL da API para carregar um imóvel específico
            $location = 'https://painel.concretizaconstrucoes.com/rest.php?class=ImoveisRestService&method=load';
    
            // Adiciona o ID como um parâmetro na URL
            $location .= '&id=' . urlencode($id);
    
            // Fazendo a requisição à API
            //$retorno = $this->request($location, 'GET', null);

            $data = new \App\RequestData();
            $result = $data->request($location, 'GET', null);
            return $result;
    
            // Retorna o resultado da requisição
            //return $retorno;
        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage();
            return null;
        }
    }

    public function loadCategoriaImoveis(){
         
        try {
            // Parâmetros de filtro
            /*$body = [
                'limit' => '3',
                'order' => 'nome',
                'direction' => 'desc'
            ];*/

            // URL da API que você quer acessar
            $location = 'https://painel.concretizaconstrucoes.com/rest.php?class=CategoriaImoveisRestService&method=LoadAll';

            // Fazendo a requisição à API
            $data = new \App\RequestData();
            $result = $data->request($location, 'GET', null);
            return $result;
        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage();
            return null;
        }
        //$categoriaImoveis = new \App\RequestImoveis;
        //$resultCategoriaImoveis = $categoriaImoveis->loadPropertyCategory(); 
    }

    public function loadTipoImoveis(){
         
        try {
            // Parâmetros de filtro
            /*$body = [
                'limit' => '3',
                'order' => 'nome',
                'direction' => 'desc'
            ];*/

            // URL da API que você quer acessar
            $location = 'https://painel.concretizaconstrucoes.com/rest.php?class=TipoImoveisRestService&method=loadAll';

            // Fazendo a requisição à API
            $data = new \App\RequestData();
            $resultTipoImovel = $data->request($location, 'GET', null);
            //var_dump($resultTipoImovel);
            return $resultTipoImovel;
        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage();
            return null;
        }
        //$categoriaImoveis = new \App\RequestImoveis;
        //$resultCategoriaImoveis = $categoriaImoveis->loadPropertyCategory(); 
    }

}