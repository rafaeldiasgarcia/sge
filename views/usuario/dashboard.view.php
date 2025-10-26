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
<section>
    <div class="container">
        <section class="welcome-section">
            <div class="welcome-text">
                <h1 class="welcome-title">SEJA BEM-VINDO</h1>
                <h1 class="welcome-title">A NOSSA SALA DE EVENTOS</h1>
            </div>
        </section>

        <!-- CARROSSEL COM 2 SLIDES -->
        <div id="carouselExampleIndicators" class="carousel slide carousel-fade" data-bs-ride="false">
            <div class="carousel-indicators">
                <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
                <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="1" aria-label="Slide 2"></button>
            </div>

    <div class="carousel-inner">
        <!-- SLIDE 1: Container do Calendário -->
        <div class="carousel-item active">
            <div class="calendar-wrapper">
                <div class="calendar-card">
                    <div class="calendar-content">
                        <div class="calendar-text">
                            <div class="calendar-text-container">
                                <div class="text-line">ESTE É O CALENDÁRIO!</div>
                                <div class="text-line">CONFIRA OS DIAS DOS</div>
                                <div class="text-line">EVENTOS PARA QUE</div>
                                <div class="text-line">VOCÊ NÃO PERCA</div>
                            </div>
                            <div class="calendar-text-final">
                                <span class="highlight">NADA!!!</span>
                            </div>
                            <div class="calendar-stats">
                                <div class="stat-item">
                                    <span class="stat-number" id="calendarTotalEvents"><?php echo count($todos_eventos ?? []); ?></span>
                                    <span class="stat-label">Eventos</span>
                                </div>
                                <div class="stat-item">
                                    <span class="stat-number" id="calendarAvailableDays"><?php echo date('t') - count($todos_eventos ?? []); ?></span>
                                    <span class="stat-label">Dias Livres</span>
                                </div>
                                <div class="stat-item">
                                    <span class="stat-number" id="calendarBusyDays"><?php echo count($todos_eventos ?? []); ?></span>
                                    <span class="stat-label">Ocupados</span>
                                </div>
                            </div>
                        </div>
                        <div class="calendar-character">
                            <img src="/img/jogador-laranja2.webp" alt="Jogador" class="player-image">
                            <div class="calendar-widget">
                                <div class="calendar-header">
                                    <h4>Calendário</h4>
                                    <div class="calendar-nav">
                                        <button class="nav-btn" id="calendarPrevMonth">‹</button>
                                        <button class="nav-btn" id="calendarNextMonth">›</button>
                                    </div>
                                </div>
                                <div class="calendar-month" id="calendarCurrentMonth"><?php echo strtoupper($dataMes->format('F')); ?></div>
                                <div class="calendar-year" id="calendarCurrentYear"><?php echo $dataMes->format('Y'); ?></div>
                                <div class="calendar-grid" id="calendarGridWidget">
                                    <!-- Dias da semana -->
                                    <div class="calendar-day">DOM</div>
                                    <div class="calendar-day">SEG</div>
                                    <div class="calendar-day">TER</div>
                                    <div class="calendar-day">QUA</div>
                                    <div class="calendar-day">QUI</div>
                                    <div class="calendar-day">SEX</div>
                                    <div class="calendar-day">SAB</div>
                                    
                                    <!-- Dias do mês serão gerados dinamicamente -->
                                    <?php
                                    $hoje = new DateTime();
                                    $mesAtual = $_GET['mes'] ?? date('Y-m');
                                    $dataMes = new DateTime($mesAtual . '-01');
                                    $primeiroDia = new DateTime($dataMes->format('Y-m-01'));
                                    $ultimoDia = new DateTime($dataMes->format('Y-m-t'));
                                    $primeiroW = (int)$primeiroDia->format('w');
                                    $diasNoMes = (int)$ultimoDia->format('d');
                                    
                                    // Adiciona células vazias antes do primeiro dia do mês
                                    for ($i = 0; $i < $primeiroW; $i++): ?>
                                        <div class="calendar-date empty"></div>
                                    <?php endfor;
                                    
                                    // Loop por cada dia do mês
                                    for ($dia = 1; $dia <= $diasNoMes; $dia++):
                                        $dataAtual = new DateTime($dataMes->format('Y-m') . '-' . str_pad($dia, 2, '0', STR_PAD_LEFT));
                                        $isToday = $dataAtual->format('Y-m-d') === $hoje->format('Y-m-d');
                                        $isPast = $dataAtual < $hoje;
                                        
                                    // Verifica quantos horários estão ocupados neste dia
                                    $primeiroHorarioOcupado = false;
                                    $segundoHorarioOcupado = false;
                                    $eventosDoDia = [];
                                    
                                    if (!empty($todos_eventos)) {
                                        foreach ($todos_eventos as $evento) {
                                            if (date('Y-m-d', strtotime($evento['data_agendamento'])) === $dataAtual->format('Y-m-d')) {
                                                $eventosDoDia[] = $evento;
                                                
                                                // Verificar qual horário está ocupado
                                                if ($evento['periodo'] === 'primeiro') {
                                                    $primeiroHorarioOcupado = true;
                                                } elseif ($evento['periodo'] === 'segundo') {
                                                    $segundoHorarioOcupado = true;
                                                }
                                            }
                                        }
                                    }
                                    
                                    // Determinar cor baseada na ocupação dos horários
                                    $corDia = '';
                                    if (!$primeiroHorarioOcupado && !$segundoHorarioOcupado) {
                                        $corDia = 'dia-livre'; // Verde - nenhum horário ocupado
                                    } elseif (($primeiroHorarioOcupado && !$segundoHorarioOcupado) || (!$primeiroHorarioOcupado && $segundoHorarioOcupado)) {
                                        $corDia = 'periodo-livre'; // Amarelo - apenas 1 horário ocupado
                                    } else {
                                        $corDia = 'dia-ocupado'; // Vermelho - ambos os horários ocupados
                                    }
                                        
                                        $class = 'calendar-date ' . $corDia;
                                        if ($isToday) $class .= ' today';
                                        if ($isPast) $class .= ' past';
                                        if ($primeiroHorarioOcupado || $segundoHorarioOcupado) $class .= ' has-event';
                                        ?>
                                        <div class="<?= $class ?>" data-date="<?= $dataAtual->format('Y-m-d') ?>" data-eventos='<?= json_encode($eventosDoDia) ?>' style="<?= $corDia === 'dia-livre' ? 'background-color: #dcfce7 !important; border: 2px solid #16a34a !important; color: #166534 !important;' : ($corDia === 'periodo-livre' ? 'background-color: #fef3c7 !important; border: 2px solid #d97706 !important; color: #92400e !important;' : ($corDia === 'dia-ocupado' ? 'background-color: #fecaca !important; border: 2px solid #dc2626 !important; color: #991b1b !important;' : '')) ?>">
                                            <div class="calendar-day-number"><?= $dia ?></div>
                                        </div>
                                    <?php endfor; ?>
                                </div>
                                
                                <div class="calendar-legend">
                                    <div class="legend-item">
                                        <div class="legend-color legend-red"></div>
                                        <span>DIA OCUPADO (2 HORÁRIOS)</span>
                                    </div>
                                    <div class="legend-item">
                                        <div class="legend-color legend-orange"></div>
                                        <span>PERÍODO LIVRE (1 HORÁRIO)</span>
                                    </div>
                                    <div class="legend-item">
                                        <div class="legend-color legend-green"></div>
                                        <span>DIA LIVRE (SEM EVENTOS)</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Modal de detalhes do evento -->
                <div class="event-modal" id="calendarEventModal">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h3 id="calendarModalTitle">Detalhes do Evento</h3>
                            <button class="close-modal" id="calendarCloseModal">&times;</button>
                        </div>
                        <div class="modal-body" id="calendarModalBody">
                            <!-- Conteúdo será preenchido dinamicamente -->
                        </div>
                    </div>
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

            <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Previous</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Next</span>
            </button>
        </div>
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

    <div class="mt-4">
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
</section>
