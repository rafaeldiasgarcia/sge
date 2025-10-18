document.addEventListener('DOMContentLoaded', function() {
    // Selecionar os elementos
    const statusRadios = document.querySelectorAll('input[name="atletica_join_status"]');
    const vinculoDetalhadoSelect = document.querySelector('select[name="tipo_usuario_detalhado"]');

    // Função para atualizar o vínculo detalhado baseado no status
    function atualizarVinculoDetalhado(statusValue) {
        if (statusValue === 'aprovado') {
            vinculoDetalhadoSelect.value = 'Membro das Atléticas';
        } else if (statusValue === 'none' || statusValue === 'pendente') {
            vinculoDetalhadoSelect.value = 'Aluno';
        }
    }

    // Sincronizar o campo ao carregar a página com o status atual
    statusRadios.forEach(radio => {
        if (radio.checked) {
            atualizarVinculoDetalhado(radio.value);
        }
    });

    // Adicionar listener para cada radio button de status
    statusRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            atualizarVinculoDetalhado(this.value);
        });
    });
});