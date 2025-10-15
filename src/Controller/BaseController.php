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

    /**
     * Helpers compartilhados para evitar duplicação nos controllers
     * Mantém padrão de mensagens de sessão e redirects
     */

    /**
     * Define uma mensagem de erro em sessão e redireciona
     * Preserva o padrão $_SESSION['error_message']
     */
    protected function setErrorAndRedirect(string $message, string $redirectTo): void
    {
        $_SESSION['error_message'] = $message;
        redirect($redirectTo);
    }

    /**
     * Define uma mensagem de sucesso em sessão e redireciona
     * Preserva o padrão $_SESSION['success_message']
     */
    protected function setSuccessAndRedirect(string $message, string $redirectTo): void
    {
        $_SESSION['success_message'] = $message;
        redirect($redirectTo);
    }

    /**
     * Exige que uma chave exista na sessão, caso contrário redireciona
     */
    protected function requireSessionKeyOrRedirect(string $key, string $redirectTo): void
    {
        if (!isset($_SESSION[$key])) {
            redirect($redirectTo);
        }
    }

    /**
     * Exige campos obrigatórios no array informado; em erro, executa callback (ex.: salvar form e redirecionar)
     */
    protected function requireFieldsOrRedirect(array $requiredFields, array $source, callable $onError): void
    {
        foreach ($requiredFields as $field) {
            if (!isset($source[$field]) || $source[$field] === '' || $source[$field] === null) {
                $onError();
                return;
            }
        }
    }

    /**
     * Lê um ID inteiro do POST e valida; em erro, define mensagem e redireciona
     * Retorna o ID válido
     */
    protected function requireValidIdFromPostOrRedirect(string $postKey, string $redirectPath, string $errorMessage = 'ID inválido.'): int
    {
        $id = (int)($_POST[$postKey] ?? 0);
        if ($id <= 0) {
            $_SESSION['error_message'] = $errorMessage;
            redirect($redirectPath);
        }
        return $id;
    }

    /**
     * Guardas de autenticação para padronizar chamadas
     */
    protected function requireAuth(): void
    {
        Auth::protect();
    }

    protected function requireAdmin(): void
    {
        Auth::protectAdmin();
    }

    protected function requireSuperAdmin(): void
    {
        Auth::protectSuperAdmin();
    }

    /**
     * Exige que o usuário possua atletica_id; em falta, define erro e redireciona
     * Retorna o atletica_id válido
     */
    protected function requireAtleticaIdOrRedirect(string $redirectPath = '/dashboard', string $errorMessage = 'Usuário não possui uma atlética associada. Entre em contato com o administrador do sistema.'): int
    {
        Auth::protectAdmin();
        $atleticaId = (int)(Auth::get('atletica_id') ?? 0);
        if ($atleticaId <= 0) {
            $_SESSION['error_message'] = $errorMessage;
            redirect($redirectPath);
        }
        return $atleticaId;
    }

    /**
     * Resposta JSON padronizada e encerramento do fluxo
     */
    protected function jsonResponse(array $payload, int $statusCode = 200): void
    {
        header('Content-Type: application/json; charset=utf-8');
        http_response_code($statusCode);
        echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }
}