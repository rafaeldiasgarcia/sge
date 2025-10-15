<?php
/**
 * ============================================================================
 * VIEW: AGENDA PÚBLICA DA QUADRA
 * ============================================================================
 * 
 * Exibe todos os eventos aprovados (futuros e passados). A agenda é PÚBLICA
 * (não requer login), mas para marcar presença é necessário estar autenticado.
 * 
 * FUNCIONALIDADES:
 * - Visualizar eventos esportivos e não esportivos separadamente (PÚBLICO)
 * - Marcar/desmarcar presença em eventos futuros via AJAX (REQUER LOGIN)
 * - Ver contador de presenças confirmadas em tempo real
 * - Expandir seção de eventos passados
 * - Popup com detalhes do evento ao clicar
 * - Interface responsiva com alternância entre abas
 * 
 * VARIÁVEIS RECEBIDAS:
 * @var array $eventos_futuros_esportivos     - Eventos esportivos futuros
 * @var array $eventos_futuros_nao_esportivos - Eventos não esportivos futuros
 * @var array $eventos_passados_esportivos    - Eventos esportivos passados
 * @var array $eventos_passados_nao_esportivos- Eventos não esportivos passados
 * @var array $eventos_passados               - Todos os eventos passados
 * @var bool  $is_logged_in                   - Se o usuário está autenticado
 * 
 * ESTRUTURA DE EVENTOS:
 * - id, titulo, data_agendamento, horario_periodo, periodo
 * - esporte_tipo (apenas esportivos)
 * - responsavel, atletica_nome, atletica_confirmada
 * - total_presencas, presenca_id (se user marcou presença)
 * 
 * FEATURES:
 * - Atualização AJAX de presenças sem reload (apenas para logados)
 * - Contadores dinâmicos
 * - Toggle entre eventos esportivos/não esportivos
 * - Seção colapsável de eventos passados
 * - Link para login quando usuário não autenticado tenta interagir
 * 
 * CONTROLLER: AgendaController::index()
 * JAVASCRIPT: Inline (marcação de presença AJAX, toggles)
 * CSS: agenda.css, calendar.css
 */
?>
<h1 class="title-dashboard">Agenda da Quadra</h1>
<p class="title-dashboard">Confira os próximos eventos aprovados e marque sua presença.</p>

<div class="mb-5">
    <h2 class="mb-4"><i class="bi bi-calendar-plus text-success icon-calender"></i> Próximos Eventos</h2>

    <!-- Botões para alternar entre tipos de eventos -->
    <div class="btn-eventos-container">
        <button class="btn btn-primary btn-evento-toggle flex-fill d-flex align-items-center justify-content-center" id="btnEventosEsportivos" onclick="toggleEventos('esportivos')">
            <i class="bi bi-trophy-fill me-2"></i>
            <span>Eventos Esportivos</span>
            <span class="badge bg-light text-dark ms-2"><?php echo count($eventos_futuros_esportivos); ?></span>
        </button>
        <button class="btn title-nao-esportivos btn-evento-toggle flex-fill d-flex align-items-center justify-content-center" id="btnEventosNaoEsportivos" onclick="toggleEventos('nao_esportivos')">
            <i class="bi bi-calendar-event-fill me-2"></i>
            <span>Eventos Não Esportivos</span>
            <span class="badge bg-light text-dark ms-2"><?php echo count($eventos_futuros_nao_esportivos); ?></span>
        </button>
    </div>

    <!-- Seção de Eventos Esportivos -->
    <div id="eventosEsportivos" class="eventos-section" style="display: none;">
        <div class="card shadow-sm border-primary">
            <div class="card-header bg-primary text-white">
                <h3 class="mb-0"><i class="bi bi-trophy-fill"></i> Eventos Esportivos</h3>
            </div>
            <div class="card-body">
                <?php if (!empty($eventos_futuros_esportivos)): ?>
                    <div class="list-group">
                        <?php foreach ($eventos_futuros_esportivos as $evento): ?>
                            <div class="list-group-item list-group-item-action flex-column align-items-start mb-3 border-primary border-radius-card event-clickable"
                                 data-event-id="<?php echo $evento['id']; ?>"
                                 style="cursor: pointer;">
                                <div class="d-flex w-100 justify-content-between">
                                    <h5 class="mb-1 title-eventos-nao-es"><?php echo htmlspecialchars($evento['titulo'] ?? ''); ?></h5>
                                    <small class="text-primary fw-bold"><?php echo date('d/m/Y', strtotime($evento['data_agendamento'])); ?></small>
                                </div>

                                <div class="row mb-2">
                                    <div class="col-md-6">
                                        <p class="mb-1"><strong>Horário:</strong> <?php echo htmlspecialchars($evento['horario_periodo'] ?? $evento['periodo']); ?></p>
                                        <p class="mb-1"><strong>Esporte:</strong> <?php echo htmlspecialchars($evento['esporte_tipo'] ?? 'Não informado'); ?></p>
                                    </div>
                                    <div class="col-md-6">
                                        <small class="text-muted"><strong>Responsável:</strong> <?php echo htmlspecialchars($evento['responsavel'] ?? ''); ?></small>
                                    </div>
                                </div>

                                <?php if ($evento['atletica_confirmada']): ?>
                                    <div class="mb-2">
                                        <span class="badge bg-success">
                                            <i class="bi bi-check-circle"></i> <?php echo htmlspecialchars($evento['atletica_nome'] ?? ''); ?> confirmada
                                            (<?php echo $evento['quantidade_atletica']; ?> pessoas)
                                        </span>
                                    </div>
                                <?php endif; ?>

                                <div class="mb-2">
                                    <span class="badge bg-info">
                                        <i class="bi bi-people-fill"></i> <?php echo $evento['total_presencas']; ?> pessoa(s) confirmaram presença
                                    </span>
                                </div>

                                <div class="mt-2">
                                    <?php if ($is_logged_in): ?>
                                        <button type="button" class="btn btn-sm presenca-btn"
                                                data-agendamento-id="<?php echo $evento['id']; ?>"
                                                data-action="<?php echo $evento['presenca_id'] ? 'desmarcar' : 'marcar'; ?>">
                                            <?php if ($evento['presenca_id']): ?>
                                                <i class="bi bi-x-circle-fill"></i> Desmarcar Presença
                                            <?php else: ?>
                                                <i class="bi bi-check-circle"></i> Marcar Presença
                                            <?php endif; ?>
                                        </button>
                                        <div class="spinner-border spinner-border-sm d-none ms-2" role="status">
                                            <span class="visually-hidden">Carregando...</span>
                                        </div>
                                    <?php else: ?>
                                        <a href="/login" class="btn btn-sm btn-primary">
                                            <i class="bi bi-box-arrow-in-right"></i> Faça login para marcar presença
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i> Nenhum evento esportivo agendado no momento.
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Seção de Eventos Não Esportivos -->
    <div id="eventosNaoEsportivos" class="eventos-section" style="display: none;">
        <div class="card shadow-sm">
            <div class="card-header title-nao-esportivos text-white">
                <h3 class="mb-0"><i class="bi bi-calendar-event-fill"></i> Eventos Não Esportivos</h3>
            </div>
            <div class="card-body">
                <?php if (!empty($eventos_futuros_nao_esportivos)): ?>
                    <div class="list-group">
                        <?php foreach ($eventos_futuros_nao_esportivos as $evento): ?>
                            <div class="list-group-item list-group-item-action flex-column align-items-start mb-3 borda-card border-radius-card event-clickable"
                                 data-event-id="<?php echo $evento['id']; ?>"
                                 style="cursor: pointer;">
                                <div class="d-flex w-100 justify-content-between">
                                    <h5 class="mb-1 title-eventos-nao-es"><?php echo htmlspecialchars($evento['titulo'] ?? ''); ?></h5>
                                    <small class="data-card fw-bold"><?php echo date('d/m/Y', strtotime($evento['data_agendamento'])); ?></small>
                                </div>

                                <div class="row mb-2">
                                    <div class="col-md-6">
                                        <p class="mb-1"><strong>Horário:</strong> <?php echo htmlspecialchars($evento['horario_periodo'] ?? $evento['periodo']); ?></p>
                                    </div>
                                    <div class="col-md-6">
                                        <small class="text-muted"><strong>Responsável:</strong> <?php echo htmlspecialchars($evento['responsavel'] ?? ''); ?></small>
                                    </div>
                                </div>

                                <div class="mb-2">
                                    <span class="badge bg-info">
                                        <i class="bi bi-people-fill"></i> <?php echo $evento['total_presencas']; ?> pessoa(s) confirmaram presença
                                    </span>
                                </div>

                                <div class="mt-2">
                                    <?php if ($is_logged_in): ?>
                                        <button type="button" class="btn btn-sm presenca-btn"
                                                data-agendamento-id="<?php echo $evento['id']; ?>"
                                                data-action="<?php echo $evento['presenca_id'] ? 'desmarcar' : 'marcar'; ?>">
                                            <?php if ($evento['presenca_id']): ?>
                                                <i class="bi bi-x-circle-fill"></i> Desmarcar Presença
                                            <?php else: ?>
                                                <i class="bi bi-check-circle"></i> Marcar Presença
                                            <?php endif; ?>
                                        </button>
                                        <div class="spinner-border spinner-border-sm d-none ms-2" role="status">
                                            <span class="visually-hidden">Carregando...</span>
                                        </div>
                                    <?php else: ?>
                                        <a href="/login" class="btn btn-sm btn-primary">
                                            <i class="bi bi-box-arrow-in-right"></i> Faça login para marcar presença
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i> Nenhum evento não esportivo agendado no momento.
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php $total_eventos_passados = count($eventos_passados ?? []); ?>

<?php if ($total_eventos_passados > 0): ?>
    <div class="text-center mb-4">
        <button class="btn btn-outline-secondary btn-lg" id="toggleEventosPassados" onclick="toggleEventosPassados()">
            <i class="bi bi-clock-history"></i> <span id="toggleText">Ver Eventos Passados</span>
            <span class="badge bg-secondary ms-2"><?php echo $total_eventos_passados; ?></span>
            <i class="bi bi-chevron-down ms-1" id="toggleIcon"></i>
        </button>
    </div>
<?php endif; ?>

<?php if ($total_eventos_passados > 0): ?>
    <div id="eventosPassadosSection" style="display: none;">
        <hr class="my-5">
        <div class="mb-5">
            <h2 class="mb-4 text-muted"><i class="bi bi-clock-history"></i> Eventos Passados</h2>
            <div class="row">
                <div class="col-md-6">
                    <h3><i class="bi bi-trophy text-muted"></i> Eventos Esportivos</h3>
                    <div class="list-group">
                        <?php if (!empty($eventos_passados_esportivos)): ?>
                            <?php foreach ($eventos_passados_esportivos as $evento): ?>
                                <div class="list-group-item flex-column align-items-start mb-2 bg-light event-clickable"
                                     data-event-id="<?php echo $evento['id']; ?>"
                                     style="cursor: pointer;">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h5 class="mb-1 text-muted"><?php echo htmlspecialchars($evento['titulo'] ?? ''); ?></h5>
                                        <small class="text-muted"><?php echo date('d/m/Y', strtotime($evento['data_agendamento'])); ?></small>
                                    </div>
                                    <p class="mb-1 text-muted"><strong>Horário:</strong> <?php echo htmlspecialchars($evento['horario_periodo'] ?? $evento['periodo']); ?> | <strong>Esporte:</strong> <?php echo htmlspecialchars($evento['esporte_tipo'] ?? 'Não informado'); ?></p>
                                    <small class="text-muted">Responsável: <?php echo htmlspecialchars($evento['responsavel'] ?? ''); ?></small>
                                    <div class="mt-2">
                                        <?php if ($evento['presenca_id']): ?>
                                            <span class="badge bg-success"><i class="bi bi-check-circle-fill"></i> Presença marcada</span>
                                        <?php else: ?>
                                            <span class="badge bg-light text-dark"><i class="bi bi-x-circle"></i> Evento finalizado</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="text-muted">Nenhum evento esportivo passado.</p>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="col-md-6">
                    <h3><i class="bi bi-calendar-event text-muted"></i> Eventos Não Esportivos</h3>
                    <div class="list-group">
                        <?php if (!empty($eventos_passados_nao_esportivos)): ?>
                            <?php foreach ($eventos_passados_nao_esportivos as $evento): ?>
                                <div class="list-group-item flex-column align-items-start mb-2 bg-light event-clickable"
                                     data-event-id="<?php echo $evento['id']; ?>"
                                     style="cursor: pointer;">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h5 class="mb-1 text-muted"><?php echo htmlspecialchars($evento['titulo'] ?? ''); ?></h5>
                                        <small class="text-muted"><?php echo date('d/m/Y', strtotime($evento['data_agendamento'])); ?></small>
                                    </div>
                                    <p class="mb-1 text-muted"><strong>Horário:</strong> <?php echo htmlspecialchars($evento['horario_periodo'] ?? $evento['periodo']); ?></p>
                                    <small class="text-muted">Responsável: <?php echo htmlspecialchars($evento['responsavel'] ?? ''); ?></small>
                                    <div class="mt-2">
                                        <?php if ($evento['presenca_id']): ?>
                                            <span class="badge bg-success"><i class="bi bi-check-circle-fill"></i> Presença marcada</span>
                                        <?php else: ?>
                                            <span class="badge bg-light text-dark"><i class="bi bi-x-circle"></i> Evento finalizado</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="text-muted">Nenhum evento não esportivo passado.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>
</body>
<script>
function toggleEventos(tipo) {
    // Esconder todas as seções
    document.querySelectorAll('.eventos-section').forEach(section => {
        section.style.display = 'none';
    });

    // Remover classe ativa de todos os botões
    document.getElementById('btnEventosEsportivos').classList.remove('active');
    document.getElementById('btnEventosNaoEsportivos').classList.remove('active');

    // Mostrar seção selecionada e ativar botão
    if (tipo === 'esportivos') {
        document.getElementById('eventosEsportivos').style.display = 'block';
        document.getElementById('btnEventosEsportivos').classList.add('active');
    } else if (tipo === 'nao_esportivos') {
        document.getElementById('eventosNaoEsportivos').style.display = 'block';
        document.getElementById('btnEventosNaoEsportivos').classList.add('active');
    }

    // Removi o scroll automático - agora a página fica no lugar
}

function toggleEventosPassados() {
    const section = document.getElementById('eventosPassadosSection');
    const toggleText = document.getElementById('toggleText');
    const toggleIcon = document.getElementById('toggleIcon');

    if (section.style.display === 'none' || section.style.display === '') {
        section.style.display = 'block';
        toggleText.textContent = 'Ocultar Eventos Passados';
        toggleIcon.className = 'bi bi-chevron-up ms-1';
        setTimeout(() => {
            section.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }, 100);
    } else {
        section.style.display = 'none';
        toggleText.textContent = 'Ver Eventos Passados';
        toggleIcon.className = 'bi bi-chevron-down ms-1';
        document.getElementById('toggleEventosPassados').scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
}

// Inicializar mostrando eventos esportivos por padrão
document.addEventListener('DOMContentLoaded', function() {
    toggleEventos('esportivos');
});

// Gerenciar cliques nos botões de presença
document.addEventListener('click', function(event) {
    // Verificar se clicou no botão de presença ou em um elemento dentro dele (como o ícone)
    const presencaBtn = event.target.closest('.presenca-btn');

    if (presencaBtn) {
        // IMPORTANTE: Parar a propagação IMEDIATAMENTE para não abrir o popup
        event.stopPropagation();
        event.preventDefault();

        const btn = presencaBtn;
        const agendamentoId = btn.getAttribute('data-agendamento-id');
        const action = btn.getAttribute('data-action');
        const spinner = btn.nextElementSibling;

        // Mostrar spinner e desabilitar botão
        if (spinner) spinner.classList.remove('d-none');
        btn.setAttribute('disabled', 'true');

        // Criar FormData como se fosse um formulário tradicional
        const formData = new FormData();
        formData.append('agendamento_id', agendamentoId);
        formData.append('action', action);

        // Fazer requisição AJAX para marcar/desmarcar presença
        fetch('/agenda/presenca', {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: formData
        })
        .then(response => {
            return response.json();
        })
        .then(data => {
            // Atualizar interface com base na resposta
            if (data.success) {
                // Encontrar o badge de contagem de presenças no mesmo evento
                const eventoContainer = btn.closest('.list-group-item');
                const badgePresencas = eventoContainer.querySelector('.badge.bg-info');
                
                if (action === 'marcar') {
                    btn.setAttribute('data-action', 'desmarcar');
                    btn.innerHTML = '<i class="bi bi-x-circle-fill"></i> Desmarcar Presença';
                    
                    // Incrementar contador
                    if (badgePresencas) {
                        const currentCount = parseInt(badgePresencas.textContent.match(/\d+/)[0]);
                        badgePresencas.innerHTML = '<i class="bi bi-people-fill"></i> ' + (currentCount + 1) + ' pessoa(s) confirmaram presença';
                    }
                } else {
                    btn.setAttribute('data-action', 'marcar');
                    btn.innerHTML = '<i class="bi bi-check-circle"></i> Marcar Presença';
                    
                    // Decrementar contador
                    if (badgePresencas) {
                        const currentCount = parseInt(badgePresencas.textContent.match(/\d+/)[0]);
                        badgePresencas.innerHTML = '<i class="bi bi-people-fill"></i> ' + Math.max(0, currentCount - 1) + ' pessoa(s) confirmaram presença';
                    }
                }
            } else {
                console.error('Erro na resposta:', data.message);
                alert('Erro ao atualizar presença. Tente novamente mais tarde.');
            }
        })
        .catch(error => {
            console.error('Erro na requisição:', error);
            alert('Erro ao atualizar presença. Tente novamente mais tarde.');
        })
        .finally(() => {
            // Esconder spinner e habilitar botão novamente
            if (spinner) spinner.classList.add('d-none');
            btn.removeAttribute('disabled');
        });
    }
});
</script>