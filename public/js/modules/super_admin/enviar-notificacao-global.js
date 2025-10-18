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