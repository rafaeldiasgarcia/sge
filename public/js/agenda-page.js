// Toggle sections between esportivos and nao_esportivos
function toggleEventos(tipo) {
    document.querySelectorAll('.eventos-section').forEach(function(section) {
        section.style.display = 'none';
    });

    var btnEsportivos = document.getElementById('btnEventosEsportivos');
    var btnNaoEsportivos = document.getElementById('btnEventosNaoEsportivos');
    if (btnEsportivos) btnEsportivos.classList.remove('active');
    if (btnNaoEsportivos) btnNaoEsportivos.classList.remove('active');

    if (tipo === 'esportivos') {
        var esportivos = document.getElementById('eventosEsportivos');
        if (esportivos) esportivos.style.display = 'block';
        if (btnEsportivos) btnEsportivos.classList.add('active');
    } else if (tipo === 'nao_esportivos') {
        var naoEsportivos = document.getElementById('eventosNaoEsportivos');
        if (naoEsportivos) naoEsportivos.style.display = 'block';
        if (btnNaoEsportivos) btnNaoEsportivos.classList.add('active');
    }
}

// Toggle past events visibility
function toggleEventosPassados() {
    var section = document.getElementById('eventosPassadosSection');
    var toggleText = document.getElementById('toggleText');
    var toggleIcon = document.getElementById('toggleIcon');

    if (!section) return;

    if (section.style.display === 'none' || section.style.display === '') {
        section.style.display = 'block';
        if (toggleText) toggleText.textContent = 'Ocultar Eventos Passados';
        if (toggleIcon) toggleIcon.className = 'bi bi-chevron-up ms-1';
        setTimeout(function() {
            section.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }, 100);
    } else {
        section.style.display = 'none';
        if (toggleText) toggleText.textContent = 'Ver Eventos Passados';
        if (toggleIcon) toggleIcon.className = 'bi bi-chevron-down ms-1';
        var btn = document.getElementById('toggleEventosPassados');
        if (btn) btn.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
}

// Initialize default tab and presence buttons handling
document.addEventListener('DOMContentLoaded', function() {
    toggleEventos('esportivos');
});

// Presence button AJAX handling
document.addEventListener('click', function(event) {
    var presencaBtn = event.target.closest('.presenca-btn');
    if (!presencaBtn) return;

    event.stopPropagation();
    event.preventDefault();

    var btn = presencaBtn;
    var agendamentoId = btn.getAttribute('data-agendamento-id');
    var action = btn.getAttribute('data-action');
    var spinner = btn.nextElementSibling;

    if (spinner) spinner.classList.remove('d-none');
    btn.setAttribute('disabled', 'true');

    var formData = new FormData();
    formData.append('agendamento_id', agendamentoId);
    formData.append('action', action);

    fetch('/agenda/presenca', {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        body: formData
    })
    .then(function(response) { return response.json(); })
    .then(function(data) {
        if (!data || !data.success) {
            console.error('Erro na resposta:', data && data.message);
            alert('Erro ao atualizar presença. Tente novamente mais tarde.');
            return;
        }

        var eventoContainer = btn.closest('.list-group-item');
        var badgePresencas = eventoContainer ? eventoContainer.querySelector('.badge.bg-info') : null;

        if (action === 'marcar') {
            btn.setAttribute('data-action', 'desmarcar');
            btn.innerHTML = '<i class="bi bi-x-circle-fill"></i> Desmarcar Presença';
            if (badgePresencas) {
                var currentCount = parseInt((badgePresencas.textContent.match(/\d+/) || ['0'])[0]);
                badgePresencas.innerHTML = '<i class="bi bi-people-fill"></i> ' + (currentCount + 1) + ' pessoa(s) confirmaram presença';
            }
        } else {
            btn.setAttribute('data-action', 'marcar');
            btn.innerHTML = '<i class="bi bi-check-circle"></i> Marcar Presença';
            if (badgePresencas) {
                var currentCount2 = parseInt((badgePresencas.textContent.match(/\d+/) || ['0'])[0]);
                badgePresencas.innerHTML = '<i class="bi bi-people-fill"></i> ' + Math.max(0, currentCount2 - 1) + ' pessoa(s) confirmaram presença';
            }
        }
    })
    .catch(function(error) {
        console.error('Erro na requisição:', error);
        alert('Erro ao atualizar presença. Tente novamente mais tarde.');
    })
    .finally(function() {
        if (spinner) spinner.classList.add('d-none');
        btn.removeAttribute('disabled');
    });
});


