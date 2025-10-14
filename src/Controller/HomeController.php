<?php
/**
 * Controller da Página Inicial (HomeController)
 * 
 * Gerencia a rota raiz ('/') da aplicação, atuando como um dispatcher
 * inteligente que redireciona usuários para a página apropriada baseado
 * no seu estado de autenticação.
 * 
 * Lógica de Redirecionamento:
 * - Usuários não autenticados → /login
 * - Todos os usuários autenticados → /dashboard (incluindo superadmin)
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
            // Todos os usuários, incluindo superadmin, vão para o dashboard comum
            redirect('/dashboard');
        } else {
            redirect('/login');
        }
    }
}