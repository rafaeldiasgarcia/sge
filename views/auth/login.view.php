<?php
/**
 * ============================================================================
 * VIEW: LOGIN
 * ============================================================================
 * 
 * Tela de autenticação principal do sistema para todos os tipos de usuários.
 * 
 * FUNCIONALIDADES:
 * - Formulário de login com email e senha
 * - Link para recuperação de senha
 * - Link para cadastro de novos usuários
 * - Exibição de mensagens de sucesso/erro
 * - Design centralizado com branding institucional
 * 
 * FLUXO DE AUTENTICAÇÃO:
 * 1. Usuário insere credenciais
 * 2. POST para /login
 * 3. Se credenciais válidas → redireciona para verificação 2FA ou dashboard
 * 4. Se inválidas → retorna com mensagem de erro
 * 
 * MENSAGENS DE SESSÃO:
 * - success_message: feedback positivo (ex: "Cadastro realizado com sucesso")
 * - error_message: feedback de erro (ex: "Credenciais inválidas")
 * 
 * SEGURANÇA:
 * - Senhas nunca são preenchidas automaticamente
 * - HTTPS recomendado em produção
 * - Rate limiting no controller
 * 
 * CONTROLLER: AuthController::login()
 * CSS: default.css, auth.css
 */
?>

<!-- Card centralizado de autenticação -->
<div class="auth-card">
    <h1 class="auth-title">Jogos Acadêmicos</h1>
    <p class="auth-subtitle">Gerenciamento da Quadra Esportiva</p>

    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="alert alert-success alert-auth">
            <?= $_SESSION['success_message']; unset($_SESSION['success_message']); ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['error_message'])): ?>
        <div class="alert alert-danger alert-auth">
            <?= $_SESSION['error_message']; unset($_SESSION['error_message']); ?>
        </div>
    <?php endif; ?>

    <form action="/login" method="post" class="auth-form">
        <!-- E-mail -->
        <div class="mb-3">
            <label for="email" class="form-label">E-mail</label>
            <input type="text" name="email" id="email" class="form-control"
                   placeholder="******@seuemail.com" required>
        </div>

        <!-- Senha com olho (overlay, sem CSS novo) -->
        <div class="mb-1">
            <label for="senha" class="form-label">Senha</label>
            <div class="position-relative">
                <input type="password" name="senha" id="senha" class="form-control pe-5"
                       placeholder="••••••••" required>
                <button type="button"
                        id="togglePassword"
                        class="btn btn-link p-0 position-absolute top-50 end-0 translate-middle-y me-3"
                        tabindex="-1" aria-label="Mostrar senha" title="Mostrar/ocultar senha">
                    <i class="bi bi-eye fs-5"></i>
                </button>
            </div>
        </div>

        <div class="auth-links mb-3">
            <a class="esqueceu-senha" href="/esqueci-senha">Esqueceu a senha?</a>
        </div>

        <button type="submit" class="btn btn-auth-primary w-100">Entrar</button>

        <div class="auth-links-register mt-3">
            <a href="/registro">Cadastre-se</a>
        </div>

        <div class="auth-help-text mt-3">
            Precisa de ajuda?<br>
            Entre em contato com a universidade
        </div>

        <div class="unifio-logo mt-3">
            <img src="/img/logo-unifio-azul.webp" alt="Logo UNIFIO">
        </div>
    </form>
</div>

<script>
document.addEventListener("DOMContentLoaded", () => {
  const toggle = document.getElementById("togglePassword");
  const input = document.getElementById("senha");
  const icon = toggle.querySelector("i");

  toggle.addEventListener("click", () => {
    const isPassword = input.type === "password";
    input.type = isPassword ? "text" : "password";
    icon.classList.toggle("bi-eye");
    icon.classList.toggle("bi-eye-slash");
  });
});
</script>
