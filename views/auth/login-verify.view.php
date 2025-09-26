<?php
#
# View para a verificação de login em duas etapas (simulada).
# O usuário insere o código recebido para finalizar o login.
#
?>
<div class="row justify-content-center">
    <div class="col-md-5">
        <div class="card shadow-sm">
            <div class="card-body p-4">
                <h2 class="text-center mb-3">Verificação de Acesso</h2>
                <p class="text-center text-muted">Para sua segurança, um código foi "enviado" para <strong><?php echo htmlspecialchars($_SESSION['login_email'] ?? ''); ?></strong>.</p>

                <div class="alert alert-info">
                    <strong>[AMBIENTE DE TESTE]</strong><br>
                    Seu código de acesso é: <strong><?php echo $_SESSION['login_code_simulado'] ?? 'Erro: código não encontrado.'; ?></strong>
                </div>

                <?php if (isset($_SESSION['error_message'])): ?>
                    <div class="alert alert-danger">
                        <?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?>
                    </div>
                <?php endif; ?>

                <form action="/login/verify" method="post">
                    <div class="mb-3">
                        <label for="code" class="form-label">Código de 6 dígitos</label>
                        <input type="text" name="code" id="code" class="form-control" required maxlength="6" pattern="[0-9]{6}" inputmode="numeric" autofocus>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">Verificar e Entrar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>