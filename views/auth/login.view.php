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
    <!-- Cabeçalho do sistema -->
    <h1 class="auth-title">Jogos Acadêmicos</h1>
    <p class="auth-subtitle">Gerenciamento da Quadra esportiva</p>

    <!-- Mensagens de feedback -->
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

    <!-- Formulário de login -->
    <form action="/login" method="post" class="auth-form">
        <!-- Campo: Email -->
        <div class="mb-3">
            <label for="email" class="form-label">E-mail</label>
            <input type="text" name="email" id="email" class="form-control" 
                   placeholder="******@seuemail.com" required>
        </div>
        
        <!-- Campo: Senha -->
        <div class="mb-1">
            <label for="senha" class="form-label">Senha</label>
            <input type="password" name="senha" id="senha" class="form-control" 
                   placeholder="••••••••" required>
        </div>
        
        <!-- Link: Recuperação de senha -->
        <div class="auth-links mb-3">
            <a class="esqueceu-senha" href="/esqueci-senha">Esqueceu a senha?</a>
        </div>
        
        <!-- Botão: Entrar -->
        <button type="submit" class="btn btn-auth-primary">Entrar</button>

        <!-- Link: Cadastro -->
        <div class="auth-links-register">
            <a href="/registro">Cadastre-se</a>
        </div>

        <!-- Texto de ajuda -->
        <div class="auth-help-text">
            Precisa de ajuda?<br>
            Entre em contato com a universidade
        </div>

        <!-- Logo institucional -->
        <div class="unifio-logo">
            <img src="/img/logo-unifio-azul.webp" alt="Logo UNIFIO">
        </div>
    </form>
</div>