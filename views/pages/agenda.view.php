<?php
#
# View da Agenda Pública da Quadra.
# Exibe todos os eventos futuros (esportivos e não esportivos) e permite
# que os usuários marquem ou desmarquem presença. Também possui uma seção
# oculta para visualizar eventos passados.
#
?>
<h1>Agenda da Quadra</h1>
<p>Confira os próximos eventos aprovados e marque sua presença.</p>

<div class="mb-5">
    <h2 class="mb-4"><i class="bi bi-calendar-plus text-success"></i> Próximos Eventos</h2>

    <!-- Botões para alternar entre tipos de eventos -->
    <div class="row mb-4">
        <div class="col-md-6">
            <button class="btn btn-primary btn-lg w-100" id="btnEventosEsportivos" onclick="toggleEventos('esportivos')">
                <i class="bi bi-trophy-fill"></i> Eventos Esportivos
                <span class="badge bg-light text-dark ms-2"><?php echo count($eventos_futuros_esportivos); ?></span>
            </button>
        </div>
        <div class="col-md-6">
            <button class="btn btn-success btn-lg w-100" id="btnEventosNaoEsportivos" onclick="toggleEventos('nao_esportivos')">
                <i class="bi bi-calendar-event-fill"></i> Eventos Não Esportivos
                <span class="badge bg-light text-dark ms-2"><?php echo count($eventos_futuros_nao_esportivos); ?></span>
            </button>
        </div>
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
                            <div class="list-group-item list-group-item-action flex-column align-items-start mb-3 border-primary">
                                <div class="d-flex w-100 justify-content-between">
                                    <h5 class="mb-1 text-primary"><?php echo htmlspecialchars($evento['titulo'] ?? ''); ?></h5>
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

                                <div class="mt-2">
                                    <form method="post" action="/agenda/presenca" class="d-inline">
                                        <input type="hidden" name="agendamento_id" value="<?php echo $evento['id']; ?>">
                                        <?php if ($evento['presenca_id']): ?>
                                            <input type="hidden" name="action" value="desmarcar">
                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                <i class="bi bi-x-circle-fill"></i> Desmarcar Presença
                                            </button>
                                        <?php else: ?>
                                            <input type="hidden" name="action" value="marcar">
                                            <button type="submit" class="btn btn-sm btn-outline-success">
                                                <i class="bi bi-check-circle"></i> Marcar Presença
                                            </button>
                                        <?php endif; ?>
                                    </form>
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
        <div class="card shadow-sm border-success">
            <div class="card-header bg-success text-white">
                <h3 class="mb-0"><i class="bi bi-calendar-event-fill"></i> Eventos Não Esportivos</h3>
            </div>
            <div class="card-body">
                <?php if (!empty($eventos_futuros_nao_esportivos)): ?>
                    <div class="list-group">
                        <?php foreach ($eventos_futuros_nao_esportivos as $evento): ?>
                            <div class="list-group-item list-group-item-action flex-column align-items-start mb-3 border-success">
                                <div class="d-flex w-100 justify-content-between">
                                    <h5 class="mb-1 text-success"><?php echo htmlspecialchars($evento['titulo'] ?? ''); ?></h5>
                                    <small class="text-success fw-bold"><?php echo date('d/m/Y', strtotime($evento['data_agendamento'])); ?></small>
                                </div>

                                <div class="row mb-2">
                                    <div class="col-md-6">
                                        <p class="mb-1"><strong>Horário:</strong> <?php echo htmlspecialchars($evento['horario_periodo'] ?? $evento['periodo']); ?></p>
                                    </div>
                                    <div class="col-md-6">
                                        <small class="text-muted"><strong>Responsável:</strong> <?php echo htmlspecialchars($evento['responsavel'] ?? ''); ?></small>
                                    </div>
                                </div>

                                <form method="post" action="/agenda/presenca" class="mt-2">
                                    <input type="hidden" name="agendamento_id" value="<?php echo $evento['id']; ?>">
                                    <?php if ($evento['presenca_id']): ?>
                                        <input type="hidden" name="action" value="desmarcar">
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            <i class="bi bi-x-circle-fill"></i> Desmarcar Presença
                                        </button>
                                    <?php else: ?>
                                        <input type="hidden" name="action" value="marcar">
                                        <button type="submit" class="btn btn-sm btn-outline-success">
                                            <i class="bi bi-check-circle"></i> Marcar Presença
                                        </button>
                                    <?php endif; ?>
                                </form>
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
                                <div class="list-group-item flex-column align-items-start mb-2 bg-light">
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
                                <div class="list-group-item flex-column align-items-start mb-2 bg-light">
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

    // Scroll suave para a seção
    setTimeout(() => {
        document.querySelector('.eventos-section[style*="block"]').scrollIntoView({
            behavior: 'smooth',
            block: 'start'
        });
    }, 100);
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
</script>