/**
 * Sistema de Notificações em Tempo Real - Versão Refatorada
 * 
 * Implementa um sistema de notificações assíncronas usando AJAX e polling.
 * As notificações são atualizadas automaticamente sem necessidade de recarregar a página.
 * 
 * Funcionalidades:
 * - Polling automático a cada 30 segundos
 * - Badge laranja no sininho quando há notificações não lidas
 * - Menu ordenado por data (mais nova primeiro)
 * - Notificações não lidas destacadas visualmente
 * - Marcar como lida ao passar o mouse (hover)
 * - Persistência do estado de não lida até o hover
 * 
 * Tipos de Notificação Suportados:
 * - agendamento_aprovado: ✅ Seu agendamento foi aprovado
 * - agendamento_rejeitado: ❌ Agendamento rejeitado
 * - agendamento_cancelado: ⚠️ Evento cancelado
 * - presenca_confirmada: ✅ Presença confirmada
 * - lembrete_evento: 📅 Lembrete de evento
 * - info: ℹ️ Informação geral
 * - aviso: ⚠️ Aviso importante
 * 
 * Integração Backend:
 * - GET /notifications - Busca notificações
 * - POST /notifications/read - Marca como lida
 * 
 * @class SimpleNotifications
 * @version 3.0
 */

class SimpleNotifications {
    constructor() {
        this.bell = null;
        this.dropdown = null;
        this.list = null;
        this.markAllBtn = null;
        this.isOpen = false;
        this.notificationContainer = null; // Container que engloba o sino e o dropdown
        this.unreadCount = 0; // Contador de notificações não lidas
        this.hoveredNotifications = new Set(); // IDs das notificações que foram hovered

        this.init();
    }

    init() {
        // Buscar elementos
        this.bell = document.getElementById('notification-bell');
        this.dropdown = document.getElementById('notification-dropdown');
        this.list = document.getElementById('notification-list');
        // botão removido do template — mantemos compatibilidade caso exista
        this.markAllBtn = document.getElementById('mark-all-read');
        this.notificationContainer = document.querySelector('.notifications'); // li.nav-item.notifications

        if (!this.bell) {
            return;
        }

        // Não precisamos mais do badge - o ícone do sino mudará de cor

        // Configurar eventos
        this.setupEvents();

        // Buscar notificações imediatamente
        this.loadNotifications();

        // Verificar periodicamente (a cada 30 segundos)
        setInterval(() => {
            this.loadNotifications();
        }, 30000);
    }

    updateBellColor(count) {
        this.unreadCount = count;
        if (this.bell) {
            const bellIcon = this.bell.querySelector('i');
            if (bellIcon) {
                if (count > 0) {
                    // Adicionar classe para notificações não lidas
                    bellIcon.classList.add('has-notifications');
                } else {
                    // Remover classe quando não há notificações não lidas
                    bellIcon.classList.remove('has-notifications');
                }
            }
        }
    }

    setupEvents() {
        // Mouse entra na área das notificações (sino ou dropdown)
        if (this.notificationContainer) {
            this.notificationContainer.addEventListener('mouseenter', () => {
                this.openDropdown();
            });

            this.notificationContainer.addEventListener('mouseleave', () => {
                this.closeDropdown();
                // NÃO marcar como lida automaticamente - apenas ao hover individual
            });
        }

        // Clique no sino (manter para mobile/touch)
        this.bell.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation(); // Previne que o clique vaze
            this.toggleDropdown();
        });

        // O botão "Marcar todas como lidas" foi removido do template.
        // Em vez disso, marcamos todas como lidas quando o usuário abre/clica no sino (comportamento estilo Instagram).

        // Fechar ao clicar fora (mas não fechar quando clicar em outros dropdowns do menu mobile)
        document.addEventListener('click', (e) => {
            if (this.notificationContainer && !this.notificationContainer.contains(e.target)) {
                // Verifica se clicou em outro dropdown (ex: perfil)
                const isOtherDropdown = e.target.closest('.nav-item.dropdown');
                const navbarCollapse = document.querySelector('.navbar-collapse');
                const isMobile = window.innerWidth <= 991.98;
                const menuIsOpen = navbarCollapse && navbarCollapse.classList.contains('show');
                
                // Se estamos no mobile com menu aberto e clicamos em outro dropdown, não fecha
                if (isMobile && menuIsOpen && isOtherDropdown) {
                    return; // Deixa o sistema de dropdowns do header gerenciar
                }
                
                this.closeDropdown();
            }
        });

        // Sincronizar estado quando o dropdown é fechado externamente (pelo sistema do header)
        if (this.dropdown) {
            const observer = new MutationObserver((mutations) => {
                mutations.forEach((mutation) => {
                    if (mutation.attributeName === 'class') {
                        const hasShow = this.dropdown.classList.contains('show');
                        // Se o dropdown foi fechado externamente, atualizar nosso estado
                        if (!hasShow && this.isOpen) {
                            this.isOpen = false;
                        } else if (hasShow && !this.isOpen) {
                            this.isOpen = true;
                        }
                    }
                });
            });
            observer.observe(this.dropdown, { attributes: true });
        }

        // Reajustar posição ao redimensionar a janela
        window.addEventListener('resize', () => {
            if (this.isOpen) {
                this.adjustDropdownPosition();
            }
        });

        // Reajustar posição ao fazer scroll
        window.addEventListener('scroll', () => {
            if (this.isOpen) {
                this.adjustDropdownPosition();
            }
        }, true);
    }

    async loadNotifications() {
        try {
            const response = await fetch('/notifications');
            if (!response.ok) {
                throw new Error(`Erro HTTP: ${response.status}`);
            }

            const data = await response.json();

            if (data.success) {
                this.updateList(data.notifications);
                this.updateBellColor(data.unreadCount || 0);
            }
        } catch (error) {
            console.error('Erro ao carregar notificações:', error);
        }
    }

    updateList(notifications) {
        if (!this.list) return;

        if (notifications.length === 0) {
            this.list.innerHTML = '<div class="notification-empty">Nenhuma notificação</div>';
            return;
        }

        // As notificações já vêm ordenadas do backend por data_criacao DESC, id DESC
        // Não precisamos reordenar no frontend
        const sortedNotifications = notifications;

        let html = '';
        sortedNotifications.forEach(notification => {
            const isUnread = notification.lida == 0;
            const wasHovered = this.hoveredNotifications.has(notification.id);
            const shouldHighlight = isUnread && !wasHovered; // Destacar apenas se não lida E não foi hovered
            const timeAgo = this.getTimeAgo(notification.data_criacao);

            html += `
                <div class="notification-item ${shouldHighlight ? 'unread' : ''}" 
                     data-id="${notification.id}" 
                     data-unread="${isUnread ? 'true' : 'false'}">
                    <div class="notification-content">
                        <h6>${this.escapeHtml(notification.titulo)}</h6>
                        <p>${this.escapeHtml(notification.mensagem)}</p>
                        <small>${timeAgo}</small>
                    </div>
                    ${shouldHighlight ? '<span class="notification-indicator"></span>' : ''}
                </div>
            `;
        });

        this.list.innerHTML = html;

        // Adicionar eventos de hover para marcar como lida
        this.list.querySelectorAll('.notification-item[data-unread="true"]').forEach(item => {
            const notificationId = item.dataset.id;
            
            item.addEventListener('mouseenter', () => {
                // Marcar como hovered
                this.hoveredNotifications.add(notificationId);
                
                // Remover destaque visual
                item.classList.remove('unread');
                
                // Remover indicador visual
                const indicator = item.querySelector('.notification-indicator');
                if (indicator) {
                    indicator.remove();
                }
                
                // Marcar como lida no backend
                this.markAsRead(notificationId);
            });
        });
    }

    toggleDropdown() {
        if (this.isOpen) {
            this.closeDropdown();
        } else {
            this.openDropdown();
        }
    }

    openDropdown() {
        if (this.dropdown && !this.isOpen) {
            // Usar classe .show como o sistema de dropdowns do header
            this.dropdown.classList.add('show');
            this.dropdown.style.display = 'block';
            this.isOpen = true;
            
            // Ajustar posição para garantir que fique dentro da viewport
            this.adjustDropdownPosition();
            
            // NÃO marcar como lida ao abrir - apenas quando fechar
        }
    }

    adjustDropdownPosition() {
        if (!this.dropdown) return;

        // Resetar posicionamento customizado
        this.dropdown.style.left = '';
        this.dropdown.style.right = '';
        this.dropdown.style.transform = '';

        // Aguardar o próximo frame para obter dimensões corretas
        requestAnimationFrame(() => {
            const rect = this.dropdown.getBoundingClientRect();
            const viewportWidth = window.innerWidth;
            const viewportHeight = window.innerHeight;
            
            // Verificar se está saindo pela direita
            if (rect.right > viewportWidth) {
                const overflow = rect.right - viewportWidth;
                this.dropdown.style.transform = `translateX(-${overflow + 10}px)`;
            }
            
            // Verificar se está saindo pela esquerda
            if (rect.left < 0) {
                const overflow = Math.abs(rect.left);
                this.dropdown.style.transform = `translateX(${overflow + 10}px)`;
            }
            
            // Verificar se está saindo por baixo
            if (rect.bottom > viewportHeight) {
                const maxHeight = viewportHeight - rect.top - 20; // 20px de margem
                if (maxHeight > 200) { // Altura mínima razoável
                    this.dropdown.style.maxHeight = `${maxHeight}px`;
                }
            }
        });
    }

    closeDropdown() {
        if (this.dropdown && this.isOpen) {
            // Usar classe .show como o sistema de dropdowns do header
            this.dropdown.classList.remove('show');
            this.dropdown.style.display = 'none';
            this.isOpen = false;
            
            // NÃO marcar todas como lidas automaticamente
            // As notificações só são marcadas como lidas ao passar o mouse sobre elas
        }
    }

    async markAsRead(notificationId) {
        try {
            const response = await fetch('/notifications/read', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ notification_id: notificationId })
            });

            if (response.ok) {
                // Atualizar contador local
                if (this.unreadCount > 0) {
                    this.updateBellColor(this.unreadCount - 1);
                }
                
                // Recarregar notificações para sincronizar com o backend
                this.loadNotifications();
            }
        } catch (error) {
            console.error('Erro ao marcar como lida:', error);
        }
    }

    async markAllAsRead() {
        try {
            const response = await fetch('/notifications/read', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({})
            });

            if (response.ok) {
                this.loadNotifications();
            }
        } catch (error) {
            console.error('Erro ao marcar todas como lidas:', error);
        }
    }

    getTimeAgo(dateString) {
        const now = new Date();
        const date = new Date(dateString);
        const diffInSeconds = Math.floor((now - date) / 1000);

        if (diffInSeconds < 60) return 'Agora mesmo';
        if (diffInSeconds < 3600) return `${Math.floor(diffInSeconds / 60)} min atrás`;
        if (diffInSeconds < 86400) return `${Math.floor(diffInSeconds / 3600)}h atrás`;
        return `${Math.floor(diffInSeconds / 86400)}d atrás`;
    }

    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
}

// Inicializar quando página carregar
document.addEventListener('DOMContentLoaded', () => {
    new SimpleNotifications();
});

// Também tentar inicializar se DOM já estiver carregado
if (document.readyState !== 'loading') {
    new SimpleNotifications();
}
