<?php
#
# View para o Admin de Atlética gerenciar todos os membros da sua atlética.
# Permite promover a admin, rebaixar admins e remover membros da atlética.
#
?>
<h1>Gerenciar Membros da Atlética</h1>
<p>Visualize e gerencie todos os membros da sua atlética, incluindo suas permissões e status.</p>

<?php if (isset($_SESSION['success_message'])): ?>
    <div class="alert alert-success">
        <i class="bi bi-check-circle"></i> <?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?>
    </div>
<?php endif; ?>

<?php if (isset($_SESSION['error_message'])): ?>
    <div class="alert alert-danger">
        <i class="bi bi-exclamation-triangle"></i> <?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?>
    </div>
<?php endif; ?>

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
                                    <?php if ($membro['id'] != $_SESSION['id']): // Não permitir que o admin gerencie a si mesmo ?>
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
    <div class="mt-4">
        <div class="row">
            <div class="col-md-6">
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
            <div class="col-md-6">
                <div class="card border-danger">
                    <div class="card-body">
                        <h6 class="card-title text-danger">
                            <i class="bi bi-exclamation-triangle"></i> Ação Irreversível
                        </h6>
                        <ul class="mb-0 small">
                            <li><strong>Remover:</strong> O membro será completamente removido da atlética.</li>
                            <li>Ele precisará solicitar entrada novamente para voltar a fazer parte da atlética.</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<div class="mt-3">
    <a href="/admin/atletica/dashboard" class="btn btn-secondary">
        <i class="bi bi-arrow-left"></i> Voltar ao Dashboard
    </a>
</div>
