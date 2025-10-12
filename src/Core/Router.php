<?php
/**
 * Classe do Roteador (Router)
 * 
 * Responsável por gerenciar todas as rotas da aplicação e direcionar requisições HTTP
 * para os controllers e métodos apropriados.
 * 
 * Funcionalidades principais:
 * - Registrar rotas GET, POST e PUT
 * - Suportar parâmetros dinâmicos nas URLs (ex: /usuario/:id)
 * - Despachar requisições para o controller e método corretos
 * - Suportar method override (PUT via POST com campo _method)
 * 
 * As rotas são definidas no arquivo routes.php usando métodos estáticos desta classe.
 * 
 * Exemplo de uso:
 * Router::get('/usuarios', 'UsuarioController@index');
 * Router::get('/usuario/:id', 'UsuarioController@show');
 * Router::post('/usuario/salvar', 'UsuarioController@store');
 * 
 * @package Application\Core
 */
namespace Application\Core;

class Router
{
    /**
     * Array que armazena todas as rotas registradas, organizadas por método HTTP
     * 
     * Estrutura:
     * [
     *   'GET' => ['/rota' => 'Controller@metodo'],
     *   'POST' => ['/rota' => 'Controller@metodo'],
     *   'PUT' => ['/rota' => 'Controller@metodo']
     * ]
     * 
     * @var array
     */
    protected static array $routes = [];

    /**
     * Registra uma rota GET
     * 
     * Rotas GET são usadas para:
     * - Exibir páginas e formulários
     * - Listar recursos
     * - Buscar informações
     * 
     * @param string $uri O padrão da URI (ex: '/usuarios' ou '/usuario/:id')
     * @param string $action A ação no formato 'NomeDoController@nomeDoMetodo'
     * @return void
     */
    public static function get(string $uri, string $action): void
    {
        self::$routes['GET'][$uri] = $action;
    }

    /**
     * Registra uma rota POST
     * 
     * Rotas POST são usadas para:
     * - Enviar formulários
     * - Criar novos recursos
     * - Processar dados do usuário
     * 
     * @param string $uri O padrão da URI
     * @param string $action A ação no formato 'NomeDoController@nomeDoMetodo'
     * @return void
     */
    public static function post(string $uri, string $action): void
    {
        self::$routes['POST'][$uri] = $action;
    }

    /**
     * Registra uma rota PUT
     * 
     * Rotas PUT são usadas para:
     * - Atualizar recursos existentes
     * - Editar informações
     * 
     * Como formulários HTML não suportam PUT nativamente, usamos um campo
     * oculto _method="PUT" em formulários POST.
     * 
     * @param string $uri O padrão da URI
     * @param string $action A ação no formato 'NomeDoController@nomeDoMetodo'
     * @return void
     */
    public static function put(string $uri, string $action): void
    {
        self::$routes['PUT'][$uri] = $action;
    }

    /**
     * Extrai parâmetros dinâmicos de uma URI
     * 
     * Compara um padrão de rota com a URI atual e extrai os parâmetros dinâmicos.
     * 
     * Exemplos:
     * - Padrão: '/usuario/:id', URI: '/usuario/123' → ['id' => '123']
     * - Padrão: '/post/:categoria/:slug', URI: '/post/tech/meu-post' → ['categoria' => 'tech', 'slug' => 'meu-post']
     * 
     * @param string $pattern O padrão da rota com parâmetros (ex: '/usuario/:id')
     * @param string $uri A URI atual da requisição
     * @return array|null Array associativo com os parâmetros extraídos, ou null se não houver match
     */
    protected static function extractParams(string $pattern, string $uri): ?array
    {
        // Escapa as barras para uso em regex
        $pattern = str_replace('/', '\/', $pattern);
        
        // Substitui os parâmetros nomeados (ex: :id) por grupos de captura nomeados em regex
        // :id se torna (?P<id>[^\/]+) que captura qualquer caractere exceto /
        $pattern = preg_replace('/:([^\/]+)/', '(?P<\1>[^\/]+)', $pattern);
        
        // Adiciona âncoras de início e fim para match exato
        $pattern = '/^' . $pattern . '$/';

        // Tenta fazer match da URI com o padrão
        if (preg_match($pattern, $uri, $matches)) {
            // Remove o primeiro elemento (match completo)
            array_shift($matches);
            
            // Mantém apenas os parâmetros nomeados (remove índices numéricos criados por preg_match)
            return array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
        }

        return null;
    }

    /**
     * Despacha uma requisição para o controller apropriado
     * 
     * Este método é chamado pelo index.php para cada requisição recebida.
     * 
     * Processo:
     * 1. Verifica se há method override (PUT via POST)
     * 2. Itera sobre as rotas registradas para o método HTTP
     * 3. Tenta fazer match da URI com cada padrão de rota
     * 4. Extrai parâmetros dinâmicos da rota
     * 5. Instancia o controller e chama o método correspondente
     * 6. Passa os parâmetros extraídos para o método do controller
     * 
     * @param string $uri A URI da requisição (ex: '/usuario/123')
     * @param string $method O método HTTP (GET, POST, PUT)
     * @return void
     * @throws \Exception Se a rota não for encontrada ou o método não for suportado
     */
    public static function dispatch(string $uri, string $method): void
    {
        // Suporte para method override: permite usar PUT em formulários HTML
        // que nativamente só suportam GET e POST
        if ($method === 'POST' && isset($_POST['_method']) && strtoupper($_POST['_method']) === 'PUT') {
            $method = 'PUT';
        }

        // Verifica se existem rotas registradas para este método HTTP
        if (!isset(self::$routes[$method])) {
            throw new \Exception("Método HTTP não suportado: {$method}");
        }

        // Itera sobre todas as rotas registradas para este método
        foreach (self::$routes[$method] as $route => $action) {
            // Tenta extrair parâmetros da URI usando o padrão da rota
            $params = self::extractParams($route, $uri);

            // Se houve match (params não é null), processa a rota
            if ($params !== null) {
                // Separa o nome do controller e do método
                // Formato esperado: 'ControllerName@methodName'
                [$controllerName, $methodName] = explode('@', $action);
                
                // Constrói o nome completo da classe com namespace
                $controllerClass = "Application\\Controller\\" . $controllerName;

                // Verifica se a classe do controller existe
                if (class_exists($controllerClass)) {
                    // Instancia o controller
                    $controllerInstance = new $controllerClass();
                    
                    // Verifica se o método existe no controller
                    if (method_exists($controllerInstance, $methodName)) {
                        // Chama o método do controller passando os parâmetros extraídos
                        // array_values() remove as chaves dos parâmetros, mantendo apenas os valores
                        call_user_func_array([$controllerInstance, $methodName], array_values($params));
                        return;
                    }
                }
            }
        }

        // Se chegou aqui, nenhuma rota foi encontrada
        throw new \Exception("Rota não encontrada: [{$method}] {$uri}");
    }
}