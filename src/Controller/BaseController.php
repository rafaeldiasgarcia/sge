<?php
#
# Controller Base.
# Classe abstrata da qual todos os outros controllers herdam.
# Contém métodos úteis compartilhados, como o método 'repository' para
# instanciar classes de repositório facilmente.
#
namespace Application\Controller;

use Application\Core\Auth;

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

    /**
     * Retorna os dados do usuário logado para ser usado nas views
     */
    protected function getUserData()
    {
        if (!Auth::check()) {
            return null;
        }

        return [
            'nome' => Auth::name(),
            'email' => Auth::get('email'),
            'role' => Auth::role(),
            'tipo_usuario' => Auth::get('tipo_usuario_detalhado'),
            'atletica_id' => Auth::get('atletica_id')
        ];
    }
}