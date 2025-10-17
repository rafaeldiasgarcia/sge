// Garantir que o popup funcione nas linhas clicáveis desta página
document.addEventListener('DOMContentLoaded', function() {
    // Aguardar o eventPopup estar disponível
    if (typeof eventPopup !== 'undefined') {
        console.log('EventPopup já inicializado, apenas adicionando listeners');
    } else {
        console.warn('EventPopup ainda não foi carregado');
    }
});