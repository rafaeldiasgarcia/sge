<?php
/**
 * ============================================================================
 * VIEW: REDEFINIR SENHA
 * ============================================================================
 * 
 * Segunda etapa do processo de recuperação de senha. Permite ao usuário
 * definir uma nova senha usando o token recebido por email.
 * 
 * FUNCIONALIDADES:
 * - Formulário para nova senha e confirmação
 * - Validação em tempo real das senhas (JavaScript)
 * - Validação de requisitos mínimos (6 caracteres)
 * - Feedback visual de correspondência de senhas
 * - Token de segurança enviado via hidden input
 * 
 * FLUXO:
 * 1. Usuário acessa via link com token: /redefinir-senha?token=xxxxx
 * 2. Insere nova senha e confirmação
 * 3. POST para /redefinir-senha com token
 * 4. Controller valida token e atualiza senha
 * 5. Redireciona para login com mensagem de sucesso
 * 
 * VALIDAÇÕES:
 * - Frontend: senhas coincidem, mínimo 6 caracteres
 * - Backend: token válido, não expirado, senhas coincidem
 * 
 * SEGURANÇA:
 * - Token de uso único (invalidado após uso)
 * - Token com prazo de validade
 * - Senha hash armazenado (nunca plaintext)
 * 
 * VARIÁVEIS RECEBIDAS:
 * @var string $token - Token de recuperação via GET ou variável
 * 
 * CONTROLLER: AuthController::redefinirSenha()
 * ETAPA ANTERIOR: esqueci-senha.view.php
 */
?>

<!-- Card centralizado de redefinição -->
<div class="auth-card">
    <h1 class="auth-title">Redefinir Senha</h1>
    <p class="auth-subtitle">Insira sua nova senha</p>

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

    <!-- Formulário de redefinição -->
    <form action="/redefinir-senha" method="post" class="auth-form">
        <!-- Token de segurança (hidden) -->
        <input type="hidden" name="token" 
               value="<?php echo htmlspecialchars($token ?? $_GET['token'] ?? ''); ?>">

        <!-- Campo: Nova senha -->
        <div class="mb-3">
            <label for="nova_senha" class="form-label">Nova Senha</label>
            <input type="password" name="nova_senha" id="nova_senha" class="form-control" 
                   placeholder="••••••••" required minlength="6">
            <small class="form-text text-muted">Mínimo de 6 caracteres</small>
        </div>

        <!-- Campo: Confirmar nova senha -->
        <div class="mb-3">
            <label for="confirmar_nova_senha" class="form-label">Confirmar Nova Senha</label>
            <input type="password" name="confirmar_nova_senha" id="confirmar_nova_senha" 
                   class="form-control" placeholder="••••••••" required minlength="6">
        </div>

        <!-- Botão: Redefinir -->
        <button type="submit" class="btn btn-auth-primary">Redefinir Senha</button>

        <!-- Link: Voltar ao login -->
        <div class="auth-links">
            <a href="/login">Voltar ao Login</a>
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

<!-- ========================================================================
     JAVASCRIPT: VALIDAÇÃO DE SENHAS EM TEMPO REAL
     Verifica se as senhas coincidem durante a digitação
     ======================================================================== -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const novaSenha = document.getElementById('nova_senha');
        const confirmarNovaSenha = document.getElementById('confirmar_nova_senha');

        /**
         * Valida se a senha e confirmação coincidem
         * Define mensagem de erro customizada se não coincidirem
         */
        function validatePasswords() {
            if (confirmarNovaSenha.value && novaSenha.value !== confirmarNovaSenha.value) {
                confirmarNovaSenha.setCustomValidity('As senhas não coincidem.');
            } else {
                confirmarNovaSenha.setCustomValidity('');
            }
        }

        // Valida a cada digitação em qualquer um dos campos
        novaSenha.addEventListener('input', validatePasswords);
        confirmarNovaSenha.addEventListener('input', validatePasswords);
    });
</script>
