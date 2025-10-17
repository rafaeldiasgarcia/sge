<?php
use Application\Core\Auth;

// Em páginas de autenticação, não renderizamos navbar
if (!empty($isAuthPage)) { return; }

$isGuest = !Auth::check();
$collapseId = $isGuest ? 'navbarNav-guest' : 'navbarNav-auth';
?>
<nav class="navbar navbar-expand-lg navbar-light bg-white p-1">
  <div class="container d-flex justify-content-between align-items-center">

    <!-- Left: Logo -->
    <div class="header-left">
      <a class="navbar-brand" href="/">
        <img src="/img/logo-quadra.webp" alt="Logo Quadra" class="logo-header">
      </a>
    </div>

    <!-- Center (reservado) -->
    <div class="header-center"></div>

    <!-- Right -->
    <div class="header-right d-flex align-items-center">
      <?php if (!$isGuest): ?>
        <!-- ===== LOGADO ===== -->
        <button class="navbar-toggler" type="button"
                data-bs-toggle="collapse"
                data-bs-target="#<?= $collapseId ?>"
                aria-controls="<?= $collapseId ?>"
                aria-expanded="false"
                aria-label="Alternar navegação">
          <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="<?= $collapseId ?>">
          <!-- animação só aqui -->
          <ul class="navbar-nav ms-auto nav-animated">

            <li class="nav-item">
              <a class="nav-link" href="/dashboard">
                <i class="bi bi-house"></i> <span>Meu Painel</span>
              </a>
            </li>

            <li class="nav-item">
              <a class="nav-link" href="/agenda">
                <i class="bi bi-calendar-week"></i> <span>Agenda</span>
              </a>
            </li>

            <?php if (Auth::role() === 'superadmin'): ?>
              <li class="nav-item">
                <a class="nav-link" href="/superadmin/dashboard">
                  <i class="bi bi-gear"></i> <span>Painel Admin</span>
                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="/agendar-evento">
                  <i class="bi bi-calendar-plus"></i> <span>Agendar</span>
                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="/superadmin/relatorios">
                  <i class="bi bi-file-earmark-bar-graph"></i> <span>Relatórios</span>
                </a>
              </li>
            <?php elseif (Auth::role() === 'admin'): ?>
              <li class="nav-item">
                <a class="nav-link" href="/admin/atletica/dashboard">
                  <i class="bi bi-trophy"></i> <span>Painel Atlética</span>
                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="/agendar-evento">
                  <i class="bi bi-calendar-plus"></i> <span>Agendar Evento</span>
                </a>
              </li>
            <?php else: ?>
              <?php $is_coordenador = Auth::get('is_coordenador'); ?>
              <?php if ((int)$is_coordenador === 1): ?>
                <li class="nav-item">
                  <a class="nav-link" href="/agendar-evento">
                    <i class="bi bi-calendar-plus"></i> <span>Agendar Event.</span>
                  </a>
                </li>
              <?php endif; ?>
            <?php endif; ?>

            <!-- Notificações -->
            <li class="nav-item dropdown me-2 notifications">
              <a class="nav-link" href="#" id="notification-bell" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="bi bi-bell fs-5"></i> <span>Notificações</span>
                <span class="notification-badge" id="notification-badge"></span>
              </a>
              <div class="dropdown-menu dropdown-menu-end notification-dropdown" aria-labelledby="notification-bell">
                <h6 class="dropdown-header d-grid justify-content-between align-items-center">
                  <span>Notificações</span>
                </h6>
                <div id="notification-list" style="max-height: 400px; overflow-y: auto;">
                  <div class="notification-empty">Carregando...</div>
                </div>
              </div>
            </li>

            <!-- Usuário -->
            <li class="nav-item dropdown user-menu-item">
              <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="bi bi-person-circle"></i><span> Perfil</span>
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
        <!-- ===== DESLOGADO ===== -->
        <button class="navbar-toggler d-lg-none" type="button"
                data-bs-toggle="collapse"
                data-bs-target="#<?= $collapseId ?>"
                aria-controls="<?= $collapseId ?>"
                aria-expanded="false"
                aria-label="Alternar navegação">
          <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Um ÚNICO collapse para mobile e desktop -->
        <div class="collapse navbar-collapse" id="<?= $collapseId ?>">
          <!-- Mobile (< lg): links simples -->
          <ul class="navbar-nav ms-auto d-lg-none">
            <li class="nav-item"><a class="nav-link" href="/login"><span>Login</span></a></li>
            <li class="nav-item"><a class="nav-link" href="/registro"><span>Cadastrar</span></a></li>
          </ul>

          <!-- Desktop (>= lg): botões -->
          <div class="ms-auto d-none d-lg-flex gap-2">
            <a href="/login" class="btn btn-outline-primary">Login</a>
            <a href="/registro" class="btn btn-primary">Cadastrar</a>
          </div>
        </div>
      <?php endif; ?>
    </div>
  </div>
</nav>
