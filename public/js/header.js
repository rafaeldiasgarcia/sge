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
 * @version 2.1
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
             * Controle de clique para dropdowns
             *
             * Permite que o dropdown funcione tanto com hover quanto com clique.
             * Especialmente importante para o dropdown do perfil do usuário.
             */
            const dropdownToggle = dropdown.querySelector('[data-bs-toggle="dropdown"]');
            if (dropdownToggle) {
                dropdownToggle.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation(); // Previne que o clique vaze

                    const navbarCollapse = document.querySelector('.navbar-collapse');
                    const isMobile = window.innerWidth <= 991.98;
                    const menuIsOpen = navbarCollapse && navbarCollapse.classList.contains('show');

                    // Se estamos no mobile E o menu está aberto, apenas alterna entre dropdowns
                    if (isMobile && menuIsOpen) {
                        // Fechar outros dropdowns abertos antes de abrir este
                        document.querySelectorAll('.dropdown-menu.show').forEach(menu => {
                            if (menu !== dropdownMenu) {
                                menu.classList.remove('show');
                                // Também define display:none para compatibilidade com notifications.js
                                if (menu.classList.contains('notification-dropdown')) {
                                    menu.style.display = 'none';
                                }
                            }
                        });
                        // Sempre abre o dropdown clicado (não fecha se já estava aberto)
                        dropdownMenu.classList.add('show');
                        // Se for o dropdown de notificações, também define display:block
                        if (dropdownMenu.classList.contains('notification-dropdown')) {
                            dropdownMenu.style.display = 'block';
                        }
                    } else {
                        // Comportamento normal (desktop ou menu fechado): toggle
                        if (dropdownMenu.classList.contains('show')) {
                            dropdownMenu.classList.remove('show');
                        } else {
                            // Fechar outros dropdowns abertos antes de abrir este
                            document.querySelectorAll('.dropdown-menu.show').forEach(menu => {
                                if (menu !== dropdownMenu) {
                                    menu.classList.remove('show');
                                }
                            });
                            dropdownMenu.classList.add('show');
                        }
                    }
                }, false); // Usa bubble phase
            }
        });

        // Fechar todos os dropdowns ao clicar fora (uma única vez para todos)
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.nav-item.dropdown')) {
                document.querySelectorAll('.dropdown-menu.show').forEach(menu => {
                    menu.classList.remove('show');
                });
            }
        });
    }

    /**
     * Fecha popups de eventos quando o menu mobile é aberto
     *
     * Previne conflito de interação entre o menu mobile e popups de eventos
     * que podem estar visíveis por trás do menu.
     */
    function initMobileMenuProtection() {
        const navbarCollapse = document.querySelector('.navbar-collapse');
        const navbarToggler = document.querySelector('.navbar-toggler');
        
        if (navbarCollapse && navbarToggler) {
            // Listener para quando o collapse Bootstrap é mostrado
            navbarCollapse.addEventListener('show.bs.collapse', function() {
                // Fecha popup de evento se estiver aberto
                const eventPopup = document.querySelector('.event-popup-overlay');
                if (eventPopup && eventPopup.classList.contains('active')) {
                    eventPopup.classList.remove('active');
                }
            });

            // Previne que cliques nos links normais do menu mobile vazem para elementos abaixo
            // MAS NÃO afeta os toggles de dropdown
            const menuLinks = navbarCollapse.querySelectorAll('a.nav-link:not(.dropdown-toggle), a.dropdown-item');
            menuLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    e.stopPropagation();
                }, true);
            });

            // Fecha o menu ao clicar fora dele (mas não ao clicar em dropdowns internos)
            document.addEventListener('click', function(e) {
                // Se o menu está aberto
                if (navbarCollapse.classList.contains('show')) {
                    const isMobile = window.innerWidth <= 991.98;
                    
                    // Verifica se o clique foi em um dropdown interno (notificações ou perfil)
                    const isDropdownClick = e.target.closest('.nav-item.dropdown');
                    
                    // Se não clicou no menu, nem no toggler, nem em dropdown interno, fecha o menu
                    if (!navbarCollapse.contains(e.target) && 
                        !navbarToggler.contains(e.target) && 
                        !isDropdownClick) {
                        navbarToggler.click(); // Fecha o menu
                    }
                }
            });
        }
    }

    /**
     * Inicializa todas as funcionalidades do header
     *
     * Esta função é chamada quando o DOM está completamente carregado
     * e inicializa todos os sistemas do header.
     */
    function initHeader() {
        initDropdownHover();
        initMobileMenuProtection();

        // Aqui podem ser adicionadas outras inicializações do header no futuro
        // As notificações agora são gerenciadas pelo arquivo notifications.js
    }

    // Executa a inicialização
    initHeader();

});
