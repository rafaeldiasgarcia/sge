/**
 * ============================================================================
 * DASHBOARD CALENDAR - JAVASCRIPT ESPECÍFICO
 * ============================================================================
 * 
 * JavaScript específico para o calendário do segundo container do dashboard.
 * Implementa funcionalidades de navegação, interatividade e modal de eventos.
 * 
 * Funcionalidades:
 * - Navegação entre meses
 * - Interatividade dos dias
 * - Modal de detalhes de eventos
 * - Atualização de estatísticas
 * - Integração com dados PHP
 * 
 * @version 1.0
 */

document.addEventListener('DOMContentLoaded', function() {
    // ========================================
    // ELEMENTOS DO CALENDÁRIO
    // ========================================
    
    const prevBtn = document.getElementById('calendarPrevMonth');
    const nextBtn = document.getElementById('calendarNextMonth');
    const monthElement = document.getElementById('calendarCurrentMonth');
    const yearElement = document.getElementById('calendarCurrentYear');
    const calendarGrid = document.getElementById('calendarGridWidget');
    const eventModal = document.getElementById('calendarEventModal');
    const modalTitle = document.getElementById('calendarModalTitle');
    const modalBody = document.getElementById('calendarModalBody');
    const closeModal = document.getElementById('calendarCloseModal');
    
    // Verificar se os elementos existem (apenas no slide do calendário)
    if (!prevBtn || !nextBtn || !monthElement || !yearElement || !calendarGrid) {
        return; // Sair se não estiver no slide do calendário
    }
    
    // ========================================
    // CONFIGURAÇÃO INICIAL
    // ========================================
    
    const today = new Date();
    
    // Obter mês atual da URL ou usar o mês atual
    const urlParams = new URLSearchParams(window.location.search);
    const mesParam = urlParams.get('mes');
    let currentMonth, currentYear;
    
    if (mesParam) {
        const [year, month] = mesParam.split('-');
        currentYear = parseInt(year);
        currentMonth = parseInt(month) - 1; // JavaScript usa 0-11 para meses
    } else {
        currentMonth = today.getMonth();
        currentYear = today.getFullYear();
    }
    
    // Array com nomes dos meses em português
    const months = [
        'JANEIRO', 'FEVEREIRO', 'MARÇO', 'ABRIL', 'MAIO', 'JUNHO', 
        'JULHO', 'AGOSTO', 'SETEMBRO', 'OUTUBRO', 'NOVEMBRO', 'DEZEMBRO'
    ];
    
    // ========================================
    // DADOS DE EVENTOS (BASEADOS NO PHP)
    // ========================================
    
    // Obter dados dos eventos do PHP via data attributes
    const eventosData = window.todosEventos || [];
    
    // Converter dados PHP para formato JavaScript
    const eventsData = {};
    eventosData.forEach(evento => {
        const dataEvento = new Date(evento.data_agendamento);
        const year = dataEvento.getFullYear();
        const month = dataEvento.getMonth();
        const day = dataEvento.getDate();
        
        if (!eventsData[year]) eventsData[year] = {};
        if (!eventsData[year][month]) eventsData[year][month] = {};
        
        eventsData[year][month][day] = {
            type: evento.tipo_agendamento === 'esportivo' ? 'green' : 'blue',
            title: evento.titulo,
            description: `${evento.responsavel} - ${evento.horario_periodo}`,
            esporte: evento.esporte_tipo || 'Evento'
        };
    });
    
    // ========================================
    // FUNÇÕES DO CALENDÁRIO
    // ========================================
    
    /**
     * Inicializa o calendário
     */
    function initCalendar() {
        // Não chamar updateCalendar() na inicialização para evitar loops
        updateStats();
    }
    
    /**
     * Atualiza a exibição do calendário via AJAX
     */
function updateCalendarAjax() {
    // Atualizar cabeçalho
    monthElement.textContent = months[currentMonth];
    yearElement.textContent = currentYear;

    // Fazer requisição AJAX para obter o calendário do novo mês
    const mesParam = `${currentYear}-${String(currentMonth + 1).padStart(2, '0')}`;

    fetch(`/agendamento/calendar-partial?mes=${mesParam}`)
        .then(response => response.text())
        .then(html => {
            // Atualizar apenas o grid do calendário
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            const newGrid = doc.querySelector('.calendar-grid');

            if (newGrid) {
                calendarGrid.innerHTML = newGrid.innerHTML;

                // Re-adicionar event listeners aos novos dias
                addEventListenersToDays();
            }
        })
        .catch(error => {
            console.error('Erro ao carregar calendário:', error);
        });

    // Atualizar contadores via AJAX
    updateCalendarCounters(mesParam);
}

function updateCalendarCounters(mesParam) {
    fetch(`/agendamento/calendar-stats?mes=${mesParam}`)
        .then(response => response.json())
        .then(data => {
            // Atualizar contadores
            const totalEventsEl = document.getElementById('calendarTotalEvents');
            const availableDaysEl = document.getElementById('calendarAvailableDays');
            const busyDaysEl = document.getElementById('calendarBusyDays');

            if (totalEventsEl) totalEventsEl.textContent = data.totalEventos;
            if (availableDaysEl) availableDaysEl.textContent = data.diasLivres;
            if (busyDaysEl) busyDaysEl.textContent = data.diasOcupados;
        })
        .catch(error => {
            console.error('Erro ao carregar estatísticas:', error);
        });
}
    
    /**
     * Mostra detalhes dos eventos do dia em modal
     */
    function showEventDetails(day, eventData) {
        const dataFormatada = `${day} de ${months[currentMonth]} de ${currentYear}`;
        modalTitle.textContent = dataFormatada;
        
        if (eventData && eventData.length > 0) {
            let eventosHtml = '';
            
            eventData.forEach((evento, index) => {
                const tipoEvento = evento.tipo_agendamento === 'esportivo' ? 'Esportivo' : 'Não Esportivo';
                const horario = evento.horario_periodo || 'Horário não definido';
                const esporte = evento.esporte_tipo || 'N/A';
                
                eventosHtml += `
                    <div class="evento-item" style="border-left: 4px solid ${evento.tipo_agendamento === 'esportivo' ? '#10b981' : '#3b82f6'}; padding: 15px; margin-bottom: 15px; background: #f8fafc; border-radius: 8px;">
                        <h4 style="margin: 0 0 10px 0; color: #1f2937; font-size: 16px;">${evento.titulo}</h4>
                        <p style="margin: 5px 0; color: #6b7280;"><strong>Tipo:</strong> ${tipoEvento}</p>
                        <p style="margin: 5px 0; color: #6b7280;"><strong>Horário:</strong> ${horario}</p>
                        <p style="margin: 5px 0; color: #6b7280;"><strong>Esporte:</strong> ${esporte}</p>
                        <p style="margin: 5px 0; color: #6b7280;"><strong>Responsável:</strong> ${evento.responsavel}</p>
                    </div>
                `;
            });
            
            modalBody.innerHTML = `
                <div class="event-details">
                    <p style="margin-bottom: 20px; color: #374151; font-weight: 600;">
                        ${eventData.length} evento(s) agendado(s) para este dia:
                    </p>
                    ${eventosHtml}
                </div>
            `;
        } else {
            modalBody.innerHTML = `
                <div class="event-details">
                    <p style="margin-bottom: 10px; color: #374151;">Nenhum evento agendado para este dia.</p>
                    <p style="color: #6b7280;">Dia disponível para reservas.</p>
                </div>
            `;
        }
        
        eventModal.classList.add('active');
    }
    
    /**
     * Obtém nome amigável do tipo de evento
     */
    function getEventTypeName(type) {
        const types = {
            'red': 'Dia Não Disponível',
            'orange': 'Período Livre',
            'green': 'Dia Livre',
            'blue': 'Evento Não Esportivo'
        };
        return types[type] || 'Indisponível';
    }
    
    /**
     * Atualiza estatísticas do calendário
     */
    function updateStats() {
        const monthEvents = eventsData[currentYear]?.[currentMonth] || {};
        const totalEvents = Object.keys(monthEvents).length;
        const availableDays = Object.values(monthEvents).filter(event => event.type === 'green').length;
        const busyDays = Object.values(monthEvents).filter(event => event.type === 'red').length;
        
        // Atualizar elementos se existirem
        const totalEventsEl = document.getElementById('calendarTotalEvents');
        const availableDaysEl = document.getElementById('calendarAvailableDays');
        const busyDaysEl = document.getElementById('calendarBusyDays');
        
        if (totalEventsEl) totalEventsEl.textContent = totalEvents;
        if (availableDaysEl) availableDaysEl.textContent = availableDays;
        if (busyDaysEl) busyDaysEl.textContent = busyDays;
    }
    
    // ========================================
    // EVENT LISTENERS
    // ========================================
    
    // Navegação para mês anterior
    if (prevBtn) {
        prevBtn.addEventListener('click', () => {
            currentMonth = currentMonth === 0 ? 11 : currentMonth - 1;
            if (currentMonth === 11) currentYear--;
            
            // Atualizar calendário via AJAX
            updateCalendarAjax();
        });
    }
    
    // Navegação para próximo mês
    if (nextBtn) {
        nextBtn.addEventListener('click', () => {
            currentMonth = currentMonth === 11 ? 0 : currentMonth + 1;
            if (currentMonth === 0) currentYear++;
            
            // Atualizar calendário via AJAX
            updateCalendarAjax();
        });
    }
    
    // Fechar modal
    if (closeModal) {
        closeModal.addEventListener('click', () => {
            eventModal.classList.remove('active');
        });
    }
    
    // Fechar modal clicando fora
    if (eventModal) {
        eventModal.addEventListener('click', (e) => {
            if (e.target === eventModal) {
                eventModal.classList.remove('active');
            }
        });
    }
    
    // ========================================
    // INICIALIZAÇÃO
    // ========================================
    
    // Inicializar calendário (apenas navegação)
    initCalendar();
    
    // Atualizar contadores na inicialização
    const mesAtual = new Date();
    const mesParamInicial = `${mesAtual.getFullYear()}-${String(mesAtual.getMonth() + 1).padStart(2, '0')}`;
    updateCalendarCounters(mesParamInicial);
    
    /**
     * Adiciona event listeners aos dias do calendário
     */
    function addEventListenersToDays() {
        const calendarDates = document.querySelectorAll('.calendar-date[data-eventos]');
        calendarDates.forEach(dateElement => {
            dateElement.addEventListener('click', () => {
                const day = dateElement.querySelector('.calendar-day-number').textContent;
                const eventosDoDia = JSON.parse(dateElement.getAttribute('data-eventos') || '[]');
                showEventDetails(day, eventosDoDia);
            });
        });
    }
    
    // Adicionar event listeners aos dias do calendário gerado pelo PHP
    addEventListenersToDays();
});
