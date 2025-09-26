<?php
#
# View com o formulário para o usuário solicitar a redefinição de senha.
# O usuário insere seu e-mail de cadastro para iniciar o processo.
#
?>
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-body p-4">
                <h2 class="text-center mb-3">Recuperar Senha</h2>
                <p class="text-center text-muted">Digite seu e-mail e enviaremos um link para você redefinir sua senha.</p>

                <?php if (isset($_SESSION['success_message'])): ?>
                    <div class="alert alert-success">
                        <?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?>
                    </div>
                <?php endif; ?>

                <?php if (isset($_SESSION['error_message'])): ?>
                    <div class="alert alert-danger">
                        <?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?>
                    </div>
                <?php endif; ?>

                <form action="/esqueci-senha" method="post">
                    <div class="mb-3">
                        <label for="email" class="form-label">Seu E-mail de Cadastro</label>
                        <input type="email" name="email" id="email" class="form-control" required autofocus>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">Enviar Link de Recuperação</button>
                    </div>
                    <p class="mt-3 text-center">
                        Lembrou a senha? <a href="/login">Voltar para o Login</a>
                    </p>
                </form>
            </div>
        </div>
    </div>
</div>