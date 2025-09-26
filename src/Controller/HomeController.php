<?php
#
# Controller da Página Inicial.
# Gerencia a rota raiz ('/') da aplicação. Sua principal função é atuar
# como um despachante, redirecionando os usuários para o dashboard
# apropriado após o login, ou para a página de login se não estiverem autenticados.
#
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