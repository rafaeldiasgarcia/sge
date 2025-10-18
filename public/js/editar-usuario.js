document.addEventListener('DOMContentLoaded', function() {
    var statusRadios = document.querySelectorAll('input[name="atletica_join_status"]');
    var vinculoDetalhadoSelect = document.querySelector('select[name="tipo_usuario_detalhado"]');
    if (!statusRadios.length || !vinculoDetalhadoSelect) return;

    function atualizarVinculoDetalhado(statusValue) {
        if (statusValue === 'aprovado') {
            vinculoDetalhadoSelect.value = 'Membro das Atléticas';
        } else if (statusValue === 'none' || statusValue === 'pendente') {
            vinculoDetalhadoSelect.value = 'Aluno';
        }
    }

    statusRadios.forEach(function(radio) {
        if (radio.checked) atualizarVinculoDetalhado(radio.value);
        radio.addEventListener('change', function() { atualizarVinculoDetalhado(this.value); });
    });
});


