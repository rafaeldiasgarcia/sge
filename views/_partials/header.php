<?php
use Application\Core\Auth;

// Em páginas de autenticação, não renderizamos navbar
if (!empty($isAuthPage)) { return; }

$isGuest = !Auth::check();
$collapseId = $isGuest ? 'navbarNav-guest' : 'navbarNav-auth';
?>
<nav class="navbar navbar-expand-lg navbar-light bg-white p-1" role="navigation" aria-label="Barra principal">
  <div class="container d-flex justify-content-between align-items-center">

    <!-- Left: Logo -->
    <div class="header-left">
      <a class="navbar-brand" href="/" aria-label="Página inicial">
        <img src="/img/logo-quadra.webp" alt="Logo Quadra" class="logo-header" width="120" height="50">
      </a>
    </div>

    <!-- Center (reservado) -->
    <div class="header-center" aria-hidden="true"></div>

    <!-- Right -->
    <div class="header-right d-flex align-items-center">
      <?php if (!$isGuest): ?>
        <!-- ===== LOGADO ===== -->
        <button class="navbar-toggler" type="button"
                data-bs-toggle="collapse"
                data-bs-target="#<?= $collapseId ?>"
                aria-controls="<?= $collapseId ?>"
                aria-expanded="false"
                aria-label="Abrir menu">
          <span class="navbar-toggler-icon" aria-hidden="true"></span>
        </button>

        <div class="collapse navbar-collapse" id="<?= $collapseId ?>">
          <!-- animação só aqui -->
          <ul class="navbar-nav ms-auto nav-animated">

            <li class="nav-item">
              <a class="nav-link" href="/dashboard">
                <i class="bi bi-house" aria-hidden="true"></i> <span>Meu Painel</span>
              </a>
            </li>

            <li class="nav-item">
              <a class="nav-link" href="/agenda">
                <i class="bi bi-calendar-week" aria-hidden="true"></i> <span>Agenda</span>
              </a>
            </li>

            <?php if (Auth::role() === 'superadmin'): ?>
              <li class="nav-item">
                <a class="nav-link" href="/superadmin/dashboard">
                  <i class="bi bi-gear" aria-hidden="true"></i> <span>Painel Admin</span>
                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="/agendar-evento">
                  <i class="bi bi-calendar-plus" aria-hidden="true"></i> <span>Agendar</span>
                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="/superadmin/relatorios">
                  <i class="bi bi-file-earmark-bar-graph" aria-hidden="true"></i> <span>Relatórios</span>
                </a>
              </li>
            <?php elseif (Auth::role() === 'admin'): ?>
              <li class="nav-item">
                <a class="nav-link" href="/admin/atletica/dashboard">
                  <i class="bi bi-trophy" aria-hidden="true"></i> <span>Painel Atlética</span>
                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="/agendar-evento">
                  <i class="bi bi-calendar-plus" aria-hidden="true"></i> <span>Agendar Evento</span>
                </a>
              </li>
            <?php else: ?>
              <?php $is_coordenador = Auth::get('is_coordenador'); ?>
              <?php if ((int)$is_coordenador === 1): ?>
                <li class="nav-item">
                  <a class="nav-link" href="/agendar-evento">
                    <i class="bi bi-calendar-plus" aria-hidden="true"></i> <span>Agendar Event.</span>
                  </a>
                </li>
              <?php endif; ?>
            <?php endif; ?>

            <!-- Notificações -->
            <?php
              // IDs estáveis para relação ARIA
              $notifToggleId = 'notification-bell';
              $notifMenuId   = 'notification-menu';
            ?>
            <li class="nav-item dropdown me-2 notifications">
              <a class="nav-link" href="#" id="<?= $notifToggleId ?>" data-bs-toggle="dropdown" role="button"
                 aria-haspopup="true" aria-expanded="false" aria-controls="<?= $notifMenuId ?>">
                <i class="bi bi-bell fs-5" aria-hidden="true"></i> <span>Notificações</span>
                <span class="notification-badge" id="notification-badge" aria-live="polite"></span>
              </a>
              <div class="dropdown-menu dropdown-menu-end notification-dropdown"
                   id="<?= $notifMenuId ?>" aria-labelledby="<?= $notifToggleId ?>" role="menu">
                <h6 class="dropdown-header d-grid justify-content-between align-items-center">
                  <span>Notificações</span>
                </h6>
                <div id="notification-list" style="max-height: 400px; overflow-y: auto;"
                     role="status" aria-live="polite" aria-atomic="true">
                  <div class="notification-empty">Carregando...</div>
                </div>
              </div>
            </li>

            <!-- Usuário -->
            <?php
              $userToggleId = 'user-menu-toggle';
              $userMenuId   = 'user-menu';
            ?>
            <li class="nav-item dropdown user-menu-item">
              <!-- Mantemos <a> por compat com estilos .nav-link; JS previne navegação -->
              <a class="nav-link dropdown-toggle" href="#"
                 id="<?= $userToggleId ?>"
                 data-bs-toggle="dropdown" role="button"
                 aria-haspopup="true" aria-expanded="false"
                 aria-controls="<?= $userMenuId ?>">
                <i class="bi bi-person-circle" aria-hidden="true"></i><span> Perfil</span>
              </a>
              <ul class="dropdown-menu dropdown-menu-end"
                  id="<?= $userMenuId ?>" aria-labelledby="<?= $userToggleId ?>" role="menu">
                <?php if (Auth::role() === 'admin'): ?>
                  <li><a class="dropdown-item" href="/admin/atletica/dashboard" role="menuitem"><span>Meu Painel</span></a></li>
                <?php elseif (Auth::role() === 'superadmin'): ?>
                  <li><a class="dropdown-item" href="/superadmin/dashboard" role="menuitem"><span>Meu Painel</span></a></li>
                <?php endif; ?>
                <li><a class="dropdown-item" href="/perfil" role="menuitem"><span>Editar Perfil</span></a></li>
                <li><hr class="dropdown-divider" role="separator" aria-hidden="true"></li>
                <li><a class="dropdown-item" href="/logout" role="menuitem"><span>Sair</span></a></li>
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
                aria-label="Abrir menu">
          <span class="navbar-toggler-icon" aria-hidden="true"></span>
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
