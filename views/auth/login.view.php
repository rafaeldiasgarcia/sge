<?php
#
# View de Login com o novo template aplicado.
# A estrutura HTML foi completamente refeita para corresponder ao 02-login.html,
# mantendo a lógica PHP para exibir mensagens de erro e sucesso.
#
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($title ?? 'Login - SGE UNIFIO'); ?></title>
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
            <img src="/images/Logo unifio 2.png" alt="Logo UNIFIO" class="logo">
            <h1 class="titulo">JOGOS ACADÊMICOS</h1>
            <p class="subtitulo">Inscrição dos jogos acadêmicos UNIFIO entre atléticas</p>
            
            <?php if (isset($_SESSION['success_message'])): ?>
                <div class="alert alert-success" style="width:100%; text-align:center; padding:10px; border-radius:6px; background-color:#d4edda; color:#155724; border:1px solid #c3e6cb; margin-bottom:15px;">
                    <?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?>
                </div>
            <?php endif; ?>

            <?php if (isset($_SESSION['error_message'])): ?>
                <div class="alert alert-danger" style="width:100%; text-align:center; padding:10px; border-radius:6px; background-color:#f8d7da; color:#721c24; border:1px solid #f5c6cb; margin-bottom:15px;">
                    <?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?>
                </div>
            <?php endif; ?>

            <form id="loginForm" action="/login" method="post">
                <label for="email">E-mail</label>
                <input type="email" id="email" name="email" placeholder="seu.email@unifio.edu.br" required>
                
                <label for="senha">Senha</label>
                <input type="password" id="senha" name="senha" placeholder="*********" required>
                
                <div class="login-options">
                    <label class="lembrar">
                        <input type="checkbox" name="lembrar">
                        Lembrar-me
                    </label>
                    <a href="/esqueci-senha" class="esqueceu">Esqueceu a senha?</a>
                </div>
                
                <button type="submit" class="btn-entrar">Entrar</button>
            </form>
            
            <div class="cadastro">
                Não tem uma conta? <a href="/registro">Cadastre-se</a>
            </div>
            
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