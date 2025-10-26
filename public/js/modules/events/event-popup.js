/**
 * Sistema de Popup de Detalhes do Evento
 * 
 * Implementa um modal/popup completo para exibir todos os detalhes de um evento
 * de forma organizada e visualmente agradável. Inclui funcionalidade de impressão/PDF.
 * 
 * Funcionalidades:
 * - Busca detalhes do evento via AJAX
 * - Exibe todas as informações formatadas
 * - Lista de participantes confirmados
 * - Botão de imprimir/salvar PDF
 * - Design responsivo
 * - Fechamento por overlay ou botão X
 * - Animações suaves de abertura/fechamento
 * 
 * Informações Exibidas:
 * - Tipo e subtipo do evento
 * - Data, horário e período
 * - Responsável e solicitante
 * - Descrição completa
 * - Materiais necessários
 * - Lista de participantes (se houver)
 * - Infraestrutura adicional
 * - Observações administrativas
 * 
 * Integração:
 * - Endpoint: GET /agendamento/detalhes?id=X
 * - Retorna JSON com todos os dados do evento
 * 
 * @class EventPopup
 * @version 1.0
 */

class EventPopup {
    constructor() {
        this.overlay = null;
        this.container = null;
        this.currentEventId = null;
        this.init();
    }

    init() {
        // Criar estrutura do popup
        this.createPopupStructure();

        // Event listeners
        this.overlay.addEventListener('click', (e) => {
            if (e.target === this.overlay) {
                this.close();
            }
        });

        // Adicionar event listeners aos eventos clicáveis
        this.attachEventListeners();
    }

    createPopupStructure() {
        // Criar overlay
        this.overlay = document.createElement('div');
        this.overlay.className = 'event-popup-overlay';
        this.overlay.id = 'eventPopupOverlay';

        // Criar container
        this.container = document.createElement('div');
        this.container.className = 'event-popup-container';
        this.container.id = 'eventPopupContainer';

        this.overlay.appendChild(this.container);
        document.body.appendChild(this.overlay);
    }

    attachEventListeners() {
        // Adicionar listeners a todos os eventos na página
        document.addEventListener('click', (e) => {
            // PRIMEIRO: verificar se clicou no botão de presença ou em qualquer elemento dentro dele
            if (e.target.classList.contains('presenca-btn') || e.target.closest('.presenca-btn')) {
                // NÃO fazer nada aqui, deixar o outro handler cuidar
                return;
            }

            // Se clicou no spinner do botão de presença
            if (e.target.classList.contains('spinner-border') || e.target.closest('.spinner-border')) {
                return;
            }

            // Se clicou em um link, não fazer nada
            if (e.target.tagName === 'A' || e.target.closest('a')) {
                return;
            }

            // Verificar se clicou em um elemento com data-event-id
            const eventElement = e.target.closest('[data-event-id]');
            if (eventElement) {
                // Se chegou aqui, pode abrir o popup
                const eventId = eventElement.getAttribute('data-event-id');
                this.open(eventId);
            }
        });
    }

    async open(eventId) {
        this.currentEventId = eventId;
        this.overlay.classList.add('active');
        document.body.style.overflow = 'hidden';

        // Mostrar loading
        this.showLoading();

        try {
            const response = await fetch(`/agendamento/detalhes?id=${eventId}`);

            // Se for 401, tentar tratar mesmo sem JSON válido (ex.: retorno HTML de login)
            if (response.status === 401) {
                let data401 = {};
                try {
                    data401 = await response.clone().json();
                } catch (_) {
                    // Ignorar erro de parse; usar defaults
                }
                if (data401 && data401.error === 'not_authenticated') {
                    this.showLoginPrompt(data401.message, data401.login_url);
                } else {
                    this.showLoginPrompt('Faça login para ver mais detalhes sobre este evento', '/login');
                }
                return;
            }

            // Se o servidor retornou HTML (possível redirecionamento para login), mostrar prompt
            const contentType = response.headers.get('Content-Type') || '';
            if (contentType.includes('text/html')) {
                this.showLoginPrompt('Faça login para ver mais detalhes sobre este evento', '/login');
                return;
            }

            // Para outros status, tentar parsear JSON; se falhar, lançar erro
            let data;
            try {
                data = await response.clone().json();
            } catch (_) {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                throw new Error('Resposta inesperada do servidor');
            }

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            // Fallback adicional: se a API devolver JSON com erro de autenticação mesmo com 200
            if (data && data.error === 'not_authenticated') {
                this.showLoginPrompt(data.message, data.login_url);
                return;
            }

            if (data.error) {
                this.showError(data.error);
            } else {
                this.renderEventDetails(data);
            }
        } catch (error) {
            console.error('Erro ao buscar detalhes do evento:', error);
            this.showError('Erro ao carregar os detalhes do evento. Tente novamente.');
        }
    }

    close() {
        if (this.overlay) {
            // Remover o overlay do DOM completamente
            this.overlay.style.display = 'none';
            document.body.style.overflow = '';
            this.currentEventId = null;
            
            // Limpar o conteúdo do container
            if (this.container) {
                this.container.innerHTML = '';
            }
            
            // Recriar a estrutura do popup
            this.createPopupStructure();
        }
    }

    showLoading() {
        this.container.innerHTML = `
            <div class="event-loading">
                <div class="spinner"></div>
                <p>Carregando informações do evento...</p>
            </div>
        `;
    }

    showError(message) {
        this.container.innerHTML = `
            <div class="event-error">
                <i class="bi bi-exclamation-triangle"></i>
                <p>${message}</p>
                <button class="btn-close-popup" onclick="eventPopup.close()">Fechar</button>
            </div>
        `;
    }

    showLoginPrompt(message, loginUrl) {
        this.container.innerHTML = `
            <div class="event-login-prompt">
                <div class="login-prompt-icon">
                    <i class="bi bi-lock-fill"></i>
                </div>
                <h3>Faça login para ver os detalhes</h3>
                <p>${message || 'Para visualizar informações completas sobre este evento, você precisa estar logado no sistema.'}</p>
                <div class="login-prompt-buttons">
                    <a href="${loginUrl || '/login'}" class="btn btn-primary">
                        <i class="bi bi-box-arrow-in-right"></i> Fazer Login
                    </a>
                    <button class="btn btn-secondary" onclick="eventPopup.close()">
                        Voltar
                    </button>
                </div>
            </div>
        `;
    }

    renderEventDetails(evento) {
        const tipoEventoTexto = evento.tipo_agendamento === 'esportivo' ? 'Esportivo' : 'Não Esportivo';
        const statusClass = `status-${evento.status}`;
        const statusTexto = this.getStatusText(evento.status);

        const periodoTexto = this.getPeriodoTexto(evento.periodo);
        const dataFormatada = this.formatarData(evento.data_agendamento);

        // Usar permissões do backend
        // user_permission_level: 'full' = Superadmin ou Professor coordenador
        // user_permission_level: 'limited' = Admin de atlética ou usuário comum
        const hasFullAccess = evento.user_permission_level === 'full';
        const isSuperAdmin = evento.user_role === 'superadmin';
        
        // Para compatibilidade com código antigo
        const isAdminUser = hasFullAccess;

        let conteudo = `
            <div class="event-popup-header">
                <button class="event-popup-close" onclick="eventPopup.close()">
                    <i class="bi bi-x-lg"></i>
                </button>
                <span class="event-type-badge">
                    <i class="bi bi-${evento.tipo_agendamento === 'esportivo' ? 'trophy' : 'calendar-event'}"></i>
                    ${tipoEventoTexto}
                </span>
                <h2>${this.escapeHtml(evento.titulo)}</h2>
            </div>

            <div class="event-popup-body">
                <!-- Informações Principais -->
                <div class="event-popup-section">
                    <h3><i class="bi bi-info-circle-fill"></i> Informações Principais</h3>
                    <div class="event-info-grid">
                        <div class="event-info-item">
                            <label>Data do Evento</label>
                            <div class="value"><i class="bi bi-calendar3"></i> ${dataFormatada}</div>
                        </div>
                        <div class="event-info-item">
                            <label>Horário</label>
                            <div class="value"><i class="bi bi-clock"></i> ${periodoTexto}</div>
                        </div>
        `;

        // Status - apenas para super admin
        if (isSuperAdmin) {
            conteudo += `
                        <div class="event-info-item">
                            <label>Status</label>
                            <div class="value">
                                <span class="event-status-badge ${statusClass}">
                                    <i class="bi bi-${this.getStatusIcon(evento.status)}"></i>
                                    ${statusTexto}
                                </span>
                            </div>
                        </div>
            `;
        }

        conteudo += `
                        <div class="event-info-item">
                            <label>Responsável do Evento</label>
                            <div class="value">${this.escapeHtml(evento.responsavel_evento || '-')}</div>
                        </div>
                    </div>
                </div>
        `;

        // DADOS DO SOLICITANTE - Apenas para Admin e SuperAdmin
        if (isAdminUser) {
            conteudo += `
                <!-- Dados do Solicitante -->
                <div class="event-popup-section">
                    <h3><i class="bi bi-person-fill"></i> Dados do Solicitante</h3>
                    <div class="event-info-grid">
            `;

            // Mostrar nome se existir
            if (evento.criador_nome) {
                conteudo += `
                            <div class="event-info-item">
                                <label>Nome</label>
                                <div class="value"><i class="bi bi-person"></i> ${this.escapeHtml(evento.criador_nome)}</div>
                            </div>
                `;
            }

            // Mostrar email se existir
            if (evento.criador_email) {
                conteudo += `
                            <div class="event-info-item">
                                <label>Email</label>
                                <div class="value"><i class="bi bi-envelope"></i> ${this.escapeHtml(evento.criador_email)}</div>
                            </div>
                `;
            }

            // Mostrar telefone - APENAS PARA SUPER ADMIN
            if (evento.criador_telefone && isSuperAdmin) {
                conteudo += `
                            <div class="event-info-item">
                                <label>Telefone</label>
                                <div class="value"><i class="bi bi-telephone"></i> ${this.escapeHtml(evento.criador_telefone)}</div>
                            </div>
                `;
            }

            // Mostrar RA se existir
            if (evento.criador_ra) {
                conteudo += `
                            <div class="event-info-item">
                                <label>RA</label>
                                <div class="value"><i class="bi bi-person-badge"></i> ${this.escapeHtml(evento.criador_ra)}</div>
                            </div>
                `;
            }

            // Mostrar tipo de usuário - APENAS PARA SUPER ADMIN
            if (evento.criador_tipo && isSuperAdmin) {
                conteudo += `
                            <div class="event-info-item">
                                <label>Tipo de Usuário</label>
                                <div class="value"><i class="bi bi-shield-check"></i> ${this.escapeHtml(evento.criador_tipo)}</div>
                            </div>
                `;
            }

            // Mostrar atlética se existir
            if (evento.atletica_nome) {
                conteudo += `
                            <div class="event-info-item">
                                <label>Atlética</label>
                                <div class="value"><i class="bi bi-people"></i> ${this.escapeHtml(evento.atletica_nome)}</div>
                            </div>
                `;
            }

            conteudo += `
                    </div>
                </div>
            `;
        }

        // Detalhes específicos para eventos esportivos
        if (evento.tipo_agendamento === 'esportivo') {
            conteudo += this.renderEsportivoDetails(evento);
        } else {
            conteudo += this.renderNaoEsportivoDetails(evento);
        }

        // Descrição e observações
        if (evento.descricao || evento.observacoes) {
            conteudo += `
                <div class="event-popup-section">
                    <h3><i class="bi bi-file-text-fill"></i> Informações Adicionais</h3>
            `;

            if (evento.descricao) {
                conteudo += `
                    <div class="event-info-full">
                        <label>Descrição</label>
                        <div class="value">${this.escapeHtml(evento.descricao)}</div>
                    </div>
                `;
            }

            if (evento.observacoes) {
                conteudo += `
                    <div class="event-info-full">
                        <label>Observações</label>
                        <div class="value">${this.escapeHtml(evento.observacoes)}</div>
                    </div>
                `;
            }

            conteudo += `</div>`;
        }

        // Motivo de rejeição
        if (evento.status === 'rejeitado' && evento.motivo_rejeicao) {
            conteudo += `
                <div class="event-popup-section">
                    <h3><i class="bi bi-exclamation-triangle-fill"></i> Motivo da Rejeição</h3>
                    <div class="event-info-full" style="border-left-color: #dc3545;">
                        <div class="value">${this.escapeHtml(evento.motivo_rejeicao)}</div>
                    </div>
                </div>
            `;
        }

        // Presenças confirmadas com lista de nomes - SOMENTE PARA ADMIN E SUPERADMIN
        if (evento.presencas && evento.presencas.length > 0 && isAdminUser) {
            conteudo += `
                <div class="event-popup-section">
                    <h3><i class="bi bi-people-fill"></i> Presenças Confirmadas (${evento.presencas.length})</h3>
                    <div class="presencas-list">
            `;

            // Super admin vê nome, email e RA
            // Admin vê apenas nome e RA
            evento.presencas.forEach(p => {
                conteudo += `
                    <div class="presenca-item">
                        <i class="bi bi-person-check-fill"></i>
                        <div class="presenca-info">
                            <div class="name">${this.escapeHtml(p.nome)}</div>
                `;
                
                // Email - apenas para super admin
                if (isSuperAdmin && p.email) {
                    conteudo += `<div class="email">${this.escapeHtml(p.email)}</div>`;
                }
                
                // RA - para todos os admins
                if (p.ra) {
                    conteudo += `<div class="ra">RA: ${this.escapeHtml(p.ra)}</div>`;
                }
                
                conteudo += `
                        </div>
                    </div>
                `;
            });

            conteudo += `
                    </div>
                </div>
            `;
        }

        conteudo += `
            </div>

            <div class="event-popup-footer">
                <button class="btn-close-popup" onclick="eventPopup.close()">
                    Fechar
                </button>
            </div>
        `;

        this.container.innerHTML = conteudo;
    }

    renderEsportivoDetails(evento) {
        let conteudo = `
            <div class="event-popup-section">
                <h3><i class="bi bi-trophy-fill"></i> Detalhes Esportivos</h3>
                <div class="event-info-grid">
        `;

        if (evento.subtipo_evento) {
            conteudo += `
                <div class="event-info-item">
                    <label>Subtipo</label>
                    <div class="value">${this.escapeHtml(evento.subtipo_evento)}</div>
                </div>
            `;
        }

        if (evento.esporte_tipo) {
            conteudo += `
                <div class="event-info-item">
                    <label>Esporte</label>
                    <div class="value">${this.escapeHtml(evento.esporte_tipo)}</div>
                </div>
            `;
        }

        // Mostrar total de presenças confirmadas
        if (evento.total_presencas !== undefined) {
            conteudo += `
                <div class="event-info-item">
                    <label>Presenças Confirmadas</label>
                    <div class="value"><i class="bi bi-person-check-fill text-success"></i> ${evento.total_presencas} pessoa(s)</div>
                </div>
            `;
        }

        if (evento.arbitro_partida) {
            conteudo += `
                <div class="event-info-item">
                    <label>Árbitro</label>
                    <div class="value">${this.escapeHtml(evento.arbitro_partida)}</div>
                </div>
            `;
        }

        // Fechar a grid antes de adicionar elementos full-width
        conteudo += `</div>`;

        // Materiais - apenas para quem tem acesso total (superadmin ou professor coordenador)
        const hasFullAccess = evento.user_permission_level === 'full';

        if (hasFullAccess && evento.possui_materiais !== null) {
            const possuiMateriais = parseInt(evento.possui_materiais) === 1;
            conteudo += `
                <div class="event-info-full">
                    <label>Materiais</label>
                    <div class="value materiais-status">
                        <i class="bi bi-${possuiMateriais ? 'check-circle-fill text-success' : 'x-circle-fill text-danger'}"></i>
                        <span>${possuiMateriais ? 'Possui materiais próprios' : 'Não possui materiais'}</span>
                    </div>
                </div>
            `;

            if (!possuiMateriais && evento.materiais_necessarios) {
                conteudo += `
                    <div class="event-info-full">
                        <label>Materiais Necessários</label>
                        <div class="value">${this.escapeHtml(evento.materiais_necessarios)}</div>
                    </div>
                `;
            }
        }

        // Lista de participantes - apenas para quem tem acesso total
        if (hasFullAccess && evento.lista_participantes) {
            conteudo += `
                <div class="event-info-full">
                    <label>Lista de Participantes (RAs)</label>
                    <div class="value">${this.escapeHtml(evento.lista_participantes)}</div>
                </div>
            `;
        }

        // Atlética do solicitante (se aplicável)
        if (evento.atletica_nome) {
            conteudo += `
                <div class="event-info-full">
                    <label>Atlética do Solicitante</label>
                    <div class="value">
                        <i class="bi bi-people-fill"></i>
                        ${this.escapeHtml(evento.atletica_nome)}
                    </div>
                </div>
            `;
        }

        conteudo += `</div>`;
        return conteudo;
    }

    renderNaoEsportivoDetails(evento) {
        let conteudo = `
            <div class="event-popup-section">
                <h3><i class="bi bi-calendar-event-fill"></i> Detalhes do Evento</h3>
                <div class="event-info-grid">
        `;

        // Verificar nível de permissão do usuário
        const hasFullAccess = evento.user_permission_level === 'full';

        // Mostrar total de presenças confirmadas ao invés de estimativa
        if (evento.total_presencas !== undefined) {
            conteudo += `
                <div class="event-info-item">
                    <label>Presenças Confirmadas</label>
                    <div class="value"><i class="bi bi-person-check-fill text-success"></i> ${evento.total_presencas} pessoa(s)</div>
                </div>
            `;
        }

        // Mostrar estimativa apenas para quem tem acesso total
        if (hasFullAccess && evento.estimativa_participantes) {
            conteudo += `
                <div class="event-info-item">
                    <label>Estimativa de Participantes</label>
                    <div class="value"><i class="bi bi-people"></i> ${evento.estimativa_participantes} pessoas</div>
                </div>
            `;
        }

        if (evento.evento_aberto_publico !== null) {
            const aberto = parseInt(evento.evento_aberto_publico) === 1;
            conteudo += `
                <div class="event-info-item">
                    <label>Aberto ao Público</label>
                    <div class="value">
                        <i class="bi bi-${aberto ? 'check-circle-fill text-success' : 'x-circle-fill text-danger'}"></i>
                        ${aberto ? 'Sim' : 'Não'}
                    </div>
                </div>
            `;
        }

        conteudo += `</div>`;

        if (evento.descricao_publico_alvo) {
            conteudo += `
                <div class="event-info-full">
                    <label>Público Alvo</label>
                    <div class="value">${this.escapeHtml(evento.descricao_publico_alvo)}</div>
                </div>
            `;
        }

        if (evento.infraestrutura_adicional) {
            conteudo += `
                <div class="event-info-full">
                    <label>Infraestrutura Adicional</label>
                    <div class="value">${this.escapeHtml(evento.infraestrutura_adicional)}</div>
                </div>
            `;
        }

        conteudo += `</div>`;
        return conteudo;
    }

    getStatusText(status) {
        const statusMap = {
            'pendente': 'Pendente',
            'aprovado': 'Aprovado',
            'rejeitado': 'Rejeitado',
            'cancelado': 'Cancelado',
            'finalizado': 'Finalizado'
        };
        return statusMap[status] || status;
    }

    getStatusIcon(status) {
        const iconMap = {
            'pendente': 'clock',
            'aprovado': 'check-circle',
            'rejeitado': 'x-circle',
            'cancelado': 'slash-circle',
            'finalizado': 'flag'
        };
        return iconMap[status] || 'info-circle';
    }

    getPeriodoTexto(periodo) {
        const periodos = {
            'primeiro': '19:15 - 20:55',
            'segundo': '21:10 - 22:50',
            'manha': 'Manhã',
            'tarde': 'Tarde',
            'noite': 'Noite'
        };
        return periodos[periodo] || periodo;
    }

    formatarData(dataString) {
        const data = new Date(dataString + 'T00:00:00');
        return data.toLocaleDateString('pt-BR', {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric'
        });
    }

    escapeHtml(text) {
        if (!text) return '-';
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return text.toString().replace(/[&<>"']/g, m => map[m]);
    }
}

// Inicializar o popup quando o DOM estiver pronto
let eventPopup;
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        eventPopup = new EventPopup();
        window.eventPopup = eventPopup; // Expor globalmente para usar no perfil
    });
} else {
    eventPopup = new EventPopup();
    window.eventPopup = eventPopup; // Expor globalmente para usar no perfil
}
