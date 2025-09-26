<?php
#
# View do Dashboard do Usuário com o novo template.
# A estrutura foi adaptada do 07-dashboard.html, mas o conteúdo é dinâmico,
# vindo do backend (calendário, links de gerenciamento, etc.).
#
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($title ?? 'Dashboard - SGE UNIFIO'); ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/css/template.css">
</head>
<body>
    <header class="header">
        <div class="header-left">
            <div class="menu-icon">☰</div>
            <div class="header-title">
                <div class="title-main">GERENCIAMENTO DE QUADRA</div>
                <div class="title-sub">UNIFIO 2025</div>
            </div>
        </div>
        <div class="header-center">
            <img src="/images/Logo da qudra poliesportiva.png" alt="Logo Quadra Poliesportiva" class="header-logo">
        </div>
        <div class="header-right">
            <div class="user-info">
                <div class="user-name"><?php echo htmlspecialchars($user['nome']); ?></div>
                <a href="/perfil" class="profile-link">Meu Perfil</a>
            </div>
            <div class="profile-avatar"><?php echo strtoupper(substr($user['nome'], 0, 1)); ?></div>
        </div>
    </header>

    <main class="main-content">
        <section class="welcome-section">
            <div class="welcome-text">
                <div class="welcome-title">SEJA BEM-VINDO</div>
                <div class="welcome-subtitle">A NOSSA SALA DE EVENTOS!!!</div>
            </div>
        </section>

        <!-- Aqui você pode integrar o calendário dinâmico ou outras informações do dashboard -->
        
        <section class="management-wrapper">
            <div class="management-card">
                <div class="management-content">
                    <div class="management-text">
                        <h3 class="management-title">LOGO ABAIXO</h3>
                        <div class="management-subtitle">
                            <div class="subtitle-line"><span class="text-normal">GERENCIE SEU </span><span class="text-highlight-espaco">ESPAÇO</span><span class="text-comma">,</span></div>
                            <div class="subtitle-line"><span class="text-normal">SEU </span><span class="text-highlight-esporte">ESPORTE</span><span class="text-normal"> E</span></div>
                            <div class="subtitle-line"><span class="text-normal">SUA </span><span class="text-highlight-reserva">RESERVA</span></div>
                        </div>
                    </div>
                    <div class="management-character">
                        <img src="/images/Jogador 4.png" alt="Jogador" class="player-image">
                    </div>
                </div>
            </div>
        </section>

        <section class="event-cards">
            <div class="event-wrapper">
                <a href="/agenda" class="card sports-card" style="text-decoration: none; color: inherit;">
                    <div class="card-icons">
                        <img src="/images/bola de basquete.png" alt="Basquete" class="card-icon">
                        <img src="/images/bola de futebol.png" alt="Futebol" class="card-icon">
                        <img src="/images/bola de volei.png" alt="Vôlei" class="card-icon">
                    </div>
                    <div class="card-divider"></div>
                    <h4 class="card-title">Agenda de Eventos</h4>
                </a>
            </div>
            
            <?php if ($can_schedule): ?>
            <div class="event-wrapper non-sports-wrapper">
                <a href="/agendar-evento" class="card non-sports-card" style="text-decoration: none; color: inherit;">
                    <div class="card-icons">
                        <img src="/images/Icone doas eventos não esportivos.jpg" alt="Agendar" class="card-icon">
                    </div>
                    <div class="card-divider orange"></div>
                    <h4 class="card-title">Agendar Evento</h4>
                </a>
            </div>
            <?php endif; ?>
            
            <div class="event-wrapper">
                <a href="/meus-agendamentos" class="card regulations-card" style="text-decoration: none; color: inherit;">
                    <div class="card-icons">
                        <img src="/images/Icone dos regulamentos.png" alt="Meus Agendamentos" class="card-icon">
                    </div>
                    <div class="card-divider black"></div>
                    <h4 class="card-title">Meus Agendamentos</h4>
                </a>
            </div>
        </section>
    </main>

    <div style="width: 100%; text-align: center;">
        <img src="/images/creditos .png" alt="Créditos UNIFIO" style="max-width: 100%; height: auto; display: block; margin: 0 auto;">
    </div>
</body>
</html>