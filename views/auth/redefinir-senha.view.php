<?php
#
# View com o formulário de redefinição de senha.
#
?>
<div class="auth-card">
    <h1 class="auth-title">Redefinir Senha</h1>
    <p class="auth-subtitle">Insira sua nova senha</p>

    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="alert alert-success alert-auth">
            <?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['error_message'])): ?>
        <div class="alert alert-danger alert-auth">
            <?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?>
        </div>
    <?php endif; ?>

    <form action="/redefinir-senha" method="post" class="auth-form">
        <?php if (isset($_GET['token'])): ?>
            <input type="hidden" name="token" value="<?php echo htmlspecialchars($_GET['token']); ?>">
        <?php endif; ?>

        <div class="mb-3">
            <label for="nova_senha" class="form-label">Nova Senha</label>
            <input type="password" name="nova_senha" id="nova_senha" class="form-control" placeholder="••••••••" required>
        </div>

        <div class="mb-3">
            <label for="confirmar_nova_senha" class="form-label">Confirmar Nova Senha</label>
            <input type="password" name="confirmar_nova_senha" id="confirmar_nova_senha" class="form-control" placeholder="••••••••" required>
        </div>

        <button type="submit" class="btn btn-auth-primary">Redefinir Senha</button>

        <div class="auth-links">
            <a href="/login">Voltar ao Login</a>
        </div>

        <div class="auth-help-text">
            Precisa de ajuda?<br>
            Entre em contato com a universidade
        </div>

        <div class="unifio-logo">
            <img src="/img/logo-unifio-azul.webp" alt="Logo UNIFIO">
        </div>
    </form>
</div>