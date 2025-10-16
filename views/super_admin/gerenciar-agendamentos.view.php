<?php
/**
 * ============================================================================
 * VIEW: GERENCIAR AGENDAMENTOS (SUPER ADMIN)
 * ============================================================================
 * 
 * Interface de aprovação/rejeição de solicitações de agendamento com abas
 * para diferentes status.
 * 
 * FUNCIONALIDADES:
 * - Visualizar agendamentos por status (pendentes, aprovados, rejeitados)
 * - Aprovar/rejeitar solicitações com justificativa
 * - Ver detalhes completos de cada evento
 * - Filtros por tipo (esportivo/não esportivo)
 * - Visualizar histórico de eventos rejeitados com motivos
 * 
 * ABAS:
 * - Pendentes: aguardando análise (com ações de aprovar/rejeitar)
 * - Aprovados: eventos confirmados (com ações de editar/cancelar)
 * - Rejeitados: histórico de solicitações negadas (somente visualização)
 * 
 * VARIÁVEIS RECEBIDAS:
 * @var array $pendentes   - Agendamentos aguardando análise
 * @var array $aprovados   - Agendamentos aprovados
 * @var array $rejeitados  - Agendamentos rejeitados (com motivo)
 * 
 * CONTROLLER: SuperAdminController::gerenciarAgendamentos()
 */

use Application\Core\Auth;

?>

<h1>Gerenciar Agendamentos</h1>

<!-- Mensagens de Sucesso/Erro -->
<?php if (isset($_SESSION['success_message'])): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle-fill"></i> <?php echo htmlspecialchars($_SESSION['success_message']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php unset($_SESSION['success_message']); ?>
<?php endif; ?>

<?php if (isset($_SESSION['error_message'])): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-triangle-fill"></i> <?php echo htmlspecialchars($_SESSION['error_message']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php unset($_SESSION['error_message']); ?>
<?php endif; ?>

<div class="gerenciar-container">
    <ul class="nav nav-tabs mb-4" id="agendamentosTab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="pendentes-tab" data-bs-toggle="tab" data-bs-target="#pendentes" type="button" role="tab">
                Solicitações Pendentes
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="aprovados-tab" data-bs-toggle="tab" data-bs-target="#aprovados" type="button" role="tab">
                Eventos Aprovados
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="rejeitados-tab" data-bs-toggle="tab" data-bs-target="#rejeitados" type="button" role="tab">
                Eventos Rejeitados
            </button>
        </li>
    </ul>

    <div class="tab-content" id="agendamentosTabContent">
        <!-- Aba de Solicitações Pendentes -->
        <div class="tab-pane fade show active" id="pendentes" role="tabpanel">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Título</th>
                            <th>Tipo</th>
                            <th>Solicitante</th>
                            <th>Data</th>
                            <th>Período</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($pendentes)): ?>
                            <tr>
                                <td colspan="6" class="text-center">Não há solicitações pendentes.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($pendentes as $agendamento): ?>
                                <tr class="event-clickable" data-event-id="<?php echo $agendamento['id']; ?>" style="cursor: pointer;">
                                    <td>
                                        <?php echo htmlspecialchars($agendamento['titulo']); ?>
                                        <?php if (!empty($agendamento['foi_editado'])): ?>
                                            <span class="badge bg-warning text-dark ms-2" title="Este evento foi editado em <?php echo date('d/m/Y H:i', strtotime($agendamento['data_edicao'])); ?>">
                                                <i class="bi bi-pencil-fill"></i> EDITADO
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="badge <?php echo $agendamento['tipo_agendamento'] === 'esportivo' ? 'bg-success' : 'bg-info'; ?>">
                                            <?php echo ucfirst($agendamento['tipo_agendamento']); ?>
                                            <?php if ($agendamento['tipo_agendamento'] === 'esportivo'): ?>
                                                <br><?php echo $agendamento['esporte_tipo']; ?>
                                            <?php endif; ?>
                                        </span>
                                    </td>
                                    <td><?php echo htmlspecialchars($agendamento['solicitante']); ?></td>
                                    <td><?php echo date('d/m/Y', strtotime($agendamento['data_agendamento'])); ?></td>
                                    <td><?php echo $agendamento['periodo'] === 'primeiro' ? '19:15 - 20:55' : '21:10 - 22:50'; ?></td>
                                    <td onclick="event.stopPropagation();">
                                        <form action="/superadmin/agendamentos/aprovar" method="post" class="d-inline">
                                            <input type="hidden" name="id" value="<?php echo $agendamento['id']; ?>">
                                            <button type="submit" class="btn btn-success btn-sm">Aprovar</button>
                                        </form>
                                        <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#rejeitar-<?php echo $agendamento['id']; ?>">
                                            Rejeitar
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Aba de Eventos Aprovados -->
        <div class="tab-pane fade" id="aprovados" role="tabpanel">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Título</th>
                            <th>Tipo</th>
                            <th>Responsável</th>
                            <th>Data</th>
                            <th>Horário</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($aprovados)): ?>
                            <tr>
                                <td colspan="6" class="text-center">Não há eventos aprovados.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($aprovados as $evento): ?>
                                <tr class="event-clickable" data-event-id="<?php echo $evento['id']; ?>" style="cursor: pointer;">
                                    <td><?php echo htmlspecialchars($evento['titulo']); ?></td>
                                    <td>
                                        <span class="badge <?php echo $evento['tipo_agendamento'] === 'esportivo' ? 'bg-success' : 'bg-info'; ?>">
                                            <?php echo ucfirst($evento['tipo_agendamento']); ?>
                                            <?php if ($evento['tipo_agendamento'] === 'esportivo'): ?>
                                                <br><?php echo $evento['esporte_tipo']; ?>
                                            <?php endif; ?>
                                        </span>
                                    </td>
                                    <td><?php echo htmlspecialchars($evento['solicitante']); ?></td>
                                    <td><?php echo date('d/m/Y', strtotime($evento['data_agendamento'])); ?></td>
                                    <td><?php echo $evento['horario_periodo']; ?></td>
                                    <td onclick="event.stopPropagation();">
                                        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#editar-<?php echo $evento['id']; ?>">
                                            Editar
                                        </button>
                                        <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#cancelar-<?php echo $evento['id']; ?>">
                                            Cancelar
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Aba de Eventos Rejeitados -->
        <div class="tab-pane fade" id="rejeitados" role="tabpanel">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Título</th>
                            <th>Tipo</th>
                            <th>Solicitante</th>
                            <th>Data</th>
                            <th>Horário</th>
                            <th>Motivo da Rejeição</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($rejeitados)): ?>
                            <tr>
                                <td colspan="6" class="text-center">Não há eventos rejeitados.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($rejeitados as $evento): ?>
                                <tr class="event-clickable" data-event-id="<?php echo $evento['id']; ?>" style="cursor: pointer;">
                                    <td><?php echo htmlspecialchars($evento['titulo']); ?></td>
                                    <td>
                                        <span class="badge <?php echo $evento['tipo_agendamento'] === 'esportivo' ? 'bg-success' : 'bg-info'; ?>">
                                            <?php echo ucfirst($evento['tipo_agendamento']); ?>
                                            <?php if ($evento['tipo_agendamento'] === 'esportivo'): ?>
                                                <br><?php echo $evento['esporte_tipo']; ?>
                                            <?php endif; ?>
                                        </span>
                                    </td>
                                    <td><?php echo htmlspecialchars($evento['solicitante']); ?></td>
                                    <td><?php echo date('d/m/Y', strtotime($evento['data_agendamento'])); ?></td>
                                    <td><?php echo $evento['horario_periodo']; ?></td>
                                    <td>
                                        <span class="text-danger">
                                            <i class="bi bi-exclamation-triangle"></i>
                                            <?php 
                                                $motivo = $evento['motivo_rejeicao'] ?? 'Sem motivo especificado';
                                                echo htmlspecialchars(strlen($motivo) > 50 ? substr($motivo, 0, 50) . '...' : $motivo);
                                            ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modais de Rejeição para cada agendamento pendente -->
<?php foreach ($pendentes as $agendamento): ?>
    <div class="modal fade" id="rejeitar-<?php echo $agendamento['id']; ?>" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Rejeitar Agendamento</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="/superadmin/agendamentos/rejeitar" method="post">
                    <div class="modal-body">
                        <input type="hidden" name="id" value="<?php echo $agendamento['id']; ?>">
                        <div class="mb-3">
                            <label for="motivo_rejeicao" class="form-label">Motivo da Rejeição</label>
                            <textarea name="motivo_rejeicao" class="form-control" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-danger">Rejeitar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php endforeach; ?>

<!-- Modais para Edição e Cancelamento de eventos aprovados -->
<?php foreach ($aprovados as $evento): ?>
    <!-- Modal de Edição -->
    <div class="modal fade" id="editar-<?php echo $evento['id']; ?>" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Editar Evento</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="/superadmin/agendamentos/update-aprovado" method="post">
                    <div class="modal-body">
                        <input type="hidden" name="id" value="<?php echo $evento['id']; ?>">
                        <div class="mb-3">
                            <label class="form-label">Data do Evento</label>
                            <input type="date" name="data_agendamento" class="form-control" value="<?php echo $evento['data_agendamento']; ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Período</label>
                            <select name="periodo" class="form-control" required>
                                <option value="primeiro" <?php echo $evento['periodo'] === 'primeiro' ? 'selected' : ''; ?>>19:15 - 20:55</option>
                                <option value="segundo" <?php echo $evento['periodo'] === 'segundo' ? 'selected' : ''; ?>>21:10 - 22:50</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Observações</label>
                            <textarea name="observacoes_admin" class="form-control"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Salvar Alterações</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal de Cancelamento -->
    <div class="modal fade" id="cancelar-<?php echo $evento['id']; ?>" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Cancelar Evento</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="/superadmin/agendamentos/cancelar-aprovado" method="post">
                    <div class="modal-body">
                        <input type="hidden" name="id" value="<?php echo $evento['id']; ?>">
                        <div class="mb-3">
                            <label for="motivo_cancelamento" class="form-label">Motivo do Cancelamento</label>
                            <textarea name="motivo_cancelamento" class="form-control" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Voltar</button>
                        <button type="submit" class="btn btn-danger">Confirmar Cancelamento</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php endforeach; ?>

<!-- CSS do Event Popup -->
<link rel="stylesheet" href="/css/event-popup.css">

