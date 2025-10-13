/**
 * Sistema de Notificações em Tempo Real
 * 
 * Implementa um sistema de notificações assíncronas usando AJAX e polling.
 * As notificações são atualizadas automaticamente sem necessidade de recarregar a página.
 * 
 * Funcionalidades:
 * - Polling automático a cada 30 segundos
 * - Badge com contador de não lidas
 * - Dropdown com lista de notificações
 * - Marcar notificações como lidas individualmente ou em massa
 * - Ícones personalizados por tipo de notificação
 * - Som de notificação (opcional)
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
 * @version 2.0
 */

class SimpleNotifications {
    constructor() {
        this.badge = null;
        this.bell = null;
        this.dropdown = null;
        this.list = null;
        this.markAllBtn = null;
        this.isOpen = false;
        this.notificationContainer = null; // Container que engloba o sino e o dropdown

        this.init();
    }

    init() {
        // Buscar elementos
        this.badge = document.getElementById('notification-badge');
        this.bell = document.getElementById('notification-bell');
        this.dropdown = document.getElementById('notification-dropdown');
        this.list = document.getElementById('notification-list');
    // botão removido do template — mantemos compatibilidade caso exista
    this.markAllBtn = document.getElementById('mark-all-read');
        this.notificationContainer = document.querySelector('.notifications'); // li.nav-item.notifications

        if (!this.badge || !this.bell) {
            return;
        }

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
        // Mouse entra na área das notificações (sino ou dropdown)
        if (this.notificationContainer) {
            this.notificationContainer.addEventListener('mouseenter', () => {
                this.openDropdown();
            });

            this.notificationContainer.addEventListener('mouseleave', () => {
                this.closeDropdown();
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
    }

    async loadNotifications() {
        try {
            const response = await fetch('/notifications');
            if (!response.ok) {
                throw new Error(`Erro HTTP: ${response.status}`);
            }

            const data = await response.json();

            if (data.success) {
                this.updateBadge(data.unreadCount);
                this.updateList(data.notifications);
            }
        } catch (error) {
            console.error('Erro ao carregar notificações:', error);
        }
    }

    updateBadge(count) {
        if (!this.badge) return;

        if (count > 0) {
            // Mostrar badge com número usando a nova classe 'active'
            this.badge.textContent = count > 99 ? '99+' : count.toString();
            this.badge.classList.add('active');
        } else {
            // Esconder badge completamente
            this.badge.classList.remove('active');
            this.badge.textContent = '';
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
        if (this.dropdown && !this.isOpen) {
            // Usar classe .show como o sistema de dropdowns do header
            this.dropdown.classList.add('show');
            this.dropdown.style.display = 'block';
            this.isOpen = true;
        }
    }

    closeDropdown() {
        if (this.dropdown && this.isOpen) {
            // Usar classe .show como o sistema de dropdowns do header
            this.dropdown.classList.remove('show');
            this.dropdown.style.display = 'none';
            this.isOpen = false;
            
            // Marcar todas como lidas quando o dropdown fechar
            if (this.badge && this.badge.classList.contains('active')) {
                this.markAllAsRead();
            }
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
    new SimpleNotifications();
});

// Também tentar inicializar se DOM já estiver carregado
if (document.readyState !== 'loading') {
    new SimpleNotifications();
}
