<?php
#
# View de Verificação 2FA com o novo template aplicado.
# A estrutura HTML foi refeita para corresponder ao 04-verificacao.html.
# A lógica PHP exibe o e-mail e o código de teste.
#
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($title ?? 'Verificação - SGE UNIFIO'); ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/css/template.css">
</head>
<body>
    <div class="background-players">
        <img src="/images/Jogador 1.png" alt="Jogador" class="player player-left">
        <img src="/images/Jogador 2.png" alt="Jogador" class="player player-right">
    </div>
    <div class="login-container">
        <div class="login-box">
            <img src="/images/Logo unifio 2.png" alt="Logo" class="logo">
            <h1 class="titulo">VERIFICAÇÃO<br>DE DOIS FATORES</h1>
            <p class="subtitulo">Insira o código de verificação enviado para o email <?php echo htmlspecialchars($_SESSION['login_email'] ?? ''); ?></p>
            
            <div style="background-color: #e0f7fa; color: #00796b; padding: 10px; border-radius: 6px; margin-bottom: 15px; text-align: center; width: 100%;">
                <strong>[AMBIENTE DE TESTE]</strong><br>
                Seu código de acesso é: <strong><?php echo $_SESSION['login_code_simulado'] ?? 'Erro'; ?></strong>
            </div>

            <?php if (isset($_SESSION['error_message'])): ?>
                <div class="alert alert-danger" style="width:100%; text-align:center; padding:10px; border-radius:6px; background-color:#f8d7da; color:#721c24; border:1px solid #f5c6cb; margin-bottom:15px;">
                    <?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?>
                </div>
            <?php endif; ?>

            <form id="verificacaoForm" action="/login/verify" method="post">
                <label for="code">Código de Verificação</label>
                <input type="text" id="code" name="code" placeholder="000000" maxlength="6" required style="text-align: center; letter-spacing: 2px;">
                
                <button type="submit" class="btn-entrar">Verificar e Entrar</button>
            </form>
            
            <div class="ajuda">
                <p>Precisa de ajuda?</p>
                <p>Entre em contato com a universidade</p>
            </div>
        </div>
    </div>

    <div style="width: 100%; text-align: center; position: relative; z-index: 5;">
        <img src="/images/creditos .png" alt="Créditos UNIFIO" style="max-width: 100%; height: auto; display: block; margin: 0 auto;">
    </div>
</body>
</html>