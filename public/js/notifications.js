/**
 * Sistema de Notificações - SGE UNIFIO
 *
 * Este arquivo contém toda a lógica relacionada ao sistema de notificações em tempo real.
 * Funcionalidades:
 * - Busca de notificações do servidor
 * - Atualização da interface em tempo real
 * - Marcação de notificações como lidas
 * - Controle do dropdown de notificações
 * - Formatação de timestamps
 *
 * @author Sistema SGE UNIFIO
 * @version 1.0
 */

class NotificationSystem {
    constructor() {
        this.notificationBell = null;
        this.notificationBadge = null;
        this.notificationDropdown = null;
        this.notificationList = null;
        this.markAllReadBtn = null;
        this.isDropdownOpen = false;
        this.updateInterval = null;

        this.init();
    }

    /**
     * Inicializa o sistema de notificações
     */
    init() {
        // Buscar elementos do DOM
        this.notificationBell = document.getElementById('notification-bell');
        this.notificationBadge = document.getElementById('notification-badge');
        this.notificationDropdown = document.getElementById('notification-dropdown');
        this.notificationList = document.getElementById('notification-list');
        this.markAllReadBtn = document.getElementById('mark-all-read');

        // Verificar se os elementos existem (usuário logado)
        if (!this.notificationBell || !this.notificationDropdown) {
            return;
        }

        this.bindEvents();
        this.fetchNotifications();
        this.startPeriodicUpdate();
    }

    /**
     * Vincula eventos aos elementos
     */
    bindEvents() {
        // Toggle do dropdown
        this.notificationBell.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();
            this.toggleDropdown();
        });

        // Marcar todas como lidas
        if (this.markAllReadBtn) {
            this.markAllReadBtn.addEventListener('click', (e) => {
                e.preventDefault();
                this.markAsRead();
            });
        }

        // Fechar dropdown ao clicar fora
        document.addEventListener('click', (e) => {
            if (this.isDropdownOpen &&
                !this.notificationBell.contains(e.target) &&
                !this.notificationDropdown.contains(e.target)) {
                this.closeDropdown();
            }
        });
    }

    /**
     * Busca notificações do servidor
     */
    async fetchNotifications() {
        try {
            const response = await fetch('/notifications');
            const data = await response.json();

            if (data.success) {
                this.updateNotificationUI(data.notifications, data.unreadCount);
            }
        } catch (error) {
            console.error('Erro ao buscar notificações:', error);
        }
    }

    /**
     * Atualiza a interface das notificações
     */
    updateNotificationUI(notifications, unreadCount) {
        this.updateBadge(unreadCount);
        this.updateNotificationList(notifications);
    }

    /**
     * Atualiza o badge de contagem
     */
    updateBadge(unreadCount) {
        if (unreadCount > 0) {
            this.notificationBadge.textContent = unreadCount > 99 ? '99+' : unreadCount;
            this.notificationBadge.style.display = 'inline-block';
            this.notificationBell.classList.add('has-notifications');
        } else {
            this.notificationBadge.style.display = 'none';
            this.notificationBell.classList.remove('has-notifications');
        }
    }

    /**
     * Atualiza a lista de notificações
     */
    updateNotificationList(notifications) {
        const maxNotifications = 5;
        const limitedNotifications = notifications.slice(0, maxNotifications);
        const hasMore = notifications.length > maxNotifications;

        if (limitedNotifications.length === 0) {
            this.renderEmptyState();
        } else {
            this.renderNotifications(limitedNotifications, hasMore, notifications.length - maxNotifications);
        }
    }

    /**
     * Renderiza estado vazio
     */
    renderEmptyState() {
        this.notificationList.innerHTML = `
            <div class="notification-empty">
                <i class="bi bi-bell-slash"></i>
                Nenhuma notificação
            </div>
        `;
    }

    /**
     * Renderiza lista de notificações
     */
    renderNotifications(notifications, hasMore, remainingCount) {
        const notificationsHTML = notifications.map(notification => {
            const isUnread = notification.lida == 0;
            const timeAgo = this.formatTimeAgo(notification.data_criacao);
            const icon = this.getNotificationIcon(notification.tipo);

            return `
                <div class="notification-item ${isUnread ? 'unread' : ''}" 
                     data-notification-id="${notification.id}">
                    <div class="d-flex align-items-start">
                        <div class="me-3 mt-1" style="font-size: 1.2rem; min-width: 24px;">
                            ${icon}
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="mb-1 ${isUnread ? 'fw-bold' : ''}">${notification.titulo}</h6>
                            <p class="mb-1 small notification-message">${notification.mensagem}</p>
                            <small class="text-muted">${timeAgo}</small>
                        </div>
                        ${isUnread ? '<div class="ms-2 mt-1"><span class="badge bg-danger rounded-pill" style="font-size: 0.65rem;">Nova</span></div>' : ''}
                    </div>
                </div>
            `;
        }).join('');

        // Adicionar footer se houver mais notificações
        const footerHTML = hasMore ? `
            <div class="notification-footer">
                <small class="text-muted">
                    <i class="bi bi-three-dots"></i>
                    ${remainingCount} notificação${remainingCount > 1 ? 'ões' : ''} a mais
                </small>
            </div>
        ` : '';

        this.notificationList.innerHTML = notificationsHTML + footerHTML;

        // Adicionar event listeners simplificados
        this.bindNotificationEvents();
    }

    /**
     * Vincula eventos às notificações
     */
    bindNotificationEvents() {
        this.notificationList.querySelectorAll('.notification-item[data-notification-id]').forEach(item => {
            // Evento de clique para marcar como lida
            item.addEventListener('click', (e) => {
                const notificationId = item.dataset.notificationId;
                this.handleNotificationClick(item, notificationId);
            });
        });
    }

    /**
     * Manipula clique em notificação
     */
    handleNotificationClick(item, notificationId) {
        if (item.classList.contains('unread')) {
            this.markAsRead(notificationId);
            item.classList.remove('unread');

            // Atualizar badge
            const currentBadge = parseInt(this.notificationBadge.textContent) || 0;
            const newCount = Math.max(0, currentBadge - 1);
            this.updateBadge(newCount);
        }
    }

    /**
     * Escapa caracteres HTML para prevenir XSS
     */
    escapeHtml(text) {
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return text.replace(/[&<>"']/g, function(m) { return map[m]; });
    }

    /**
     * Mostra a mensagem completa da notificação
     */
    showFullMessage(notificationItem, fullMessageElement) {
        // Verificar se a mensagem está truncada
        const messageElement = notificationItem.querySelector('.notification-message');
        const isOverflowing = messageElement.scrollHeight > messageElement.clientHeight;

        if (isOverflowing || fullMessageElement) {
            fullMessageElement.style.display = 'block';

            // Ajustar posição se sair da tela
            const rect = fullMessageElement.getBoundingClientRect();
            const viewportHeight = window.innerHeight;

            if (rect.bottom > viewportHeight) {
                fullMessageElement.style.top = 'auto';
                fullMessageElement.style.bottom = '100%';
            } else {
                fullMessageElement.style.top = '100%';
                fullMessageElement.style.bottom = 'auto';
            }
        }
    }

    /**
     * Esconde a mensagem completa da notificação
     */
    hideFullMessage(fullMessageElement) {
        if (fullMessageElement) {
            fullMessageElement.style.display = 'none';
        }
    }

    /**
     * Retorna ícone baseado no tipo de notificação
     */
    getNotificationIcon(tipo) {
        const icons = {
            'agendamento_aprovado': '✅',
            'agendamento_rejeitado': '❌',
            'agendamento_cancelado': '⚠️',
            'presenca_confirmada': '📅',
            'lembrete_evento': '🔔',
            'info': '📢',
            'aviso': '⚡'
        };

        return icons[tipo] || '📢';
    }

    /**
     * Formata timestamp para exibição relativa
     */
    formatTimeAgo(dateString) {
        const now = new Date();
        const date = new Date(dateString);
        const diffInSeconds = Math.floor((now - date) / 1000);

        if (diffInSeconds < 60) return 'agora mesmo';
        if (diffInSeconds < 3600) return `há ${Math.floor(diffInSeconds / 60)} min`;
        if (diffInSeconds < 86400) return `há ${Math.floor(diffInSeconds / 3600)} h`;
        if (diffInSeconds < 2592000) return `há ${Math.floor(diffInSeconds / 86400)} dias`;

        return date.toLocaleDateString('pt-BR');
    }

    /**
     * Marca notificação(ões) como lida(s)
     */
    async markAsRead(notificationId = null) {
        try {
            const response = await fetch('/notifications/read', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    notification_id: notificationId
                })
            });

            const data = await response.json();
            if (data.success) {
                this.fetchNotifications();
            }
        } catch (error) {
            console.error('Erro ao marcar notificação como lida:', error);
        }
    }

    /**
     * Abre/fecha dropdown
     */
    toggleDropdown() {
        this.isDropdownOpen = !this.isDropdownOpen;

        if (this.isDropdownOpen) {
            this.openDropdown();
        } else {
            this.closeDropdown();
        }
    }

    /**
     * Abre dropdown
     */
    openDropdown() {
        this.notificationDropdown.classList.add('show');
        this.fetchNotifications(); // Atualizar ao abrir
    }

    /**
     * Fecha dropdown
     */
    closeDropdown() {
        this.isDropdownOpen = false;
        this.notificationDropdown.classList.remove('show');
    }

    /**
     * Inicia atualizações periódicas
     */
    startPeriodicUpdate() {
        // Atualizar a cada 30 segundos
        this.updateInterval = setInterval(() => {
            this.fetchNotifications();
        }, 30000);
    }

    /**
     * Para atualizações periódicas
     */
    stopPeriodicUpdate() {
        if (this.updateInterval) {
            clearInterval(this.updateInterval);
            this.updateInterval = null;
        }
    }

    /**
     * Destroi a instância
     */
    destroy() {
        this.stopPeriodicUpdate();
        // Remover event listeners se necessário
    }
}

// Auto-inicialização quando o DOM estiver pronto
document.addEventListener('DOMContentLoaded', function() {
    window.notificationSystem = new NotificationSystem();
});

// Exportar para uso externo se necessário
if (typeof module !== 'undefined' && module.exports) {
    module.exports = NotificationSystem;
}
