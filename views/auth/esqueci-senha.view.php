<?php
#
# View de "Esqueci a Senha" com o novo template.
# A estrutura HTML foi refeita para corresponder ao 05-recuperar-senha.html.
# A lógica PHP para exibir mensagens foi mantida.
#
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($title ?? 'Recuperar Senha - SGE UNIFIO'); ?></title>
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
            <h1 class="titulo">RECUPERAR SENHA</h1>
            <p class="subtitulo">Digite seu email para receber instruções de recuperação</p>
            
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

            <form id="recuperarForm" action="/esqueci-senha" method="post">
                <label for="email">E-mail institucional</label>
                <input type="email" id="email" name="email" placeholder="seu.email@unifio.edu.br" required>
                
                <button type="submit" class="btn-entrar">Enviar Instruções</button>
            </form>
            
            <div class="cadastro">
                Lembrou da senha? <a href="/login">Faça login</a>
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