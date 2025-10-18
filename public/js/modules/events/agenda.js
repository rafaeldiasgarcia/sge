/**
 * JavaScript para a página da Agenda
 * 
 * Funcionalidades:
 * - Toggle entre eventos esportivos e não esportivos
 * - Toggle de eventos passados
 * - Gerenciamento de presenças via AJAX
 * - Integração com popup de detalhes do evento
 */

function toggleEventos(tipo) {
    // Esconder todas as seções
    document.querySelectorAll('.eventos-section').forEach(section => {
        section.style.display = 'none';
    });

    // Remover classe ativa de todos os botões
    document.getElementById('btnEventosEsportivos').classList.remove('active');
    document.getElementById('btnEventosNaoEsportivos').classList.remove('active');

    // Mostrar seção selecionada e ativar botão
    if (tipo === 'esportivos') {
        document.getElementById('eventosEsportivos').style.display = 'block';
        document.getElementById('btnEventosEsportivos').classList.add('active');
    } else if (tipo === 'nao_esportivos') {
        document.getElementById('eventosNaoEsportivos').style.display = 'block';
        document.getElementById('btnEventosNaoEsportivos').classList.add('active');
    }

    // Removi o scroll automático - agora a página fica no lugar
}

function toggleEventosPassados() {
    const section = document.getElementById('eventosPassadosSection');
    const toggleText = document.getElementById('toggleText');
    const toggleIcon = document.getElementById('toggleIcon');

    if (section.style.display === 'none' || section.style.display === '') {
        section.style.display = 'block';
        toggleText.textContent = 'Ocultar Eventos Passados';
        toggleIcon.className = 'bi bi-chevron-up ms-1';
        setTimeout(() => {
            section.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }, 100);
    } else {
        section.style.display = 'none';
        toggleText.textContent = 'Ver Eventos Passados';
        toggleIcon.className = 'bi bi-chevron-down ms-1';
        document.getElementById('toggleEventosPassados').scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
}

// Inicializar mostrando eventos esportivos por padrão
document.addEventListener('DOMContentLoaded', function() {
    toggleEventos('esportivos');
});

// Gerenciar cliques nos botões de presença
document.addEventListener('click', function(event) {
    // Verificar se clicou no botão de presença ou em um elemento dentro dele (como o ícone)
    const presencaBtn = event.target.closest('.presenca-btn');

    if (presencaBtn) {
        // IMPORTANTE: Parar a propagação IMEDIATAMENTE para não abrir o popup
        event.stopPropagation();
        event.preventDefault();

        const btn = presencaBtn;
        const agendamentoId = btn.getAttribute('data-agendamento-id');
        const action = btn.getAttribute('data-action');
        const spinner = btn.nextElementSibling;

        // Mostrar spinner e desabilitar botão
        if (spinner) spinner.classList.remove('d-none');
        btn.setAttribute('disabled', 'true');

        // Criar FormData como se fosse um formulário tradicional
        const formData = new FormData();
        formData.append('agendamento_id', agendamentoId);
        formData.append('action', action);

        // Fazer requisição AJAX para marcar/desmarcar presença
        fetch('/agenda/presenca', {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: formData
        })
        .then(response => {
            return response.json();
        })
        .then(data => {
            // Atualizar interface com base na resposta
            if (data.success) {
                // Encontrar o badge de contagem de presenças no mesmo evento
                const eventoContainer = btn.closest('.list-group-item');
                const badgePresencas = eventoContainer.querySelector('.badge.bg-info');
                
                if (action === 'marcar') {
                    btn.setAttribute('data-action', 'desmarcar');
                    btn.innerHTML = '<i class="bi bi-x-circle-fill"></i> Desmarcar Presença';
                    
                    // Incrementar contador
                    if (badgePresencas) {
                        const currentCount = parseInt(badgePresencas.textContent.match(/\d+/)[0]);
                        badgePresencas.innerHTML = '<i class="bi bi-people-fill"></i> ' + (currentCount + 1) + ' pessoa(s) confirmaram presença';
                    }
                } else {
                    btn.setAttribute('data-action', 'marcar');
                    btn.innerHTML = '<i class="bi bi-check-circle"></i> Marcar Presença';
                    
                    // Decrementar contador
                    if (badgePresencas) {
                        const currentCount = parseInt(badgePresencas.textContent.match(/\d+/)[0]);
                        badgePresencas.innerHTML = '<i class="bi bi-people-fill"></i> ' + Math.max(0, currentCount - 1) + ' pessoa(s) confirmaram presença';
                    }
                }
            } else {
                console.error('Erro na resposta:', data.message);
                alert('Erro ao atualizar presença. Tente novamente mais tarde.');
            }
        })
        .catch(error => {
            console.error('Erro na requisição:', error);
            alert('Erro ao atualizar presença. Tente novamente mais tarde.');
        })
        .finally(() => {
            // Esconder spinner e habilitar botão novamente
            if (spinner) spinner.classList.add('d-none');
            btn.removeAttribute('disabled');
        });
    }
});
