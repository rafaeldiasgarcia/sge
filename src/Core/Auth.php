<?php
/**
 * Classe de Autenticação (Auth)
 * 
 * Centraliza toda a lógica de verificação de sessão e controle de acesso do sistema.
 * Esta classe fornece métodos estáticos para:
 * - Verificar se um usuário está logado
 * - Obter informações do usuário atual (ID, nome, perfil)
 * - Proteger rotas com middlewares de autenticação
 * 
 * Os perfis (roles) existentes no sistema são:
 * - 'usuario': Usuário comum que pode criar agendamentos
 * - 'admin': Administrador de atlética que pode gerenciar eventos e membros
 * - 'superadmin': Super administrador com acesso total ao sistema
 * 
 * @package Application\Core
 */
namespace Application\Core;

class Auth
{
    /**
     * Verifica se o usuário está logado
     * 
     * Checa se existe a variável de sessão 'loggedin' com valor true.
     * Esta variável é definida no AuthController após login bem-sucedido.
     * 
     * @return bool True se o usuário está autenticado, false caso contrário
     */
    public static function check(): bool
    {
        return isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true;
    }

    /**
     * Retorna o ID do usuário logado
     * 
     * Útil para identificar o usuário em operações de banco de dados,
     * como criar agendamentos, verificar permissões, etc.
     * 
     * @return int|null O ID do usuário ou null se não estiver logado
     */
    public static function id(): ?int
    {
        return self::check() ? (int)$_SESSION['id'] : null;
    }

    /**
     * Retorna o nome do usuário logado
     * 
     * Usado para exibir o nome do usuário no cabeçalho e em outras partes da interface.
     * 
     * @return string|null O nome completo do usuário ou null se não estiver logado
     */
    public static function name(): ?string
    {
        return self::check() ? $_SESSION['nome'] : null;
    }

    /**
     * Retorna o perfil (role) do usuário logado
     * 
     * O perfil determina as permissões do usuário no sistema.
     * Possíveis valores: 'usuario', 'admin', 'superadmin'
     * 
     * @return string|null O perfil do usuário ou null se não estiver logado
     */
    public static function role(): ?string
    {
        return self::check() ? $_SESSION['role'] : null;
    }

    /**
     * Retorna um campo específico da sessão do usuário
     * 
     * Método genérico para acessar qualquer dado armazenado na sessão.
     * Útil para acessar campos como 'atletica_id', 'email', 'curso_id', etc.
     * 
     * @param string $key A chave da sessão (ex: 'atletica_id', 'email')
     * @return mixed|null O valor da chave ou null se não existir ou usuário não estiver logado
     */
    public static function get(string $key)
    {
        return self::check() && isset($_SESSION[$key]) ? $_SESSION[$key] : null;
    }

    /**
     * Middleware de Autenticação Básica
     * 
     * Garante que o usuário esteja logado antes de acessar uma rota protegida.
     * Se o usuário não estiver autenticado, redireciona para a página de login
     * e armazena uma mensagem de erro na sessão.
     * 
     * Este método deve ser chamado no início de qualquer controller que
     * requer autenticação.
     * 
     * @return void Redireciona para /login se não estiver autenticado
     */
    public static function protect()
    {
        if (!self::check()) {
            // Armazena mensagem de erro para exibir na tela de login
            $_SESSION['error_message'] = 'Você precisa estar logado para acessar esta página.';
            redirect('/login');
        }
    }

    /**
     * Middleware de Autenticação para Super Admin
     * 
     * Garante que apenas usuários com perfil 'superadmin' possam acessar a rota.
     * Super admins têm acesso completo ao sistema, incluindo:
     * - Gerenciar todos os usuários
     * - Gerenciar atléticas e cursos
     * - Aprovar/rejeitar agendamentos
     * - Visualizar relatórios completos
     * 
     * Se o usuário não for super admin, retorna erro 403 (Forbidden).
     * 
     * @return void Retorna 403 e encerra execução se não for super admin
     */
    public static function protectSuperAdmin()
    {
        // Primeiro verifica se está logado
        self::protect();
        
        // Depois verifica se tem o perfil correto
        if (self::role() !== 'superadmin') {
            http_response_code(403); // Forbidden
            die('Acesso negado. Área restrita para Super Administradores.');
        }
    }

    /**
     * Middleware de Autenticação para Admin de Atlética
     * 
     * Garante que apenas usuários com perfil 'admin' possam acessar a rota.
     * Admins de atlética podem:
     * - Gerenciar eventos da sua atlética
     * - Gerenciar membros da sua atlética
     * - Visualizar inscrições em eventos
     * 
     * Se o usuário não for admin, retorna erro 403 (Forbidden).
     * 
     * @return void Retorna 403 e encerra execução se não for admin
     */
    public static function protectAdmin()
    {
        // Primeiro verifica se está logado
        self::protect();
        
        // Depois verifica se tem o perfil correto
        if (self::role() !== 'admin') {
            http_response_code(403); // Forbidden
            die('Acesso negado. Área restrita para Administradores de Atlética.');
        }
    }
}