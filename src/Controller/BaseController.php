<?php
#
# Controller Base.
# Classe abstrata da qual todos os outros controllers herdam.
# Contém métodos úteis compartilhados, como o método 'repository' para
# instanciar classes de repositório facilmente.
#
namespace Application\Controller;

abstract class BaseController
{
    /**
     * Carrega e retorna uma instância de um repositório.
     */
    protected function repository(string $repositoryName)
    {
        // Constrói o nome completo da classe do repositório com o namespace.
        $className = "Application\\Repository\\" . $repositoryName;

        if (!class_exists($className)) {
            throw new \Exception("Classe do repositório não encontrada: {$className}");
        }

        return new $className();
    }
}