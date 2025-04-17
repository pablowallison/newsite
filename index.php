<?php
//teste
require_once __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/init.php';

define('ROOT', getcwd());
//var_dump(ROOT);
define('URL', $config['url']);
define('CDN', $config['cdn']);
define('THEME', $config['theme']);
define('THEME_PATH', ROOT . '/template/' . THEME);

//var_dump($_SERVER);

$urlAtual = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
//var_dump($urlAtual);


// Configuração do Twig
$loader = new \Twig\Loader\FilesystemLoader(__DIR__ . '/template/' . THEME);
$twig = new \Twig\Environment($loader);

// Função para renderizar o layout com conteúdo (se necessário)
function renderLayout($twig, $template, $data = []) {

    //var_dump($data);
    $categoriaImoveis = new \App\CategoriaImoveisService();
    $resultCategoriaImoveis = $categoriaImoveis->loadAll();
    
    // Carrega os tipos de imóveis
    $TipoImoveis = new \App\TipoImoveisService();
    $resultTipoImoveis = $TipoImoveis->loadAll();
    //var_dump($resultTipoImoveis);   

    //Armazena o ícone do site
    $iconeSite = './imagens/assets/icon.svg';
    //var_dump($iconeSite);
    
    $logosPartners = new \App\LogosPartnersService();
    $resultLogosPartners = $logosPartners->loadAll();
    //var_dump($resultLogosPartners);

    $data['dropdown_categoria_imoveis'] = $resultCategoriaImoveis['data'];
    $data['dropdown_tipo_imoveis'] = $resultTipoImoveis['data']['tipo_imoveis'];
    $data['contagem_por_tipo'] = $resultTipoImoveis['data']['contagem_por_tipo'];
    $data['logos_partners'] = $resultLogosPartners['data'];
    $data['url'] = URL;
    $data['cdn'] = CDN;
    $data['theme'] = THEME;
    $data['root'] = ROOT;
    //var_dump(ROOT);
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
    header("Location: home" );
    exit();
});

$route->add('home', function($args) use ($twig) {

    $results = new \App\ImoveisService();
    $result = $results->loadAll();

    // Inicializa os arrays
    $imoveis = [];
    $imoveisAgrupados = [];

    // Percorre os imóveis e trabalha apenas com os ativos
    foreach ($result['data']['imoveis'] as &$imovel) {
        if ($imovel['status'] == 1) {
            // Formata o preço e adiciona ao array de imóveis ativos
            $imovel['preco'] = number_format($imovel['preco'], 2, ',', '.');
            $imoveis[] = $imovel;

            // Processa o link do bairro, se definido
            if (isset($imovel['bairro'])) {
                $bairroLink = $imovel['bairro'];
                $bairroLink = mb_strtolower($bairroLink);
                $bairroLink = iconv('UTF-8', 'ASCII//TRANSLIT', $bairroLink);
                $bairroLink = preg_replace('/[^a-z0-9 ]/', '', $bairroLink);
                $bairroLink = trim(preg_replace('/\s+/', ' ', $bairroLink));
                $bairroLink = str_replace(' ', '+', $bairroLink);
            }

            // Define o bairro e agrupa os imóveis ativos por bairro
            $bairro = $imovel['bairro'];
            if ($bairro) {
                if (!isset($imoveisAgrupados[$bairro])) {
                    $imoveisAgrupados[$bairro] = [
                        'bairro' => $bairro,
                        'total' => 0,
                        'bairroLink' => $bairroLink,
                        'imoveis' => [] // Aqui serão armazenados os imóveis deste bairro
                    ];
                }

                // Incrementa a contagem e adiciona o imóvel ativo ao agrupamento
                $imoveisAgrupados[$bairro]['total']++;
                $imoveisAgrupados[$bairro]['imoveis'][] = $imovel;
            }
        }
    }

    // Determina a classe ativa para a página
    $classActive = isset($args['action']) ? $args['action'] : 'home';

    // Dados para renderizar na view
    $data = [
        'title' => 'Concretiza Construções',
        'active' => $classActive,
        'imoveis' => $imoveis,
        'imoveisAgrupados' => $imoveisAgrupados
    ];

    // Renderiza a view utilizando Twig
    renderLayout($twig, 'index.html', $data);
});


$route->add('imovel', function($args) use ($twig) {
    //TRATA OS DADOS DO FORMULÁRIO
    //var_dump($args);
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['sendmessage'])) {
        // Bloco de código a ser executado quando o botão for clicado
    
        $imoveis_id = filter_input(INPUT_POST, 'imoveis_id', FILTER_SANITIZE_NUMBER_INT);
        $nome = trim(htmlspecialchars(filter_input(INPUT_POST, 'nome', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?? '', ENT_QUOTES, 'UTF-8'));
        $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
        $telefone = trim(preg_replace('/\D/', '', filter_input(INPUT_POST, 'telefone', FILTER_SANITIZE_NUMBER_INT) ?? ''));
        $mensagem = trim(htmlspecialchars(filter_input(INPUT_POST, 'message_lead', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?? '', ENT_QUOTES, 'UTF-8'));
        
        $postData = [
            'data' => [
                'imoveis_id'   => intval($imoveis_id) ?: null,
                'nome'         => $nome,
                'email'        => $email,
                'telefone'     => $telefone,
                'message_lead' => $mensagem
            ]
        ];

        $result = new \App\LeadImobService();
        $resultAll = $result->lead($postData);
        
        //var_dump($resultAll);

        header("Location: index?action=imovel&id=$imoveis_id");
        //exit;
        // Aqui você pode incluir o processamento desejado, como inserir dados no banco ou enviar e-mails.
    }
    //var_dump($args);

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
    //var_dump($args);
    $result = $imoveis->load(isset($args['params'][0]) ? $args['params'][0] : $args['id']);
    //var_dump($result);
    //var_dump($result['data']['0']);
    
    // Verifica se a requisição foi bem-sucedida
    $geolocalizacao = new \App\GoogleMapsService();
    $coordenadas = $geolocalizacao->loadCoord($result);
    //var_dump($coordenadas);

    // Determina a classe ativa para a página
    $classActive = isset($args['action']) ? $args['action'] : 'home';

    // Dados para renderizar na view
    $data = [
        'title' => 'Concretiza Construções',
        'active' => $classActive,
        'imovel' => $result['data']['0'],
        'imoveis' => $imoveisAll,
        'imovel_id' => intval(isset($args['params'][0]) ? $args['params'][0] : $args['id']),
        'coordenadas' => isset($coordenadas) ? $coordenadas : null,
        'action' => isset($args['action']) ? $args['action'] : 'home'
    ];

    // Renderiza a view utilizando Twig
    renderLayout($twig, 'property-detail.html', $data);
});

$route->add('lead2', function($args) use ($twig) {

    // Captura e sanitiza os dados do formulário
    $imoveis_id = filter_input(INPUT_POST, 'imoveis_id', FILTER_SANITIZE_NUMBER_INT);
    $nome = trim(htmlspecialchars(filter_input(INPUT_POST, 'nome', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?? '', ENT_QUOTES, 'UTF-8'));
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $telefone = trim(preg_replace('/\D/', '', filter_input(INPUT_POST, 'telefone', FILTER_SANITIZE_NUMBER_INT) ?? '')); // Apenas números
    $mensagem = trim(htmlspecialchars(filter_input(INPUT_POST, 'message_lead', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?? '', ENT_QUOTES, 'UTF-8'));

    // Valida os campos obrigatórios
    /*if (empty($nome) || !$email || empty($telefone) || empty($mensagem)) {
        echo "<script>alert('Erro: Todos os campos são obrigatórios e o e-mail deve ser válido!'); window.history.back();</script>";
        exit;
    }*/

    // Validação de telefone: apenas números, mínimo de 10 e máximo de 15 dígitos
    /*if (!preg_match('/^\d{10,15}$/', $telefone)) {
        echo "<script>alert('Erro: Telefone inválido!'); window.history.back();</script>";
        exit;
    }*/

    // Dados para envio na API
    $postData = [
        'data' => [
            'imoveis_id'   => intval($imoveis_id) ?: null,
            'nome'         => $nome,
            'email'        => $email,
            'telefone'     => $telefone,
            'message_lead' => $mensagem
        ]
    ];
    //var_dump($postData);

    // URL da API
    $url = "https://painel.concretizaconstrucoes.com/rest.php?class=LeadImobRestService&method=Store";

    // Armazene o token de forma segura
    $token = getenv('API_TOKEN') ?: 'Basic 9fbbb2c765d1d5d12c1e3582a9329108c4ed9a96b199ffab6700a413869c';

    // Configuração do cURL
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData));
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Authorization: ' . $token
    ]);

    // Executa a requisição e captura a resposta
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    var_dump($httpCode);
    $curlError = curl_error($ch);
    var_dump($curlError);
    curl_close($ch);
    
    if ($response === false) {
        error_log("Erro ao conectar à API. Erro: " . $curlError);
        echo "<script>alert('Erro ao conectar à API. Tente novamente mais tarde.'); window.history.back();</script>";
        //exit;
    }

    // Decodifica a resposta da API
    $responseData = json_decode($response, true);
    var_dump($responseData);
    // Verifica se a requisição foi bem-sucedida
    if (!is_array($responseData) || !isset($responseData['status']) || $responseData['status'] !== 'success') {
        error_log("Erro na API: " . json_encode($responseData));
        echo "<script>alert('Erro ao enviar a mensagem. Verifique os dados e tente novamente.'); window.history.back();</script>";
        //exit;
    }

    echo "<script>alert('Mensagem enviada com sucesso!'); window.location.href='index.php';</script>";
});

$route->add('blogs', function($args) use ($twig) {

    // Determina a classe ativa para a página
    $classActive = isset($args['action']) ? $args['action'] : 'home';

    $data = [
        'active' => $classActive,
    ];


    // Renderiza a view utilizando Twig
    renderLayout($twig, 'blogs.html', $data);

});

$route->add('blog-detail', function($args) use ($twig) {

    // Determina a classe ativa para a página
    $classActive = isset($args['action']) ? $args['action'] : 'home';

    $data = [
        'active' => $classActive,
    ];


    // Renderiza a view utilizando Twig
    renderLayout($twig, 'blog-detail.html', $data);

});

$route->add('about', function($args) use ($twig) {

    // Determina a classe ativa para a página
    $classActive = isset($args['action']) ? $args['action'] : 'home';

    $data = [
        'active' => $classActive,
    ];


    // Renderiza a view utilizando Twig
    renderLayout($twig, 'about.html', $data);

});

$route->add('contate-nos', function($args) use ($twig) {

    // Determina a classe ativa para a página
    $classActive = isset($args['action']) ? $args['action'] : 'home';

    $data = [
        'active' => $classActive,
    ];


    // Renderiza a view utilizando Twig
    renderLayout($twig, 'contact.html', $data);

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
    
    //faz o tratamento do parametro localizacao
    if(isset($args['localizacao'])){
        
        $args['localizacao'] = str_replace('+', ' ', $args['localizacao']);
    }
    //var_dump($args);
    
    // Captura os dados do formulário enviados via GET
    $filters = [];
    //var_dump($args);
    
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
    $perPage = 8;
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
    //var_dump($result);
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
        'imoveis' => isset($imoveis) ? $imoveis : NULL, //? $imoveis : ['not-found' => 'Imóveis não encontrado!'],
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