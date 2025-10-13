<?php
/**
 * Controller da Página Inicial (HomeController)
 * 
 * Gerencia a rota raiz ('/') da aplicação, atuando como um dispatcher
 * inteligente que redireciona usuários para a página apropriada baseado
 * no seu estado de autenticação e perfil.
 * 
 * Lógica de Redirecionamento:
 * - Usuários não autenticados → /login
 * - Super Administradores → /superadmin/dashboard
 * - Demais usuários autenticados → /dashboard
 * 
 * Este controller implementa o padrão Front Controller, centralizando
 * o ponto de entrada da aplicação e delegando para os controllers específicos.
 * 
 * @package Application\Controller
 */
namespace Application\Controller;

class HomeController extends BaseController
{
    public function index()
    {
        if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
            $role = $_SESSION['role'] ?? 'usuario';

            if ($role === 'superadmin') {
                redirect('/superadmin/dashboard');
            } else {
                redirect('/dashboard');
            }
        } else {
            redirect('/login');
        }
    }
}