/**
 * Header JavaScript - Sistema de Gestão de Eventos UNIFIO
 *
 * Este arquivo contém toda a funcionalidade JavaScript relacionada ao cabeçalho da aplicação.
 * Inclui controle de dropdowns, animações e interações dos menus de navegação.
 *
 * Funcionalidades:
 * - Controle de dropdowns via hover (mouse enter/leave)
 * - Prevenção de conflitos com o Bootstrap
 * - Animações suaves nos menus
 *
 * @author Sistema SGE UNIFIO
 * @version 1.0
 */

document.addEventListener('DOMContentLoaded', function() {

    /**
     * Inicializa o sistema de controle de dropdowns do header
     *
     * Os dropdowns padrão do Bootstrap funcionam apenas com clique,
     * mas aqui implementamos funcionalidade hover para melhor UX.
     *
     * Comportamento:
     * - Mouse entra na área do dropdown → Menu aparece
     * - Mouse sai da área completa → Menu desaparece suavemente
     * - Delay de 100ms para evitar flickering ao mover o mouse
     */
    function initDropdownHover() {
        // Seleciona todos os elementos dropdown no header
        const dropdowns = document.querySelectorAll('.nav-item.dropdown');

        dropdowns.forEach(dropdown => {
            const dropdownMenu = dropdown.querySelector('.dropdown-menu');
            let hoverTimeout;

            // Verifica se o dropdown menu existe antes de adicionar eventos
            if (!dropdownMenu) return;

            /**
             * Event Handler: Mouse entra no dropdown
             *
             * Quando o usuário passa o mouse sobre o item dropdown,
             * cancela qualquer timeout pendente e mostra o menu imediatamente.
             */
            dropdown.addEventListener('mouseenter', function() {
                clearTimeout(hoverTimeout);
                dropdownMenu.classList.add('show');
            });

            /**
             * Event Handler: Mouse sai do dropdown
             *
             * Quando o usuário tira o mouse do dropdown,
             * inicia um timer para fechar o menu após um pequeno delay.
             * Isso evita que o menu feche acidentalmente ao mover o mouse.
             */
            dropdown.addEventListener('mouseleave', function() {
                hoverTimeout = setTimeout(() => {
                    dropdownMenu.classList.remove('show');
                }, 100); // 100ms de delay para suavizar a experiência
            });

            /**
             * Previne interferência do Bootstrap
             *
             * O Bootstrap por padrão controla dropdowns via clique.
             * Este código previne o comportamento padrão para que
             * nosso sistema hover funcione corretamente.
             */
            const dropdownToggle = dropdown.querySelector('[data-bs-toggle="dropdown"]');
            if (dropdownToggle) {
                dropdownToggle.addEventListener('click', function(e) {
                    e.preventDefault();
                });
            }
        });
    }

    /**
     * Inicializa todas as funcionalidades do header
     *
     * Esta função é chamada quando o DOM está completamente carregado
     * e inicializa todos os sistemas do header.
     */
    function initHeader() {
        initDropdownHover();

        // Aqui podem ser adicionadas outras inicializações do header no futuro
        // Por exemplo: inicialização de notificações, search, etc.
    }

    // Executa a inicialização
    initHeader();

});
