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
    $data['root'] = ROOT;
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

    // Determina a classe ativa para a página
    $classActive = isset($args['action']) ? $args['action'] : 'home';

    // Dados para renderizar na view
    $data = [
        'title' => 'Concretiza Construções',
        'active' => $classActive,
        'imoveis' => $imoveisComImagens
    ];

    // Renderiza a view utilizando Twig
    renderLayout($twig, 'home.html', $data);
});

$route->add('imovel', function($args) use ($twig) {
    $imoveis = new \App\RequestImoveis;
    $result = $imoveis->load($args['id']);
    var_dump($result);

    // Determina a classe ativa para a página
    $classActive = isset($args['action']) ? $args['action'] : 'home';

    // Dados para renderizar na view
    $data = [
        'title' => 'Concretiza Construções',
        'active' => $classActive,
    ];

    // Renderiza a view utilizando Twig
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