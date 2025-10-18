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
    let currentMonth = today.getMonth();
    let currentYear = today.getFullYear();
    
    // Array com nomes dos meses em português
    const months = [
        'JANEIRO', 'FEVEREIRO', 'MARÇO', 'ABRIL', 'MAIO', 'JUNHO', 
        'JULHO', 'AGOSTO', 'SETEMBRO', 'OUTUBRO', 'NOVEMBRO', 'DEZEMBRO'
    ];
    
    // ========================================
    // DADOS DE EVENTOS (BASEADOS NO PHP)
    // ========================================
    
    // Obter dados dos eventos do PHP via data attributes
    const eventosData = window.eventosPresenca || [];
    
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
        updateCalendar();
        updateStats();
    }
    
    /**
     * Atualiza a exibição do calendário
     */
    function updateCalendar() {
        // Atualizar cabeçalho
        monthElement.textContent = months[currentMonth];
        yearElement.textContent = currentYear;
        
        // Limpar grid (manter apenas os dias da semana)
        const existingDates = calendarGrid.querySelectorAll('.calendar-date');
        existingDates.forEach(date => date.remove());
        
        // Obter primeiro dia do mês e número de dias
        const firstDay = new Date(currentYear, currentMonth, 1).getDay();
        const daysInMonth = new Date(currentYear, currentMonth + 1, 0).getDate();
        const today = new Date();
        const isCurrentMonth = currentYear === today.getFullYear() && currentMonth === today.getMonth();
        
        // Adicionar dias vazios no início
        for (let i = 0; i < firstDay; i++) {
            const emptyDay = document.createElement('div');
            emptyDay.className = 'calendar-date empty';
            calendarGrid.appendChild(emptyDay);
        }
        
        // Adicionar dias do mês
        for (let day = 1; day <= daysInMonth; day++) {
            const dayElement = document.createElement('div');
            dayElement.className = 'calendar-date';
            
            // Criar estrutura interna
            const dayNumber = document.createElement('div');
            dayNumber.className = 'calendar-day-number';
            dayNumber.textContent = day;
            dayElement.appendChild(dayNumber);
            
            // Verificar se é hoje
            if (isCurrentMonth && day === today.getDate()) {
                dayElement.classList.add('today');
            }
            
            // Verificar se tem evento
            const event = eventsData[currentYear]?.[currentMonth]?.[day];
            if (event) {
                dayElement.classList.add(`date-${event.type}`);
                dayElement.classList.add('has-event');
                dayElement.setAttribute('data-event', JSON.stringify(event));
                dayElement.setAttribute('title', event.title);
                
                // Adicionar badge de evento
                const badge = document.createElement('div');
                badge.className = 'calendar-day-badge';
                const badgeSpan = document.createElement('span');
                badgeSpan.className = 'badge bg-primary';
                badgeSpan.textContent = '●';
                badge.appendChild(badgeSpan);
                dayElement.appendChild(badge);
            } else {
                dayElement.setAttribute('title', `${day} de ${months[currentMonth]} - Dia disponível`);
            }
            
            // Adicionar evento de clique
            dayElement.addEventListener('click', () => showEventDetails(day, event));
            
            calendarGrid.appendChild(dayElement);
        }
    }
    
    /**
     * Mostra detalhes do evento em modal
     */
    function showEventDetails(day, event) {
        if (event) {
            modalTitle.textContent = `${day} de ${months[currentMonth]} - ${event.title}`;
            modalBody.innerHTML = `
                <div class="event-details">
                    <p><strong>Descrição:</strong> ${event.description}</p>
                    <p><strong>Tipo:</strong> ${getEventTypeName(event.type)}</p>
                    <p><strong>Esporte:</strong> ${event.esporte}</p>
                    <p><strong>Data:</strong> ${day} de ${months[currentMonth]} de ${currentYear}</p>
                </div>
            `;
        } else {
            modalTitle.textContent = `${day} de ${months[currentMonth]}`;
            modalBody.innerHTML = `
                <div class="event-details">
                    <p>Nenhum evento agendado para este dia.</p>
                    <p>Dia disponível para reservas.</p>
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
            updateCalendar();
            updateStats();
        });
    }
    
    // Navegação para próximo mês
    if (nextBtn) {
        nextBtn.addEventListener('click', () => {
            currentMonth = currentMonth === 11 ? 0 : currentMonth + 1;
            if (currentMonth === 0) currentYear++;
            updateCalendar();
            updateStats();
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
    
    // Inicializar calendário
    initCalendar();
});
