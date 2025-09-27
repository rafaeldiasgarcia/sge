<?php
#
# View com o formulário de recuperação de senha.
#
?>
<div class="auth-card">
    <h1 class="auth-title">Esqueci a Senha</h1>
    <p class="auth-subtitle">Insira seu e-mail para receber<br>as instruções de recuperação</p>

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

    <form action="/esqueci-senha" method="post" class="auth-form">
        <div class="mb-3">
            <label for="email" class="form-label">E-mail</label>
            <input type="email" name="email" id="email" class="form-control" placeholder="matricula@unifio.edu.br" required>
        </div>

        <button type="submit" class="btn btn-auth-primary">Enviar Instruções</button>

        <div class="auth-links-es">
            <a href="/login">Fazer login</a>
        </div>

        <div class="auth-help-text">
            Precisa de ajuda?<br>
            Entre em contato com a universidade
        </div>

        <div class="unifio-logo">
            <img src="/public/img/logo-unifio.png" alt="">
        </div>
    </form>
</div>