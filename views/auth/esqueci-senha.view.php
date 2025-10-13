<?php
/**
 * ============================================================================
 * VIEW: RECUPERAÇÃO DE SENHA (ESQUECI A SENHA)
 * ============================================================================
 * 
 * Primeira etapa do processo de recuperação de senha. Solicita o email do
 * usuário e envia instruções de redefinição.
 * 
 * FUNCIONALIDADES:
 * - Formulário para inserir email
 * - Envio de email com token de recuperação
 * - Link de volta para login
 * - Exibição de mensagens de feedback
 * 
 * FLUXO DE RECUPERAÇÃO:
 * 1. Usuário insere email
 * 2. POST para /esqueci-senha
 * 3. Sistema gera token único e temporário
 * 4. Email enviado com link /redefinir-senha?token=xxxxx
 * 5. Token válido por tempo limitado (definido no controller)
 * 
 * MODO DESENVOLVIMENTO:
 * - Exibe link de recuperação diretamente na tela para facilitar testes
 * - Em produção, link é enviado apenas por email
 * 
 * SEGURANÇA:
 * - Token único e temporário
 * - Não revela se o email existe no sistema (retorna sempre sucesso)
 * - Rate limiting recomendado no controller
 * 
 * VARIÁVEIS DE SESSÃO:
 * - success_message: confirmação de envio
 * - error_message: problemas no envio
 * - recovery_link: (dev) link direto de recuperação
 * 
 * CONTROLLER: AuthController::esqueciSenha()
 * PRÓXIMO PASSO: redefinir-senha.view.php
 */
?>

<!-- Card centralizado de recuperação -->
<div class="auth-card">
    <h1 class="auth-title">Esqueci a Senha</h1>
    <p class="auth-subtitle">Insira seu e-mail para receber<br>as instruções de recuperação</p>

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

    <!-- MODO DESENVOLVIMENTO: Exibir link direto de recuperação -->
    <?php if (isset($_SESSION['recovery_link'])): ?>
        <div class="alert alert-info alert-auth">
            <strong>Link de recuperação disponível:</strong><br>
            <a href="<?php echo htmlspecialchars($_SESSION['recovery_link']); ?>" 
               style="word-break: break-all; color: #0056b3; text-decoration: underline;">
                <?php echo htmlspecialchars($_SESSION['recovery_link']); ?>
            </a>
            <?php unset($_SESSION['recovery_link']); ?>
        </div>
    <?php endif; ?>

    <!-- Formulário de recuperação -->
    <form action="/esqueci-senha" method="post" class="auth-form">
        <!-- Campo: Email -->
        <div class="mb-3">
            <label for="email" class="form-label">E-mail</label>
            <input type="email" name="email" id="email" class="form-control" 
                   placeholder="matricula@unifio.edu.br" required>
        </div>

        <!-- Botão: Enviar instruções -->
        <button type="submit" class="btn btn-auth-primary">Enviar Instruções</button>

        <!-- Link: Voltar ao login -->
        <div class="auth-links-es">
            <a href="/login">Fazer login</a>
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