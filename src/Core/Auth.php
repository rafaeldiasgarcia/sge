<?php
#
# Classe de Autenticação.
# Centraliza toda a lógica de verificação de sessão e controle de acesso.
# Fornece métodos estáticos para verificar se um usuário está logado, qual é o seu
# perfil e para proteger rotas.
#
namespace Application\Core;

class Auth
{
    /**
     * Verifica se o usuário está logado.
     * @return bool
     */
    public static function check(): bool
    {
        return isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true;
    }

    /**
     * Retorna o ID do usuário logado.
     * @return int|null
     */
    public static function id(): ?int
    {
        return self::check() ? (int)$_SESSION['id'] : null;
    }

    /**
     * Retorna o nome do usuário logado.
     * @return string|null
     */
    public static function name(): ?string
    {
        return self::check() ? $_SESSION['nome'] : null;
    }

    /**
     * Retorna o perfil (role) do usuário logado.
     * @return string|null
     */
    public static function role(): ?string
    {
        return self::check() ? $_SESSION['role'] : null;
    }

    /**
     * Retorna um campo específico da sessão do usuário.
     * @param string $key A chave da sessão (ex: 'atletica_id')
     * @return mixed|null
     */
    public static function get(string $key)
    {
        return self::check() && isset($_SESSION[$key]) ? $_SESSION[$key] : null;
    }

    /**
     * Middleware: Garante que o usuário esteja logado. Se não, redireciona para o login.
     */
    public static function protect()
    {
        if (!self::check()) {
            $_SESSION['error_message'] = 'Você precisa estar logado para acessar esta página.';
            redirect('/login');
        }
    }

    /**
     * Middleware: Garante que o usuário seja um Super Admin.
     */
    public static function protectSuperAdmin()
    {
        self::protect();
        if (self::role() !== 'superadmin') {
            http_response_code(403);
            die('Acesso negado. Área restrita para Super Administradores.');
        }
    }

    /**
     * Middleware: Garante que o usuário seja um Admin de Atlética.
     */
    public static function protectAdmin()
    {
        self::protect();
        if (self::role() !== 'admin') {
            http_response_code(403);
            die('Acesso negado. Área restrita para Administradores de Atlética.');
        }
    }
}