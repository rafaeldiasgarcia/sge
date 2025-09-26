<?php
#
# View com o formulário de registro de novos usuários.
# Inclui lógica JavaScript para mostrar/ocultar campos dinamicamente.
#
?>
<div class="row justify-content-center">
    <div class="col-md-7">
        <div class="card shadow-sm">
            <div class="card-body p-4">
                <h2 class="text-center mb-4">Criar Conta</h2>

                <?php if (isset($_SESSION['error_message'])): ?>
                    <div class="alert alert-danger">
                        <?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?>
                    </div>
                <?php endif; ?>

                <form action="/registro" method="post">
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
                        <label for="ra" class="form-label">RA / Matrícula</label>
                        <input type="text" name="ra" id="ra" class="form-control" inputmode="numeric" maxlength="6" pattern="[0-9]{6}" title="O RA deve conter exatamente 6 números.">
                    </div>
                    <div class="mb-3">
                        <label for="data_nascimento" class="form-label">Data de Nascimento</label>
                        <input type="date" name="data_nascimento" id="data_nascimento" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" name="email" id="email" class="form-control" required>
                    </div>
                    <div id="campo_curso" class="mb-3" style="display:none;">
                        <label for="curso_id" class="form-label">Qual seu curso?</label>
                        <select name="curso_id" id="curso_id" class="form-select">
                            <option value="">-- Selecione seu curso --</option>
                            <?php foreach ($cursos as $curso): ?>
                                <option value="<?php echo $curso['id']; ?>"><?php echo htmlspecialchars($curso['nome']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="senha" class="form-label">Senha</label>
                            <input type="password" name="senha" id="senha" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="confirmar_senha" class="form-label">Confirmar Senha</label>
                            <input type="password" name="confirmar_senha" id="confirmar_senha" class="form-control" required>
                        </div>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">Registrar</button>
                    </div>
                    <p class="mt-3 text-center">Já possui uma conta? <a href="/login">Faça login</a></p>
                </form>
            </div>
        </div>
    </div>
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
            } else {
                campoRa.style.display = 'none';
                inputRa.required = false;
            }

            if (tipo === 'Aluno' || tipo === 'Membro das Atléticas') {
                campoCurso.style.display = 'block';
            } else {
                campoCurso.style.display = 'none';
            }

            if (tipo !== 'Comunidade Externa' && tipo !== '') {
                emailInput.placeholder = 'Use seu e-mail @unifio.edu.br';
            } else {
                emailInput.placeholder = '';
            }
        }

        tipoUsuarioSelect.addEventListener('change', toggleFields);
        toggleFields();
    });
</script>