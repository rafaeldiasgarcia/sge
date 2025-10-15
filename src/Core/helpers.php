<?php
/**
 * Arquivo de Funções Auxiliares (Helpers)
 * 
 * Contém funções globais que simplificam tarefas comuns em toda a aplicação.
 * Este arquivo é carregado automaticamente pelo Composer através da configuração
 * no composer.json (seção "files" do autoload).
 * 
 * Funções disponíveis:
 * - view(): Renderiza views com layout automático
 * - redirect(): Redireciona para outras páginas
 * - formatarTelefone(): Formata telefones brasileiros
 * 
 * @package Application\Core
 */

/**
 * Renderiza um arquivo de view com layout automático
 * 
 * Esta função centraliza o processo de renderização de views, automaticamente
 * incluindo o header e footer em todas as páginas. Isso elimina a necessidade
 * de incluir manualmente esses arquivos em cada view.
 * 
 * A função utiliza extract() para tornar todas as variáveis do array $data
 * disponíveis diretamente na view (sem precisar acessar via array).
 * 
 * Exemplo de uso:
 * view('auth/login', ['erro' => 'Senha incorreta']);
 * 
 * Isso renderizará:
 * 1. views/_partials/header.php
 * 2. views/auth/login.view.php (com variável $erro disponível)
 * 3. views/_partials/footer.php
 * 
 * @param string $view O nome do arquivo da view sem extensão (ex: 'auth/login', 'pages/agenda')
 * @param array $data Array associativo de dados que serão extraídos como variáveis na view
 * @return void
 * @throws \Exception Se o arquivo de view não for encontrado
 */
function view(string $view, array $data = [])
{
    // Transforma as chaves do array $data em variáveis acessíveis na view
    // Exemplo: ['nome' => 'João'] torna-se $nome = 'João'
    extract($data);

    // Detecta se é uma página de autenticação baseada no prefixo do caminho
    // Usado no header.php para ajustar o layout (ex: não mostrar menu de navegação)
    $isAuthPage = strpos($view, 'auth/') === 0;

    // Constrói o caminho completo para o arquivo da view
    // Substitui pontos por barras e adiciona a extensão .view.php
    // Exemplo: 'auth/login' -> ROOT_PATH . '/views/auth/login.view.php'
    $viewPath = ROOT_PATH . '/views/' . str_replace('.', '/', $view) . '.view.php';

    // Verifica se o arquivo existe antes de tentar incluí-lo
    if (file_exists($viewPath)) {
        // Inclui o cabeçalho (DOCTYPE, <head>, <body>, navegação)
        require ROOT_PATH . '/views/_partials/header.php';
        
        // Inclui a view principal (conteúdo específico da página)
        require $viewPath;
        
        // Inclui o rodapé (scripts JavaScript, fechamento de tags)
        require ROOT_PATH . '/views/_partials/footer.php';
    } else {
        // Se a view não existir, lança uma exceção com o caminho esperado
        // Isso ajuda no debug mostrando exatamente onde o sistema procurou
        throw new \Exception("View não encontrada em: {$viewPath}");
    }
}

/**
 * Redireciona o usuário para uma URL específica
 * 
 * Esta função envia um header HTTP de redirecionamento (Location) e encerra
 * a execução do script. É uma forma simplificada de fazer redirecionamentos.
 * 
 * A função garante que a URL sempre comece com '/' (caminho absoluto),
 * adicionando a barra se necessário.
 * 
 * Exemplos de uso:
 * redirect('/login');              // Redireciona para a página de login
 * redirect('/usuario/perfil');     // Redireciona para o perfil do usuário
 * redirect('dashboard');           // Adiciona '/' automaticamente
 * 
 * @param string $url A URL de destino (com ou sem '/' inicial)
 * @return void Esta função nunca retorna (chama exit())
 */
function redirect(string $url)
{
    // Garante que a URL comece com '/'
    if (strpos($url, '/') !== 0) {
        $url = '/' . $url;
    }
    
    // Envia o header de redirecionamento
    header('Location: ' . $url);
    
    // Encerra a execução do script para garantir que nenhum código
    // adicional seja executado após o redirecionamento
    exit();
}

/**
 * Formata um telefone brasileiro para o padrão (00) 00000-0000
 * 
 * Esta função pega um número de telefone com 11 dígitos (DDD + número com 9 dígitos)
 * e o formata no padrão brasileiro legível.
 * 
 * Processo:
 * 1. Remove todos os caracteres não-numéricos
 * 2. Verifica se tem exatamente 11 dígitos
 * 3. Formata: (XX) XXXXX-XXXX
 * 
 * Exemplos:
 * formatarTelefone('11987654321')  -> '(11) 98765-4321'
 * formatarTelefone('(11)98765-4321') -> '(11) 98765-4321'
 * formatarTelefone('1234')         -> '1234' (retorna sem formatação se não tiver 11 dígitos)
 * formatarTelefone(null)           -> '' (retorna vazio para valores nulos)
 * 
 * @param string|null $telefone O telefone em qualquer formato (aceita caracteres especiais)
 * @return string O telefone formatado ou o valor original se não puder ser formatado
 */
function formatarTelefone(?string $telefone): string
{
    // Se o telefone for nulo ou vazio, retorna string vazia
    if (empty($telefone)) {
        return '';
    }
    
    // Remove tudo que não for número (parênteses, hífens, espaços, etc)
    $telefone = preg_replace('/[^0-9]/', '', $telefone);
    
    // Verifica se tem exatamente 11 dígitos (padrão brasileiro com DDD + 9 dígitos)
    if (strlen($telefone) !== 11) {
        // Se não tiver 11 dígitos, retorna como está (sem formatação)
        return $telefone;
    }
    
    // Formata no padrão brasileiro: (DDD) 9XXXX-XXXX
    // substr($telefone, 0, 2) -> DDD (primeiros 2 dígitos)
    // substr($telefone, 2, 5) -> Primeira parte do número (5 dígitos incluindo o 9)
    // substr($telefone, 7, 4) -> Segunda parte do número (últimos 4 dígitos)
    return '(' . substr($telefone, 0, 2) . ') ' . substr($telefone, 2, 5) . '-' . substr($telefone, 7, 4);
}

/**
 * Verifica se a requisição é AJAX
 *
 * @return bool
 */
function is_ajax_request(): bool
{
    return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
}

/**
 * Obtém um valor inteiro do POST com default seguro
 *
 * @param string $key
 * @param int $default
 * @return int
 */
function post_int(string $key, int $default = 0): int
{
    return isset($_POST[$key]) ? (int)$_POST[$key] : $default;
}

/**
 * Obtém uma string do POST com saneamento básico
 *
 * @param string $key
 * @param string $default
 * @return string
 */
function post_string(string $key, string $default = ''): string
{
    if (!isset($_POST[$key])) {
        return $default;
    }
    $value = is_string($_POST[$key]) ? $_POST[$key] : $default;
    return trim($value);
}

/**
 * Envia resposta JSON com cabeçalho adequado
 *
 * @param array $payload
 * @param int $statusCode
 * @return void
 */
function json_response(array $payload, int $statusCode = 200): void
{
    http_response_code($statusCode);
    header('Content-Type: application/json');
    echo json_encode($payload);
}

/**
 * Envia resposta JSON de sucesso padrão
 *
 * @param array $extra
 * @return void
 */
function json_success(array $extra = []): void
{
    json_response(array_merge(['success' => true], $extra));
}

/**
 * Envia resposta JSON de erro padrão
 *
 * @param string $message
 * @param int $statusCode
 * @param array $extra
 * @return void
 */
function json_error(string $message = 'Erro ao processar solicitação', int $statusCode = 400, array $extra = []): void
{
    json_response(array_merge(['success' => false, 'message' => $message], $extra), $statusCode);
}

/**
 * Resolve URL de redirecionamento após POST não-AJAX
 * Prioriza 'redirect_to' do POST, depois Referer com fallback seguro
 *
 * @param string $default
 * @return string
 */
function resolve_post_redirect(string $default = '/agenda'): string
{
    $redirectTo = $_POST['redirect_to'] ?? null;
    if ($redirectTo) {
        return $redirectTo;
    }
    $referer = $_SERVER['HTTP_REFERER'] ?? $default;
    // Normaliza fallback entre /perfil e /agenda
    return (strpos($referer, '/perfil') !== false) ? '/perfil' : $default;
}