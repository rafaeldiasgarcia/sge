/**
 * Calendário Interativo de Agendamentos
 * Versão Bootstrap com dropdown de horários
 *
 * Implementa um calendário dinâmico para seleção de datas e períodos
 * de agendamento da quadra poliesportiva usando dropdowns do Bootstrap.
 *
 * Funcionalidades:
 * - Seleção visual de data e período via dropdown
 * - Navegação entre meses via AJAX (sem recarregar página)
 * - Indicadores visuais de disponibilidade
 * - Validação de datas passadas (não permite seleção)
 * - Sincronização com campos hidden do formulário
 * - Feedback visual da seleção atual
 * - Otimizado para mobile
 *
 * @version 2.0 - Bootstrap Dropdown
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

        // Lógica para os itens do dropdown (slots)
        document.querySelectorAll('.slot-item').forEach(item => {
            item.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();

                // Verificar se o item está desabilitado
                if (item.classList.contains('disabled')) {
                    console.log('Horário desabilitado - não pode selecionar');
                    return false;
                }

                // Verificar se tem o atributo onclick="return false"
                if (item.getAttribute('onclick') === 'return false;') {
                    console.log('Horário bloqueado - não pode selecionar');
                    return false;
                }

                // Verificação adicional no lado do cliente para datas inválidas
                const dateStr = item.dataset.date;
                if (dateStr) {
                    const eventDate = new Date(dateStr);
                    const today = new Date();
                    today.setHours(0, 0, 0, 0);
                    eventDate.setHours(0, 0, 0, 0);
                    
                    // Verifica se a data já passou
                    if (eventDate < today) {
                        console.log('Data já passou - não pode selecionar');
                        return false;
                    }
                    
                    const diffTime = eventDate.getTime() - today.getTime();
                    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
                    
                    // Verificar se é campeonato para aplicar regras especiais
                    const tipoAgendamento = document.getElementById('tipo_agendamento');
                    const subtipoEvento = document.getElementById('subtipo_evento');
                    const isCampeonato = tipoAgendamento && tipoAgendamento.value === 'esportivo' && 
                                       subtipoEvento && subtipoEvento.value === 'campeonato';
                    
                    // Para campeonatos: SEM NENHUMA restrição de data (exceto datas passadas)
                    if (!isCampeonato) {
                        if (diffDays < 4) {
                            console.log('Antecedência insuficiente - não pode selecionar');
                            return false;
                        }
                        
                        if (diffDays > 30) {
                            console.log('Data muito distante - não pode selecionar');
                            return false;
                        }
                    }
                }

                console.log('Horário selecionado com sucesso!');

                // Remover seleção anterior
                document.querySelectorAll('.slot-item.selected').forEach(i => i.classList.remove('selected'));
                document.querySelectorAll('.calendar-day-btn.selected').forEach(b => b.classList.remove('selected'));

                // Adicionar seleção ao item do dropdown
                item.classList.add('selected');

                // Adicionar seleção visual ao botão do dia
                const dayWrapper = item.closest('.calendar-day-wrapper');
                if (dayWrapper) {
                    const dayBtn = dayWrapper.querySelector('.calendar-day-btn');
                    if (dayBtn) {
                        dayBtn.classList.add('selected');
                        dayBtn.textContent = item.dataset.periodo === 'P1' ? '19:15-20:55' : '21:10-22:50';
                    }
                }

                // Atualizar campos hidden
                if (dataInput) dataInput.value = item.dataset.date;
                if (perInput) perInput.value = (item.dataset.periodo === 'P1' ? 'primeiro' : 'segundo');

                // Atualizar label de horário selecionado
                if (labelSel) {
                    const [year, month, day] = item.dataset.date.split('-');
                    const dataFormatada = `${day}/${month}/${year}`;
                    labelSel.textContent = dataFormatada + ' • ' +
                        (item.dataset.periodo === 'P1' ? '19:15 às 20:55' : '21:10 às 22:50');
                }

                // Habilitar botão de envio
                if (salvar) salvar.disabled = false;

                return false;
            });
        });

        // Lógica para os botões de navegação de mês (AJAX)
        document.querySelectorAll('.nav-cal').forEach(btn => {
            btn.addEventListener('click', async () => {
                const mes = btn.dataset.mes;
                calendarContainer.style.opacity = '0.5';

                // Verificar se é campeonato para passar o parâmetro correto
                const tipoField = document.getElementById('tipo_agendamento');
                const subtipoField = document.getElementById('subtipo_evento');
                const isCampeonato = tipoField && tipoField.value === 'esportivo' && 
                                   subtipoField && subtipoField.value === 'campeonato';

                try {
                    const response = await fetch(`/calendario-partial?mes=${mes}&is_campeonato=${isCampeonato}`);
                    const html = await response.text();
                    calendarContainer.innerHTML = html;
                } catch (e) {
                    calendarContainer.innerHTML = '<div class="alert alert-danger">Erro ao carregar o calendário.</div>';
                } finally {
                    calendarContainer.style.opacity = '1';
                    initializeCalendarLogic();
                }
            });
        });
    };

    // Chama a função pela primeira vez para a carga inicial da página
    initializeCalendarLogic();
    
    // Adicionar listeners para atualizar disponibilidade quando tipo/subtipo mudarem
    const tipoAgendamento = document.getElementById('tipo_agendamento');
    let subtipoEvento = document.getElementById('subtipo_evento');
    
    function updateSlotAvailability() {
        // Re-buscar os campos caso tenham sido criados dinamicamente
        const tipoField = document.getElementById('tipo_agendamento');
        const subtipoField = document.getElementById('subtipo_evento');
        
        const isCampeonato = tipoField && tipoField.value === 'esportivo' && 
                           subtipoField && subtipoField.value === 'campeonato';
        
        if (isCampeonato) {
            setTimeout(() => {
                aplicarRegrasCampeonato();
            }, 100);
        } else {
            restaurarRegrasNormais();
        }
    }
    
    function recarregarCalendarioNormal() {
        const calendarContainer = document.getElementById('calendar-wrapper');
        if (!calendarContainer) return;
        
        const mesAtual = calendarContainer.querySelector('.fw-bold')?.textContent;
        if (!mesAtual) return;
        
        const partes = mesAtual.split(' de ');
        if (partes.length !== 2) return;
        
        const meses = {
            'janeiro': '01', 'fevereiro': '02', 'março': '03', 'abril': '04',
            'maio': '05', 'junho': '06', 'julho': '07', 'agosto': '08',
            'setembro': '09', 'outubro': '10', 'novembro': '11', 'dezembro': '12'
        };
        
        const mesNome = partes[0].toLowerCase();
        const ano = partes[1];
        const mesNumero = meses[mesNome];
        
        if (!mesNumero) return;
        
        const mesParam = `${ano}-${mesNumero}`;
        
        calendarContainer.style.opacity = '0.5';
        
        fetch(`/calendario-partial?mes=${mesParam}&is_campeonato=false`)
            .then(response => response.text())
            .then(html => {
                calendarContainer.innerHTML = html;
                calendarContainer.style.opacity = '1';
                initializeCalendarLogic();
            })
            .catch(e => {
                calendarContainer.innerHTML = '<div class="alert alert-danger">Erro ao carregar o calendário.</div>';
                calendarContainer.style.opacity = '1';
            });
    }
    
    function recarregarCalendarioParaCampeonato() {
        const calendarContainer = document.getElementById('calendar-wrapper');
        if (!calendarContainer) return;
        
        const mesAtual = calendarContainer.querySelector('.fw-bold')?.textContent;
        if (!mesAtual) return;
        
        const partes = mesAtual.split(' de ');
        if (partes.length !== 2) return;
        
        const meses = {
            'janeiro': '01', 'fevereiro': '02', 'março': '03', 'abril': '04',
            'maio': '05', 'junho': '06', 'julho': '07', 'agosto': '08',
            'setembro': '09', 'outubro': '10', 'novembro': '11', 'dezembro': '12'
        };
        
        const mesNome = partes[0].toLowerCase();
        const ano = partes[1];
        const mesNumero = meses[mesNome];
        
        if (!mesNumero) return;
        
        const mesParam = `${ano}-${mesNumero}`;
        
        calendarContainer.style.opacity = '0.5';
        
        fetch(`/calendario-partial?mes=${mesParam}&is_campeonato=true`)
            .then(response => response.text())
            .then(html => {
                calendarContainer.innerHTML = html;
                calendarContainer.style.opacity = '1';
                aplicarRegrasCampeonato();
                initializeCalendarLogic();
            })
            .catch(e => {
                calendarContainer.innerHTML = '<div class="alert alert-danger">Erro ao carregar o calendário.</div>';
                calendarContainer.style.opacity = '1';
            });
    }
    
    function aplicarRegrasCampeonato() {
        const today = new Date();
        today.setHours(0, 0, 0, 0);
        
        // Atualizar mensagem do cabeçalho
        const regraAntecedencia = document.getElementById('regra-antecedencia');
        const regraCampeonato = document.getElementById('regra-campeonato');
        
        if (regraAntecedencia) regraAntecedencia.style.display = 'none';
        if (regraCampeonato) regraCampeonato.style.display = 'inline';
        
        // Aplicar regras para todos os dias
        document.querySelectorAll('.calendar-day-wrapper').forEach(wrapper => {
            const dateStr = wrapper.dataset.date;
            if (dateStr) {
                const eventDate = new Date(dateStr);
                eventDate.setHours(0, 0, 0, 0);
                
                const dayBtn = wrapper.querySelector('.calendar-day-btn');
                const slotItems = wrapper.querySelectorAll('.slot-item');
                const badge = wrapper.querySelector('.calendar-badge');

                if (eventDate < today) {
                    // Datas passadas: manter desabilitado
                    if (dayBtn) {
                        dayBtn.disabled = true;
                        dayBtn.classList.add('disabled');
                    }
                } else {
                    // Datas futuras: sempre permitir
                    if (dayBtn) {
                        dayBtn.disabled = false;
                        dayBtn.classList.remove('disabled');
                        dayBtn.classList.add('campeonato-forced');
                    }

                    // Habilitar todos os slots
                    slotItems.forEach(item => {
                        item.classList.remove('disabled');
                        item.removeAttribute('aria-disabled');
                    });

                    // Forçar badge verde
                    if (badge) {
                        badge.className = 'badge bg-success calendar-badge';
                    }

                    // Remover classe past-date
                    wrapper.classList.remove('past-date');
                }
            }
        });
    }
    
    function restaurarRegrasNormais() {
        // Restaurar mensagem do cabeçalho
        const regraAntecedencia = document.getElementById('regra-antecedencia');
        const regraCampeonato = document.getElementById('regra-campeonato');
        
        if (regraAntecedencia) regraAntecedencia.style.display = 'inline';
        if (regraCampeonato) regraCampeonato.style.display = 'none';
        
        // Limpar classes temporárias
        document.querySelectorAll('.calendar-day-btn').forEach(btn => {
            btn.classList.remove('campeonato-forced');
        });

        // Recarregar calendário para restaurar estado normal
        recarregarCalendarioNormal();
    }
    
    if (tipoAgendamento) {
        tipoAgendamento.addEventListener('change', function() {
            setTimeout(() => {
                updateSlotAvailability();
            }, 100);
        });
    }

    if (subtipoEvento) {
        subtipoEvento.addEventListener('change', function() {
            updateSlotAvailability();
        });
    }
    
    // Expor função para uso externo
    window.updateSlotAvailability = updateSlotAvailability;
});