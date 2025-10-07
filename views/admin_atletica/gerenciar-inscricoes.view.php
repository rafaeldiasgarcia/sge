<?php
#
# View para o Admin de Atlética gerenciar as solicitações de entrada na atlética
# e os membros já aprovados da atlética.
#
?>
<h1>Gerenciar Inscrições e Membros</h1>
<p>Aprove solicitações de entrada na atlética e gerencie os membros, suas permissões e status.</p>

<?php if (isset($_SESSION['success_message'])): ?>
    <div class="alert alert-success"><?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?></div>
<?php endif; ?>

<?php if (isset($_SESSION['error_message'])): ?>
    <div class="alert alert-danger"><?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?></div>
<?php endif; ?>

<!-- Tabs Navigation -->
<ul class="nav nav-tabs mb-4" id="inscricoesTab" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active" id="solicitacoes-tab" data-bs-toggle="tab" data-bs-target="#solicitacoes" type="button" role="tab">
            <i class="bi bi-person-plus"></i> Solicitações de Entrada
            <?php if (!empty($solicitacoes_pendentes)): ?>
                <span class="badge bg-warning text-dark ms-1"><?php echo count($solicitacoes_pendentes); ?></span>
            <?php endif; ?>
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="membros-tab" data-bs-toggle="tab" data-bs-target="#membros" type="button" role="tab">
            <i class="bi bi-people-fill"></i> Membros da Atlética
            <?php if (!empty($membros)): ?>
                <span class="badge bg-primary ms-1"><?php echo count($membros); ?></span>
            <?php endif; ?>
        </button>
    </li>
</ul>

<!-- Tabs Content -->
<div class="tab-content" id="inscricoesTabContent">

    <!-- Tab: Solicitações de Entrada na Atlética -->
    <div class="tab-pane fade show active" id="solicitacoes" role="tabpanel">
        <div class="card">
            <div class="card-header">
                <strong>Solicitações Pendentes de Entrada na Atlética</strong>
                <span class="badge bg-warning ms-2"><?php echo count($solicitacoes_pendentes ?? []); ?></span>
            </div>
            <div class="card-body">
                <?php if (empty($solicitacoes_pendentes)): ?>
                    <div class="text-center py-4">
                        <i class="bi bi-inbox fs-1 text-muted"></i>
                        <h5 class="text-muted mt-2">Nenhuma solicitação pendente</h5>
                        <p class="text-muted">Quando alunos solicitarem entrada na sua atlética, eles aparecerão aqui.</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th><i class="bi bi-person"></i> Aluno</th>
                                    <th><i class="bi bi-book"></i> Curso</th>
                                    <th><i class="bi bi-tools"></i> Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($solicitacoes_pendentes as $req): ?>
                                    <tr>
                                        <td>
                                            <strong><?php echo htmlspecialchars($req['nome']); ?></strong>
                                        </td>
                                        <td><?php echo htmlspecialchars($req['curso_nome']); ?></td>
                                        <td>
                                            <form method="post" action="/admin/atletica/membros/acao" class="d-inline"
                                                  onsubmit="return confirm('Tem certeza que deseja executar esta ação?')">
                                                <input type="hidden" name="aluno_id" value="<?php echo $req['id']; ?>">
                                                <div class="btn-group" role="group">
                                                    <button type="submit" name="acao" value="aprovar" class="btn btn-sm btn-success">
                                                        <i class="bi bi-check-circle"></i> Aprovar
                                                    </button>
                                                    <button type="submit" name="acao" value="recusar" class="btn btn-sm btn-danger">
                                                        <i class="bi bi-x-circle"></i> Recusar
                                                    </button>
                                                </div>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <?php if (!empty($solicitacoes_pendentes)): ?>
            <div class="mt-3">
                <div class="card border-info">
                    <div class="card-body">
                        <h6><i class="bi bi-info-circle text-info"></i> Informações Importantes</h6>
                        <ul class="mb-0">
                            <li><strong>Aprovar:</strong> O aluno se tornará membro oficial da atlética e poderá se inscrever em modalidades.</li>
                            <li><strong>Recusar:</strong> A solicitação será rejeitada e o aluno poderá fazer uma nova solicitação no futuro.</li>
                        </ul>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Tab: Membros da Atlética -->
    <div class="tab-pane fade" id="membros" role="tabpanel">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="bi bi-people-fill text-primary"></i> Todos os Membros da Atlética
                    <span class="badge bg-primary ms-2"><?php echo count($membros ?? []); ?></span>
                </h5>
            </div>
            <div class="card-body">
                <?php if (empty($membros)): ?>
                    <div class="text-center py-4">
                        <i class="bi bi-people fs-1 text-muted"></i>
                        <h5 class="text-muted mt-2">Nenhum membro encontrado</h5>
                        <p class="text-muted">Quando houver membros aprovados na atlética, eles aparecerão aqui.</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th><i class="bi bi-person"></i> Membro</th>
                                    <th><i class="bi bi-envelope"></i> Email</th>
                                    <th><i class="bi bi-book"></i> Curso</th>
                                    <th><i class="bi bi-shield"></i> Perfil</th>
                                    <th><i class="bi bi-tools"></i> Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($membros as $membro): ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <?php if ($membro['role'] === 'admin'): ?>
                                                    <i class="bi bi-shield-fill-check text-warning me-2" title="Administrador"></i>
                                                <?php else: ?>
                                                    <i class="bi bi-person-circle text-primary me-2" title="Membro"></i>
                                                <?php endif; ?>
                                                <div>
                                                    <strong><?php echo htmlspecialchars($membro['nome']); ?></strong>
                                                    <?php if ($membro['ra']): ?>
                                                        <br><small class="text-muted">RA: <?php echo htmlspecialchars($membro['ra']); ?></small>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <small><?php echo htmlspecialchars($membro['email']); ?></small>
                                        </td>
                                        <td>
                                            <small class="text-muted"><?php echo htmlspecialchars($membro['curso_nome'] ?? 'Não definido'); ?></small>
                                        </td>
                                        <td>
                                            <?php if ($membro['role'] === 'admin'): ?>
                                                <span class="badge bg-warning text-dark">
                                                    <i class="bi bi-shield-fill"></i> Administrador
                                                </span>
                                            <?php else: ?>
                                                <span class="badge bg-primary">
                                                    <i class="bi bi-person"></i> Membro
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($membro['id'] != $_SESSION['id']): ?>
                                                <div class="btn-group" role="group">
                                                    <?php if ($membro['role'] === 'usuario'): ?>
                                                        <!-- Promover a Admin -->
                                                        <form method="post" action="/admin/atletica/gerenciar-membros/acao" class="d-inline">
                                                            <input type="hidden" name="membro_id" value="<?php echo $membro['id']; ?>">
                                                            <button type="submit" name="acao" value="promover_admin"
                                                                    class="btn btn-sm btn-outline-warning"
                                                                    onclick="return confirm('Tem certeza que deseja promover este membro a Administrador da Atlética?')"
                                                                    title="Promover a Administrador">
                                                                <i class="bi bi-arrow-up-circle"></i> Promover
                                                            </button>
                                                        </form>
                                                    <?php elseif ($membro['role'] === 'admin'): ?>
                                                        <!-- Rebaixar Admin -->
                                                        <form method="post" action="/admin/atletica/gerenciar-membros/acao" class="d-inline">
                                                            <input type="hidden" name="membro_id" value="<?php echo $membro['id']; ?>">
                                                            <button type="submit" name="acao" value="rebaixar_admin"
                                                                    class="btn btn-sm btn-outline-secondary"
                                                                    onclick="return confirm('Tem certeza que deseja rebaixar este administrador a membro comum?')"
                                                                    title="Rebaixar a Membro">
                                                                <i class="bi bi-arrow-down-circle"></i> Rebaixar
                                                            </button>
                                                        </form>
                                                    <?php endif; ?>

                                                    <!-- Remover da Atlética -->
                                                    <form method="post" action="/admin/atletica/gerenciar-membros/acao" class="d-inline">
                                                        <input type="hidden" name="membro_id" value="<?php echo $membro['id']; ?>">
                                                        <button type="submit" name="acao" value="remover_atletica"
                                                                class="btn btn-sm btn-outline-danger"
                                                                onclick="return confirm('ATENÇÃO: Esta ação removerá o membro da atlética permanentemente. Tem certeza?')"
                                                                title="Remover da Atlética">
                                                            <i class="bi bi-person-x"></i> Remover
                                                        </button>
                                                    </form>
                                                </div>
                                            <?php else: ?>
                                                <span class="badge bg-info">
                                                    <i class="bi bi-person-badge"></i> Você
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <?php if (!empty($membros)): ?>
            <div class="mt-3">
                <div class="card border-warning">
                    <div class="card-body">
                        <h6 class="card-title text-warning">
                            <i class="bi bi-info-circle"></i> Informações sobre Administradores
                        </h6>
                        <ul class="mb-0 small">
                            <li><strong>Promover:</strong> O membro se tornará administrador da atlética com as mesmas permissões que você.</li>
                            <li><strong>Rebaixar:</strong> O administrador se tornará um membro comum, perdendo as permissões administrativas.</li>
                        </ul>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

</div>