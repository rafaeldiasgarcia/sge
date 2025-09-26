<?php
#
# View da página de Perfil do Usuário.
# Contém dois formulários: um para atualizar dados pessoais e outro
# para alterar a senha.
#
?>
<div class="container">
    <h2 class="mb-3">Meu Perfil</h2>
    <p class="text-muted mb-4">Gerencie suas informações pessoais, de acesso e seu vínculo com a atlética.</p>

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

    <div class="row">
        <div class="col-lg-7 mb-4">
            <div class="card">
                <div class="card-header"><strong>Dados Pessoais</strong></div>
                <div class="card-body">
                    <form action="/perfil" method="post">
                        <input type="hidden" name="form_type" value="dados_pessoais">
                        <div class="mb-3">
                            <label for="ra" class="form-label">RA</label>
                            <input type="text" id="ra" class="form-control" value="<?php echo htmlspecialchars($user['ra'] ?? ''); ?>" disabled>
                        </div>
                        <div class="mb-3">
                            <label for="nome" class="form-label">Nome Completo</label>
                            <input type="text" name="nome" id="nome" class="form-control" value="<?php echo htmlspecialchars($user['nome'] ?? ''); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" name="email" id="email" class="form-control" value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="data_nascimento" class="form-label">Data de Nascimento</label>
                            <input type="date" name="data_nascimento" id="data_nascimento" class="form-control" value="<?php echo htmlspecialchars($user['data_nascimento'] ?? ''); ?>" required>
                        </div>
                        <?php if (!empty($user['ra'])): ?>
                            <div class="mb-3">
                                <label for="curso_id" class="form-label">Curso</label>
                                <select name="curso_id" id="curso_id" class="form-select">
                                    <option value="">-- Selecione seu curso --</option>
                                    <?php foreach ($cursos as $curso): ?>
                                        <option value="<?php echo $curso['id']; ?>" <?php echo ($user['curso_id'] == $curso['id']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($curso['nome']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        <?php endif; ?>
                        <button type="submit" class="btn btn-primary">Salvar Dados</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="card">
                <div class="card-header"><strong>Alterar Senha</strong></div>
                <div class="card-body">
                    <form action="/perfil" method="post">
                        <input type="hidden" name="form_type" value="alterar_senha">
                        <div class="mb-3">
                            <label for="senha_atual" class="form-label">Senha Atual</label>
                            <input type="password" name="senha_atual" id="senha_atual" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="nova_senha" class="form-label">Nova Senha</label>
                            <input type="password" name="nova_senha" id="nova_senha" class="form-control" required minlength="6">
                        </div>
                        <div class="mb-3">
                            <label for="confirmar_nova_senha" class="form-label">Confirmar Nova Senha</label>
                            <input type="password" name="confirmar_nova_senha" id="confirmar_nova_senha" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-warning">Alterar Senha</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>