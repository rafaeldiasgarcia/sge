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
                                $tipo_usuario = Auth::get('tipo_usuario_detalhado');
                                if ($tipo_usuario === 'Professor'): ?>
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

                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                <i class="bi bi-person-circle"><span> Olá, <?php
                                if (isset($user) && isset($user['nome']) && !empty($user['nome'])) {
                                    $nomeCompleto = htmlspecialchars($user['nome']);
                                    $primeiroNome = explode(' ', $nomeCompleto)[0];
                                    echo $primeiroNome;
                                } else {
                                    echo 'Usuário';
                                }
                                ?></span></i>
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