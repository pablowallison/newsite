<?php
require_once __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/init.php';

define('ROOT', getcwd());
//var_dump(ROOT);
define('URL', $config['url']);
define('THEME', $config['theme']);
define('THEME_PATH', ROOT . '/template/' . THEME);

// Configuração do Twig
$loader = new \Twig\Loader\FilesystemLoader(__DIR__ . '/template/' . THEME);
$twig = new \Twig\Environment($loader);

// Função para renderizar o layout com conteúdo (se necessário)
function renderLayout($twig, $template, $data = []) {

    //var_dump($data);
    $categoriaImoveis = new \App\ImoveisService();
    $resultCategoriaImoveis = $categoriaImoveis->loadCategoriaImoveis();
    $resultTipoImoveis = $categoriaImoveis->loadTipoImoveis();
    $iconeSite = './imagens/assets/icon.svg';
    //var_dump($iconeSite);
    

    $data['dropdown_categoria_imoveis'] = $resultCategoriaImoveis['data'];
    $data['dropdown_tipo_imoveis'] = $resultTipoImoveis['data'];
    $data['url'] = URL;
    $data['theme'] = THEME;
    $data['root'] = ROOT;
    $data['icon'] = $iconeSite;
    //var_dump($data);

    $content = $twig->render($template, $data);
    echo $twig->render($template, array_merge($data, ['content' => $content]));
}

$route = new \App\Route();
// Obtém o parâmetro 'action' da URL, sanitiza-o para evitar XSS, e combina os arrays $_GET e $_POST em um único array $param.
$action = isset($_GET['action']) ? htmlspecialchars($_GET['action']) : '';
$param = array_merge($_GET, $_POST);

$route->add('', function($args) use ($twig) {
    header("Location: index.php?action=home" );
    exit();
});

$route->add('home', function($args) use ($twig) {
    
    $results = new \App\ImoveisService();
    $result = $results->loadAll();
    //var_dump($result['data']['0']['imagens']);
    // Verifica se o imóvel está ativo e formata o preço
    foreach($result['data']['imoveis'] as &$imovel){
        if ($imovel['status'] == 1) {
            $imovel['preco'] = number_format($imovel['preco'], 2, ',', '.');
            $imoveis[] = $imovel;
        }
        //var_dump($imovel[0]);
    }
    /*if ($imoveis['status'] == 1) {
        $imoveis['preco'] = number_format($imoveis['preco'], 2, ',', '.');
        $imoveis[] = $imoveis;
    }*/
    //var_dump($imovel);

    // Determina a classe ativa para a página
    $classActive = isset($args['action']) ? $args['action'] : 'home';

    // Dados para renderizar na view
    $data = [
        'title' => 'Concretiza Construções',
        'active' => $classActive,
        'imoveis' => $imoveis
    ];

    // Renderiza a view utilizando Twig
    renderLayout($twig, 'index.html', $data);
});

$route->add('imovel', function($args) use ($twig) {
    //var_dump($_GET);
    $imoveis = new \App\ImoveisService();
    $result_all = $imoveis->loadAll();
    
    foreach($result_all['data']['imoveis'] as &$imovelAll){
        if ($imovelAll['status'] == 1) {
            $imovelAll['preco'] = number_format($imovelAll['preco'], 2, ',', '.');
            $imoveisAll[] = $imovelAll;
        }
        //var_dump($imovel[0]);
    }

    $result = $imoveis->load($args['id']);
    //var_dump($result);
    //var_dump($result['data']['0']);
    // Determina a classe ativa para a página
    $classActive = isset($args['action']) ? $args['action'] : 'home';

    // Dados para renderizar na view
    $data = [
        'title' => 'Concretiza Construções',
        'active' => $classActive,
        'imovel' => $result['data']['0'],
        'imoveis' => $imoveisAll
    ];

    // Renderiza a view utilizando Twig
    renderLayout($twig, 'property-detail.html', $data);
});

$route->add('venda', function($args) use ($twig) {
    
    // Determina a classe ativa para a página
    $classActive = isset($args['action']) ? $args['action'] : 'home';

    // Dados para renderizar na view
    $data = [
        'title' => 'Concretiza Construções',
        'active' => $classActive,
    ];

    // Renderiza a view utilizando Twig
    renderLayout($twig, 'venda.html', $data);
});

$route->add('destaques', function($args) use ($twig) {
    
    // Determina a classe ativa para a página
    $classActive = isset($args['action']) ? $args['action'] : 'home';

    // Dados para renderizar na view
    $data = [
        'title' => 'Concretiza Construções',
        'active' => $classActive,
    ];

    // Renderiza a view utilizando Twig
    renderLayout($twig, 'venda.html', $data);
});

$route->add('search', function($args) use ($twig) {
    
    
    //var_dump($args);
    
    // Captura os dados do formulário enviados via GET
    $filters = [];
    
    // Captura e adiciona os filtros, se existirem
    if (!empty($args['pretensao'])) {
        $filters[] = ['categoria_imoveis_id', '=', (int) $args['pretensao']];
    }
    
    if (!empty($args['tipo_imovel'])) {
        $filters[] = ['tipo_imovel_id', '=', $args['tipo_imovel']];
    }

    if (!empty($args['localizacao'])) {
        $filters[] = ['bairro', 'LIKE', '%' . $args['localizacao'] . '%'];
    }

    if (!empty($args['preco'])) {
        $precoFormatado1 = str_replace('R$', '', $args['preco']);
        $precoFormatado = str_replace('.', '', $precoFormatado1);
        // Substitui a vírgula pelo ponto para usar o formato decimal do PHP
        $args['preco'] = str_replace(',', '.', $precoFormatado);
        //var_dump($args['preco']);
        $filters[] = ['preco', '<=', (float) $args['preco']];
        
    }

    if (!empty($args['quartos'])) {
        $filters[] = ['quarto', '>=', (int) $args['quartos']];
    }

    if (!empty($args['banheiros'])) {
        $filters[] = ['banheiro', '>=', (int) $args['banheiros']];
    }
    
    //implementa a paginação na busca
    $page = !empty($args['page']) ? (int) $args['page'] : 1;
    $perPage = 1;
    $offset = ($page - 1) * $perPage;

    // Adiciona mais filtros conforme necessário
    $params = [
        'filters' => $filters,
        'order' => 'created_at',
        'direction' => 'desc',
        'limit'     => $perPage,
        'offset'    => $offset
    ];

    $imoveisService = new App\ImoveisService();
    $result = $imoveisService->loadAll($params);
    
    //carrega a lista de imóveis
    $listaImoveis = $result['data']['imoveis'];
    //var_dump($listaImoveis);
    $totalImoveis = $result['data']['total']; 
    $totalPaginas = ceil($totalImoveis / $perPage);

    // Verifica se o imóvel está ativo e formata o preço
     foreach($listaImoveis as &$imovel){
        if ($imovel['status'] == 1) {
            $imovel['preco'] = number_format($imovel['preco'], 2, ',', '.');
            $imoveis[] = $imovel;
        }
        
    }
    //var_dump($totalImoveis);

    // Determina a classe ativa para a página
    $classActive = isset($args['action']) ? $args['action'] : 'home';
    //var_dump($args['preco']);
    //$args['preco'] = urldecode($args['preco']);
    // Dados para renderizar na view
    // Decodifica os dados da URL
    //var_dump($args['preco']);
    /*if (!empty($args['preco'])) {
        $args['preco'] = urldecode($args['preco']); // Decodifica o valor para o formato original
        // Remove os separadores de milhares e converte a vírgula em ponto
        $args['preco'] = str_replace('.', '', $args['preco']);
        
    }*/
    if(!empty($args['preco'])){
        $args['preco'] = number_format($args['preco'], 2, ',', '.');
    }
    
    //var_dump($args['preco']);
    $data = [
        'args' => $args,
        'title' => 'Concretiza Construções',
        'active' => $classActive,
        'imoveis' => isset($imoveis) ? $imoveis : ['not-found' => 'Imóveis não encontrado!'],
        'totalPaginas'  => $totalPaginas,
        'paginaAtual'   => $page
        
    ];
    //var_dump($data);
    // Renderiza a view utilizando Twig
    renderLayout($twig, 'properties-list.html', $data);
});

// Executa a rota correspondente
try {
    //var_dump($param);
    $route->run($action, $param);
} catch (Exception $e) {
    echo "Página não encontrada";
    exit();
}
?>