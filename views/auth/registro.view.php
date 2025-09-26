<?php
#
# View de Cadastro com o novo template aplicado.
# A estrutura HTML foi refeita para corresponder ao 03-cadastro.html.
# A lógica PHP para exibir erros e popular o select de cursos foi mantida.
#
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($title ?? 'Cadastro - SGE UNIFIO'); ?></title>
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
            <h1 class="titulo">CADASTRE-SE</h1>
            <p class="subtitulo">Cadastro para os jogos universitários da UNIFIO</p>
            
            <?php if (isset($_SESSION['error_message'])): ?>
                <div class="alert alert-danger" style="width:100%; text-align:center; padding:10px; border-radius:6px; background-color:#f8d7da; color:#721c24; border:1px solid #f5c6cb; margin-bottom:15px;">
                    <?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?>
                </div>
            <?php endif; ?>

            <form id="cadastroForm" action="/registro" method="post">
                <label for="nome">Nome Completo</label>
                <input type="text" name="nome" id="nome" class="form-control" required>
                
                <label for="tipo_usuario_detalhado">Vínculo com a Instituição</label>
                <select name="tipo_usuario_detalhado" id="tipo_usuario_detalhado" required>
                    <option value="" disabled selected>-- Selecione uma opção --</option>
                    <option value="Aluno">Aluno (em geral)</option>
                    <option value="Membro das Atléticas">Membro das Atléticas</option>
                    <option value="Professor">Professor</option>
                    <option value="Comunidade Externa">Comunidade Externa</option>
                </select>

                <div id="campo_ra" style="display:none; width: 100%;">
                    <label for="ra">Matrícula (RA)</label>
                    <input type="text" name="ra" id="ra" placeholder="000000" maxlength="6">
                </div>

                <label for="data_nascimento">Data de Nascimento</label>
                <input type="date" name="data_nascimento" id="data_nascimento" required>

                <label for="email">E-mail</label>
                <input type="email" id="email" name="email" placeholder="seu.email@unifio.edu.br" required>
                
                <div id="campo_curso" style="display:none; width: 100%;">
                    <label for="curso_id">Qual seu curso?</label>
                    <select name="curso_id" id="curso_id">
                        <option value="">-- Selecione seu curso --</option>
                        <?php foreach ($cursos as $curso): ?>
                            <option value="<?php echo $curso['id']; ?>"><?php echo htmlspecialchars($curso['nome']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <label for="senha">Senha</label>
                <input type="password" id="senha" name="senha" placeholder="*********" required>

                <label for="confirmar_senha">Confirmar Senha</label>
                <input type="password" name="confirmar_senha" id="confirmar_senha" placeholder="*********" required>
                
                <button type="submit" class="btn-entrar">Cadastrar</button>
            </form>
            
            <div class="cadastro">
                Já tem uma conta? <a href="/login">Faça login</a>
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

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const tipoUsuarioSelect = document.getElementById('tipo_usuario_detalhado');
            const campoRa = document.getElementById('campo_ra');
            const inputRa = document.getElementById('ra');
            const campoCurso = document.getElementById('campo_curso');
            const emailInput = document.getElementById('email');

            function toggleFields() {
                const tipo = tipoUsuarioSelect.value;
                if (tipo === 'Aluno' || tipo === 'Membro das Atléticas') {
                    campoRa.style.display = 'block';
                    inputRa.required = true;
                    campoCurso.style.display = 'block';
                } else {
                    campoRa.style.display = 'none';
                    inputRa.required = false;
                    campoCurso.style.display = 'none';
                }
                if (tipo !== 'Comunidade Externa' && tipo !== '') {
                    emailInput.placeholder = 'Use seu e-mail @unifio.edu.br';
                } else {
                    emailInput.placeholder = '';
                }
            }
            tipoUsuarioSelect.addEventListener('change', toggleFields);
            toggleFields();
        });
    </script>
</body>
</html>