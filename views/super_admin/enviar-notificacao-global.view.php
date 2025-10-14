<?php
/**
 * VIEW: ENVIAR NOTIFICAÇÃO GLOBAL (SUPER ADMIN)
 * Formulário para enviar notificações para todos os usuários do sistema.
 * Útil para avisos de manutenção, mudanças importantes, etc.
 * CONTROLLER: SuperAdminController::enviarNotificacaoGlobal()
 */
?>
<h1>Enviar Notificação Global</h1>
<p>Envie uma notificação para todos os usuários da plataforma.</p>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Nova Notificação Global</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="/superadmin/notificacao-global/enviar">
                    <div class="mb-3">
                        <label for="titulo" class="form-label">Título da Notificação <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="titulo" name="titulo"
                               placeholder="Ex: Manutenção da Quadra" required maxlength="100">
                        <div class="form-text">Máximo 100 caracteres</div>
                    </div>

                    <div class="mb-3">
                        <label for="mensagem" class="form-label">Mensagem <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="mensagem" name="mensagem" rows="5"
                                  placeholder="Digite a mensagem que será enviada para todos os usuários..."
                                  required maxlength="500"></textarea>
                        <div class="form-text">Máximo 500 caracteres</div>
                    </div>

                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        <strong>Atenção:</strong> Esta notificação será enviada para todos os usuários ativos da plataforma.
                        Certifique-se de que a mensagem está correta antes de enviar.
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-warning">
                            <i class="fas fa-paper-plane"></i> Enviar Notificação
                        </button>
                        <a href="/superadmin/dashboard" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Voltar
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <div class="card mt-4">
            <div class="card-header">
                <h6 class="card-title mb-0">Exemplos de Notificações</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6>Manutenção da Quadra</h6>
                        <small class="text-muted">
                            <strong>Título:</strong> Manutenção Programada<br>
                            <strong>Mensagem:</strong> A quadra estará indisponível para agendamentos de 15/10 a 20/10 devido à manutenção do piso. Agendamentos neste período serão reagendados.
                        </small>
                    </div>
                    <div class="col-md-6">
                        <h6>Aviso Geral</h6>
                        <small class="text-muted">
                            <strong>Título:</strong> Novas Regras de Uso<br>
                            <strong>Mensagem:</strong> A partir de 01/11, os eventos devem ter no mínimo 10 participantes. Consulte o regulamento atualizado no site.
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Contador de caracteres
document.getElementById('titulo').addEventListener('input', function() {
    const maxLength = 100;
    const currentLength = this.value.length;
    const remaining = maxLength - currentLength;

    const helpText = this.nextElementSibling;
    helpText.textContent = `${remaining} caracteres restantes`;

    if (remaining < 20) {
        helpText.classList.add('text-warning');
    } else {
        helpText.classList.remove('text-warning');
    }
});

document.getElementById('mensagem').addEventListener('input', function() {
    const maxLength = 500;
    const currentLength = this.value.length;
    const remaining = maxLength - currentLength;

    const helpText = this.nextElementSibling;
    helpText.textContent = `${remaining} caracteres restantes`;

    if (remaining < 50) {
        helpText.classList.add('text-warning');
    } else {
        helpText.classList.remove('text-warning');
    }
});

// Confirmação antes de enviar
document.querySelector('form').addEventListener('submit', function(e) {
    const titulo = document.getElementById('titulo').value;
    const mensagem = document.getElementById('mensagem').value;

    if (!confirm(`Tem certeza que deseja enviar esta notificação para todos os usuários?\n\nTítulo: ${titulo}\nMensagem: ${mensagem.substring(0, 100)}${mensagem.length > 100 ? '...' : ''}`)) {
        e.preventDefault();
    }
});
</script>
