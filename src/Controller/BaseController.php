<?php
/**
 * Controller Base (BaseController)
 * 
 * Classe abstrata que serve como base para todos os controllers da aplicação.
 * Implementa o padrão Template Method, fornecendo funcionalidades comuns
 * que são herdadas por todos os controllers específicos.
 * 
 * Funcionalidades fornecidas:
 * - Carregamento dinâmico de repositories (factory method)
 * - Acesso aos dados do usuário logado
 * - Métodos utilitários compartilhados
 * 
 * Todos os controllers do sistema devem estender esta classe para:
 * - Manter consistência no código
 * - Reutilizar funcionalidades comuns
 * - Facilitar manutenção e evolução
 * 
 * Exemplo de uso:
 * class MeuController extends BaseController {
 *     public function index() {
 *         $repo = $this->repository('MeuRepository');
 *         $user = $this->getUserData();
 *     }
 * }
 * 
 * @package Application\Controller
 */
namespace Application\Controller;

use Application\Core\Auth;

abstract class BaseController
{
    /**
     * Factory Method para instanciar repositories dinamicamente
     * 
     * Este método implementa um padrão Factory simplificado,
     * permitindo que os controllers obtenham instâncias de
     * repositories sem se preocupar com a instanciação.
     * 
     * O namespace completo é construído automaticamente, então
     * basta passar o nome da classe (ex: 'UsuarioRepository').
     * 
     * @param string $repositoryName Nome da classe do repository (sem namespace)
     * @return object Instância do repository solicitado
     * @throws \Exception Se a classe do repository não existir
     * 
     * @example
     * $userRepo = $this->repository('UsuarioRepository');
     * $agendaRepo = $this->repository('AgendamentoRepository');
     */
    protected function repository(string $repositoryName)
    {
        // Constrói o nome completo da classe com namespace
        $className = "Application\\Repository\\" . $repositoryName;

        // Verifica se a classe existe antes de instanciar
        if (!class_exists($className)) {
            throw new \Exception("Classe do repositório não encontrada: {$className}");
        }

        // Retorna uma nova instância do repository
        return new $className();
    }

    /**
     * Obtém os dados do usuário logado para uso nas views
     * 
     * Este método centraliza a coleta de dados do usuário da sessão,
     * retornando um array padronizado com as informações mais comumente
     * necessárias nas views.
     * 
     * Os dados retornados incluem:
     * - nome: Nome completo do usuário
     * - email: Endereço de e-mail
     * - role: Perfil (usuario, admin, superadmin)
     * - tipo_usuario: Tipo detalhado (Aluno, Professor, Membro das Atléticas, etc)
     * - atletica_id: ID da atlética associada (pode ser null)
     * - is_coordenador: Se o usuário é coordenador
     * 
     * @return array|null Array com dados do usuário ou null se não estiver logado
     * 
     * @example
     * // No controller
     * view('dashboard', ['user' => $this->getUserData()]);
     * 
     * // Na view
     * echo $user['nome']; // Exibe o nome do usuário
     */
    protected function getUserData()
    {
        // Verifica se há usuário logado
        if (!Auth::check()) {
            return null;
        }

        // Retorna array padronizado com dados do usuário
        return [
            'nome' => Auth::name(),
            'email' => Auth::get('email'),
            'role' => Auth::role(),
            'tipo_usuario' => Auth::get('tipo_usuario_detalhado'),
            'atletica_id' => Auth::get('atletica_id'),
            'is_coordenador' => Auth::get('is_coordenador')
        ];
    }
}