public function loadAll() {
        try {
            //var_dump($param);
            // Parâmetros de filtro
            /*$body = [
                'limit' => '3',
                'order' => 'nome',
                'direction' => 'desc'
            ];*/

            // URL da API que você quer acessar
            $location = 'https://painel.concretizaconstrucoes.com/rest.php?class=ImoveisRestService&method=LoadAll';

            // Chave de autorização no formato Basic
            $authorization = 'Basic 9fbbb2c765d1d5d12c1e3582a9329108c4ed9a96b199ffab6700a413869c';

            // Fazendo a requisição à API
            return $this->request($location, 'GET', null, $authorization);
        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage();
            return null;
        }
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
    
            // Chave de autorização no formato Basic
            $authorization = 'Basic 9fbbb2c765d1d5d12c1e3582a9329108c4ed9a96b199ffab6700a413869c';
    
            // Fazendo a requisição à API
            $retorno = $this->request($location, 'GET', null, $authorization);
    
            // Retorna o resultado da requisição
            return $retorno;
        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage();
            return null;
        }
    }

    public function loadPropertyCategory(){
        
        try {
            // Parâmetros de filtro
            /*$body = [
                'limit' => '3',
                'order' => 'nome',
                'direction' => 'desc'
            ];*/

            // URL da API que você quer acessar
            $location = 'https://painel.concretizaconstrucoes.com/rest.php?class=CategoriaImoveisRestService&method=LoadAll';

            // Chave de autorização no formato Basic
            $authorization = 'Basic 9fbbb2c765d1d5d12c1e3582a9329108c4ed9a96b199ffab6700a413869c';

            // Fazendo a requisição à API
            return $this->request($location, 'GET', null, $authorization);
        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage();
            return null;
        }

        // Fazendo a requisição à API
        return $this->request($location, 'GET', null, $authorization);
    }