<?php
/**
 * VIEW: EDITAR USUÁRIO (SUPER ADMIN)
 * Formulário completo para editar dados de um usuário (nome, email, perfil, curso, etc).
 * CONTROLLER: SuperAdminController::editarUsuario()
 */

$podeSerAdmin = !empty($usuario_editado['atletica_id']) || !empty($usuario_editado['atletica_nome']);
$isProfessor = ($usuario_editado['tipo_usuario_detalhado'] ?? '') === 'Professor';
$isExterno = ($usuario_editado['tipo_usuario_detalhado'] ?? '') === 'Comunidade Externa';

?>
<h2>Editando Usuário: <?php echo htmlspecialchars($usuario_editado['nome']); ?></h2>
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
            <input type="hidden" name="id" value="<?php echo $usuario_editado['id']; ?>">
            <div class="row">
                <div class="col-md-6 mb-3"><label class="form-label">Nome</label><input type="text" name="nome" class="form-control" value="<?php echo htmlspecialchars($usuario_editado['nome'] ?? ''); ?>"></div>
                <div class="col-md-6 mb-3"><label class="form-label">Email</label><input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($usuario_editado['email'] ?? ''); ?>"></div>
                <?php if (!$isExterno): ?>
                <div class="col-md-6 mb-3"><label class="form-label">RA/Matrícula</label><input type="text" name="ra" class="form-control" value="<?php echo htmlspecialchars($usuario_editado['ra'] ?? ''); ?>"></div>
                <?php endif; ?>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Perfil Principal (Role)</label>
                    <select name="role" class="form-select">
                        <option value="usuario" <?php if($usuario_editado['role'] == 'usuario') echo 'selected'; ?>>Usuário</option>
                        <option value="admin" <?php if($usuario_editado['role'] == 'admin') echo 'selected'; ?> <?php if (!$podeSerAdmin) echo 'disabled'; ?>>
                            Admin da Atlética <?php if (!$podeSerAdmin) echo '(Requer atlética)'; ?>
                        </option>
                        <option value="superadmin" <?php if($usuario_editado['role'] == 'superadmin') echo 'selected'; ?>>Super Admin</option>
                    </select>
                    <?php if (!$podeSerAdmin): ?>
                        <div class="form-text text-warning">Para promover a Admin, associe este usuário a um curso que pertença a uma atlética.</div>
                    <?php endif; ?>
                </div>
                <div class="col-md-6 mb-3"><label class="form-label">Vínculo Detalhado</label>
                    <select name="tipo_usuario_detalhado" class="form-select">
                        <option value="Aluno" <?php if($usuario_editado['tipo_usuario_detalhado'] == 'Aluno') echo 'selected'; ?>>Aluno</option>
                        <option value="Membro das Atléticas" <?php if($usuario_editado['tipo_usuario_detalhado'] == 'Membro das Atléticas') echo 'selected'; ?>>Membro das Atléticas</option>
                        <option value="Professor" <?php if($usuario_editado['tipo_usuario_detalhado'] == 'Professor') echo 'selected'; ?>>Professor</option>
                        <option value="Comunidade Externa" <?php if($usuario_editado['tipo_usuario_detalhado'] == 'Comunidade Externa') echo 'selected'; ?>>Comunidade Externa</option>
                    </select>
                </div>
                <?php if (!$isExterno): ?>
                <div class="col-md-6 mb-3"><label class="form-label">Curso do Aluno</label>
                    <select name="curso_id" class="form-select">
                        <option value="">Nenhum</option>
                        <?php foreach ($cursos as $c): ?>
                            <option value="<?php echo $c['id']; ?>" <?php if($usuario_editado['curso_id'] == $c['id']) echo 'selected'; ?>><?php echo htmlspecialchars($c['nome']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <?php endif; ?>
                <?php if (!$isProfessor && !$isExterno): ?>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Atlética Associada</label>
                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($usuario_editado['atletica_nome'] ?? 'Nenhuma (via curso)'); ?>" disabled>
                    <div class="form-text">A atlética é definida pelo curso do usuário.</div>
                </div>
                <?php endif; ?>
            </div>

            <!-- Nova seção: Status na Atlética -->
            <?php if (!$isProfessor && !$isExterno): ?>
            <?php if (!empty($usuario_editado['curso_id']) && !empty($usuario_editado['atletica_nome'])): ?>
                <div class="card mb-3">
                    <div class="card-header">
                        <h6 class="mb-0"><i class="bi bi-people-fill text-primary"></i> Status na Atlética</h6>
                    </div>
                    <div class="card-body">
                        <p class="mb-3">
                            <strong>Atlética do curso:</strong> <?php echo htmlspecialchars($usuario_editado['atletica_nome']); ?><br>
                            <strong>Status atual:</strong>
                            <span class="badge <?php
                                echo match($usuario_editado['atletica_join_status'] ?? 'none') {
                                    'aprovado' => 'bg-success',
                                    'pendente' => 'bg-warning text-dark',
                                    default => 'bg-secondary'
                                };
                            ?>">
                                <?php
                                echo match($usuario_editado['atletica_join_status'] ?? 'none') {
                                    'aprovado' => 'Membro Ativo',
                                    'pendente' => 'Aguardando Aprovação',
                                    default => 'Não é Membro'
                                };
                                ?>
                            </span>
                        </p>

                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="atletica_join_status" value="aprovado" id="atletica_aprovado"
                                   <?php if(($usuario_editado['atletica_join_status'] ?? 'none') === 'aprovado') echo 'checked'; ?>>
                            <label class="form-check-label" for="atletica_aprovado">
                                <strong class="text-success">Membro da Atlética</strong> - Usuário faz parte da atlética
                            </label>
                        </div>

                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="atletica_join_status" value="pendente" id="atletica_pendente"
                                   <?php if(($usuario_editado['atletica_join_status'] ?? 'none') === 'pendente') echo 'checked'; ?>>
                            <label class="form-check-label" for="atletica_pendente">
                                <strong class="text-warning">Solicitação Pendente</strong> - Aguardando aprovação do admin da atlética
                            </label>
                        </div>

                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="atletica_join_status" value="none" id="atletica_none"
                                   <?php if(($usuario_editado['atletica_join_status'] ?? 'none') === 'none') echo 'checked'; ?>>
                            <label class="form-check-label" for="atletica_none">
                                <strong class="text-secondary">Não é Membro</strong> - Usuário não faz parte da atlética
                            </label>
                        </div>

                        <div class="mt-3 p-2 bg-light rounded">
                            <small class="text-muted">
                                <i class="bi bi-info-circle"></i>
                                <strong>Importante:</strong> Alterar para "Membro da Atlética" automaticamente mudará o tipo de usuário para "Membro das Atléticas" se necessário.
                            </small>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="alert alert-info">
                    <i class="bi bi-info-circle"></i>
                    <strong>Atlética não disponível</strong><br>
                    Este usuário não pode fazer parte de uma atlética porque:
                    <?php if (empty($usuario_editado['curso_id'])): ?>
                        não possui curso definido.
                    <?php else: ?>
                        o curso não possui atlética associada.
                    <?php endif; ?>
                    <input type="hidden" name="atletica_join_status" value="none">
                </div>
            <?php endif; ?>
            <?php endif; ?>

            <?php if ($isProfessor && !$isExterno): ?>
            <div class="form-check mb-3">
                <input class="form-check-input" type="checkbox" name="is_coordenador" value="1" id="is_coordenador" <?php if($usuario_editado['is_coordenador']) echo 'checked'; ?>>
                <label class="form-check-label" for="is_coordenador">Marcar como Professor Coordenador</label>
            </div>
            <?php endif; ?>
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
                    <p><strong>Atenção!</strong> Esta ação é irreversível e irá apagar permanentemente o usuário <strong><?php echo htmlspecialchars($usuario_editado['nome']); ?></strong>.</p>
                    <p>Para confirmar, por favor, digite a sua senha de Super Administrador.</p>
                    <input type="hidden" name="id" value="<?php echo $usuario_editado['id']; ?>">
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Selecionar os elementos
    const statusRadios = document.querySelectorAll('input[name="atletica_join_status"]');
    const vinculoDetalhadoSelect = document.querySelector('select[name="tipo_usuario_detalhado"]');

    // Função para atualizar o vínculo detalhado baseado no status
    function atualizarVinculoDetalhado(statusValue) {
        if (statusValue === 'aprovado') {
            vinculoDetalhadoSelect.value = 'Membro das Atléticas';
        } else if (statusValue === 'none' || statusValue === 'pendente') {
            vinculoDetalhadoSelect.value = 'Aluno';
        }
    }

    // Sincronizar o campo ao carregar a página com o status atual
    statusRadios.forEach(radio => {
        if (radio.checked) {
            atualizarVinculoDetalhado(radio.value);
        }
    });

    // Adicionar listener para cada radio button de status
    statusRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            atualizarVinculoDetalhado(this.value);
        });
    });
});
</script>
