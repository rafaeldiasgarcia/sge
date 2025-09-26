<?php
#
# View para o Super Admin listar todos os usuários do sistema.
# Apresenta uma tabela com as informações básicas de cada usuário e um link para edição.
#
?>
<h2>Gerenciar Todos os Usuários</h2>
<p>Visualize e edite as informações de qualquer usuário cadastrado no sistema.</p>

<?php if (isset($_SESSION['success_message'])): ?>
    <div class="alert alert-success"><?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?></div>
<?php endif; ?>
<?php if (isset($_SESSION['error_message'])): ?>
    <div class="alert alert-danger"><?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?></div>
<?php endif; ?>

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