<?php
#
# View com o formulário de verificação de dois fatores.
#
?>
<div class="auth-card">
    <h1 class="auth-title">Verificação</h1>
    <h2 class="auth-title" style="margin-top: -10px; margin-bottom: 20px;">de Dois Fatores</h2>
    <p class="auth-subtitle">Insira o código de verificação enviado<br>no email ****@unifio.edu.br</p>

    <?php if (isset($_SESSION['error_message'])): ?>
        <div class="alert alert-danger alert-auth">
            <?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?>
        </div>
    <?php endif; ?>

    <!-- Exibir códigos de verificação para desenvolvimento/teste -->
    <?php if (isset($_SESSION['verification_codes']) || isset($_SESSION['verification_code'])): ?>
        <div class="alert alert-info alert-auth">
            <strong>Códigos de verificação disponíveis:</strong><br>
            <?php
            if (isset($_SESSION['verification_codes'])) {
                foreach ($_SESSION['verification_codes'] as $code) {
                    echo "<code style='background: #f8f9fa; padding: 2px 6px; margin: 2px; border-radius: 4px;'>" . htmlspecialchars($code) . "</code> ";
                }
            } elseif (isset($_SESSION['verification_code'])) {
                echo "<code style='background: #f8f9fa; padding: 2px 6px; margin: 2px; border-radius: 4px;'>" . htmlspecialchars($_SESSION['verification_code']) . "</code>";
            }
            ?>
        </div>
    <?php endif; ?>

    <form action="/login/verify" method="post" class="auth-form">
        <div class="mb-4">
            <input type="text" name="code" id="codigo" class="form-control verification-code-input" placeholder="000000" maxlength="6" pattern="[0-9]{6}" title="Digite o código de 6 dígitos" required>
        </div>

        <button type="submit" class="btn btn-auth-primary">Entrar</button>

        <div class="auth-help-text">
            Precisa de ajuda?<br>
            Entre em contato com a universidade
        </div>

        <div class="unifio-logo">
            <img src="/public/img/logo-unifio.png" alt="">
        </div>
    </form>
</div>

<script>
    // Auto-format verification code input
    document.getElementById('codigo').addEventListener('input', function(e) {
        let value = e.target.value.replace(/\D/g, ''); // Remove non-digits
        if (value.length > 6) value = value.slice(0, 6); // Limit to 6 digits
        e.target.value = value;
    });

    // Auto-focus on page load
    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('codigo').focus();
    });
</script>
