/**
 * Controle Dinâmico do Formulário de Agendamento
 * 
 * Gerencia a exibição condicional de campos do formulário de agendamento
 * baseado nas escolhas do usuário.
 * 
 * Funcionalidades:
 * - Mostrar/ocultar campos conforme tipo de evento (esportivo/não-esportivo)
 * - Mostrar/ocultar campos de materiais conforme necessidade
 * - Validação de campos condicionais
 * - Controle de campos "Outro" personalizados
 * - Feedback visual das seleções
 * 
 * Lógica Condicional:
 * - Se Esportivo: mostra subtipo, esporte, participantes
 * - Se Não Esportivo: mostra subtipo alternativo, público alvo
 * - Se Possui Materiais: mostra lista de materiais e responsabilização
 * - Se Evento Aberto ao Público: mostra descrição do público alvo
 * - Se subtipo "Outro": mostra campo de texto para especificar
 * 
 * @version 1.0
 */
document.addEventListener('DOMContentLoaded', function() {
    // Elementos do formulário
    const tipoAgendamento = document.getElementById('tipo_agendamento');
    const subtipoWrapper = document.getElementById('subtipo_wrapper');
    const subtipoEvento = document.getElementById('subtipo_evento');
    const camposEsportivos = document.getElementById('campos_esportivos');
    const camposNaoEsportivos = document.getElementById('campos_nao_esportivos');
    const possuiMateriais = document.getElementsByName('possui_materiais');
    const camposSemMateriais = document.getElementById('campos_sem_materiais');
    const materiaisNecessarios = document.getElementById('materiais_necessarios');
    const responsabilizaDevolucao = document.getElementById('responsabiliza_devolucao');
    const eventoAberto = document.getElementsByName('evento_aberto_publico');
    const campoPublicoAlvo = document.getElementById('campo_publico_alvo');
    const subtipoEventoNaoEsp = document.getElementById('subtipo_evento_nao_esp');
    const campoOutroTipo = document.getElementById('campo_outro_tipo');
    const outroTipoInput = document.getElementById('outro_tipo_evento');

    // Utilitário: habilita/desabilita todos os campos dentro de um container
    function setDisabledForContainer(containerEl, disabled) {
        if (!containerEl) return;
        containerEl.querySelectorAll('input, select, textarea').forEach(function(el) {
            el.disabled = !!disabled;
        });
    }

    // Detectar se estamos na página de edição (hidden input id presente)
    const isEditMode = !!document.querySelector('#agendamentoForm input[name="id"]');

    // Travar campo e ainda submeter valor (espelha em hidden)
    function lockAndMirrorField(fieldEl) {
        if (!fieldEl || fieldEl.dataset.locked === '1') return;
        const hidden = document.createElement('input');
        hidden.type = 'hidden';
        hidden.name = fieldEl.name;
        // Se o select estiver sem valor, tenta usar a option marcada como selected no HTML
        if ((fieldEl.tagName === 'SELECT') && (!fieldEl.value || fieldEl.value === '')) {
            const optSelected = fieldEl.querySelector('option[selected]');
            if (optSelected && optSelected.value) {
                fieldEl.value = optSelected.value;
            }
        }
        // usa o valor atual ou o valor da opção selecionada
        hidden.value = fieldEl.value || (fieldEl.selectedOptions && fieldEl.selectedOptions[0] ? fieldEl.selectedOptions[0].value : '');
        fieldEl.parentNode.insertBefore(hidden, fieldEl.nextSibling);
        fieldEl.disabled = true;
        fieldEl.dataset.locked = '1';
        fieldEl.title = 'Campo bloqueado na edição';
        const observer = new MutationObserver(function() {
            hidden.value = fieldEl.value || (fieldEl.selectedOptions && fieldEl.selectedOptions[0] ? fieldEl.selectedOptions[0].value : '');
        });
        observer.observe(fieldEl, { attributes: true, attributeFilter: ['value'] });
    }

    // Função para mostrar/esconder campos de materiais necessários
    function toggleCamposMateriais() {
        const naoEl = document.getElementById('materiais_nao');
        const naoTemMateriais = !!(naoEl && naoEl.checked);

        if (camposSemMateriais) {
            camposSemMateriais.style.display = naoTemMateriais ? 'block' : 'none';

            if (materiaisNecessarios) {
                materiaisNecessarios.required = naoTemMateriais;
                materiaisNecessarios.disabled = !naoTemMateriais; // evita required em campo oculto
                if (!naoTemMateriais) {
                    materiaisNecessarios.value = '';
                }
            }

            if (responsabilizaDevolucao) {
                responsabilizaDevolucao.required = naoTemMateriais;
                responsabilizaDevolucao.disabled = !naoTemMateriais; // evita required em campo oculto
                if (!naoTemMateriais) {
                    responsabilizaDevolucao.checked = false;
                }
            }
        }
    }

    // Event listeners para radio buttons de materiais
    if (possuiMateriais && typeof possuiMateriais.forEach === 'function' && possuiMateriais.length > 0) {
        possuiMateriais.forEach(function(radio) {
            if (radio && radio.addEventListener) {
                radio.addEventListener('change', toggleCamposMateriais);
            }
        });
        // Executar na carga inicial
        toggleCamposMateriais();
    }

    // Função para mostrar/esconder campos do tipo "outro"
    function toggleOutroTipo() {
        const isOutro = subtipoEventoNaoEsp.value === 'outro';
        campoOutroTipo.style.display = isOutro ? 'block' : 'none';

        if (isOutro) {
            outroTipoInput.required = true;
            outroTipoInput.focus();
        } else {
            outroTipoInput.required = false;
            outroTipoInput.value = '';
        }
    }

    // Event listeners
    if (subtipoEventoNaoEsp) {
        subtipoEventoNaoEsp.addEventListener('change', toggleOutroTipo);
        // Executar na carga inicial para caso já esteja selecionado "outro"
        toggleOutroTipo();
    }

    // Mostrar campos corretos baseado no tipo de agendamento
    if (tipoAgendamento) {
        tipoAgendamento.addEventListener('change', function() {
            const subtipo1 = document.getElementById('subtipo_evento');
            const subtipo2 = document.getElementById('subtipo_evento_nao_esp');
            const estimativaEsp = document.getElementById('estimativa_participantes_esp');
            const estimativaNaoEsp = document.getElementById('estimativa_participantes_nao_esp');

            if (this.value === 'esportivo') {
                // Mostrar campo de subtipo para eventos esportivos
                if (subtipoWrapper) {
                    subtipoWrapper.style.display = 'block';
                }
                if (subtipo1) {
                    subtipo1.required = true;
                    // Adicionar listener para o subtipo quando ele aparecer
                    subtipo1.addEventListener('change', function() {
                        console.log('Subtipo mudou no event-form.js');
                        // Chamar função do calendar.js se existir
                        if (typeof window.updateSlotAvailability === 'function') {
                            window.updateSlotAvailability();
                        }
                    });
                }
                
                camposEsportivos.style.display = 'block';
                camposNaoEsportivos.style.display = 'none';
                setDisabledForContainer(camposEsportivos, false);
                setDisabledForContainer(camposNaoEsportivos, true);
                
                if (subtipo1) {
                    const esporteTipoEl = document.getElementById('esporte_tipo');
                    const listaEl = document.getElementById('lista_participantes');
                    if (esporteTipoEl) { esporteTipoEl.required = true; esporteTipoEl.disabled = false; }
                    if (listaEl) { listaEl.required = true; listaEl.disabled = false; }
                }
                if (estimativaEsp) {
                    estimativaEsp.required = true;
                    estimativaEsp.disabled = false;
                }
                if (subtipo2) {
                    subtipo2.required = false;
                    if (outroTipoInput) outroTipoInput.required = false;
                }
                if (estimativaNaoEsp) {
                    estimativaNaoEsp.required = false;
                    estimativaNaoEsp.disabled = true;
                }
            } else if (this.value === 'nao_esportivo') {
                // Ocultar campo de subtipo para eventos não esportivos
                if (subtipoWrapper) {
                    subtipoWrapper.style.display = 'none';
                }
                if (subtipo1) {
                    subtipo1.required = false;
                }
                
                camposEsportivos.style.display = 'none';
                camposNaoEsportivos.style.display = 'block';
                setDisabledForContainer(camposEsportivos, true);
                setDisabledForContainer(camposNaoEsportivos, false);
                
                if (subtipo2) {
                    subtipo2.required = true;
                    // Verificar se precisa ativar o campo "outro"
                    toggleOutroTipo();
                }
                if (estimativaNaoEsp) {
                    estimativaNaoEsp.required = true;
                    estimativaNaoEsp.disabled = false;
                }
                if (subtipo1) {
                    const esporteTipoEl = document.getElementById('esporte_tipo');
                    const listaEl = document.getElementById('lista_participantes');
                    if (esporteTipoEl) { esporteTipoEl.required = false; esporteTipoEl.disabled = true; }
                    if (listaEl) { listaEl.required = false; listaEl.disabled = true; }
                }
                if (estimativaEsp) {
                    estimativaEsp.required = false;
                    estimativaEsp.disabled = true;
                }
            } else {
                // Nenhum tipo selecionado
                if (subtipoWrapper) {
                    subtipoWrapper.style.display = 'none';
                }
                if (subtipo1) {
                    subtipo1.required = false;
                }
                
                camposEsportivos.style.display = 'none';
                camposNaoEsportivos.style.display = 'none';
                setDisabledForContainer(camposEsportivos, true);
                setDisabledForContainer(camposNaoEsportivos, true);
            }

            // Sempre solicitar atualização de disponibilidade do calendário
            if (typeof window.updateSlotAvailability === 'function') {
                window.updateSlotAvailability();
            }
        });

        // Trigger inicial para mostrar os campos corretos
        tipoAgendamento.dispatchEvent(new Event('change'));
        
        // Inicialização na carga da página para desabilitar campos não usados
        const estimativaEsp = document.getElementById('estimativa_participantes_esp');
        const estimativaNaoEsp = document.getElementById('estimativa_participantes_nao_esp');
        
        if (tipoAgendamento.value === 'esportivo') {
            if (estimativaEsp) {
                estimativaEsp.required = true;
                estimativaEsp.disabled = false;
            }
            if (estimativaNaoEsp) {
                estimativaNaoEsp.required = false;
                estimativaNaoEsp.disabled = true;
            }
        } else if (tipoAgendamento.value === 'nao_esportivo') {
            if (estimativaEsp) {
                estimativaEsp.required = false;
                estimativaEsp.disabled = true;
            }
            if (estimativaNaoEsp) {
                estimativaNaoEsp.required = true;
                estimativaNaoEsp.disabled = false;
            }
        }
    }

    // Aplicar regras de bloqueio para EDIÇÃO (UI)
    if (isEditMode) {
        const tipoAtual = tipoAgendamento ? tipoAgendamento.value : '';
        if (tipoAtual === 'nao_esportivo') {
            // Bloquear categoria e tipo não esportivo
            lockAndMirrorField(tipoAgendamento);
            const tipoNaoEsp = document.getElementById('subtipo_evento_nao_esp');
            lockAndMirrorField(tipoNaoEsp);
            // Se o subtipo for 'outro', bloquear também a descrição
            if (tipoNaoEsp && tipoNaoEsp.value === 'outro') {
                lockAndMirrorField(outroTipoInput);
            }
        } else if (tipoAtual === 'esportivo') {
            // Bloquear categoria, subtipo esportivo e esporte
            lockAndMirrorField(tipoAgendamento);
            const subtipoEsp = document.getElementById('subtipo_evento');
            const esporteTipo = document.getElementById('esporte_tipo');
            lockAndMirrorField(subtipoEsp);
            lockAndMirrorField(esporteTipo);
        }
    }

    // Event listener para o formulário
    const form = document.getElementById('agendamentoForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            try {
                const tipoSelecionado = tipoAgendamento ? tipoAgendamento.value : '';
                const dataAgendamento = document.getElementById('data_agendamento');
                const periodo = document.getElementById('periodo');
                const titulo = document.getElementById('titulo');
                const esporteTipoEl = document.getElementById('esporte_tipo');

                // VALIDAÇÃO 1: Verificar se data e período foram selecionados
                if (!dataAgendamento || !dataAgendamento.value || !periodo || !periodo.value) {
                    console.error('[Agendamento][Submit Blocked] Data/Período ausentes', {
                        hasDataField: !!dataAgendamento,
                        dataValue: dataAgendamento && dataAgendamento.value,
                        hasPeriodoField: !!periodo,
                        periodoValue: periodo && periodo.value
                    });
                    e.preventDefault();
                    alert('⚠️ Por favor, selecione uma data e horário no calendário antes de enviar a solicitação.\n\nRole a página para o topo e clique em um horário disponível no calendário.');
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                    return false;
                }

                // VALIDAÇÃO 1.1: Campos básicos
                if (!titulo || !titulo.value.trim()) {
                    console.error('[Agendamento][Submit Blocked] Título vazio');
                    e.preventDefault();
                    alert('Por favor, informe o título do evento.');
                    if (titulo) titulo.focus();
                    return false;
                }
                if (!tipoSelecionado) {
                    console.error('[Agendamento][Submit Blocked] Tipo de agendamento não selecionado');
                    e.preventDefault();
                    alert('Por favor, selecione a categoria do evento (Esportivo ou Não Esportivo).');
                    if (tipoAgendamento) tipoAgendamento.focus();
                    return false;
                }

                // VALIDAÇÃO 2: Validar campo outro tipo quando necessário
                if (tipoSelecionado === 'nao_esportivo' &&
                    subtipoEventoNaoEsp && subtipoEventoNaoEsp.value === 'outro' &&
                    outroTipoInput && !outroTipoInput.value.trim()) {
                    console.error('[Agendamento][Submit Blocked] Tipo "Outro" sem descrição', {
                        tipoSelecionado,
                        subtipo: subtipoEventoNaoEsp && subtipoEventoNaoEsp.value
                    });
                    e.preventDefault();
                    alert('Por favor, especifique qual será o tipo do evento.');
                    outroTipoInput.focus();
                    return false;
                }

                // VALIDAÇÃO 3: Validar materiais necessários quando não possui materiais
                if (tipoSelecionado === 'esportivo') {
                    // Regras específicas de esportivo
                    if (subtipoEvento && !subtipoEvento.disabled && !subtipoEvento.value) {
                        console.error('[Agendamento][Submit Blocked] Subtipo esportivo não selecionado');
                        e.preventDefault();
                        alert('Por favor, selecione o tipo do evento esportivo (Treino/Campeonato).');
                        subtipoEvento.focus();
                        return false;
                    }
                    if (esporteTipoEl && !esporteTipoEl.disabled && !esporteTipoEl.value) {
                        console.error('[Agendamento][Submit Blocked] Esporte não selecionado');
                        e.preventDefault();
                        alert('Por favor, selecione o esporte.');
                        esporteTipoEl.focus();
                        return false;
                    }

                    const materiasSim = document.getElementById('materiais_sim');
                    const materiasNao = document.getElementById('materiais_nao');

                    // Verificar se alguma opção de materiais foi selecionada
                    if ((materiasSim && !materiasSim.checked) && (materiasNao && !materiasNao.checked)) {
                        console.error('[Agendamento][Submit Blocked] Materiais não selecionados');
                        e.preventDefault();
                        alert('Por favor, informe se você possui materiais esportivos.');
                        window.scrollTo({
                            top: (materiasSim ? materiasSim.offsetTop : 0) - 100,
                            behavior: 'smooth'
                        });
                        return false;
                    }

                    // Se não tem materiais, validar os campos relacionados
                    if (materiasNao && materiasNao.checked) {
                        if (!materiaisNecessarios || !materiaisNecessarios.value.trim()) {
                            console.error('[Agendamento][Submit Blocked] Materiais necessários vazio');
                            e.preventDefault();
                            alert('Por favor, descreva os materiais que serão necessários.');
                            if (materiaisNecessarios) {
                                materiaisNecessarios.focus();
                                materiaisNecessarios.scrollIntoView({ behavior: 'smooth', block: 'center' });
                            }
                            return false;
                        }

                        if (!responsabilizaDevolucao || !responsabilizaDevolucao.checked) {
                            console.error('[Agendamento][Submit Blocked] Termo de responsabilização não marcado');
                            e.preventDefault();
                            alert('Você precisa se responsabilizar pela devolução dos materiais.');
                            if (responsabilizaDevolucao) {
                                responsabilizaDevolucao.focus();
                                responsabilizaDevolucao.scrollIntoView({ behavior: 'smooth', block: 'center' });
                            }
                            return false;
                        }
                    }
                } else if (tipoSelecionado === 'nao_esportivo') {
                    if (subtipoEventoNaoEsp && !subtipoEventoNaoEsp.disabled && !subtipoEventoNaoEsp.value) {
                        console.error('[Agendamento][Submit Blocked] Subtipo não esportivo não selecionado');
                        e.preventDefault();
                        alert('Por favor, selecione o tipo do evento não esportivo.');
                        subtipoEventoNaoEsp.focus();
                        return false;
                    }
                }

                // Se chegou aqui, todas validações passaram - permitir envio
                console.log('[Agendamento][Submit] Enviando formulário de edição', {
                    tipoSelecionado,
                    data: dataAgendamento && dataAgendamento.value,
                    periodo: periodo && periodo.value
                });
                return true;
            } catch (err) {
                console.error('[Agendamento][Submit Error] Erro inesperado ao tentar enviar', err);
                e.preventDefault();
                alert('Ocorreu um erro inesperado ao validar o formulário. Tente novamente.');
                return false;
            }
        });
    }
});
