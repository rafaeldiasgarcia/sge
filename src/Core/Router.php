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

    public static function dispatch(string $uri, string $method): void
    {
        if (isset(self::$routes[$method][$uri])) {
            $action = self::$routes[$method][$uri];
            [$controllerName, $methodName] = explode('@', $action);

            // Constrói o nome completo da classe com o namespace.
            $controllerClass = "Application\\Controller\\" . $controllerName;

            if (class_exists($controllerClass)) {
                $controllerInstance = new $controllerClass();
                if (method_exists($controllerInstance, $methodName)) {
                    $controllerInstance->$methodName();
                    return;
                }
            }
        }

        throw new \Exception("Rota não encontrada: [{$method}] {$uri}");
    }
}