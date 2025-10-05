/**
 * Sistema de Popup de Detalhes do Evento
 * Exibe todas as informações do evento e permite salvar como PDF
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
            console.log('Buscando evento ID:', eventId);
            const response = await fetch(`/agendamento/detalhes?id=${eventId}`);
            console.log('Status da resposta:', response.status);
            console.log('Headers:', response.headers);

            const contentType = response.headers.get('content-type');
            console.log('Content-Type:', contentType);

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const textResponse = await response.text();
            console.log('Resposta do servidor (texto):', textResponse);

            let data;
            try {
                data = JSON.parse(textResponse);
                console.log('Dados parseados:', data);
            } catch (e) {
                console.error('Erro ao fazer parse do JSON:', e);
                console.error('Resposta recebida:', textResponse);
                throw new Error('Resposta do servidor não é um JSON válido');
            }

            if (data.error) {
                this.showError(data.error);
            } else {
                this.renderEventDetails(data);
            }
        } catch (error) {
            console.error('Erro ao buscar detalhes do evento:', error);
            this.showError('Erro ao carregar os detalhes do evento. Tente novamente. Detalhes: ' + error.message);
        }
    }

    close() {
        this.overlay.classList.remove('active');
        document.body.style.overflow = '';
        this.currentEventId = null;
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

    renderEventDetails(evento) {
        const tipoEventoTexto = evento.tipo_agendamento === 'esportivo' ? 'Esportivo' : 'Não Esportivo';
        const statusClass = `status-${evento.status}`;
        const statusTexto = this.getStatusText(evento.status);

        const periodoTexto = this.getPeriodoTexto(evento.periodo);
        const dataFormatada = this.formatarData(evento.data_agendamento);

        // Verificar se o usuário é admin ou superadmin
        const isAdminUser = window.userRole === 'admin' || window.userRole === 'superadmin';

        let conteudo = `
            <div class="event-popup-header">
                <button class="event-popup-close" onclick="eventPopup.close()">
                    <i class="bi bi-x"></i>
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
                        <div class="event-info-item">
                            <label>Status</label>
                            <div class="value">
                                <span class="event-status-badge ${statusClass}">
                                    <i class="bi bi-${this.getStatusIcon(evento.status)}"></i>
                                    ${statusTexto}
                                </span>
                            </div>
                        </div>
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

            // Mostrar telefone se existir
            if (evento.criador_telefone) {
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

            // Mostrar tipo de usuário se existir
            if (evento.criador_tipo) {
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
                        ${evento.presencas.map(p => `
                            <div class="presenca-item">
                                <i class="bi bi-person-check-fill"></i>
                                <div class="presenca-info">
                                    <div class="name">${this.escapeHtml(p.nome)}</div>
                                    <div class="email">${this.escapeHtml(p.email)}</div>
                                </div>
                            </div>
                        `).join('')}
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

        conteudo += `</div>`;

        // Materiais - apenas para admin e superadmin
        const isAdminUser = window.userRole === 'admin' || window.userRole === 'superadmin';

        if (isAdminUser && evento.possui_materiais !== null) {
            const possuiMateriais = parseInt(evento.possui_materiais) === 1;
            conteudo += `
                <div class="event-info-full">
                    <label>Materiais</label>
                    <div class="value">
                        <i class="bi bi-${possuiMateriais ? 'check-circle-fill text-success' : 'x-circle-fill text-danger'}"></i>
                        ${possuiMateriais ? 'Possui materiais próprios' : 'Não possui materiais'}
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

        // Lista de participantes - apenas para admin e superadmin
        if (isAdminUser && evento.lista_participantes) {
            conteudo += `
                <div class="event-info-full">
                    <label>Lista de Participantes (RAs)</label>
                    <div class="value">${this.escapeHtml(evento.lista_participantes)}</div>
                </div>
            `;
        }

        // Atlética confirmada
        if (evento.atletica_confirmada && evento.atletica_confirmada_nome) {
            conteudo += `
                <div class="event-info-full">
                    <label>Atlética Confirmada</label>
                    <div class="value">
                        <i class="bi bi-check-circle-fill text-success"></i>
                        ${this.escapeHtml(evento.atletica_confirmada_nome)} (${evento.quantidade_atletica} pessoas)
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

        // Verificar se o usuário é admin ou superadmin
        const isAdminUser = window.userRole === 'admin' || window.userRole === 'superadmin';

        // Mostrar total de presenças confirmadas ao invés de estimativa
        if (evento.total_presencas !== undefined) {
            conteudo += `
                <div class="event-info-item">
                    <label>Presenças Confirmadas</label>
                    <div class="value"><i class="bi bi-person-check-fill text-success"></i> ${evento.total_presencas} pessoa(s)</div>
                </div>
            `;
        }

        // Mostrar estimativa apenas para admin e superadmin
        if (isAdminUser && evento.estimativa_participantes) {
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
    });
} else {
    eventPopup = new EventPopup();
}
