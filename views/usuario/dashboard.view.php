<?php
/**
 * ============================================================================
 * VIEW: DASHBOARD DO USUÁRIO
 * ============================================================================
 * 
 * Página inicial para usuários autenticados. Exibe cards de atalhos
 * personalizados por role e eventos com presença confirmada.
 * 
 * FUNCIONALIDADES:
 * - Carrossel de boas-vindas animado (2 slides)
 * - Cards de atalhos baseados em permissões
 * - Visualização de próximos eventos com presença marcada
 * - Separação entre eventos esportivos e não esportivos
 * - Links diretos para principais funcionalidades
 * 
 * CARDS DE ATALHO (CONDICIONAIS):
 * - Agenda dos Eventos: todos os usuários
 * - Solicitar Agendamento: admin, superadmin, coordenadores
 * - Meus Agendamentos: admin, superadmin, coordenadores
 * - Painel da Atlética: apenas admins com atlética vinculada
 * 
 * VARIÁVEIS RECEBIDAS:
 * @var array $user              - Dados do usuário logado
 *                                 [nome, email, role, tipo_usuario,
 *                                  is_coordenador, atletica_id]
 * @var array $eventos_presenca  - Eventos futuros onde usuário marcou presença
 *                                 [id, titulo, tipo_agendamento, data_agendamento,
 *                                  horario_periodo, responsavel, esporte_tipo]
 * 
 * FEATURES:
 * - Design responsivo com carrossel Bootstrap
 * - Cards clicáveis que abrem popup de evento
 * - Separação visual entre esportivos (verde) e não esportivos (azul)
 * - Animações CSS customizadas
 * 
 * CONTROLLER: HomeController::dashboard()
 * CSS: usuario.css, default.css, calendar.css
 * JAVASCRIPT: event-popup.js (popup de detalhes)
 */
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SGE UNIFIO - Dashboard</title>
    <link rel="stylesheet" href="/css/usuario.css">
    <link rel="stylesheet" href="/css/default.css">
    <link rel="stylesheet" href="/css/calendar.css">
</head>

<body>

<section>
    <div class="container">
        <div class="title-dashboard">
            <div>
                <h1>SEJA BEM-VINDO</h1>
                <h1>À NOSSA SALA DE EVENTOS!!!</h1>
            </div>
        </div>

        <!-- CARROSSEL COM 2 SLIDES -->
        <div id="carouselExampleIndicators" class="carousel slide carousel-fade" data-bs-ride="carousel" data-bs-interval="8000">
            <div class="carousel-indicators">
                <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
                <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="1" aria-label="Slide 2"></button>
            </div>

    <div class="carousel-inner">
        <!-- SLIDE 1: Card Laranja -->
        <div class="carousel-item active">
            <div class="custom-card orange-card">
                <div class="text-content">
                    <h2>AQUI VOCÊ VERÁ</h2>
                    <p>OS EVENTOS MAIS AGUARDADOS DA NOSSA<p>
                    <span class="highlight-text">UNIVERSIDADE!</span>
                </div>
                <div class="character-image-container static-character">
                    <img src="/img/jogador-laranja2.webp" alt="Jogador de Vôlei">
                </div>
            </div>
        </div>

        <!-- SLIDE 2: Card Branco com Efeito 3D -->
        <div class="carousel-item">
            <div class="custom-card white-card">
                <div class="text-content">
                    <h2>
                        <span class="gradient-text-orange">LOGO</span>
                        <span class="gradient-text-blue">ABAIXO</span>
                    </h2>
                    <p class="card-text-body">
                        <span class="text-orange">GERENCIE SEU</span>
                        <span class="text-blue">ESPAÇO,</span><br>
                        <span class="text-blue">SEU</span>
                        <span class="text-orange">ESPORTE</span>
                        <span class="text-blue">E</span><br>
                        <span class="text-blue">SUA</span>
                        <span class="text-orange">RESERVA</span>
                    </p>
                </div>
                <!-- Container da imagem que vai "sair" do card -->
                <div class="character-image-container popup-character">
                    <img src="/img/jogadora-laranja.webp" alt="Personagem de cabelo branco saindo do card">
                </div>
            </div>
        </div>
    </div>

    <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide="next">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Previous</span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide="prev">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Next</span>
    </button>
</div>

       
</section>


<section>
    <?php
    $tipo_usuario = $user['tipo_usuario'] ?? '';
    $role = $user['role'] ?? '';
    $is_coordenador = $user['is_coordenador'] ?? 0;
    ?>
    <div class="dashboard-shortcuts">
        <div class="shortcut-row">
            <div class="shortcut-card">
                <div class="card-body">
                    <h5 class="card-title"><i class="bi bi-calendar-check-fill text-primary"></i> AGENDA DOS EVENTOS</h5>
                    <p class="card-text">Confira os próximos eventos e marque sua presença.</p>
                    <a href="/agenda" class="btn btn-primary mt-auto">Ver Agenda</a>
                </div>
            </div>
            <?php if ($role === 'admin' || $role === 'superadmin' || $is_coordenador == 1): ?>
            <div class="shortcut-card">
                <div class="card-body">
                    <h5 class="card-title"><i class="bi bi-calendar-plus text-warning"></i> Solicitar Agendamento</h5>
                    <p class="card-text">Solicite o uso da quadra esportiva para suas atividades.</p>
                    <a href="/agendar-evento" class="btn btn-warning mt-auto">Solicitar Aluguel</a>
                </div>
            </div>
            <?php endif; ?>
        </div>
        <div class="shortcut-row">
            <?php if ($role === 'admin' || $role === 'superadmin' || $is_coordenador == 1): ?>
            <div class="shortcut-card">
                <div class="card-body">
                    <h5 class="card-title"><i class="bi bi-journal-text text-success"></i> Meus Agendamentos</h5>
                    <p class="card-text">Acompanhe o status das suas solicitações de uso da quadra.</p>
                    <a href="/meus-agendamentos" class="btn btn-success mt-auto">Ver Solicitações</a>
                </div>
            </div>
            <?php endif; ?>
            <?php if ($role === 'admin'): ?>
                <?php $atleticaId = $_SESSION['atletica_id'] ?? null; ?>
                <?php if ($atleticaId): ?>
                <div class="shortcut-card">
                    <div class="card-body">
                        <h5 class="card-title"><i class="bi bi-people-fill text-info"></i> Painel da Atlética</h5>
                        <p class="card-text">Gerencie os membros, inscrições e participações da sua atlética.</p>
                        <a href="/admin/atletica/dashboard" class="btn btn-info mt-auto text-white">Acessar Painel</a>
                    </div>
                </div>
                <?php else: ?>
                <div class="shortcut-card">
                    <div class="card-body">
                        <h5 class="card-title"><i class="bi bi-exclamation-triangle-fill text-warning"></i> Configuração Pendente</h5>
                        <p class="card-text">Sua conta de administrador ainda não foi associada a uma atlética. Entre em contato com o super administrador.</p>
                        <small class="text-muted mt-auto">Aguardando configuração do sistema</small>
                    </div>
                </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-12">
            <div class="card border-0 mb-4 card-events-presence">
                <div class="card-header text-white">
                    <h5 class="mb-0"><i class="bi bi-calendar-check"></i> Próximos Eventos (Presença Marcada)</h5>
                </div>
                <div class="card-body">
                    <?php if (empty($eventos_presenca)): ?>
                        <div class="text-center text-muted py-4">
                            <i class="bi bi-calendar-x fs-1 text-muted"></i>
                            <p class="mt-2">Você ainda não marcou presença em nenhum evento futuro.</p>
                        </div>
                    <?php else: ?>
                        <div class="row">
                            <!-- Eventos Esportivos -->
                            <div class="col-md-6">
                                <h6><i class="bi bi-trophy trofeu-icon"></i> Próximo evento esportivo</h6>
                                <?php
                                $eventosEsportivos = array_filter($eventos_presenca, function($evento) {
                                    return $evento['tipo_agendamento'] === 'esportivo';
                                });
                                ?>
                                <?php if (empty($eventosEsportivos)): ?>
                                    <div class="text-center text-muted py-3">
                                        <p class="mb-0">Nenhum evento esportivo confirmado</p>
                                    </div>
                                <?php else: ?>
                                    <?php foreach ($eventosEsportivos as $evento): ?>
                                        <div class="card mb-3 border-primary event-clickable" data-event-id="<?php echo $evento['id']; ?>" style="cursor: pointer;">
                                            <div class="card-body">
                                                <h6 class="card-title text-black"><?php echo htmlspecialchars($evento['titulo']); ?></h6>
                                                <?php if ($evento['esporte_tipo']): ?>
                                                    <small class="text-muted d-block"><?php echo htmlspecialchars($evento['esporte_tipo']); ?></small>
                                                <?php endif; ?>
                                                <p class="card-text mb-2">
                                                    <strong>Data:</strong> <?php echo date('d/m/Y', strtotime($evento['data_agendamento'])); ?><br>
                                                    <strong>Horário:</strong> <?php echo htmlspecialchars($evento['horario_periodo']); ?><br>
                                                    <strong>Responsável:</strong> <?php echo htmlspecialchars($evento['responsavel']); ?>
                                                </p>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>

                            <!-- Eventos Não Esportivos -->
                            <div class="col-md-6">
                                <h6><i class="bi bi-calendar-event agenda-icon"></i> Proximo evento não esportivo</h6>
                                <?php
                                $eventosNaoEsportivos = array_filter($eventos_presenca, function($evento) {
                                    return $evento['tipo_agendamento'] !== 'esportivo';
                                });
                                ?>
                                <?php if (empty($eventosNaoEsportivos)): ?>
                                    <div class="text-center text-muted py-3">
                                        <p class="mb-0">Nenhum evento não esportivo confirmado</p>
                                    </div>
                                <?php else: ?>
                                    <?php foreach ($eventosNaoEsportivos as $evento): ?>
                                        <div class="card mb-3 borda-card event-clickable" data-event-id="<?php echo $evento['id']; ?>" style="cursor: pointer;">
                                            <div class="card-body">
                                                <h6 class="card-title text-black"><?php echo htmlspecialchars($evento['titulo']); ?></h6>
                                                <p class="card-text mb-2">
                                                    <strong>Data:</strong> <?php echo date('d/m/Y', strtotime($evento['data_agendamento'])); ?><br>
                                                    <strong>Horário:</strong> <?php echo htmlspecialchars($evento['horario_periodo']); ?><br>
                                                    <strong>Responsável:</strong> <?php echo htmlspecialchars($evento['responsavel']); ?>
                                                </p>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="text-center mt-3">
                            <a href="/agenda" class="btn btn-outline-primary">
                                <i class="bi bi-calendar3"></i> Ver Agenda Completa
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
</section>

</body>
</html>