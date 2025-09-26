<?php
#
# Arquivo de Funções Auxiliares (Helpers).
# Contém funções globais que simplificam tarefas comuns, como renderizar views
# ou redirecionar usuários. É carregado globalmente pelo composer.json.
#

/**
 * Renderiza um arquivo de view, opcionalmente passando dados para ele.
 * A função agora gerencia o header e o footer, centralizando o layout.
 *
 * @param string $view O nome do arquivo da view (ex: 'auth/login').
 * @param array $data Um array associativo de dados a serem extraídos como variáveis na view.
 */
function view(string $view, array $data = [])
{
    // Transforma as chaves do array $data em variáveis
    extract($data);

    // Constrói o caminho completo para o arquivo da view usando a constante ROOT_PATH
    $viewPath = ROOT_PATH . '/views/' . str_replace('.', '/', $view) . '.view.php';

    if (file_exists($viewPath)) {
        // Inclui o cabeçalho, a view principal e o rodapé.
        // Isso remove a necessidade de require_once em cada arquivo de view.
        require ROOT_PATH . '/views/_partials/header.php';
        require $viewPath;
        require ROOT_PATH . '/views/_partials/footer.php';
    } else {
        throw new \Exception("View não encontrada em: {$viewPath}");
    }
}

/**
 * Redireciona o usuário para uma URL.
 *
 * @param string $url A URL para a qual redirecionar (ex: '/login').
 */
function redirect(string $url)
{
    if (strpos($url, '/') !== 0) {
        $url = '/' . $url;
    }
    header('Location: ' . $url);
    exit();
}