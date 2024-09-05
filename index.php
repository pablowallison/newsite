<?php
require_once __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/init.php';

define('ROOT', getcwd());
define('THEME', $config['theme']);
define('THEME_PATH', ROOT . '/template/' . THEME);

// Configuração do Twig
$loader = new \Twig\Loader\FilesystemLoader(__DIR__ . '/template/' . THEME);
$twig = new \Twig\Environment($loader);

// Função para renderizar o layout com conteúdo (se necessário)
function renderLayout($twig, $template, $data = []) {

    $categoriaImoveis = new \App\ImoveisService();
    $resultCategoriaImoveis = $categoriaImoveis->loadCategoriaImoveis();
    $resultTipoImoveis = $categoriaImoveis->loadTipoImoveis();
    //var_dump($resultTipoImoveis);


    $data['dropdown_categoria_imoveis'] = $resultCategoriaImoveis['data'];
    $data['dropdown_tipo_imoveis'] = $resultTipoImoveis['data'];
    $data['theme'] = THEME;
    $data['root'] = ROOT;
    //var_dump($data);

    $content = $twig->render($template, $data);
    echo $twig->render($template, array_merge($data, ['content' => $content]));
}

$route = new \App\Route();
// Obtém o parâmetro 'action' da URL, sanitiza-o para evitar XSS, e combina os arrays $_GET e $_POST em um único array $param.
$action = isset($_GET['action']) ? htmlspecialchars($_GET['action']) : '';
$param = array_merge($_GET, $_POST);

$route->add('', function($args) use ($twig) {
    header('Location: index.php?action=home');
    exit();
});

$route->add('home', function($args) use ($twig) {
    
    $result = new \App\ImoveisService();
    $imoveis = $result->loadAllProperty();

    // Determina a classe ativa para a página
    $classActive = isset($args['action']) ? $args['action'] : 'home';

    // Dados para renderizar na view
    $data = [
        'title' => 'Concretiza Construções',
        'active' => $classActive,
        'imoveis' => $imoveis
    ];

    // Renderiza a view utilizando Twig
    renderLayout($twig, 'home.html', $data);
});

$route->add('imovel', function($args) use ($twig) {
    $imoveis = new \App\ImoveisService();
    $result = $imoveis->load($args['id']);
    //var_dump($result['data']['0']);

    // Determina a classe ativa para a página
    $classActive = isset($args['action']) ? $args['action'] : 'home';

    // Dados para renderizar na view
    $data = [
        'title' => 'Concretiza Construções',
        'active' => $classActive,
        'imovel' => $result['data']['0']
    ];

    // Renderiza a view utilizando Twig
    renderLayout($twig, 'imovel.html', $data);
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
        $filters[] = ['preco', '<=', (float) $args['preco']];
    }

    if (!empty($args['quartos'])) {
        $filters[] = ['quarto', '>=', (int) $args['quartos']];
    }

    if (!empty($args['banheiros'])) {
        $filters[] = ['banheiro', '>=', (int) $args['banheiros']];
    }

    // Adiciona mais filtros conforme necessário
    $params = [
        'filters' => $filters,
        'order' => 'created_at',
        'direction' => 'desc'
    ];

    $imoveisService = new App\ImoveisService();
    $result = $imoveisService->loadAll($params);

    var_dump($result);

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

// Executa a rota correspondente
try {
    $route->run($action, $param);
} catch (Exception $e) {
    echo "Página não encontrada";
    exit();
}
?>