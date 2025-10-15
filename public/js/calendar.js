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
                    
                    // Verificar se é campeonato para aplicar regras especiais
                    const tipoAgendamento = document.getElementById('tipo_agendamento');
                    const subtipoEvento = document.getElementById('subtipo_evento');
                    const isCampeonato = tipoAgendamento && tipoAgendamento.value === 'esportivo' && 
                                       subtipoEvento && subtipoEvento.value === 'campeonato';
                    
                    // Para campeonatos: SEM NENHUMA restrição de data (exceto datas passadas)
                    // Para outros eventos: aplicar restrições normais
                    if (!isCampeonato) {
                        if (diffDays < 4) {
                            return; // Não permite seleção de datas com menos de 4 dias de antecedência
                        }
                        
                        if (diffDays > 30) {
                            return; // Não permite seleção de datas com mais de 1 mês de antecedência
                        }
                    }
                    // Se for campeonato, não aplica NENHUMA restrição de data (exceto datas passadas)
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

                // Scroll removido - usuário não quer que a página role automaticamente
            });
        });

        // REQUISITO 2: Lógica para os botões de navegação de mês (AJAX)
        document.querySelectorAll('.nav-cal').forEach(btn => {
            btn.addEventListener('click', async () => {
                const mes = btn.dataset.mes;
                calendarContainer.style.opacity = '0.5'; // Feedback visual de carregamento

                // Verificar se é campeonato para passar o parâmetro correto
                const tipoField = document.getElementById('tipo_agendamento');
                const subtipoField = document.getElementById('subtipo_evento');
                const isCampeonato = tipoField && tipoField.value === 'esportivo' && 
                                   subtipoField && subtipoField.value === 'campeonato';

                try {
                    // O caminho da requisição AJAX agora é absoluto, sem subpastas.
                    const response = await fetch(`/calendario-partial?mes=${mes}&is_campeonato=${isCampeonato}`);
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
            // Para campeonatos: aplicar regras especiais diretamente no calendário atual
            setTimeout(() => {
                aplicarRegrasCampeonato();
            }, 100); // Pequeno delay para garantir que o DOM foi atualizado
        } else {
            // Para outros tipos, restaurar comportamento normal
            restaurarRegrasNormais();
        }
    }
    
    function recarregarCalendarioNormal() {
        const calendarContainer = document.getElementById('calendar-wrapper');
        if (!calendarContainer) return;
        
        // Obter mês atual do calendário (escopado ao container do calendário)
        const mesAtual = calendarContainer.querySelector('.fw-semibold')?.textContent;
        if (!mesAtual) return;
        
        // Extrair mês e ano (assumindo formato "janeiro de 2024")
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
        
        // Recarregar calendário via AJAX
        calendarContainer.style.opacity = '0.5';
        
        fetch(`/calendario-partial?mes=${mesParam}&is_campeonato=false`)
            .then(response => response.text())
            .then(html => {
                calendarContainer.innerHTML = html;
                calendarContainer.style.opacity = '1';
                
                // Re-inicializar a lógica para o novo HTML
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
        
        // Obter mês atual do calendário (escopado ao container do calendário)
        const mesAtual = calendarContainer.querySelector('.fw-semibold')?.textContent;
        if (!mesAtual) return;
        
        // Extrair mês e ano (assumindo formato "janeiro de 2024")
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
        
        // Recarregar calendário via AJAX
        calendarContainer.style.opacity = '0.5';
        
        fetch(`/calendario-partial?mes=${mesParam}&is_campeonato=true`)
            .then(response => response.text())
            .then(html => {
                calendarContainer.innerHTML = html;
                calendarContainer.style.opacity = '1';
                
                // Aplicar regras especiais de campeonato após carregar
                aplicarRegrasCampeonato();
                
                // Re-inicializar a lógica para o novo HTML
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
        
        // Aplicar regras para todos os slots
        document.querySelectorAll('.slot').forEach(btn => {
            const dateStr = btn.dataset.date;
            if (dateStr) {
                const eventDate = new Date(dateStr);
                eventDate.setHours(0, 0, 0, 0);
                
                if (eventDate < today) {
                    // Para datas passadas: manter cores originais mas desabilitar
                    btn.disabled = true;
                    btn.classList.add('disabled');
                    btn.style.opacity = '0.5';
                } else {
                    // Para datas futuras: sempre permitir seleção (mesmo ocupado)
                    btn.disabled = false;
                    btn.classList.remove('disabled');
                    // Forçar visual verde para campeonatos sem alterar classes originais
                    btn.classList.add('campeonato-forced');
                    btn.style.backgroundColor = '#198754';
                    btn.style.color = '#fff';
                    btn.style.opacity = '1';
                    // Remover atributo disabled se existir
                    btn.removeAttribute('disabled');
                    // Forçar remoção de qualquer classe que impeça clique
                    btn.style.pointerEvents = 'auto';
                    btn.style.cursor = 'pointer';
                }
            }
        });
        
        // Atualizar badges de disponibilidade - não substituir classes definitivas
        document.querySelectorAll('.calendar-cell').forEach(cell => {
            const dateStr = cell.querySelector('.slot')?.dataset.date;
            if (dateStr) {
                const eventDate = new Date(dateStr);
                eventDate.setHours(0, 0, 0, 0);
                
                const badge = cell.querySelector('.badge');
                if (badge) {
                    if (eventDate < today) {
                        // Para datas passadas, manter cores originais mas com overlay
                        badge.style.opacity = '0.5';
                    } else {
                        // Para datas futuras, forçar visual verde temporário
                        badge.classList.add('campeonato-forced');
                        badge.style.backgroundColor = '#198754';
                        badge.style.color = '#fff';
                        badge.style.opacity = '1';
                    }
                }
                
                // Remover classe past-date para datas futuras em campeonatos
                if (eventDate >= today) {
                    cell.classList.remove('past-date');
                    // Limpar estilos CSS que podem estar aplicados
                    cell.style.opacity = '';
                    cell.style.filter = '';
                    cell.style.pointerEvents = '';
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
        
        // Limpar estilos e classes temporárias aplicadas pelos campeonatos
        document.querySelectorAll('.slot').forEach(btn => {
            btn.style.opacity = '';
            btn.style.backgroundColor = '';
            btn.style.color = '';
            btn.style.pointerEvents = '';
            btn.style.cursor = '';
            btn.classList.remove('campeonato-forced');
            // Não remover o disabled aqui, pois pode ser necessário para slots ocupados
        });
        
        document.querySelectorAll('.badge').forEach(badge => {
            badge.style.opacity = '';
            badge.style.backgroundColor = '';
            badge.style.color = '';
            badge.classList.remove('campeonato-forced');
        });

        // Aplicar imediatamente a regra visual de antecedência padrão (fallback instantâneo)
        try {
            const today = new Date();
            today.setHours(0, 0, 0, 0);
            document.querySelectorAll('.calendar-cell').forEach(cell => {
                const slot = cell.querySelector('.slot');
                const badge = cell.querySelector('.badge');
                const dateStr = slot?.dataset.date;
                if (!dateStr) return;
                const eventDate = new Date(dateStr);
                eventDate.setHours(0, 0, 0, 0);

                const diffTime = eventDate.getTime() - today.getTime();
                const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
                const isPast = eventDate < today;
                const insufficientAdvance = (!isPast && (diffDays < 4 || diffDays > 30));

                if (isPast || insufficientAdvance) {
                    cell.classList.add('past-date');
                    if (badge) badge.style.opacity = '0.5';
                    cell.querySelectorAll('.slot').forEach(b => {
                        b.setAttribute('disabled', 'disabled');
                        b.classList.add('disabled');
                        b.style.backgroundColor = ''; // deixa classes do servidor determinarem a cor
                        b.style.color = '';
                    });
                }
            });
        } catch (_) {}
        
        // Recarregar o calendário para restaurar o comportamento normal
        recarregarCalendarioNormal();
    }
    
    if (tipoAgendamento) {
        tipoAgendamento.addEventListener('change', function() {
            // Aguardar um pouco para o campo subtipo aparecer
            setTimeout(() => {
                updateSlotAvailability();
            }, 100);
        });
    }

    // Listener imediato para o subtipo se já estiver presente no DOM
    if (subtipoEvento) {
        subtipoEvento.addEventListener('change', function() {
            updateSlotAvailability();
        });
    }
    
    // Usar MutationObserver para detectar quando o campo subtipo aparece
    const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.type === 'attributes' && mutation.attributeName === 'style') {
                const target = mutation.target;
                if (target.id === 'subtipo_wrapper' && target.style.display !== 'none') {
                    const subtipoField = document.getElementById('subtipo_evento');
                    if (subtipoField) {
                        subtipoField.addEventListener('change', function() {
                            updateSlotAvailability();
                        });
                    }
                }
            }
        });
    });
    
    // Observar mudanças no wrapper do subtipo
    const subtipoWrapper = document.getElementById('subtipo_wrapper');
    if (subtipoWrapper) {
        observer.observe(subtipoWrapper, { attributes: true });
    }
    
    // Executar na carga inicial para aplicar regras corretas
    updateSlotAvailability();
    
    // Tornar a função global para ser chamada de outros scripts
    window.updateSlotAvailability = updateSlotAvailability;
    
});