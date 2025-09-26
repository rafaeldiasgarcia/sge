<?php
#
# View de "Redefinir Senha" com o novo template.
# O usuário define a nova senha nesta página.
#
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($title ?? 'Redefinir Senha - SGE UNIFIO'); ?></title>
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
            <h1 class="titulo">REDEFINIR SENHA</h1>
            <p class="subtitulo">Crie uma nova senha para sua conta.</p>
            
            <?php if (isset($_SESSION['error_message'])): ?>
                <div class="alert alert-danger" style="width:100%; text-align:center; padding:10px; border-radius:6px; background-color:#f8d7da; color:#721c24; border:1px solid #f5c6cb; margin-bottom:15px;">
                    <?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?>
                </div>
            <?php endif; ?>

            <form action="/redefinir-senha" method="post">
                <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">

                <label for="nova_senha">Nova Senha</label>
                <input type="password" name="nova_senha" id="nova_senha" placeholder="*********" required minlength="6">
                
                <label for="confirmar_nova_senha">Confirmar Nova Senha</label>
                <input type="password" name="confirmar_nova_senha" id="confirmar_nova_senha" placeholder="*********" required>
                
                <button type="submit" class="btn-entrar">Salvar Nova Senha</button>
            </form>
        </div>
    </div>

    <div style="width: 100%; text-align: center; position: relative; z-index: 5;">
        <img src="/images/creditos .png" alt="Créditos UNIFIO" style="max-width: 100%; height: auto; display: block; margin: 0 auto;">
    </div>
</body>
</html>