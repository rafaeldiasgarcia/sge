<?php
/**
 * ============================================================================
 * VIEW: GERENCIAR USUÁRIOS (SUPER ADMIN)
 * ============================================================================
 * 
 * Lista e permite editar todos os usuários do sistema.
 * 
 * FUNCIONALIDADES:
 * - Visualizar lista completa de usuários
 * - Ver role principal e vínculo institucional
 * - Acessar edição detalhada de cada usuário
 * 
 * VARIÁVEIS RECEBIDAS:
 * @var array $usuarios - Lista completa de usuários do sistema
 * 
 * CONTROLLER: SuperAdminController::gerenciarUsuarios()
 */
?>
<h2>Gerenciar Todos os Usuários</h2>
<p>Visualize e edite as informações de qualquer usuário cadastrado no sistema.</p>

<?php if (isset($_SESSION['success_message'])): ?>
    <div class="alert alert-success"><?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?></div>
<?php endif; ?>
<?php if (isset($_SESSION['error_message'])): ?>
    <div class="alert alert-danger"><?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?></div>
<?php endif; ?>

<!-- Tabs de Navegação -->
<ul class="nav nav-tabs mb-4" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active" id="usuarios-tab" data-bs-toggle="tab" data-bs-target="#usuarios" type="button" role="tab">
            <i class="bi bi-people"></i> Lista de Usuários
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="solicitacoes-tab" data-bs-toggle="tab" data-bs-target="#solicitacoes" type="button" role="tab">
            <i class="bi bi-arrow-left-right"></i> Solicitações de Troca de Curso
            <?php if (!empty($solicitacoes_pendentes)): ?>
                <span class="badge bg-danger"><?php echo count($solicitacoes_pendentes); ?></span>
            <?php endif; ?>
        </button>
    </li>
</ul>

<!-- Conteúdo das Tabs -->
<div class="tab-content">
    <!-- Tab: Lista de Usuários -->
    <div class="tab-pane fade show active" id="usuarios" role="tabpanel">

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                <tr>
                    <th>Nome</th>
                    <th>Email</th>
                    <th>Perfil Principal</th>
                    <th>Vínculo</th>
                    <th>Ação</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($usuarios as $user): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($user['nome']); ?></td>
                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                        <td><?php echo ucfirst($user['role']); ?></td>
                        <td>
                            <?php echo htmlspecialchars($user['tipo_usuario_detalhado'] ?? 'N/A'); ?>
                            <?php if ($user['is_coordenador']): ?>
                                <span class='badge bg-primary'>Coordenador</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="/superadmin/usuario/editar?id=<?php echo $user['id']; ?>" class="btn btn-sm btn-primary">Editar</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

    </div><!-- Fim Tab: Lista de Usuários -->

    <!-- Tab: Solicitações de Troca de Curso -->
    <div class="tab-pane fade" id="solicitacoes" role="tabpanel">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="bi bi-arrow-left-right"></i> Solicitações Pendentes de Troca de Curso</h5>
            </div>
            <div class="card-body">
                <?php if (empty($solicitacoes_pendentes)): ?>
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i> Não há solicitações de troca de curso pendentes no momento.
                    </div>
                <?php else: ?>
                    <p class="text-muted mb-4">
                        <i class="bi bi-exclamation-circle"></i> Analise as solicitações de troca de curso dos alunos. 
                        Lembre-se: <strong>ao aprovar, alunos que são membros de atléticas voltarão a ser alunos padrão</strong> e poderão solicitar entrada na nova atlética.
                    </p>
                    
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Aluno</th>
                                    <th>RA / Email / Telefone</th>
                                    <th>Curso Atual</th>
                                    <th>Curso Desejado</th>
                                    <th>Justificativa</th>
                                    <th>Data</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($solicitacoes_pendentes as $solicitacao): ?>
                                    <tr>
                                        <td>
                                            <strong><?php echo htmlspecialchars($solicitacao['usuario_nome']); ?></strong>
                                        </td>
                                        <td>
                                            <small>
                                                <strong>RA:</strong> <?php echo htmlspecialchars($solicitacao['usuario_ra'] ?? 'N/A'); ?><br>
                                                <strong>Email:</strong> <?php echo htmlspecialchars($solicitacao['usuario_email']); ?><br>
                                                <strong>Tel:</strong> <?php echo htmlspecialchars(formatarTelefone($solicitacao['usuario_telefone']) ?: 'N/A'); ?>
                                            </small>
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary">
                                                <?php echo htmlspecialchars($solicitacao['curso_atual_nome'] ?? 'Sem curso'); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-success">
                                                <?php echo htmlspecialchars($solicitacao['curso_novo_nome']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-info" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#modalJustificativa<?php echo $solicitacao['id']; ?>">
                                                <i class="bi bi-file-text"></i> Ver Justificativa
                                            </button>
                                            
                                            <!-- Modal de Justificativa -->
                                            <div class="modal fade" id="modalJustificativa<?php echo $solicitacao['id']; ?>" tabindex="-1">
                                                <div class="modal-dialog modal-dialog-centered">
                                                    <div class="modal-content">
                                                        <div class="modal-header bg-info text-white">
                                                            <h5 class="modal-title">Justificativa</h5>
                                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <p><strong>Aluno:</strong> <?php echo htmlspecialchars($solicitacao['usuario_nome']); ?></p>
                                                            <hr>
                                                            <p style="white-space: pre-wrap; text-align: justify;">
                                                                <?php echo htmlspecialchars($solicitacao['justificativa']); ?>
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <small><?php echo date('d/m/Y H:i', strtotime($solicitacao['data_solicitacao'])); ?></small>
                                        </td>
                                        <td>
                                            <!-- Botão Aprovar -->
                                            <form action="/superadmin/solicitacao-troca-curso/aprovar" method="post" 
                                                  style="display: inline;" 
                                                  onsubmit="return confirm('Confirma a APROVAÇÃO desta solicitação?\n\nO curso do aluno será alterado. Se ele for membro de uma atlética, voltará a ser aluno padrão.')">
                                                <input type="hidden" name="solicitacao_id" value="<?php echo $solicitacao['id']; ?>">
                                                <button type="submit" class="btn btn-sm btn-success">
                                                    <i class="bi bi-check-circle"></i> Aprovar
                                                </button>
                                            </form>
                                            
                                            <!-- Botão Recusar -->
                                            <button type="button" class="btn btn-sm btn-danger" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#modalRecusar<?php echo $solicitacao['id']; ?>">
                                                <i class="bi bi-x-circle"></i> Recusar
                                            </button>
                                            
                                            <!-- Modal de Recusar -->
                                            <div class="modal fade" id="modalRecusar<?php echo $solicitacao['id']; ?>" tabindex="-1">
                                                <div class="modal-dialog modal-dialog-centered">
                                                    <div class="modal-content">
                                                        <div class="modal-header bg-danger text-white">
                                                            <h5 class="modal-title">Recusar Solicitação</h5>
                                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <p><strong>Aluno:</strong> <?php echo htmlspecialchars($solicitacao['usuario_nome']); ?></p>
                                                            <p><strong>Curso Desejado:</strong> <?php echo htmlspecialchars($solicitacao['curso_novo_nome']); ?></p>
                                                            <hr>
                                                            <form action="/superadmin/solicitacao-troca-curso/recusar" method="post">
                                                                <input type="hidden" name="solicitacao_id" value="<?php echo $solicitacao['id']; ?>">
                                                                <div class="mb-3">
                                                                    <label for="motivo_recusa<?php echo $solicitacao['id']; ?>" class="form-label">
                                                                        Motivo da Recusa (opcional)
                                                                    </label>
                                                                    <textarea name="motivo_recusa" 
                                                                              id="motivo_recusa<?php echo $solicitacao['id']; ?>" 
                                                                              class="form-control" 
                                                                              rows="3" 
                                                                              placeholder="Explique o motivo da recusa (será enviado ao aluno)"></textarea>
                                                                </div>
                                                                <button type="submit" class="btn btn-danger w-100">
                                                                    <i class="bi bi-x-circle"></i> Confirmar Recusa
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Histórico de Solicitações Antigas (Gaveta) -->
        <?php if (!empty($solicitacoes_processadas)): ?>
        <div class="card mt-4">
            <div class="card-header bg-secondary text-white" style="cursor: pointer;" data-bs-toggle="collapse" data-bs-target="#historicoSolicitacoes" aria-expanded="false">
                <h5 class="mb-0 d-flex justify-content-between align-items-center">
                    <span>
                        <i class="bi bi-clock-history"></i> Histórico de Solicitações Processadas 
                        <span class="badge bg-light text-dark ms-2"><?php echo count($solicitacoes_processadas); ?></span>
                    </span>
                    <i class="bi bi-chevron-down"></i>
                </h5>
            </div>
            <div id="historicoSolicitacoes" class="collapse">
                <div class="card-body">
                    <p class="text-muted mb-3">
                        <i class="bi bi-info-circle"></i> Todas as solicitações já respondidas (aprovadas ou recusadas).
                    </p>
                    
                    <div class="solicitacoes-historico">
                    <?php foreach ($solicitacoes_processadas as $sol): ?>
                        <div class="historico-card <?php echo $sol['status'] === 'aprovada' ? 'historico-aprovada' : 'historico-recusada'; ?>">
                            <div class="historico-header">
                                <div class="historico-status-badge">
                                    <?php if ($sol['status'] === 'aprovada'): ?>
                                        <span class="badge bg-success">
                                            <i class="bi bi-check-circle-fill"></i> APROVADA
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-danger">
                                            <i class="bi bi-x-circle-fill"></i> RECUSADA
                                        </span>
                                    <?php endif; ?>
                                </div>
                                <div class="historico-dates">
                                    <small class="text-muted">
                                        <strong>Solicitado:</strong> <?php echo date('d/m/Y H:i', strtotime($sol['data_solicitacao'])); ?>
                                        <br>
                                        <strong>Respondido:</strong> <?php echo date('d/m/Y H:i', strtotime($sol['data_resposta'])); ?>
                                    </small>
                                </div>
                            </div>
                            
                            <div class="historico-body">
                                <div class="historico-row">
                                    <div class="historico-col">
                                        <strong><i class="bi bi-person"></i> Aluno:</strong>
                                        <p><?php echo htmlspecialchars($sol['usuario_nome']); ?></p>
                                    </div>
                                    <div class="historico-col">
                                        <strong><i class="bi bi-card-text"></i> RA:</strong>
                                        <p><?php echo htmlspecialchars($sol['usuario_ra'] ?? 'N/A'); ?></p>
                                    </div>
                                    <div class="historico-col">
                                        <strong><i class="bi bi-envelope"></i> Email:</strong>
                                        <p><?php echo htmlspecialchars($sol['usuario_email']); ?></p>
                                    </div>
                                    <div class="historico-col">
                                        <strong><i class="bi bi-telephone"></i> Telefone:</strong>
                                        <p><?php echo htmlspecialchars(formatarTelefone($sol['usuario_telefone']) ?: 'N/A'); ?></p>
                                    </div>
                                </div>
                                
                                <div class="historico-row mt-3">
                                    <div class="historico-col">
                                        <strong><i class="bi bi-arrow-right"></i> Troca:</strong>
                                        <p>
                                            <span class="badge bg-secondary"><?php echo htmlspecialchars($sol['curso_atual_nome'] ?? 'Sem curso'); ?></span>
                                            <i class="bi bi-arrow-right mx-2"></i>
                                            <span class="badge bg-primary"><?php echo htmlspecialchars($sol['curso_novo_nome']); ?></span>
                                        </p>
                                    </div>
                                    <div class="historico-col">
                                        <strong><i class="bi bi-person-check"></i> Respondido por:</strong>
                                        <p><?php echo htmlspecialchars($sol['respondido_por_nome'] ?? 'Sistema'); ?></p>
                                    </div>
                                </div>
                                
                                <div class="historico-justificativa mt-3">
                                    <strong><i class="bi bi-file-text"></i> Justificativa do aluno:</strong>
                                    <p class="justificativa-texto"><?php echo htmlspecialchars($sol['justificativa']); ?></p>
                                </div>
                                
                                <?php if ($sol['status'] === 'recusada'): ?>
                                <div class="historico-justificativa-resposta mt-3">
                                    <strong><i class="bi bi-exclamation-triangle text-danger"></i> Justificativa da recusa:</strong>
                                    <?php if (!empty($sol['justificativa_resposta'])): ?>
                                        <p class="justificativa-resposta-texto text-danger"><?php echo htmlspecialchars($sol['justificativa_resposta']); ?></p>
                                    <?php else: ?>
                                        <p class="justificativa-resposta-texto text-muted"><em>Nenhuma justificativa fornecida pelo super admin</em></p>
                                    <?php endif; ?>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>

    </div><!-- Fim Tab: Solicitações -->
</div><!-- Fim Tab Content -->