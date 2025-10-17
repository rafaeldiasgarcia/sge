<?php
use Application\Core\Auth;
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Sistema completo para gerenciamento de quadras da atlética: agendamentos, controle de uso, reservas online e organização eficiente em um só lugar.">
    <title><?= isset($title) ? htmlspecialchars($title) : 'Sistema de Atléticas' ?></title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css?family=Montserrat:400,700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="/css/default.css">

    <?php if (!empty($additional_styles) && is_array($additional_styles)): ?>
        <?php foreach ($additional_styles as $style): ?>
    <link rel="stylesheet" href="<?= htmlspecialchars($style) ?>">
        <?php endforeach; ?>
    <?php endif; ?>

    <?php if (Auth::check()): ?>
    <script>
        window.userRole = '<?= Auth::role(); ?>';
    </script>
    <?php endif; ?>
</head>
<body class="<?= !empty($isAuthPage) ? 'auth-body' : 'd-flex flex-column min-vh-100' ?>">
    <?php if (!empty($isAuthPage)): ?>
    <div class="auth-background"></div>
    <?php endif; ?>

    <header>
        <?php include ROOT_PATH . "/views/_partials/header.php"; ?>
    </header>

    <?php if (!empty($isAuthPage)): ?>
    <main class="auth-container">
    <?php else: ?>
    <main class="container mt-4 flex-grow-1">
    <?php endif; ?>
        <?= $content ?>
    </main>

    <footer>
        <?php include ROOT_PATH . '/views/_partials/footer.php'; ?>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <?php if (empty($isAuthPage)): ?>
    <script src="/js/modules/_partials/calendar.js"></script>
    <script src="/js/modules/events/event-form.js"></script>
    <script src="/js/modules/_partials/header.js"></script>
    <?php endif; ?>
    <?php if (Auth::check()): ?>
    <script src="/js/modules/_partials/notifications.js"></script>
    <?php endif; ?>

    <!-- Scripts adicionais específicos das páginas de autenticação -->
    <!-- Login -->
    <script src="/js/modules/auth/login.js"></script>
    <!-- Register -->    
    <script src="/js/modules/auth/register.js"></script>

    <!-- Profile -->
    <script src="/js/modules/users/profile.js"></script>
    <script src="/js/modules/events/event-popup.js"></script>

    <!-- Scripts adicionais específicos das páginas do Super Admin -->
    <script src="/js/modules/super_admin/editar-usuario.js"></script>
    <script src="/js/modules/super_admin/enviar-notificacao-global.js"></script>
    <script src="/js/modules/super_admin/gerenciar-agendamentos.js"></script>
    <script src="/js/modules/super_admin/gerenciar-usuarios.js"></script>
</body>
</html>