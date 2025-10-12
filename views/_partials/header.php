<?php
/**
 * Template do Cabeçalho da Aplicação (Header Partial)
 * 
 * Arquivo incluído automaticamente em todas as views pela função view() do helpers.php.
 * Contém toda a estrutura inicial do HTML e a barra de navegação.
 * 
 * Conteúdo:
 * - DOCTYPE e tags HTML de abertura
 * - <head> completo com meta tags, título e links de CSS
 * - Navbar responsivo com Bootstrap 5.3
 * - Sistema de notificações em tempo real
 * - Menu dropdown de usuário
 * - Links condicionais baseados no perfil (role)
 * 
 * Perfis e Menus:
 * - Super Admin: Todos os menus + link para painel de admin
 * - Admin Atlética: Menus de gerenciamento da atlética
 * - Usuário Comum: Agenda, Agendar, Meus Agendamentos, Perfil
 * - Não autenticado: Apenas Login e Registro
 * 
 * CSS Incluídos:
 * - Bootstrap 5.3.3 (framework CSS)
 * - Bootstrap Icons 1.11.3 (ícones)
 * - Fontes: Montserrat do Google Fonts
 * - Estilos personalizados: header, default, calendar, notifications, event-popup
 * - auth.css (apenas em páginas de autenticação)
 * 
 * JavaScript Inline:
 * - window.userRole: Role do usuário atual para lógica JS
 * 
 * @package Views\Partials
 */
use Application\Core\Auth;
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($title) ? htmlspecialchars($title) : 'SGE - UNIFIO'; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="/css/header.css">
    <link href="https://fonts.googleapis.com/css?family=Montserrat:400,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/css/default.css">
    <link rel="stylesheet" href="/css/calendar.css">
    <link rel="stylesheet" href="/css/notifications.css">
    <link rel="stylesheet" href="/css/event-popup.css">
    <?php if (isset($isAuthPage) && $isAuthPage): ?>
    <link rel="stylesheet" href="/css/auth.css">
    <?php endif; ?>
    <?php if (Auth::check()): ?>
    <script>
        // Variável global com o role do usuário para uso em JavaScript
        window.userRole = '<?php echo Auth::role(); ?>';
    </script>
    <?php endif; ?>
</head>
<?php if (isset($isAuthPage) && $isAuthPage): ?>
<body class="auth-body">
<div class="auth-background"></div>
<main class="auth-container">
<?php else: ?>
<body class="d-flex flex-column min-vh-100">

<nav class="navbar navbar-expand-lg navbar-light bg-white p-1">
    <div class="container d-flex justify-content-between align-items-center">
        <div class="header-left">
            <a class="navbar-brand" href="/">
                <img src="/img/logo-quadra.webp" alt="Logo Quadra" class="logo-header">
            </a>
        </div>
        <div class="header-center">
        </div>
        <div class="header-right d-flex align-items-center">
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                    aria-controls="navbarNav" aria-expanded="false" aria-label="Alternar navegação">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <?php if (Auth::check()): ?>
                            <li class="nav-item"><a class="nav-link" href="/dashboard"><i class="bi bi-house"></i> <span>Meu Painel</span></a></li>
                            <li class="nav-item"><a class="nav-link" href="/agenda"><i class="bi bi-calendar-week"></i> <span>Agenda</span></a></li>

                            <?php if (Auth::role() === 'superadmin'): ?>
                                <li class="nav-item"><a class="nav-link" href="/superadmin/dashboard"><i class="bi bi-gear"></i> <span>Painel Admin</span></a></li>
                                <li class="nav-item"><a class="nav-link" href="/agendar-evento"><i class="bi bi-calendar-plus"></i> <span>Agendar Event.</span></a></li>
                                <li class="nav-item"><a class="nav-link" href="/superadmin/relatorios"><i class="bi bi-file-earmark-bar-graph"></i> <span>Relatórios</span></a></li>
                            <?php elseif (Auth::role() === 'admin'): ?>
                                <li class="nav-item"><a class="nav-link" href="/admin/atletica/dashboard"><i class="bi bi-trophy"></i> <span>Painel Atlética</span></a></li>
                                <li class="nav-item"><a class="nav-link" href="/agendar-evento"><i class="bi bi-calendar-plus"></i> <span>Agendar Event.</span></a></li>
                            <?php else: ?>
                                <?php
                                $is_coordenador = Auth::get('is_coordenador');
                                if ($is_coordenador == 1): ?>
                                    <li class="nav-item"><a class="nav-link" href="/agendar-evento"><i class="bi bi-calendar-plus"></i> <span>Agendar Event.</span></a></li>
                                <?php endif; ?>
                            <?php endif; ?>

                        <li class="nav-item dropdown me-2 notifications">
                           <a class="nav-link" href="#" id="notification-bell">
                            <i class="bi bi-bell fs-5"></i>
                            <span>Notificações</span>
                            <span class="notification-badge" id="notification-badge"></span>
                            </a>

                            <div class="dropdown-menu dropdown-menu-end notification-dropdown" id="notification-dropdown">
                                <h6 class="dropdown-header d-grid justify-content-between align-items-center">
                                    <span>Notificações</span>
                                </h6>
                                <div id="notification-list" style="max-height: 400px; overflow-y: auto;">
                                    <div class="notification-empty">Carregando...</div>
                                </div>
                            </div>
                        </li>

                        <li class="nav-item dropdown user-menu-item">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                <i class="bi bi-person-circle"><span> Perfil</span></i>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <?php if (Auth::role() === 'admin'): ?>
                                    <li><a class="dropdown-item" href="/admin/atletica/dashboard"><span>Meu Painel</span></a></li>
                                <?php elseif (Auth::role() === 'superadmin'): ?>
                                    <li><a class="dropdown-item" href="/superadmin/dashboard"><span>Meu Painel</span></a></li>
                                <?php endif; ?>
                                <li><a class="dropdown-item" href="/perfil"><span>Editar Perfil</span></a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="/logout"><span>Sair</span></a></li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <li class="nav-item"><a class="nav-link" href="/login"><span>Login</span></a></li>
                        <li class="nav-item"><a class="nav-link" href="/registro"><span>Registrar</span></a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </div>
</nav>

<main class="container mt-4 flex-grow-1">
<?php endif; ?>