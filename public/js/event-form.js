/**
 * Controle dos formulários de eventos (agendamento e edição)
 */
document.addEventListener('DOMContentLoaded', function() {
    // Elementos do formulário
    const tipoAgendamento = document.getElementById('tipo_agendamento');
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

    // Função para mostrar/esconder campos de materiais necessários
    function toggleCamposMateriais() {
        const naoTemMateriais = document.getElementById('materiais_nao').checked;

        if (camposSemMateriais) {
            camposSemMateriais.style.display = naoTemMateriais ? 'block' : 'none';

            if (materiaisNecessarios) {
                materiaisNecessarios.required = naoTemMateriais;
                if (!naoTemMateriais) {
                    materiaisNecessarios.value = '';
                }
            }

            if (responsabilizaDevolucao) {
                responsabilizaDevolucao.required = naoTemMateriais;
                if (!naoTemMateriais) {
                    responsabilizaDevolucao.checked = false;
                }
            }
        }
    }

    // Event listeners para radio buttons de materiais
    if (possuiMateriais.length > 0) {
        possuiMateriais.forEach(function(radio) {
            radio.addEventListener('change', toggleCamposMateriais);
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
                camposEsportivos.style.display = 'block';
                camposNaoEsportivos.style.display = 'none';
                if (subtipo1) {
                    subtipo1.required = true;
                    document.getElementById('esporte_tipo').required = true;
                    document.getElementById('lista_participantes').required = true;
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
                camposEsportivos.style.display = 'none';
                camposNaoEsportivos.style.display = 'block';
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
                    subtipo1.required = false;
                    document.getElementById('esporte_tipo').required = false;
                    document.getElementById('lista_participantes').required = false;
                }
                if (estimativaEsp) {
                    estimativaEsp.required = false;
                    estimativaEsp.disabled = true;
                }
            } else {
                camposEsportivos.style.display = 'none';
                camposNaoEsportivos.style.display = 'none';
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

    // Event listener para o formulário
    const form = document.getElementById('agendamentoForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            const tipoSelecionado = tipoAgendamento ? tipoAgendamento.value : '';
            const dataAgendamento = document.getElementById('data_agendamento');
            const periodo = document.getElementById('periodo');

            // VALIDAÇÃO 1: Verificar se data e período foram selecionados
            if (!dataAgendamento || !dataAgendamento.value || !periodo || !periodo.value) {
                e.preventDefault();
                alert('⚠️ Por favor, selecione uma data e horário no calendário antes de enviar a solicitação.\n\nRole a página para o topo e clique em um horário disponível no calendário.');
                window.scrollTo({ top: 0, behavior: 'smooth' });
                return false;
            }

            // VALIDAÇÃO 2: Validar campo outro tipo quando necessário
            if (tipoSelecionado === 'nao_esportivo' &&
                subtipoEventoNaoEsp && subtipoEventoNaoEsp.value === 'outro' &&
                outroTipoInput && !outroTipoInput.value.trim()) {
                e.preventDefault();
                alert('Por favor, especifique qual será o tipo do evento.');
                outroTipoInput.focus();
                return false;
            }

            // VALIDAÇÃO 3: Validar materiais necessários quando não possui materiais
            if (tipoSelecionado === 'esportivo') {
                const materiasSim = document.getElementById('materiais_sim');
                const materiasNao = document.getElementById('materiais_nao');

                // Verificar se alguma opção de materiais foi selecionada
                if (!materiasSim.checked && !materiaisNao.checked) {
                    e.preventDefault();
                    alert('Por favor, informe se você possui materiais esportivos.');
                    window.scrollTo({
                        top: document.getElementById('materiais_sim').offsetTop - 100,
                        behavior: 'smooth'
                    });
                    return false;
                }

                // Se não tem materiais, validar os campos relacionados
                if (materiaisNao && materiaisNao.checked) {
                    if (!materiaisNecessarios || !materiaisNecessarios.value.trim()) {
                        e.preventDefault();
                        alert('Por favor, descreva os materiais que serão necessários.');
                        if (materiaisNecessarios) {
                            materiaisNecessarios.focus();
                            materiaisNecessarios.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        }
                        return false;
                    }

                    if (!responsabilizaDevolucao || !responsabilizaDevolucao.checked) {
                        e.preventDefault();
                        alert('Você precisa se responsabilizar pela devolução dos materiais.');
                        if (responsabilizaDevolucao) {
                            responsabilizaDevolucao.focus();
                            responsabilizaDevolucao.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        }
                        return false;
                    }
                }
            }

            // Se chegou aqui, todas validações passaram - permitir envio
            return true;
        });
    }
});
