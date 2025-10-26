<?php
/**
 * VIEW: Agenda Pública da Quadra (Bootstrap-only, compatível com CSS/JS enviados)
 *
 * Mantido:
 * - IDs, classes e data-attrs usados pelo JS: 
 *   #btnEventosEsportivos, #btnEventosNaoEsportivos, 
 *   #eventosEsportivos, #eventosNaoEsportivos, .eventos-section, 
 *   .event-clickable, .presenca-btn, .badge.bg-info, etc.
 * - Botões de toggle com classes .btn-evento-toggle e .title-nao-esportivos
 * - Seções .eventos-section com display controlado pelo teu JS
 * - Seção de eventos passados com #eventosPassadosSection, toggle por JS
 */
?>

<section class="mb-4">
  <h1 class="h4 fw-bold mb-1 d-flex align-items-center gap-2">
    <i class="bi bi-calendar-plus icon-calender"></i>
    Agenda da Quadra
  </h1>
  <p class="text-muted mb-3">Confira os próximos eventos aprovados e marque sua presença.</p>
</section>

<section class="mb-5">
  <h2 class="h5 fw-semibold mb-3 d-flex align-items-center gap-2">
    <i class="bi bi-calendar-plus text-success"></i>
    Próximos Eventos
  </h2>

  <!-- Botões de alternância (IDs/classe conforme teu JS/CSS) -->
  <div class="btn-eventos-container">
    <button
      class="btn btn-primary btn-evento-toggle d-flex align-items-center justify-content-center flex-fill"
      id="btnEventosEsportivos"
      type="button"
      onclick="toggleEventos('esportivos')">
      <i class="bi bi-trophy-fill me-2"></i>
      <span>Eventos Esportivos</span>
      <span class="badge bg-light text-dark ms-2"><?php echo count($eventos_futuros_esportivos ?? []); ?></span>
    </button>

    <button
      class="btn title-nao-esportivos btn-evento-toggle d-flex align-items-center justify-content-center flex-fill"
      id="btnEventosNaoEsportivos"
      type="button"
      onclick="toggleEventos('nao_esportivos')">
      <i class="bi bi-calendar-event-fill me-2"></i>
      <span>Eventos Não Esportivos</span>
      <span class="badge bg-light text-dark ms-2"><?php echo count($eventos_futuros_nao_esportivos ?? []); ?></span>
    </button>
  </div>

  <!-- Seção: Esportivos (display controlado pelo teu JS) -->
  <div id="eventosEsportivos" class="eventos-section" style="display: none;">
    <div class="card shadow-sm border-primary border-radius-card">
      <div class="card-header bg-primary text-white py-2">
        <h3 class="h6 mb-0 d-flex align-items-center gap-2">
          <i class="bi bi-trophy-fill"></i> Eventos Esportivos
        </h3>
      </div>
      <div class="card-body">
        <?php if (!empty($eventos_futuros_esportivos)): ?>
          <div class="list-group list-group-flush">
            <?php foreach ($eventos_futuros_esportivos as $evento): ?>
              <div
                class="list-group-item list-group-item-action flex-column align-items-start mb-3 border-primary border-radius-card event-clickable"
                data-event-id="<?php echo (int)$evento['id']; ?>"
                style="cursor: pointer;">
                <div class="d-flex w-100 justify-content-between">
                  <h5 class="mb-1 title-eventos-nao-es">
                    <?php echo htmlspecialchars($evento['titulo'] ?? ''); ?>
                  </h5>
                  <small class="text-primary fw-bold">
                    <?php echo date('d/m/Y', strtotime($evento['data_agendamento'])); ?>
                  </small>
                </div>

                <div class="row mb-2">
                  <div class="col-md-6">
                    <p class="mb-1">
                      <strong>Horário:</strong>
                      <?php echo htmlspecialchars($evento['horario_periodo'] ?? $evento['periodo'] ?? ''); ?>
                    </p>
                    <p class="mb-1">
                      <strong>Esporte:</strong>
                      <?php echo htmlspecialchars($evento['esporte_tipo'] ?? 'Não informado'); ?>
                    </p>
                  </div>
                  <div class="col-md-6">
                    <small class="text-muted d-block">
                      <strong>Responsável:</strong>
                      <?php echo htmlspecialchars($evento['responsavel'] ?? ''); ?>
                    </small>
                  </div>
                </div>

                <?php if (!empty($evento['atletica_confirmada'])): ?>
                  <div class="mb-2">
                    <span class="badge bg-success">
                      <i class="bi bi-check-circle"></i>
                      <?php echo htmlspecialchars($evento['atletica_nome'] ?? ''); ?> confirmada
                      <?php if (!empty($evento['quantidade_atletica'])): ?>
                        (<?php echo (int)$evento['quantidade_atletica']; ?> pessoas)
                      <?php endif; ?>
                    </span>
                  </div>
                <?php endif; ?>

                <div class="mb-2">
                  <!-- IMPORTANTE: manter .badge.bg-info (teu JS busca por ela) -->
                  <span class="badge bg-info">
                    <i class="bi bi-people-fill"></i>
                    <?php echo (int)($evento['total_presencas'] ?? 0); ?> pessoa(s) confirmaram presença
                  </span>
                </div>

                <div class="mt-2 d-flex align-items-center">
                  <?php if (!empty($is_logged_in)): ?>
                    <button
                      type="button"
                      class="btn btn-sm presenca-btn"
                      data-agendamento-id="<?php echo (int)$evento['id']; ?>"
                      data-action="<?php echo !empty($evento['presenca_id']) ? 'desmarcar' : 'marcar'; ?>">
                      <?php if (!empty($evento['presenca_id'])): ?>
                        <i class="bi bi-x-circle-fill"></i> Desmarcar Presença
                      <?php else: ?>
                        <i class="bi bi-check-circle"></i> Marcar Presença
                      <?php endif; ?>
                    </button>
                    <div class="spinner-border spinner-border-sm d-none ms-2" role="status" aria-hidden="true"></div>
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
          <div class="alert alert-info mb-0">
            <i class="bi bi-info-circle"></i> Nenhum evento esportivo agendado no momento.
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <!-- Seção: Não Esportivos (display controlado pelo teu JS) -->
  <div id="eventosNaoEsportivos" class="eventos-section" style="display: none;">
    <div class="card shadow-sm border-radius-card">
      <div class="card-header title-nao-esportivos text-white py-2">
        <h3 class="h6 mb-0 d-flex align-items-center gap-2">
          <i class="bi bi-calendar-event-fill"></i> Eventos Não Esportivos
        </h3>
      </div>
      <div class="card-body">
        <?php if (!empty($eventos_futuros_nao_esportivos)): ?>
          <div class="list-group list-group-flush">
            <?php foreach ($eventos_futuros_nao_esportivos as $evento): ?>
              <div
                class="list-group-item list-group-item-action flex-column align-items-start mb-3 borda-card border-radius-card event-clickable"
                data-event-id="<?php echo (int)$evento['id']; ?>"
                style="cursor: pointer;">
                <div class="d-flex w-100 justify-content-between">
                  <h5 class="mb-1 title-eventos-nao-es">
                    <?php echo htmlspecialchars($evento['titulo'] ?? ''); ?>
                  </h5>
                  <small class="data-card fw-bold">
                    <?php echo date('d/m/Y', strtotime($evento['data_agendamento'])); ?>
                  </small>
                </div>

                <div class="row mb-2">
                  <div class="col-md-6">
                    <p class="mb-1">
                      <strong>Horário:</strong>
                      <?php echo htmlspecialchars($evento['horario_periodo'] ?? $evento['periodo'] ?? ''); ?>
                    </p>
                  </div>
                  <div class="col-md-6">
                    <small class="text-muted d-block">
                      <strong>Responsável:</strong>
                      <?php echo htmlspecialchars($evento['responsavel'] ?? ''); ?>
                    </small>
                  </div>
                </div>

                <div class="mb-2">
                  <!-- Mantém .badge.bg-info para o contador via JS -->
                  <span class="badge bg-info">
                    <i class="bi bi-people-fill"></i>
                    <?php echo (int)($evento['total_presencas'] ?? 0); ?> pessoa(s) confirmaram presença
                  </span>
                </div>

                <div class="mt-2 d-flex align-items-center">
                  <?php if (!empty($is_logged_in)): ?>
                    <button
                      type="button"
                      class="btn btn-sm presenca-btn"
                      data-agendamento-id="<?php echo (int)$evento['id']; ?>"
                      data-action="<?php echo !empty($evento['presenca_id']) ? 'desmarcar' : 'marcar'; ?>">
                      <?php if (!empty($evento['presenca_id'])): ?>
                        <i class="bi bi-x-circle-fill"></i> Desmarcar Presença
                      <?php else: ?>
                        <i class="bi bi-check-circle"></i> Marcar Presença
                      <?php endif; ?>
                    </button>
                    <div class="spinner-border spinner-border-sm d-none ms-2" role="status" aria-hidden="true"></div>
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
          <div class="alert alert-info mb-0">
            <i class="bi bi-info-circle"></i> Nenhum evento não esportivo agendado no momento.
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</section>

<?php $total_eventos_passados = count($eventos_passados ?? []); ?>

<?php if ($total_eventos_passados > 0): ?>
  <div class="text-center mb-4">
    <button
      class="btn btn-outline-secondary btn-lg"
      id="toggleEventosPassados"
      type="button"
      onclick="toggleEventosPassados()">
      <i class="bi bi-clock-history"></i>
      <span id="toggleText">Ver Eventos Passados</span>
      <span class="badge bg-secondary ms-2"><?php echo $total_eventos_passados; ?></span>
      <i class="bi bi-chevron-down ms-1" id="toggleIcon"></i>
    </button>
  </div>
<?php endif; ?>

<?php if ($total_eventos_passados > 0): ?>
  <section id="eventosPassadosSection" style="display: none;">
    <hr class="my-5">
    <div class="mb-4">
      <h2 class="h5 text-muted d-flex align-items-center gap-2 mb-3">
        <i class="bi bi-clock-history"></i> Eventos Passados
      </h2>
      <div class="row g-4">
        <!-- Passados Esportivos -->
        <div class="col-md-6">
          <h3 class="h6 text-muted d-flex align-items-center gap-2 mb-2">
            <i class="bi bi-trophy"></i> Esportivos
          </h3>
          <div class="list-group list-group-flush">
            <?php if (!empty($eventos_passados_esportivos)): ?>
              <?php foreach ($eventos_passados_esportivos as $evento): ?>
                <div
                  class="list-group-item bg-light event-clickable"
                  data-event-id="<?php echo (int)$evento['id']; ?>"
                  style="cursor: pointer;">
                  <div class="d-flex w-100 justify-content-between">
                    <h5 class="h6 mb-1 text-muted"><?php echo htmlspecialchars($evento['titulo'] ?? ''); ?></h5>
                    <small class="text-muted"><?php echo date('d/m/Y', strtotime($evento['data_agendamento'])); ?></small>
                  </div>
                  <div class="small text-muted">
                    <strong>Horário:</strong>
                    <?php echo htmlspecialchars($evento['horario_periodo'] ?? $evento['periodo'] ?? ''); ?>
                    <?php if (!empty($evento['esporte_tipo'])): ?>
                      | <strong>Esporte:</strong> <?php echo htmlspecialchars($evento['esporte_tipo']); ?>
                    <?php endif; ?>
                  </div>
                  <div class="small text-muted">Responsável: <?php echo htmlspecialchars($evento['responsavel'] ?? ''); ?></div>
                  <div class="mt-2">
                    <?php if (!empty($evento['presenca_id'])): ?>
                      <span class="badge bg-success">
                        <i class="bi bi-check-circle-fill"></i> Presença marcada
                      </span>
                    <?php else: ?>
                      <span class="badge bg-light text-dark border">
                        <i class="bi bi-x-circle"></i> Evento finalizado
                      </span>
                    <?php endif; ?>
                  </div>
                </div>
              <?php endforeach; ?>
            <?php else: ?>
              <p class="text-muted mb-0">Nenhum evento esportivo passado.</p>
            <?php endif; ?>
          </div>
        </div>

        <!-- Passados Não Esportivos -->
        <div class="col-md-6">
          <h3 class="h6 text-muted d-flex align-items-center gap-2 mb-2">
            <i class="bi bi-calendar-event"></i> Não Esportivos
          </h3>
          <div class="list-group list-group-flush">
            <?php if (!empty($eventos_passados_nao_esportivos)): ?>
              <?php foreach ($eventos_passados_nao_esportivos as $evento): ?>
                <div
                  class="list-group-item bg-light event-clickable"
                  data-event-id="<?php echo (int)$evento['id']; ?>"
                  style="cursor: pointer;">
                  <div class="d-flex w-100 justify-content-between">
                    <h5 class="h6 mb-1 text-muted"><?php echo htmlspecialchars($evento['titulo'] ?? ''); ?></h5>
                    <small class="text-muted"><?php echo date('d/m/Y', strtotime($evento['data_agendamento'])); ?></small>
                  </div>
                  <div class="small text-muted">
                    <strong>Horário:</strong>
                    <?php echo htmlspecialchars($evento['horario_periodo'] ?? $evento['periodo'] ?? ''); ?>
                  </div>
                  <div class="small text-muted">Responsável: <?php echo htmlspecialchars($evento['responsavel'] ?? ''); ?></div>
                  <div class="mt-2">
                    <?php if (!empty($evento['presenca_id'])): ?>
                      <span class="badge bg-success">
                        <i class="bi bi-check-circle-fill"></i> Presença marcada
                      </span>
                    <?php else: ?>
                      <span class="badge bg-light text-dark border">
                        <i class="bi bi-x-circle"></i> Evento finalizado
                      </span>
                    <?php endif; ?>
                  </div>
                </div>
              <?php endforeach; ?>
            <?php else: ?>
              <p class="text-muted mb-0">Nenhum evento não esportivo passado.</p>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>
  </section>
<?php endif; ?>
