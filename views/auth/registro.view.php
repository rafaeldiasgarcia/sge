<?php
#
# View com o formulário de registro de novos usuários.
# Inclui lógica JavaScript para mostrar/ocultar campos dinamicamente.
#
?>
<div class="auth-card">
    <h1 class="auth-title">Cadastre-se</h1>
    <p class="auth-subtitle">Cadastro para os jogos universitários da UNIFIO</p>

    <?php if (isset($_SESSION['error_message'])): ?>
        <div class="alert alert-danger alert-auth">
            <?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?>
        </div>
    <?php endif; ?>

    <form action="/registro" method="post" class="auth-form">
        <div class="mb-3">
            <label for="nome" class="form-label">Nome Completo</label>
            <input type="text" name="nome" id="nome" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="tipo_usuario_detalhado" class="form-label">Vínculo com a Instituição</label>
            <select name="tipo_usuario_detalhado" id="tipo_usuario_detalhado" class="form-select" required>
                <option value="" disabled selected>-- Selecione uma opção --</option>
                <option value="Aluno">Aluno (em geral)</option>
                <option value="Membro das Atléticas">Membro das Atléticas</option>
                <option value="Professor">Professor</option>
                <option value="Comunidade Externa">Comunidade Externa</option>
            </select>
        </div>

        <div id="campo_ra" class="mb-3" style="display:none;">
            <label for="ra" class="form-label">Matrícula (RA)</label>
            <input type="text" name="ra" id="ra" class="form-control" placeholder="00000" inputmode="numeric" maxlength="6" pattern="[0-9]{6}" title="O RA deve conter exatamente 6 números.">
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">E-mail institucional</label>
            <input type="email" name="email" id="email" class="form-control" placeholder="*******@unifio.edu.br" required>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="telefone" class="form-label">Telefone</label>
                <input type="tel" name="telefone" id="telefone" class="form-control" placeholder="(**) ****-****">
            </div>
            <div class="col-md-6 mb-3">
                <label for="cargo" class="form-label">Cargo</label>
                <select name="cargo" id="cargo" class="form-select">
                    <option value="Aluno" selected>Aluno</option>
                    <option value="Professor">Professor</option>
                    <option value="Funcionário">Funcionário</option>
                </select>
            </div>
        </div>

        <div id="campo_curso" class="mb-3" style="display:none;">
            <label for="curso_id" class="form-label">Qual seu curso?</label>
            <select name="curso_id" id="curso_id" class="form-select">
                <option value="">-- Selecione seu curso --</option>
                <?php if (isset($cursos)): foreach ($cursos as $curso): ?>
                    <option value="<?php echo $curso['id']; ?>"><?php echo htmlspecialchars($curso['nome']); ?></option>
                <?php endforeach; endif; ?>
            </select>
        </div>

        <div class="mb-3">
            <label for="data_nascimento" class="form-label">Data de Nascimento</label>
            <input type="date" name="data_nascimento" id="data_nascimento" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="senha" class="form-label">Senha</label>
            <input type="password" name="senha" id="senha" class="form-control" placeholder="••••••••" required>
        </div>

        <div class="mb-3">
            <label for="confirmar_senha" class="form-label">Confirmar Senha</label>
            <input type="password" name="confirmar_senha" id="confirmar_senha" class="form-control" placeholder="••••••••" required>
        </div>

        <button type="submit" class="btn btn-auth-primary">Cadastrar</button>

        <div class="auth-help-text">
            Precisa de ajuda?<br>
            Entre em contato com a universidade
        </div>

        <div class="unifio-logo">UniFio</div>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const tipoUsuarioSelect = document.getElementById('tipo_usuario_detalhado');
        const campoRa = document.getElementById('campo_ra');
        const inputRa = document.getElementById('ra');
        const campoCurso = document.getElementById('campo_curso');
        const emailInput = document.getElementById('email');

        function toggleFields() {
            const tipo = tipoUsuarioSelect.value;

            if (tipo === 'Aluno' || tipo === 'Membro das Atléticas') {
                campoRa.style.display = 'block';
                inputRa.required = true;
                campoCurso.style.display = 'block';
            } else {
                campoRa.style.display = 'none';
                inputRa.required = false;
                campoCurso.style.display = 'none';
            }

            if (tipo !== 'Comunidade Externa' && tipo !== '') {
                emailInput.placeholder = 'Use seu e-mail @unifio.edu.br';
            } else {
                emailInput.placeholder = 'Seu e-mail';
            }
        }

        tipoUsuarioSelect.addEventListener('change', toggleFields);
        toggleFields();
    });
</script>