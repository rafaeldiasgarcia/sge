<?php
#
# Classe do Roteador.
# Armazena as rotas definidas em routes.php e despacha a requisição para o
# Controller e método corretos. Com o autoloader, não precisa mais incluir
# os arquivos de controller manualmente.
#
namespace Application\Core;

class Router
{
    protected static array $routes = [];

    public static function get(string $uri, string $action): void
    {
        self::$routes['GET'][$uri] = $action;
    }

    public static function post(string $uri, string $action): void
    {
        self::$routes['POST'][$uri] = $action;
    }

    public static function put(string $uri, string $action): void
    {
        self::$routes['PUT'][$uri] = $action;
    }

    protected static function extractParams(string $pattern, string $uri): ?array
    {
        $pattern = str_replace('/', '\/', $pattern);
        $pattern = preg_replace('/:([^\/]+)/', '(?P<\1>[^\/]+)', $pattern);
        $pattern = '/^' . $pattern . '$/';

        if (preg_match($pattern, $uri, $matches)) {
            // Remove a correspondência completa
            array_shift($matches);
            // Manter apenas os parâmetros nomeados
            return array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
        }

        return null;
    }

    public static function dispatch(string $uri, string $method): void
    {
        // Verificar se é um método PUT via POST com _method
        if ($method === 'POST' && isset($_POST['_method']) && strtoupper($_POST['_method']) === 'PUT') {
            $method = 'PUT';
        }

        if (!isset(self::$routes[$method])) {
            throw new \Exception("Método HTTP não suportado: {$method}");
        }

        foreach (self::$routes[$method] as $route => $action) {
            $params = self::extractParams($route, $uri);

            if ($params !== null) {
                [$controllerName, $methodName] = explode('@', $action);
                $controllerClass = "Application\\Controller\\" . $controllerName;

                if (class_exists($controllerClass)) {
                    $controllerInstance = new $controllerClass();
                    if (method_exists($controllerInstance, $methodName)) {
                        call_user_func_array([$controllerInstance, $methodName], array_values($params));
                        return;
                    }
                }
            }
        }

        throw new \Exception("Rota não encontrada: [{$method}] {$uri}");
    }
}