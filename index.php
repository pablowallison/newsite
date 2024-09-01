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
    
    $urlBase = 'https://painel.concretizaconstrucoes.com/';  
    $diretorio = 'imagens/imobiliaria/imoveis/'; // Caminho absoluto

    if (!is_dir($diretorio)) {
        mkdir($diretorio, 0755, true);
    }

    $imoveisComImagens = []; // Inicializando o array para armazenar imóveis com imagens

    foreach ($result['data'] as $imovel) {
        foreach ($imovel['imagens'] as &$imagem) {
            $array_path_imagens = explode('/', $imagem['imagem']);

            // Verificação da existência do índice 2
            if (isset($array_path_imagens[2])) {
                $subdir = $diretorio . $array_path_imagens[2];
                if (!is_dir($subdir)) {
                    mkdir($subdir, 0755, true);
                }
            } else {
                // Tratamento de erro: diretório não foi identificado corretamente
                continue;
            }

            // Sanitização e codificação do nome da imagem
            $nomeArquivoOriginal = basename($imagem['imagem']);
            $nomeArquivoSanitizado = str_replace(['+', '(', ')', ' '], ['-', '_', '_', '-'], $nomeArquivoOriginal);
            $nomeArquivoCodificado = rawurlencode($nomeArquivoSanitizado); // Codificação do nome do arquivo

            // Construção da URL completa e codificação de espaços
            $imagemUrl = $urlBase . ltrim(str_replace(' ', '%20', $imagem['imagem']), '/'); 

            $caminhoSalvar = $subdir . '/' . $nomeArquivoSanitizado;

            try {
                // Download e salvamento da imagem
                $imagemConteudo = file_get_contents($imagemUrl); // Usa a URL codificada para download
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

            // Atualiza o caminho da imagem para o Twig
            $imagem['imagem'] = $subdir . '/' . $nomeArquivoCodificado;
        }

        if ($imovel['status'] == 1) {
            $imovel['preco'] = number_format($imovel['preco'], 2, ',', '.');
            $imoveisComImagens[] = $imovel;
        }
    }

    $classActive = isset($args['action']) ? $args['action'] : 'home';

    $data = [
        'title' => 'Concretiza Construções',
        'active' => $classActive,
        'imoveis' => $imoveisComImagens
    ];

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