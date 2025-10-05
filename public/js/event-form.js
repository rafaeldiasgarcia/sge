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
    const eventoAberto = document.getElementsByName('evento_aberto_publico');
    const campoPublicoAlvo = document.getElementById('campo_publico_alvo');
    const subtipoEventoNaoEsp = document.getElementById('subtipo_evento_nao_esp');
    const campoOutroTipo = document.getElementById('campo_outro_tipo');
    const outroTipoInput = document.getElementById('outro_tipo_evento');

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

            if (this.value === 'esportivo') {
                camposEsportivos.style.display = 'block';
                camposNaoEsportivos.style.display = 'none';
                if (subtipo1) {
                    subtipo1.required = true;
                    document.getElementById('esporte_tipo').required = true;
                    document.getElementById('lista_participantes').required = true;
                }
                if (subtipo2) {
                    subtipo2.required = false;
                    if (outroTipoInput) outroTipoInput.required = false;
                }
            } else if (this.value === 'nao_esportivo') {
                camposEsportivos.style.display = 'none';
                camposNaoEsportivos.style.display = 'block';
                if (subtipo2) {
                    subtipo2.required = true;
                    // Verificar se precisa ativar o campo "outro"
                    toggleOutroTipo();
                }
                if (subtipo1) {
                    subtipo1.required = false;
                    document.getElementById('esporte_tipo').required = false;
                    document.getElementById('lista_participantes').required = false;
                }
            } else {
                camposEsportivos.style.display = 'none';
                camposNaoEsportivos.style.display = 'none';
            }
        });

        // Trigger inicial para mostrar os campos corretos
        tipoAgendamento.dispatchEvent(new Event('change'));
    }

    // Event listener para o formulário
    const form = document.getElementById('agendamentoForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            const tipoSelecionado = tipoAgendamento.value;

            // Validar campo outro tipo quando necessário
            if (tipoSelecionado === 'nao_esportivo' &&
                subtipoEventoNaoEsp.value === 'outro' &&
                !outroTipoInput.value.trim()) {
                e.preventDefault();
                alert('Por favor, especifique qual será o tipo do evento.');
                outroTipoInput.focus();
                return;
            }
        });
    }
});
