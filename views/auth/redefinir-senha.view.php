<?php
#
# View com o formulário para o usuário definir uma nova senha.
# Esta página só é acessível através de um link com um token válido.
#
?>
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-body p-4">
                <h2 class="text-center mb-3">Redefinir Senha</h2>
                <p class="text-center text-muted">Crie uma nova senha para sua conta.</p>

                <?php if (isset($_SESSION['error_message'])): ?>
                    <div class="alert alert-danger">
                        <?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?>
                    </div>
                <?php endif; ?>

                <form action="/redefinir-senha" method="post">
                    <!-- O token é enviado de forma oculta para ser validado no backend -->
                    <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">

                    <div class="mb-3">
                        <label for="nova_senha" class="form-label">Nova Senha</label>
                        <input type="password" name="nova_senha" id="nova_senha" class="form-control" required minlength="6">
                    </div>
                    <div class="mb-3">
                        <label for="confirmar_nova_senha" class="form-label">Confirmar Nova Senha</label>
                        <input type="password" name="confirmar_nova_senha" id="confirmar_nova_senha" class="form-control" required>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">Salvar Nova Senha</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>