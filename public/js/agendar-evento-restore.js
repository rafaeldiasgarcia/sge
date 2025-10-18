document.addEventListener('DOMContentLoaded', function() {
    var hasFormData = !!(window.__hasFormDataAgendarEvento);
    if (!hasFormData) return;

    setTimeout(function() {
        var tipoAgendamento = document.getElementById('tipo_agendamento');
        if (tipoAgendamento && tipoAgendamento.value) {
            tipoAgendamento.dispatchEvent(new Event('change'));

            if (tipoAgendamento.value === 'nao_esportivo') {
                var subtipoNaoEsp = document.getElementById('subtipo_evento_nao_esp');
                if (subtipoNaoEsp && subtipoNaoEsp.value) {
                    subtipoNaoEsp.dispatchEvent(new Event('change'));
                }
            }

            if (tipoAgendamento.value === 'esportivo') {
                var possuiMateriais = document.getElementsByName('possui_materiais');
                Array.prototype.forEach.call(possuiMateriais, function(radio) {
                    if (radio.checked) {
                        radio.dispatchEvent(new Event('change'));
                    }
                });
            }

            if (tipoAgendamento.value === 'nao_esportivo') {
                var eventoAberto = document.getElementsByName('evento_aberto_publico');
                Array.prototype.forEach.call(eventoAberto, function(radio) {
                    if (radio.checked) {
                        radio.dispatchEvent(new Event('change'));
                    }
                });
            }
        }

        var dataAgendamento = document.getElementById('data_agendamento');
        var periodo = document.getElementById('periodo');
        if (dataAgendamento && dataAgendamento.value && periodo && periodo.value) {
            var selecionado = document.getElementById('selecionado');
            if (selecionado) {
                var data = new Date(dataAgendamento.value);
                var dataFormatada = data.toLocaleDateString('pt-BR');
                var periodoTexto = periodo.value === 'primeiro' ? 'Primeiro Período' : 'Segundo Período';
                selecionado.textContent = dataFormatada + ' - ' + periodoTexto;
            }
            if (typeof window.selectCalendarSlot === 'function') {
                window.selectCalendarSlot(dataAgendamento.value, periodo.value);
            }
        }
    }, 100);
});


