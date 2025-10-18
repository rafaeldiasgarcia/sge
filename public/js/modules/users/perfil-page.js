// Tabs control
function openTab(event, tabName) {
    var tabContents = document.querySelectorAll('.profile-tab-content');
    tabContents.forEach(function(content) { content.classList.remove('active'); });

    var tabs = document.querySelectorAll('.profile-tab');
    tabs.forEach(function(tab) { tab.classList.remove('active'); });

    var contentEl = document.getElementById(tabName);
    if (contentEl) contentEl.classList.add('active');

    if (event && event.target) {
        event.target.classList.add('active');
    }
}

// Modals open helpers
function openPasswordModal() {
    var modal = new bootstrap.Modal(document.getElementById('modalAlterarSenha'));
    modal.show();
}

function openTrocaCursoModal() {
    var modal = new bootstrap.Modal(document.getElementById('modalTrocarCurso'));
    modal.show();
}

// Validate troca de curso form
document.getElementById('formTrocarCurso')?.addEventListener('submit', function(e) {
    var justificativa = (document.getElementById('justificativa')?.value || '').trim();
    var cursoNovoId = (document.getElementById('curso_novo_id')?.value || '');

    if (!cursoNovoId) {
        e.preventDefault();
        alert('Por favor, selecione o curso desejado.');
        return false;
    }

    if (justificativa.length < 50) {
        e.preventDefault();
        alert('A justificativa deve ter no mínimo 50 caracteres. Você digitou ' + justificativa.length + ' caracteres.');
        return false;
    }

    return confirm('Confirma o envio da solicitação de troca de curso? Você receberá uma resposta do coordenador em breve.');
});

// Ajuste de tamanho do nome
function ajustarTamanhoNome() {
    var nomeElement = document.querySelector('.profile-info h2');
    if (!nomeElement) return;

    var containerWidth = nomeElement.parentElement.offsetWidth;
    var fontSize = 28;
    nomeElement.style.fontSize = fontSize + 'px';

    while (nomeElement.scrollWidth > containerWidth && fontSize > 16) {
        fontSize -= 1;
        nomeElement.style.fontSize = fontSize + 'px';
    }
}

document.addEventListener('DOMContentLoaded', ajustarTamanhoNome);
window.addEventListener('resize', ajustarTamanhoNome);


