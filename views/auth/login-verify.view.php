<?php
#
# View com o formulário de verificação de dois fatores.
#
$email = $_SESSION['login_email'] ?? '';
$maskedEmail = '';
if ($email) {
    $parts = explode('@', $email);
    if (count($parts) == 2) {
        $name = $parts[0];
        $domain = $parts[1];
        $maskedEmail = substr($name, 0, 1) . str_repeat('*', strlen($name)-1) . '@' . $domain;
    }
}
?>
<div class="auth-card">
    <h1 class="auth-title">Verificação</h1>
    <h2 class="auth-title" style="margin-top: -10px; margin-bottom: 20px;">de Dois Fatores</h2>
    <p class="auth-subtitle">Insira o código de verificação enviado<br>no email <?php echo htmlspecialchars($maskedEmail); ?></p>

    <?php if (isset($_SESSION['error_message'])): ?>
        <div class="alert alert-danger alert-auth">
            <?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['verification_code'])): ?>
        <div class="alert alert-info alert-auth">
            <strong>Código de verificação:</strong><br>
            <code style='background: #f8f9fa; padding: 2px 6px; margin: 2px; border-radius: 4px;'><?php echo htmlspecialchars($_SESSION['verification_code']); ?></code>
        </div>
    <?php endif; ?>

    <form action="/login/verify" method="post" class="auth-form" id="verifyForm">
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

        <button type="submit" class="btn btn-auth-primary">Verificar</button>

        <div class="auth-help-text">
            Precisa de ajuda?<br>
            Entre em contato com a universidade
        </div>

        <div class="unifio-logo">
            <img src="/img/logo-unifio-azul.webp" alt="">
        </div>
    </form>
</div>

<script>
    // Formatar entrada para aceitar apenas números
    document.getElementById('codigo').addEventListener('input', function(e) {
        let value = e.target.value.replace(/\D/g, ''); // Remove não-dígitos
        if (value.length > 6) value = value.slice(0, 6); // Limita a 6 dígitos
        e.target.value = value;
    });

    // Auto-focus ao carregar a página
    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('codigo').focus();
    });

    // Prevenir envio duplicado do formulário
    document.getElementById('verifyForm').addEventListener('submit', function(e) {
        let submitButton = this.querySelector('button[type="submit"]');
        submitButton.disabled = true;
        submitButton.textContent = 'Verificando...';
    });
</script>
