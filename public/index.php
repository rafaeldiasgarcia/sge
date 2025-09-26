<?php
#
# Ponto de Entrada Único (Front Controller) da Aplicação.
# Todas as requisições são direcionadas para este arquivo.
# Ele inicializa o autoloader do Composer, a sessão, define a timezone,
# e despacha a URL para o roteador.
#

// Garante que a sessão seja a primeira coisa a ser iniciada.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Define o fuso horário padrão da aplicação
date_default_timezone_set('America/Sao_Paulo');

// Define uma constante para o caminho raiz do projeto, útil para incluir views.
define('ROOT_PATH', dirname(__DIR__));

// Carrega o autoloader do Composer, que permite o carregamento automático de classes.
require_once ROOT_PATH . '/vendor/autoload.php';

// Carrega o arquivo de rotas
require_once ROOT_PATH . '/src/routes.php';

// A URL vem do .htaccess. Se não vier, é a raiz.
$url = $_GET['url'] ?? '/';
if ($url !== '/') {
    $url = '/' . rtrim($url, '/');
}

$method = $_SERVER['REQUEST_METHOD'];

try {
    // Usa o roteador para encontrar e executar a ação do controller correspondente.
    Application\Core\Router::dispatch($url, $method);
} catch (Exception $e) {
    // Em caso de erro, exibe uma mensagem amigável.
    if (ob_get_level() > 0) ob_end_clean();
    http_response_code(500);
    echo "<h1>Erro na Aplicação</h1>";
    echo "<p>Ocorreu um erro inesperado. Detalhes:</p>";
    echo "<pre>" . htmlspecialchars($e->getMessage()) . "</pre>";
}