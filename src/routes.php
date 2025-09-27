<?php
#
# Arquivo de Definição de Rotas.
# Mapeia cada URL (URI) e método HTTP (GET, POST) para uma ação específica
# em um Controller (ex: 'HomeController@index').
# É incluído pelo index.php e usado pela classe Router.
#

use Application\Core\Router;

// --- Rotas de Autenticação e Acesso Público ---
Router::get('/', 'HomeController@index');
Router::get('/login', 'AuthController@showLoginForm');
Router::post('/login', 'AuthController@login');
Router::get('/login/verify', 'AuthController@showVerifyForm');
Router::post('/login/verify', 'AuthController@verifyCode');
Router::get('/registro', 'AuthController@showRegistrationForm');
Router::post('/registro', 'AuthController@register');
Router::get('/logout', 'AuthController@logout');
Router::get('/esqueci-senha', 'AuthController@showForgotPasswordForm');
Router::post('/esqueci-senha', 'AuthController@sendRecoveryLink');
Router::get('/redefinir-senha', 'AuthController@showResetPasswordForm');
Router::post('/redefinir-senha', 'AuthController@resetPassword');

// --- Rotas do Painel do Usuário Comum ---
Router::get('/dashboard', 'UsuarioController@dashboard');
Router::get('/perfil', 'UsuarioController@perfil');
Router::post('/perfil', 'UsuarioController@updatePerfil');
Router::post('/perfil/solicitar-atletica', 'UsuarioController@solicitarEntradaAtletica');
Router::get('/inscricoes', 'UsuarioController@showInscricoes');
Router::post('/inscricoes/inscrever', 'UsuarioController@inscreverEmModalidade');
Router::post('/inscricoes/cancelar', 'UsuarioController@cancelarInscricao');

// --- Rotas de Páginas Gerais (requerem login) ---
Router::get('/agenda', 'AgendaController@index');
Router::post('/agenda/presenca', 'AgendaController@handlePresenca');
Router::get('/agendar-evento', 'AgendamentoController@showForm');
Router::post('/agendar-evento', 'AgendamentoController@create');
Router::get('/meus-agendamentos', 'AgendamentoController@showMeusAgendamentos');
Router::get('/agendamento/editar', 'AgendamentoController@showEditForm');
Router::post('/agendamento/editar', 'AgendamentoController@update');
Router::post('/agendamento/cancelar', 'AgendamentoController@cancel');
Router::get('/calendario-partial', 'AgendamentoController@getCalendarPartial');

// --- Rotas do Painel do Admin da Atlética ---
Router::get('/admin/atletica/dashboard', 'AdminAtleticaController@dashboard');
Router::get('/admin/atletica/membros', 'AdminAtleticaController@gerenciarMembros');
Router::post('/admin/atletica/membros/acao', 'AdminAtleticaController@handleMembroAction');
Router::get('/admin/atletica/inscricoes', 'AdminAtleticaController@gerenciarInscricoes');
Router::post('/admin/atletica/inscricoes/acao', 'AdminAtleticaController@handleInscricaoAction');
Router::get('/admin/atletica/eventos', 'AdminAtleticaController@gerenciarEventos');
Router::post('/admin/atletica/eventos/inscrever', 'AdminAtleticaController@inscreverEmEvento');
Router::post('/admin/atletica/eventos/remover', 'AdminAtleticaController@removerDeEvento');

// --- Rotas do Painel do Super Admin ---
Router::get('/superadmin/dashboard', 'SuperAdminController@dashboard');
Router::get('/superadmin/agendamentos', 'SuperAdminController@gerenciarAgendamentos');
Router::post('/superadmin/agendamentos/aprovar', 'SuperAdminController@aprovarAgendamento');
Router::post('/superadmin/agendamentos/rejeitar', 'SuperAdminController@rejeitarAgendamento');

// Gerenciar Usuários
Router::get('/superadmin/usuarios', 'SuperAdminController@gerenciarUsuarios');
Router::get('/superadmin/usuario/editar', 'SuperAdminController@showEditUserForm');
Router::post('/superadmin/usuario/editar', 'SuperAdminController@updateUser');
Router::post('/superadmin/usuario/excluir', 'SuperAdminController@deleteUser');

// Gerenciar Estrutura (Página Unificada)
Router::get('/superadmin/estrutura', 'SuperAdminController@gerenciarEstrutura');

// Ações para Cursos (chamadas pela página de estrutura)
Router::post('/superadmin/cursos/criar', 'SuperAdminController@createCurso');
Router::get('/superadmin/curso/editar', 'SuperAdminController@showEditCursoForm');
Router::post('/superadmin/curso/editar', 'SuperAdminController@updateCurso');
Router::post('/superadmin/curso/excluir', 'SuperAdminController@deleteCurso');

// Ações para Atléticas (chamadas pela página de estrutura)
Router::post('/superadmin/atleticas/criar', 'SuperAdminController@createAtletica');
Router::get('/superadmin/atletica/editar', 'SuperAdminController@showEditAtleticaForm');
Router::post('/superadmin/atletica/editar', 'SuperAdminController@updateAtletica');
Router::post('/superadmin/atletica/excluir', 'SuperAdminController@deleteAtletica');

// Gerenciar Modalidades
Router::get('/superadmin/modalidades', 'SuperAdminController@gerenciarModalidades');
Router::post('/superadmin/modalidades/criar', 'SuperAdminController@createModalidade');
Router::get('/superadmin/modalidade/editar', 'SuperAdminController@showEditModalidadeForm');
Router::post('/superadmin/modalidade/editar', 'SuperAdminController@updateModalidade');
Router::post('/superadmin/modalidade/excluir', 'SuperAdminController@deleteModalidade');

// Gerenciar Admins
Router::get('/superadmin/admins', 'SuperAdminController@gerenciarAdmins');
Router::post('/superadmin/admins/promover', 'SuperAdminController@promoteAdmin');
Router::post('/superadmin/admins/rebaixar', 'SuperAdminController@demoteAdmin');

// Relatórios
Router::get('/superadmin/relatorios', 'SuperAdminController@showRelatorios');
Router::post('/superadmin/relatorios', 'SuperAdminController@gerarRelatorio');
Router::post('/superadmin/relatorios/imprimir', 'SuperAdminController@imprimirRelatorio');

// --- Rotas da API ---
Router::get('/notifications', 'NotificationController@getNotifications');
Router::post('/notifications/read', 'NotificationController@markAsRead');