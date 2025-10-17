document.addEventListener('DOMContentLoaded', function() {
    const historicoCollapse = document.getElementById('historicoSolicitacoes');
    if (historicoCollapse) {
        historicoCollapse.addEventListener('show.bs.collapse', function() {
            const header = document.querySelector('[data-bs-target="#historicoSolicitacoes"]');
            if (header) {
                header.setAttribute('aria-expanded', 'true');
            }
        });
        
        historicoCollapse.addEventListener('hide.bs.collapse', function() {
            const header = document.querySelector('[data-bs-target="#historicoSolicitacoes"]');
            if (header) {
                header.setAttribute('aria-expanded', 'false');
            }
        });
    }
});