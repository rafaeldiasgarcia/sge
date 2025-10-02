<?php
#
# View com o formulário de login principal da aplicação.
#
?>
<div class="auth-card">
    <h1 class="auth-title">Jogos Acadêmicos</h1>
    <p class="auth-subtitle">Inscrição dos jogos acadêmicos UNIFIO<br>entre atléticas</p>

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

    <form action="/login" method="post" class="auth-form">
        <div class="mb-3">
            <label for="email" class="form-label">E-mail</label>
            <input type="text" name="email" id="email" class="form-control" placeholder="******@seuemail.com" required>
        </div>
        <div class="mb-1">
            <label for="senha" class="form-label">Senha</label>
            <input type="password" name="senha" id="senha" class="form-control" placeholder="••••••••" required>
        </div>
        <div class="auth-links mb-3">
            <a class="esqueceu-senha" href="/esqueci-senha">Esqueceu a senha?</a>
        </div>
        <button type="submit" class="btn btn-auth-primary">Entrar</button>

        <div class="auth-links-register">
            <a href="/registro">Cadastre-se</a>
        </div>

        <div class="auth-help-text">
            Precisa de ajuda?<br>
            Entre em contato com a universidade
        </div>

        <div class="unifio-logo">
            <img src="/img/logo-unifio-azul.webp" alt="">
        </div>
    </form>
</div>