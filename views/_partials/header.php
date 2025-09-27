<?php
#
# Cabeçalho Padrão da Aplicação.
# Contém o início do HTML, o <head> com os links de CSS, e a barra de navegação principal.
# É incluído em todas as páginas pela função view().
#
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
    <!-- Caminhos para os assets agora são diretos na raiz de public -->
        <!-- Fonte Montserrat Google Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Montserrat:400,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/css/default.css">
    <link rel="stylesheet" href="/css/calendar.css">
    <?php if (isset($isAuthPage) && $isAuthPage): ?>
    <link rel="stylesheet" href="/css/auth.css">
    <?php endif; ?>
</head>
<?php if (isset($isAuthPage) && $isAuthPage): ?>
<body class="auth-body">
<div class="auth-background"></div>
<main class="auth-container">
<?php else: ?>
<body class="d-flex flex-column min-vh-100">

<nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
    <div class="container">
        <a class="navbar-brand" href="/"><strong>SGE UNIFIO</strong></a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"><span class="navbar-toggler-icon"></span></button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <?php if (Auth::check()): ?>
                    <?php if (Auth::role() === 'usuario'): ?>
                        <li class="nav-item"><a class="nav-link" href="/dashboard"><i class="bi bi-house"></i> Dashboard</a></li>
                        <li class="nav-item"><a class="nav-link" href="/agenda"><i class="bi bi-calendar-week"></i> Agenda</a></li>
                    <?php else: ?>
                        <li class="nav-item"><a class="nav-link" href="/agenda"><i class="bi bi-calendar-week"></i> Agenda da Quadra</a></li>
                    <?php endif; ?>

                    <?php
                    $tipo_usuario = Auth::get('tipo_usuario_detalhado');
                    $role = Auth::role();
                    $can_schedule = ($tipo_usuario === 'Professor') || ($role === 'superadmin') || ($role === 'admin' && $tipo_usuario === 'Membro das Atléticas');

                    if ($can_schedule): ?>
                        <li class="nav-item"><a class="nav-link" href="/agendar-evento"><i class="bi bi-calendar-plus"></i> Agendar Evento</a></li>
                    <?php endif; ?>

                    <?php if (Auth::get('tipo_usuario_detalhado') === 'Membro das Atléticas'): ?>
                        <li class="nav-item"><a class="nav-link" href="/inscricoes">Minhas Inscrições</a></li>
                    <?php endif; ?>

                    <?php if (Auth::role() === 'superadmin'): ?>
                        <li class="nav-item"><a class="nav-link" href="/superadmin/dashboard">Painel Super Admin</a></li>
                        <li class="nav-item"><a class="nav-link" href="/superadmin/relatorios">Relatórios</a></li>
                    <?php elseif (Auth::role() === 'admin'): ?>
                        <li class="nav-item"><a class="nav-link" href="/admin/atletica/dashboard">Painel Admin</a></li>
                    <?php endif; ?>

                    <li class="nav-item dropdown me-2">
                        <a class="nav-link position-relative" href="#" role="button" data-bs-toggle="dropdown" id="notificationDropdown">
                            <i class="bi bi-bell fs-5"></i>
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" id="notificationBadge" style="display: none;"></span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end notification-dropdown" style="width: 350px;">
                            <h6 class="dropdown-header d-flex justify-content-between align-items-center">
                                <span>Notificações</span>
                                <button class="btn btn-sm btn-outline-secondary" id="markAllReadBtn">Marcar todas como lidas</button>
                            </h6>
                            <div id="notificationsList" style="max-height: 400px; overflow-y: auto;">
                                <div class="dropdown-item text-muted text-center">Carregando...</div>
                            </div>
                        </div>
                    </li>

                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle"></i> Olá, <?php echo htmlspecialchars(Auth::name()); ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <?php if (Auth::role() === 'admin'): ?>
                                <li><a class="dropdown-item" href="/admin/atletica/dashboard">Meu Painel</a></li>
                            <?php elseif (Auth::role() === 'superadmin'): ?>
                                <li><a class="dropdown-item" href="/superadmin/dashboard">Meu Painel</a></li>
                            <?php endif; ?>
                            <li><a class="dropdown-item" href="/perfil">Editar Perfil</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="/logout">Sair</a></li>
                        </ul>
                    </li>
                <?php else: ?>
                    <li class="nav-item"><a class="nav-link" href="/login">Login</a></li>
                    <li class="nav-item"><a class="nav-link" href="/registro">Registrar</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>
<main class="container mt-4 flex-grow-1">
<?php endif; ?>
