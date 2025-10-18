<?php
/**
 * ============================================================================
 * VIEW: MEUS AGENDAMENTOS
 * ============================================================================
 * 
 * Lista todos os agendamentos solicitados pelo usuário logado com opções
 * de edição e cancelamento.
 * 
 * FUNCIONALIDADES:
 * - Visualizar histórico completo de solicitações
 * - Ver status de cada agendamento (pendente/aprovado/rejeitado)
 * - Editar eventos pendentes
 * - Cancelar eventos (qualquer status)
 * - Ver observações/justificativas de aprovação/rejeição
 * - Clicar no evento para ver detalhes em popup
 * 
 * STATUS POSSÍVEIS:
 * - pendente: aguardando análise do coordenador
 * - aprovado: evento confirmado, aparece na agenda pública
 * - rejeitado: solicitação negada pelo coordenador
 * - cancelado: cancelado pelo usuário
 * 
 * VARIÁVEIS RECEBIDAS:
 * @var array $agendamentos - Lista de todos os agendamentos do usuário
 *                            [id, titulo, tipo_agendamento, subtipo_evento,
 *                             data_agendamento, periodo, horario_periodo,
 *                             status_agendamento, motivo_rejeicao]
 * 
 * AÇÕES DISPONÍVEIS:
 * - Editar (apenas pendentes): redireciona para /editar-evento/{id}
 * - Cancelar (todos): POST para /cancelar-agendamento com confirmação
 * - Visualizar detalhes: popup via event-clickable
 * 
 * BADGES DE STATUS:
 * - Pendente: badge warning (amarelo)
 * - Aprovado: badge success (verde)
 * - Rejeitado: badge danger (vermelho)
 * - Cancelado: badge secondary (cinza)
 * 
 * CONTROLLER: AgendamentoController::meusAgendamentos()
 * JAVASCRIPT: event-popup.js (detalhes do evento)
 */
?>

<h2>Meus Agendamentos</h2>
<p>Acompanhe e gerencie o status de todas as suas solicitações de uso da quadra.</p>

<!-- Mensagens de feedback -->
<?php if (isset($_SESSION['success_message'])): ?>
    <div class="alert alert-success">
        <?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?>
    </div>
<?php endif; ?>

<?php if (isset($_SESSION['error_message'])): ?>
    <div class="alert alert-danger">
        <?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?>
    </div>
<?php endif; ?>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                <tr>
                    <th>Título do Evento</th>
                    <th>Tipo</th>
                    <th>Data</th>
                    <th>Período</th>
                    <th>Status</th>
                    <th>Observações</th>
                    <th>Ações</th>
                </tr>
                </thead>
                <tbody>
                <?php if (empty($agendamentos)): ?>
                    <tr>
                        <td colspan="7" class="text-center">Você ainda não fez nenhuma solicitação de agendamento.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($agendamentos as $evento): ?>
                        <tr class="event-clickable" data-event-id="<?php echo $evento['id']; ?>" style="cursor: pointer;">
                            <td>
                                <strong><?php echo htmlspecialchars($evento['titulo']); ?></strong>
                                <?php if (!empty($evento['subtipo_evento'])): ?>
                                    <br><small class="text-muted"><?php echo ucfirst($evento['subtipo_evento']); ?></small>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php $tipoAgendamento = $evento['tipo_agendamento'] ?? 'nao_esportivo'; ?>
                                <span class="badge <?php echo $tipoAgendamento === 'esportivo' ? 'bg-success' : 'bg-info'; ?>">
                                    <?php echo $tipoAgendamento === 'esportivo' ? 'Esportivo' : 'Não Esportivo'; ?>
                                </span>
                                <?php if ($tipoAgendamento === 'esportivo' && !empty($evento['esporte_tipo'])): ?>
                                    <br><small><?php echo ucfirst($evento['esporte_tipo']); ?></small>
                                <?php endif; ?>
                            </td>
                            <td><?php echo date('d/m/Y', strtotime($evento['data_agendamento'])); ?></td>
                            <td>
                                <span class="badge bg-secondary">
                                    <?php
                                    $periodos = [
                                        'primeiro' => '19:15-20:55',
                                        'segundo' => '21:10-22:50',
                                        'manha' => 'Manhã',
                                        'tarde' => 'Tarde',
                                        'noite' => 'Noite'
                                    ];
                                    echo $periodos[$evento['periodo']] ?? ucfirst($evento['periodo']);
                                    ?>
                                </span>
                            </td>
                            <td>
                                <?php
                                $status_map = [
                                    'pendente' => ['class' => 'bg-warning text-dark', 'text' => 'Pendente'],
                                    'aprovado' => ['class' => 'bg-success', 'text' => 'Aprovado'],
                                    'rejeitado' => ['class' => 'bg-danger', 'text' => 'Rejeitado'],
                                    'cancelado' => ['class' => 'bg-secondary', 'text' => 'Cancelado'],
                                    'finalizado' => ['class' => 'bg-info', 'text' => 'Finalizado']
                                ];
                                $status_info = $status_map[$evento['status']] ?? ['class' => 'bg-secondary', 'text' => 'Desconhecido'];
                                ?>
                                <span class="badge <?php echo $status_info['class']; ?>"><?php echo $status_info['text']; ?></span>
                            </td>
                            <td><?php echo htmlspecialchars($evento['motivo_rejeicao'] ?? '-'); ?></td>
                            <td onclick="event.stopPropagation();">
                                <?php if (in_array($evento['status'], ['pendente', 'aprovado'])): ?>
                                    <!-- Permitir editar tanto pendentes quanto aprovados -->
                                    <a href="/agendamento/editar/<?php echo $evento['id']; ?>" class="btn btn-sm btn-info" title="Editar">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-warning"
                                            onclick="cancelarEvento(<?php echo $evento['id']; ?>, '<?php echo htmlspecialchars($evento['titulo'], ENT_QUOTES); ?>')"
                                            title="Cancelar">
                                        <i class="bi bi-x-circle"></i> <?php echo $evento['status'] === 'aprovado' ? 'Cancelar' : ''; ?>
                                    </button>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="modalCancelamento" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title"><i class="bi bi-exclamation-triangle"></i> Confirmar Cancelamento</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Tem certeza que deseja cancelar o evento:</p>
                <p><strong id="nomeEvento"></strong></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Não, manter evento</button>
                <form method="post" action="/agendamento/cancelar" id="formCancelamento" class="d-inline">
                    <input type="hidden" name="agendamento_id" id="eventoIdCancelamento">
                    <button type="submit" class="btn btn-warning">
                        <i class="bi bi-x-circle"></i> Sim, cancelar evento
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
