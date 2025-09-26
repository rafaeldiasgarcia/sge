<?php
#
# View do Perfil do Usuário com o novo template.
# A estrutura foi adaptada do 08-perfil.html, com a lógica PHP para
# exibir e atualizar os dados do usuário.
#
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($title ?? 'Meu Perfil - SGE UNIFIO'); ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/css/template.css">
    <style>
        /* Estilos específicos para a página de perfil, adaptados do template */
        .profile-header { background: white; border-radius: 16px; padding: 40px; box-shadow: 0 8px 25px rgba(0,0,0,0.1); margin-bottom: 30px; display: flex; align-items: center; gap: 30px; }
        .profile-avatar-large .avatar-circle { width: 120px; height: 120px; background: #1e40af; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-size: 48px; font-weight: 700; }
        .profile-info { flex: 1; }
        .profile-info h1 { color: #1e40af; margin-bottom: 10px; font-size: 32px; }
        .profile-info p { color: #6b7280; margin-bottom: 5px; font-size: 16px; }
        .profile-actions { display: flex; flex-direction: column; gap: 10px; }
        .btn-primary, .btn-secondary { padding: 12px 24px; border: none; border-radius: 8px; cursor: pointer; font-weight: 600; transition: all 0.2s; }
        .btn-primary { background: #1e40af; color: white; }
        .btn-secondary { background: #f97316; color: white; }
        .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 30px; }
        .info-card { background: #f8fafc; border-radius: 12px; padding: 25px; }
        .info-card h3 { color: #1e40af; margin-bottom: 20px; font-size: 20px; }
        .info-item { display: flex; flex-direction: column; margin-bottom: 15px; }
        .info-item label { font-weight: 600; color: #374151; margin-bottom: 5px; }
        .info-item input, .info-item select { width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 6px; }
    </style>
</head>
<body>
    <header class="header">
        <div class="header-left">
            <a href="/dashboard" class="menu-icon">☰</a>
            <div class="header-title">
                <div class="title-main">MEU PERFIL</div>
                <div class="title-sub">UNIFIO 2025</div>
            </div>
        </div>
        <div class="header-center">
            <img src="/images/Logo unifio 2.png" alt="UNIFIO" class="header-logo">
        </div>
        <div class="header-right">
            <div class="user-info">
                <div class="user-name"><?php echo htmlspecialchars($user['nome']); ?></div>
                <a href="/perfil" class="profile-link">Meu Perfil</a>
            </div>
            <div class="profile-avatar"><?php echo strtoupper(substr($user['nome'], 0, 1)); ?></div>
        </div>
    </header>

    <main class="main-content">
        <section class="profile-header">
            <div class="profile-avatar-large">
                <div class="avatar-circle"><?php echo strtoupper(substr($user['nome'], 0, 1)); ?></div>
            </div>
            <div class="profile-info">
                <h1><?php echo htmlspecialchars($user['nome']); ?></h1>
                <p><?php echo htmlspecialchars($user['email']); ?></p>
                <p>Matrícula: <?php echo htmlspecialchars($user['ra'] ?? 'N/A'); ?></p>
            </div>
        </section>

        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="alert alert-success" style="background-color:#d4edda; color:#155724; padding:15px; border-radius:8px; margin-bottom:20px;"><?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?></div>
        <?php endif; ?>
        <?php if (isset($_SESSION['error_message'])): ?>
            <div class="alert alert-danger" style="background-color:#f8d7da; color:#721c24; padding:15px; border-radius:8px; margin-bottom:20px;"><?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?></div>
        <?php endif; ?>

        <div class="info-grid">
            <div class="info-card">
                <h3>Editar Dados Pessoais</h3>
                <form action="/perfil" method="post">
                    <input type="hidden" name="form_type" value="dados_pessoais">
                    <div class="info-item">
                        <label for="nome">Nome Completo</label>
                        <input type="text" name="nome" id="nome" value="<?php echo htmlspecialchars($user['nome'] ?? ''); ?>" required>
                    </div>
                    <div class="info-item">
                        <label for="email">Email</label>
                        <input type="email" name="email" id="email" value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>" required>
                    </div>
                    <div class="info-item">
                        <label for="data_nascimento">Data de Nascimento</label>
                        <input type="date" name="data_nascimento" id="data_nascimento" value="<?php echo htmlspecialchars($user['data_nascimento'] ?? ''); ?>" required>
                    </div>
                    <?php if (!empty($user['ra'])): ?>
                        <div class="info-item">
                            <label for="curso_id">Curso</label>
                            <select name="curso_id" id="curso_id">
                                <option value="">-- Selecione seu curso --</option>
                                <?php foreach ($cursos as $curso): ?>
                                    <option value="<?php echo $curso['id']; ?>" <?php echo ($user['curso_id'] == $curso['id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($curso['nome']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    <?php endif; ?>
                    <button type="submit" class="btn-primary" style="margin-top: 20px;">Salvar Dados</button>
                </form>
            </div>

            <div class="info-card">
                <h3>Alterar Senha</h3>
                <form action="/perfil" method="post">
                    <input type="hidden" name="form_type" value="alterar_senha">
                    <div class="info-item">
                        <label for="senha_atual">Senha Atual</label>
                        <input type="password" name="senha_atual" id="senha_atual" required>
                    </div>
                    <div class="info-item">
                        <label for="nova_senha">Nova Senha</label>
                        <input type="password" name="nova_senha" id="nova_senha" required minlength="6">
                    </div>
                    <div class="info-item">
                        <label for="confirmar_nova_senha">Confirmar Nova Senha</label>
                        <input type="password" name="confirmar_nova_senha" id="confirmar_nova_senha" required>
                    </div>
                    <button type="submit" class="btn-secondary" style="margin-top: 20px;">Alterar Senha</button>
                </form>
            </div>
        </div>
    </main>

    <div style="width: 100%; text-align: center; margin-top: 40px;">
        <img src="/images/creditos .png" alt="Créditos UNIFIO" style="max-width: 100%; height: auto; display: block; margin: 0 auto;">
    </div>
</body>
</html>