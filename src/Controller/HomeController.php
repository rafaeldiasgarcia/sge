<?php
/**
 * Controller da Página Inicial (HomeController)
 * 
 * Gerencia a rota raiz ('/') da aplicação, atuando como um dispatcher
 * inteligente que redireciona usuários para a página apropriada baseado
 * no seu estado de autenticação.
 * 
 * Lógica de Redirecionamento:
 * - Usuários autenticados → /dashboard
 * - Usuários não autenticados → /agenda (visualização pública)
 * 
 * Este controller implementa o padrão Front Controller, centralizando
 * o ponto de entrada da aplicação e delegando para os controllers específicos.
 * 
 * Nota: A rota /agenda também está disponível diretamente e pode ser
 * acessada por usuários logados que queiram ver a agenda.
 * 
 * @package Application\Controller
 */
namespace Application\Controller;
use Application\Core\Auth;

class HomeController extends BaseController
{
    public function index()
    {
        if (Auth::check()) {
            // Usuários logados vão direto para o dashboard
            redirect('/dashboard');
            return;
        }

        // Usuários não logados veem a agenda pública
        redirect('/agenda');
    }
}