<?php
use Application\Core\Auth;

// não renderiza em páginas de login/registro
if (!empty($isAuthPage)) { return; }

$isGuest    = !Auth::check();
$collapseId = $isGuest ? 'navbarNav-guest' : 'navbarNav-auth';
?>
<nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom shadow-sm" role="navigation">
  <div class="container">

    <!-- Logo -->
    <a class="navbar-brand d-flex align-items-center gap-2" href="/" aria-label="Página inicial">
      <img src="/img/logo-quadra.webp" alt="Logo Quadra" class="logo-header" width="120" height="50">
    </a>

    <!-- Toggler -->
    <button class="navbar-toggler border-0" type="button"
            data-bs-toggle="collapse"
            data-bs-target="#<?= $collapseId ?>"
            aria-controls="<?= $collapseId ?>"
            aria-expanded="false"
            aria-label="Alternar navegação">
      <span class="navbar-toggler-icon"></span>
    </button>

    <?php if (!$isGuest): ?>
      <!-- ===== LOGADO ===== -->
      <div class="collapse navbar-collapse" id="<?= $collapseId ?>">
        <ul class="navbar-nav ms-auto align-items-center nav-animated">

          <!-- Painel -->
          <li class="nav-item">
            <a class="nav-link d-flex flex-column flex-lg-row align-items-center justify-content-center" href="/dashboard">
              <i class="bi bi-house fs-5"></i>
              <span class="d-lg-none ms-1">Painel</span>
            </a>
          </li>

          <!-- Agenda -->
          <li class="nav-item">
            <a class="nav-link d-flex flex-column flex-lg-row align-items-center justify-content-center" href="/agenda">
              <i class="bi bi-calendar-week fs-5"></i>
              <span class="d-lg-none ms-1">Agenda</span>
            </a>
          </li>

          <!-- Itens por função -->
          <?php if (Auth::role() === 'superadmin'): ?>
            <li class="nav-item">
              <a class="nav-link d-flex flex-column flex-lg-row align-items-center justify-content-center" href="/superadmin/dashboard">
                <i class="bi bi-gear fs-5"></i>
                <span class="d-lg-none ms-1">Admin</span>
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link d-flex flex-column flex-lg-row align-items-center justify-content-center" href="/agendar-evento">
                <i class="bi bi-calendar-plus fs-5"></i>
                <span class="d-lg-none ms-1">Agendar</span>
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link d-flex flex-column flex-lg-row align-items-center justify-content-center" href="/superadmin/relatorios">
                <i class="bi bi-file-earmark-bar-graph fs-5"></i>
                <span class="d-lg-none ms-1">Relatórios</span>
              </a>
            </li>

          <?php elseif (Auth::role() === 'admin'): ?>
            <li class="nav-item">
              <a class="nav-link d-flex flex-column flex-lg-row align-items-center justify-content-center" href="/admin/atletica/dashboard">
                <i class="bi bi-trophy fs-5"></i>
                <span class="d-lg-none ms-1">Atlética</span>
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link d-flex flex-column flex-lg-row align-items-center justify-content-center" href="/agendar-evento">
                <i class="bi bi-calendar-plus fs-5"></i>
                <span class="d-lg-none ms-1">Agendar</span>
              </a>
            </li>

          <?php else: ?>
            <?php $is_coordenador = Auth::get('is_coordenador'); ?>
            <?php if ((int)$is_coordenador === 1): ?>
              <li class="nav-item">
                <a class="nav-link d-flex flex-column flex-lg-row align-items-center justify-content-center" href="/agendar-evento">
                  <i class="bi bi-calendar-plus fs-5"></i>
                  <span class="d-lg-none ms-1">Agendar</span>
                </a>
              </li>
            <?php endif; ?>
          <?php endif; ?>

          <!-- Notificações -->
          <?php
            $notifToggleId = 'notification-bell';
            $notifMenuId   = 'notification-menu';
          ?>
          <li class="nav-item dropdown notifications">
            <a class="nav-link dropdown-toggle d-flex flex-column flex-lg-row align-items-center justify-content-center"
               href="#" id="<?= $notifToggleId ?>"
               data-bs-toggle="dropdown" role="button"
               aria-haspopup="true" aria-expanded="false"
               aria-controls="<?= $notifMenuId ?>">
              <i class="bi bi-bell fs-5"></i>
              <span class="visually-hidden">Notificações</span>
              <span class="d-lg-none ms-1">Notificações</span>
            </a>
            <div class="dropdown-menu dropdown-menu-end p-0" id="<?= $notifMenuId ?>" aria-labelledby="<?= $notifToggleId ?>" role="menu" style="min-width:360px; max-width:420px;">
              <h6 class="dropdown-header">Notificações</h6>
              <div id="notification-list" class="px-2 pb-2" style="max-height: 400px; overflow-y:auto;" role="status" aria-live="polite">
                <div class="text-center text-muted py-3 notification-empty">Carregando...</div>
              </div>
            </div>
          </li>

          <!-- Usuário -->
          <?php
            $userToggleId = 'user-menu-toggle';
            $userMenuId   = 'user-menu';
          ?>
          <li class="nav-item dropdown user-menu-item">
            <a class="nav-link dropdown-toggle d-flex flex-column flex-lg-row align-items-center justify-content-center"
               href="#" id="<?= $userToggleId ?>"
               data-bs-toggle="dropdown" role="button"
               aria-haspopup="true" aria-expanded="false"
               aria-controls="<?= $userMenuId ?>">
              <i class="bi bi-person-circle fs-5"></i>
              <span class="d-lg-none ms-1">Perfil</span>
            </a>
            <ul class="dropdown-menu dropdown-menu-end" id="<?= $userMenuId ?>" aria-labelledby="<?= $userToggleId ?>" role="menu">
              <?php if (Auth::role() === 'admin'): ?>
                <li><a class="dropdown-item" href="/admin/atletica/dashboard" role="menuitem">Meu Painel</a></li>
              <?php elseif (Auth::role() === 'superadmin'): ?>
                <li><a class="dropdown-item" href="/superadmin/dashboard" role="menuitem">Meu Painel</a></li>
              <?php endif; ?>
              <li><a class="dropdown-item" href="/perfil" role="menuitem">Editar Perfil</a></li>
              <li><hr class="dropdown-divider" role="separator"></li>
              <li><a class="dropdown-item" href="/logout" role="menuitem">Sair</a></li>
            </ul>
          </li>

        </ul>
      </div>

    <?php else: ?>
      <!-- ===== DESLOGADO ===== -->
      <div class="collapse navbar-collapse" id="<?= $collapseId ?>">
        <ul class="navbar-nav ms-auto d-lg-none">
          <li class="nav-item"><a class="nav-link" href="/login">Login</a></li>
          <li class="nav-item"><a class="nav-link" href="/registro">Cadastrar</a></li>
        </ul>

        <div class="ms-auto d-none d-lg-flex gap-2">
          <a href="/login" class="btn btn-outline-primary">Login</a>
          <a href="/registro" class="btn btn-primary">Cadastrar</a>
        </div>
      </div>
    <?php endif; ?>

  </div>
</nav>
