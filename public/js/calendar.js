/**
 * Calendário Interativo de Agendamentos
 * 
 * Implementa um calendário dinâmico para seleção de datas e períodos
 * de agendamento da quadra poliesportiva.
 * 
 * Funcionalidades:
 * - Seleção visual de data e período (primeiro/segundo)
 * - Navegação entre meses via AJAX (sem recarregar página)
 * - Indicadores visuais de disponibilidade:
 *   * Verde: Horário disponível
 *   * Vermelho: Horário ocupado
 *   * Cinza: Data passada (desabilitado)
 * - Validação de datas passadas (não permite seleção)
 * - Sincronização com campos hidden do formulário
 * - Feedback visual da seleção atual
 * 
 * Períodos:
 * - Primeiro: 19:15 - 20:55
 * - Segundo: 21:10 - 22:50
 * 
 * Integração:
 * - Endpoint AJAX: /calendario-partial
 * - Campos do formulário: data_agendamento, periodo
 * - Botão de envio habilitado apenas após seleção completa
 * 
 * @version 1.0
 */
document.addEventListener("DOMContentLoaded", () => {
    const calendarContainer = document.getElementById('calendar-wrapper');
    if (!calendarContainer) return;

    // Função principal que anexa todos os eventos ao calendário
    const initializeCalendarLogic = () => {
        const dataInput = document.getElementById('data_agendamento');
        const perInput = document.getElementById('periodo');
        const labelSel = document.getElementById('selecionado');
        const salvar = document.getElementById('btnEnviarSolicitacao');
        const formFields = document.getElementById('form-fields-wrapper');
        
        // Se já há valores (página de edição), habilitar o botão
        if (salvar && dataInput && perInput && dataInput.value && perInput.value) {
            salvar.disabled = false;
        }

        // Lógica para os botões de período (slots)
        document.querySelectorAll('.slot').forEach(btn => {
            btn.addEventListener('click', () => {
                if (btn.disabled || btn.classList.contains('disabled')) return;
                
                // Verificação adicional no lado do cliente para datas inválidas
                const dateStr = btn.dataset.date;
                if (dateStr) {
                    const eventDate = new Date(dateStr);
                    const today = new Date();
                    today.setHours(0, 0, 0, 0); // Zera as horas para comparação precisa
                    eventDate.setHours(0, 0, 0, 0);
                    
                    // Verifica se a data já passou
                    if (eventDate < today) {
                        return; // Não permite seleção de datas passadas
                    }
                    
                    const diffTime = eventDate.getTime() - today.getTime();
                    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
                    
                    if (diffDays < 4) {
                        return; // Não permite seleção de datas com menos de 4 dias de antecedência
                    }
                }

                document.querySelectorAll('.slot.selected').forEach(b => b.classList.remove('selected'));
                btn.classList.add('selected');

                if (dataInput) dataInput.value = btn.dataset.date;
                if (perInput) perInput.value = (btn.dataset.periodo === 'P1' ? 'primeiro' : 'segundo');

                if (labelSel) {
                    const [year, month, day] = btn.dataset.date.split('-');
                    const dataFormatada = `${day}/${month}/${year}`;
                    labelSel.textContent = dataFormatada + ' • ' + (btn.dataset.periodo === 'P1' ? '19:15 às 20:55' : '21:10 às 22:50');
                }

                if (salvar) salvar.disabled = false;

                // REQUISITO 3: Rolar a página para baixo até o formulário
                if (formFields) {
                    formFields.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            });
        });

        // REQUISITO 2: Lógica para os botões de navegação de mês (AJAX)
        document.querySelectorAll('.nav-cal').forEach(btn => {
            btn.addEventListener('click', async () => {
                const mes = btn.dataset.mes;
                calendarContainer.style.opacity = '0.5'; // Feedback visual de carregamento

                try {
                    // O caminho da requisição AJAX agora é absoluto, sem subpastas.
                    const response = await fetch(`/calendario-partial?mes=${mes}`);
                    const html = await response.text();
                    calendarContainer.innerHTML = html;
                } catch (e) {
                    calendarContainer.innerHTML = '<div class="alert alert-danger">Erro ao carregar o calendário.</div>';
                } finally {
                    calendarContainer.style.opacity = '1';
                    // Re-inicializa a lógica para o novo HTML que foi carregado
                    initializeCalendarLogic();
                }
            });
        });
    };

    // Chama a função pela primeira vez para a carga inicial da página
    initializeCalendarLogic();
});