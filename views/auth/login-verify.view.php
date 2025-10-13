<?php
/**
 * ============================================================================
 * VIEW: VERIFICAÇÃO DE DOIS FATORES (2FA)
 * ============================================================================
 * 
 * Tela de verificação do código 2FA enviado por email após login bem-sucedido.
 * 
 * FUNCIONALIDADES:
 * - Exibição do email mascarado (ex: a***@domain.com)
 * - Campo para código de 6 dígitos
 * - Validação apenas de números
 * - Auto-focus no campo ao carregar
 * - Prevenção de envio duplicado
 * - Formatação automática (apenas números)
 * 
 * FLUXO:
 * 1. Usuário faz login com credenciais válidas
 * 2. Sistema gera código de 6 dígitos
 * 3. Código enviado por email
 * 4. Usuário redirecionado para esta tela
 * 5. Insere código recebido
 * 6. POST para /login/verify
 * 7. Se válido → acesso concedido ao sistema
 * 8. Se inválido → mensagem de erro
 * 
 * SEGURANÇA:
 * - Código expira após tempo definido
 * - Limitação de tentativas recomendada
 * - Email armazenado em sessão temporária
 * - Código de uso único
 * 
 * VARIÁVEIS DE SESSÃO UTILIZADAS:
 * - login_email: email do usuário (mascarado na exibição)
 * - error_message: feedback de código inválido
 * 
 * CONTROLLER: AuthController::loginVerify()
 * ETAPA ANTERIOR: login.view.php
 */

// Mascara o email para exibição de segurança
$email = $_SESSION['login_email'] ?? '';
$maskedEmail = '';
if ($email) {
    $parts = explode('@', $email);
    if (count($parts) == 2) {
        $name = $parts[0];
        $domain = $parts[1];
        // Exibe apenas primeira letra + asteriscos + domínio
        $maskedEmail = substr($name, 0, 1) . str_repeat('*', strlen($name)-1) . '@' . $domain;
    }
}
?>

<!-- Card centralizado de verificação -->
<div class="auth-card">
    <h1 class="auth-title">Verificação</h1>
    <h2 class="auth-title" style="margin-top: -10px; margin-bottom: 20px;">de Dois Fatores</h2>
    <p class="auth-subtitle">
        Insira o código de verificação enviado<br>
        no email <?php echo htmlspecialchars($maskedEmail); ?>
    </p>

    <!-- Mensagens de erro -->
    <?php if (isset($_SESSION['error_message'])): ?>
        <div class="alert alert-danger alert-auth">
            <?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?>
        </div>
    <?php endif; ?>

    <!-- Formulário de verificação -->
    <form action="/login/verify" method="post" class="auth-form" id="verifyForm">
        <!-- Campo: Código de 6 dígitos -->
        <div class="mb-4">
            <input type="text"
                   name="code"
                   id="codigo"
                   class="form-control verification-code-input"
                   placeholder="000000"
                   maxlength="6"
                   pattern="[0-9]{6}"
                   inputmode="numeric"
                   autocomplete="off"
                   required>
        </div>

        <!-- Botão: Verificar -->
        <button type="submit" class="btn btn-auth-primary">Verificar</button>

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
     JAVASCRIPT: VALIDAÇÃO E UX DO CAMPO DE CÓDIGO
     ======================================================================== -->
<script>
    const codigoInput = document.getElementById('codigo');
    const verifyForm = document.getElementById('verifyForm');

    // Formatar entrada para aceitar apenas números e limitar a 6 dígitos
    codigoInput.addEventListener('input', function(e) {
        let value = e.target.value.replace(/\D/g, ''); // Remove não-dígitos
        if (value.length > 6) value = value.slice(0, 6); // Limita a 6 dígitos
        e.target.value = value;
    });

    // Auto-focus no campo ao carregar a página
    document.addEventListener('DOMContentLoaded', function() {
        codigoInput.focus();
    });

    // Prevenir envio duplicado do formulário
    verifyForm.addEventListener('submit', function(e) {
        let submitButton = this.querySelector('button[type="submit"]');
        submitButton.disabled = true;
        submitButton.textContent = 'Verificando...';
    });
</script>
