<?php
use Application\Core\Auth;

// Em páginas de autenticação, não renderizamos navbar
if (!empty($isAuthPage)) {
    return;
}
?>
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
            <?php if (Auth::check()): ?>
                <!-- Usuário logado: menu com ícones colapsável -->
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                        aria-controls="navbarNav" aria-expanded="false" aria-label="Alternar navegação">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto">
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
                    </ul>
                </div>
            <?php else: ?>
                <!-- Usuário NÃO logado: botões sempre visíveis no desktop, colapsáveis no mobile -->
                <button class="navbar-toggler d-lg-none" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                        aria-controls="navbarNav" aria-expanded="false" aria-label="Alternar navegação">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <!-- Desktop: visível sempre -->
                <div class="d-none d-lg-flex gap-2">
                    <a href="/login" class="btn btn-outline-primary">Login</a>
                    <a href="/registro" class="btn btn-primary">Cadastrar</a>
                </div>
                <!-- Mobile: dentro do collapse -->
                <div class="collapse navbar-collapse d-lg-none" id="navbarNav">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item"><a class="nav-link" href="/login"><span>Login</span></a></li>
                        <li class="nav-item"><a class="nav-link" href="/registro"><span>Cadastrar</span></a></li>
                    </ul>
                </div>
            <?php endif; ?>
        </div>
    </div>
</nav>