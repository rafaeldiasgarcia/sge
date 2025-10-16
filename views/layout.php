<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Sistema completo para gerenciamento de quadras da atlética: agendamentos, controle de uso, reservas online e organização eficiente em um só lugar.">
    <link rel="stylesheet" href="./css/default.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <title><?= $title ?? 'Sistema de Atléticas' ?></title>
</head>
<body>
    <header>
        <?php include VIEW_PATH . "/_partials/header.php"; ?>
    </header>
    <main>
        <?= $content ?>
    </main>
    <footer>
        <?php include VIEW_PATH . "/_partials/footer.php"; ?>
    </footer>
    <script src="./js/calendar.js" defer></script>
    <script src="./js/event-form.js"></script>
    <script src="./js/event-popup.js"></script>
    <script src="./js/header.js"></script>
    <script src="./js/notifications.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>