<?php
#
# View com o formulário para o Super Admin editar os dados de qualquer usuário.
# Permite alterar nome, email, perfil, curso e outras informações.
#
$podeSerAdmin = !empty($user['atletica_id']) || !empty($user['atletica_nome']);
?>
<h2>Editando Usuário: <?php echo htmlspecialchars($user['nome']); ?></h2>
<a href="/superadmin/usuarios" class="btn btn-secondary mb-3">Voltar para a lista</a>

<?php if (isset($_SESSION['success_message'])): ?>
    <div class="alert alert-success"><?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?></div>
<?php endif; ?>
<?php if (isset($_SESSION['error_message'])): ?>
    <div class="alert alert-danger"><?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?></div>
<?php endif; ?>

<div class="card">
    <div class="card-body">
        <form action="/superadmin/usuario/editar" method="post">
            <input type="hidden" name="id" value="<?php echo $user['id']; ?>">
            <div class="row">
                <div class="col-md-6 mb-3"><label class="form-label">Nome</label><input type="text" name="nome" class="form-control" value="<?php echo htmlspecialchars($user['nome']); ?>"></div>
                <div class="col-md-6 mb-3"><label class="form-label">Email</label><input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($user['email']); ?>"></div>
                <div class="col-md-6 mb-3"><label class="form-label">RA/Matrícula</label><input type="text" name="ra" class="form-control" value="<?php echo htmlspecialchars($user['ra']); ?>"></div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Perfil Principal (Role)</label>
                    <select name="role" class="form-select">
                        <option value="usuario" <?php if($user['role'] == 'usuario') echo 'selected'; ?>>Usuário</option>
                        <option value="admin" <?php if($user['role'] == 'admin') echo 'selected'; ?> <?php if (!$podeSerAdmin) echo 'disabled'; ?>>
                            Admin da Atlética <?php if (!$podeSerAdmin) echo '(Requer atlética)'; ?>
                        </option>
                        <option value="superadmin" <?php if($user['role'] == 'superadmin') echo 'selected'; ?>>Super Admin</option>
                    </select>
                    <?php if (!$podeSerAdmin): ?>
                        <div class="form-text text-warning">Para promover a Admin, associe este usuário a um curso que pertença a uma atlética.</div>
                    <?php endif; ?>
                </div>
                <div class="col-md-6 mb-3"><label class="form-label">Vínculo Detalhado</label>
                    <select name="tipo_usuario_detalhado" class="form-select">
                        <option value="Aluno" <?php if($user['tipo_usuario_detalhado'] == 'Aluno') echo 'selected'; ?>>Aluno</option>
                        <option value="Membro das Atléticas" <?php if($user['tipo_usuario_detalhado'] == 'Membro das Atléticas') echo 'selected'; ?>>Membro das Atléticas</option>
                        <option value="Professor" <?php if($user['tipo_usuario_detalhado'] == 'Professor') echo 'selected'; ?>>Professor</option>
                        <option value="Comunidade Externa" <?php if($user['tipo_usuario_detalhado'] == 'Comunidade Externa') echo 'selected'; ?>>Comunidade Externa</option>
                    </select>
                </div>
                <div class="col-md-6 mb-3"><label class="form-label">Curso do Aluno</label>
                    <select name="curso_id" class="form-select">
                        <option value="">Nenhum</option>
                        <?php foreach ($cursos as $c): ?>
                            <option value="<?php echo $c['id']; ?>" <?php if($user['curso_id'] == $c['id']) echo 'selected'; ?>><?php echo htmlspecialchars($c['nome']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Atlética Associada</label>
                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($user['atletica_nome'] ?? 'Nenhuma (via curso)'); ?>" disabled>
                    <div class="form-text">A atlética é definida pelo curso do usuário.</div>
                </div>
            </div>
            <div class="form-check mb-3">
                <input class="form-check-input" type="checkbox" name="is_coordenador" value="1" id="is_coordenador" <?php if($user['is_coordenador']) echo 'checked'; ?>>
                <label class="form-check-label" for="is_coordenador">Marcar como Professor Coordenador</label>
            </div>
            <button type="submit" class="btn btn-success">Salvar Alterações</button>
            <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteUserModal">Excluir Usuário</button>
        </form>
    </div>
</div>

<div class="modal fade" id="deleteUserModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title">Confirmar Exclusão</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <form action="/superadmin/usuario/excluir" method="post">
                <div class="modal-body">
                    <p><strong>Atenção!</strong> Esta ação é irreversível e irá apagar permanentemente o usuário <strong><?php echo htmlspecialchars($user['nome']); ?></strong>.</p>
                    <p>Para confirmar, por favor, digite a sua senha de Super Administrador.</p>
                    <input type="hidden" name="id" value="<?php echo $user['id']; ?>">
                    <div class="mb-3">
                        <label for="confirmation_password" class="form-label">Sua Senha</label>
                        <input type="password" name="confirmation_password" id="confirmation_password" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger">Confirmar e Excluir</button>
                </div>
            </form>
        </div>
    </div>
</div>