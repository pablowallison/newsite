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

    $data['theme'] = THEME;

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
    
    $imoveis = new \App\RequestImoveis;
    $result = $imoveis->loadAll();

    //var_dump($result);
    
    foreach ($result['data'] as $imovel) {  
        if($imovel['status'] == 1){
            $imovel['imagens']; // Certifique-se de que este dado está sendo corretamente tratado
            $imovel['preco'] = number_format($imovel['preco'], 2, ',', '.');
            $imoveisComImagens[] = $imovel; // Adiciona o imóvel com imagens no array
        }
        
    }

    $classActive = isset($args['action']) ? $args['action'] : 'home';

    $data = ['title' => 'Concretiza Construções',
             'active' => $classActive,
             'imoveis' => $imoveisComImagens];

    renderLayout($twig, 'home.html', $data);
});



// Executa a rota correspondente
try {
    $route->run($action, $param);
} catch (Exception $e) {
    echo "Página não encontrada";
    exit();
}
?>