/**
 * Sistema de Notificações - VERSÃO NOVA E SIMPLES
 * Foco: Badge aparece automaticamente quando há notificações não lidas
 */

class SimpleNotifications {
    constructor() {
        this.badge = null;
        this.bell = null;
        this.dropdown = null;
        this.list = null;
        this.markAllBtn = null;
        this.isOpen = false;

        this.init();
    }

    init() {
        // Buscar elementos
        this.badge = document.getElementById('notification-badge');
        this.bell = document.getElementById('notification-bell');
        this.dropdown = document.getElementById('notification-dropdown');
        this.list = document.getElementById('notification-list');
        this.markAllBtn = document.getElementById('mark-all-read');

        if (!this.badge || !this.bell) {
            console.log('Elementos de notificação não encontrados');
            return;
        }

        console.log('Sistema de notificações iniciado');

        // Garantir que o badge inicie COMPLETAMENTE escondido
        this.badge.classList.remove('show', 'active');
        this.badge.style.display = 'none';
        this.badge.style.visibility = 'hidden';
        this.badge.style.opacity = '0';

        // Configurar eventos
        this.setupEvents();

        // Buscar notificações imediatamente
        this.loadNotifications();

        // Verificar periodicamente (a cada 30 segundos)
        setInterval(() => {
            this.loadNotifications();
        }, 30000);
    }

    setupEvents() {
        // Clique no sino
        this.bell.addEventListener('click', (e) => {
            e.preventDefault();
            this.toggleDropdown();
        });

        // Marcar todas como lidas
        if (this.markAllBtn) {
            this.markAllBtn.addEventListener('click', () => {
                this.markAllAsRead();
            });
        }

        // Fechar ao clicar fora
        document.addEventListener('click', (e) => {
            if (!this.bell.contains(e.target) && !this.dropdown.contains(e.target)) {
                this.closeDropdown();
            }
        });
    }

    async loadNotifications() {
        try {
            console.log('Buscando notificações...');

            const response = await fetch('/notifications');
            if (!response.ok) {
                throw new Error(`Erro HTTP: ${response.status}`);
            }

            const data = await response.json();
            console.log('Dados recebidos:', data);

            if (data.success) {
                this.updateBadge(data.unreadCount);
                this.updateList(data.notifications);
            }
        } catch (error) {
            console.error('Erro ao carregar notificações:', error);
        }
    }

    updateBadge(count) {
        console.log('Atualizando badge, count:', count);

        if (!this.badge) return;

        if (count > 0) {
            // Mostrar badge com número usando a nova classe 'active'
            this.badge.textContent = count > 99 ? '99+' : count.toString();
            this.badge.classList.add('active');
            console.log('Badge mostrado com', count, 'notificações');
        } else {
            // Esconder badge completamente
            this.badge.classList.remove('active');
            this.badge.textContent = '';
            console.log('Badge escondido');
        }
    }

    updateList(notifications) {
        if (!this.list) return;

        if (notifications.length === 0) {
            this.list.innerHTML = '<div class="notification-empty">Nenhuma notificação</div>';
            return;
        }

        let html = '';
        notifications.forEach(notification => {
            const isUnread = notification.lida == 0;
            const timeAgo = this.getTimeAgo(notification.data_criacao);

            html += `
                <div class="notification-item ${isUnread ? 'unread' : ''}" data-id="${notification.id}">
                    <h6>${this.escapeHtml(notification.titulo)}</h6>
                    <p>${this.escapeHtml(notification.mensagem)}</p>
                    <small>${timeAgo}</small>
                    ${isUnread ? '<span class="badge bg-warning ms-2">Nova</span>' : ''}
                </div>
            `;
        });

        this.list.innerHTML = html;

        // Adicionar eventos de clique
        this.list.querySelectorAll('.notification-item.unread').forEach(item => {
            item.addEventListener('click', () => {
                this.markAsRead(item.dataset.id);
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
        if (this.dropdown) {
            this.dropdown.style.display = 'block';
            this.isOpen = true;
        }
    }

    closeDropdown() {
        if (this.dropdown) {
            this.dropdown.style.display = 'none';
            this.isOpen = false;
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
                // Recarregar notificações
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
    console.log('DOM carregado, iniciando notificações...');
    new SimpleNotifications();
});

// Também tentar inicializar se DOM já estiver carregado
if (document.readyState !== 'loading') {
    console.log('DOM já carregado, iniciando notificações...');
    new SimpleNotifications();
}
