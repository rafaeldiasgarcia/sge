/**
 * MÓDULO: RELATÓRIOS (SUPER ADMIN)
 * Gerencia a geração de relatórios via AJAX sem recarregar a página
 */

class RelatoriosManager {
    constructor() {
        this.resultsContainer = null;
        this.loadingOverlay = null;
        this.init();
    }

    init() {
        // Aguardar o DOM estar pronto
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => this.setupEventListeners());
        } else {
            this.setupEventListeners();
        }
    }

    setupEventListeners() {
        // Encontrar o container de resultados
        this.resultsContainer = document.getElementById('relatorio-results');
        
        // Se não existir, criar
        if (!this.resultsContainer) {
            this.createResultsContainer();
        }

        // Interceptar todos os formulários de relatório
        document.querySelectorAll('form[action="/superadmin/relatorios"]').forEach(form => {
            form.addEventListener('submit', (e) => {
                e.preventDefault();
                this.handleFormSubmit(form);
            });
        });
    }

    createResultsContainer() {
        // Criar container para os resultados
        const container = document.createElement('div');
        container.id = 'relatorio-results';
        container.className = 'mt-4';
        
        // Inserir após os cards de formulários
        const formsContainer = document.querySelector('.row');
        if (formsContainer) {
            formsContainer.insertAdjacentElement('afterend', container);
        }
        
        this.resultsContainer = container;
    }

    async handleFormSubmit(form) {
        const formData = new FormData(form);
        const tipoRelatorio = formData.get('tipo_relatorio');
        
        // Mostrar loading
        this.showLoading();
        
        try {
            const response = await fetch('/superadmin/relatorios/ajax', {
                method: 'POST',
                body: formData
            });

            if (!response.ok) {
                throw new Error(`Erro HTTP: ${response.status}`);
            }

            const data = await response.json();
            
            if (data.success) {
                this.displayResults(data.dados_relatorio);
            } else {
                this.showError(data.message || 'Erro ao gerar relatório');
            }
        } catch (error) {
            console.error('Erro ao gerar relatório:', error);
            this.showError('Erro de conexão. Tente novamente.');
        } finally {
            this.hideLoading();
        }
    }

    showLoading() {
        if (this.loadingOverlay) {
            this.loadingOverlay.remove();
        }

        this.loadingOverlay = document.createElement('div');
        this.loadingOverlay.className = 'loading-overlay';
        this.loadingOverlay.innerHTML = `
            <div class="loading-spinner">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Carregando...</span>
                </div>
                <p class="mt-2">Gerando relatório...</p>
            </div>
        `;

        // Adicionar estilos se não existirem
        this.addLoadingStyles();
        
        document.body.appendChild(this.loadingOverlay);
    }

    hideLoading() {
        if (this.loadingOverlay) {
            this.loadingOverlay.remove();
            this.loadingOverlay = null;
        }
    }

    addLoadingStyles() {
        if (document.getElementById('relatorios-loading-styles')) return;

        const style = document.createElement('style');
        style.id = 'relatorios-loading-styles';
        style.textContent = `
            .loading-overlay {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0, 0, 0, 0.5);
                display: flex;
                justify-content: center;
                align-items: center;
                z-index: 9999;
            }
            
            .loading-spinner {
                background: white;
                padding: 2rem;
                border-radius: 8px;
                text-align: center;
                box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            }
        `;
        document.head.appendChild(style);
    }

    displayResults(dadosRelatorio) {
        if (!this.resultsContainer) return;

        let html = this.generateResultsHTML(dadosRelatorio);
        this.resultsContainer.innerHTML = html;
        
        // Scroll suave para os resultados
        this.resultsContainer.scrollIntoView({ 
            behavior: 'smooth', 
            block: 'start' 
        });
    }

    generateResultsHTML(dadosRelatorio) {
        let html = `
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Resultado do Relatório</h5>
                    <form action="/superadmin/relatorios/imprimir" method="post" target="_blank">
                        <input type="hidden" name="tipo_relatorio" value="${dadosRelatorio.tipo}">
        `;

        // Adicionar campos específicos do tipo de relatório
        if (dadosRelatorio.tipo === 'periodo') {
            html += `
                <input type="hidden" name="data_inicio" value="${dadosRelatorio.periodo.inicio}">
                <input type="hidden" name="data_fim" value="${dadosRelatorio.periodo.fim}">
            `;
        } else if (dadosRelatorio.tipo === 'evento_especifico') {
            html += `<input type="hidden" name="evento_id" value="${dadosRelatorio.evento.id}">`;
        } else if (dadosRelatorio.tipo === 'usuario') {
            html += `<input type="hidden" name="usuario_id" value="${dadosRelatorio.usuario.id}">`;
        }

        html += `
                        <button type="submit" class="btn btn-secondary">
                            <i class="bi bi-printer-fill"></i> Imprimir / Salvar PDF
                        </button>
                    </form>
                </div>
                <div class="card-body">
        `;

        // Conteúdo específico por tipo de relatório
        if (dadosRelatorio.tipo === 'periodo') {
            html += this.generatePeriodoHTML(dadosRelatorio);
        } else if (dadosRelatorio.tipo === 'evento_especifico') {
            html += this.generateEventoEspecificoHTML(dadosRelatorio);
        } else if (dadosRelatorio.tipo === 'usuario') {
            html += this.generateUsuarioHTML(dadosRelatorio);
        }

        html += `
                </div>
            </div>
        `;

        return html;
    }

    generatePeriodoHTML(dadosRelatorio) {
        const stats = dadosRelatorio.estatisticas;
        const inicio = new Date(dadosRelatorio.periodo.inicio).toLocaleDateString('pt-BR');
        const fim = new Date(dadosRelatorio.periodo.fim).toLocaleDateString('pt-BR');
        
        return `
            <h5>Resumo do Período (${inicio} a ${fim})</h5>
            <ul class="list-group">
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    Total de Eventos no Período 
                    <span class="badge bg-primary rounded-pill">${stats.total_eventos}</span>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    Eventos Aprovados 
                    <span class="badge bg-success rounded-pill">${stats.eventos_aprovados}</span>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    Público Estimado (soma inicial) 
                    <span class="badge bg-secondary rounded-pill">${stats.total_pessoas_estimadas || 0}</span>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    Total de Presenças Confirmadas 
                    <span class="badge bg-info rounded-pill">${stats.total_presencas || 0}</span>
                </li>
            </ul>
        `;
    }

    generateEventoEspecificoHTML(dadosRelatorio) {
        const evento = dadosRelatorio.evento;
        const tipoClass = evento.tipo_agendamento === 'esportivo' ? 'bg-success' : 'bg-info';
        
        let html = `
            <h5>Detalhes do Evento: ${this.escapeHtml(evento.titulo)}</h5>
            <ul class="list-group">
                <li class="list-group-item">
                    <strong>Tipo:</strong> 
                    <span class="badge ${tipoClass}">
                        ${this.capitalizeFirst(evento.tipo_agendamento)}
                        ${evento.tipo_agendamento === 'esportivo' && evento.esporte_tipo ? 
                            ` - ${this.escapeHtml(evento.esporte_tipo)}` : ''}
                    </span>
                </li>
                <li class="list-group-item">
                    <strong>Responsável:</strong> ${this.escapeHtml(evento.responsavel)}
                </li>
                <li class="list-group-item">
                    <strong>Público Estimado:</strong> ${evento.estimativa_participantes || 0}
                </li>
                <li class="list-group-item">
                    <strong>Presenças Confirmadas:</strong> ${evento.total_presencas}
                </li>
        `;

        // Materiais
        if (evento.tipo_agendamento === 'esportivo' && evento.possui_materiais !== undefined) {
            const materialClass = evento.possui_materiais == 1 ? 'bg-success' : 'bg-warning text-dark';
            const materialText = evento.possui_materiais == 1 ? 
                '<i class="bi bi-check-circle-fill"></i> Possui materiais próprios' : 
                '<i class="bi bi-exclamation-circle-fill"></i> Não possui materiais próprios';
            
            html += `
                <li class="list-group-item">
                    <strong>Materiais:</strong>
                    <span class="badge ${materialClass}">${materialText}</span>
            `;
            
            if (evento.possui_materiais != 1 && evento.materiais_necessarios) {
                html += `
                    <div class="mt-2">
                        <strong class="text-danger">Materiais Necessários/Utilizados:</strong>
                        <pre class="mt-1 p-2 bg-light border rounded">${this.escapeHtml(evento.materiais_necessarios)}</pre>
                    </div>
                `;
            }
            
            html += `</li>`;
        }

        // Participantes
        if (evento.participantes_formatados && evento.participantes_formatados.length > 0) {
            html += `
                <li class="list-group-item">
                    <strong>Lista de Participantes:</strong>
                    <div class="mt-2">
                        ${evento.participantes_formatados.map(p => 
                            `<span class="badge bg-info me-1">${this.escapeHtml(p)}</span>`
                        ).join('')}
                    </div>
                </li>
            `;
        }

        html += `</ul>`;
        return html;
    }

    generateUsuarioHTML(dadosRelatorio) {
        const usuario = dadosRelatorio.usuario;
        const agendamentos = dadosRelatorio.agendamentos || [];
        const presencas = dadosRelatorio.presencas || [];
        
        return `
            <h5>Atividades de: ${this.escapeHtml(usuario.nome)}</h5>
            <ul class="list-group">
                <li class="list-group-item">
                    <strong>Agendamentos Criados:</strong> ${agendamentos.length}
                </li>
                <li class="list-group-item">
                    <strong>Presenças Marcadas:</strong> ${presencas.length}
                </li>
            </ul>
        `;
    }

    showError(message) {
        if (!this.resultsContainer) return;

        this.resultsContainer.innerHTML = `
            <div class="alert alert-danger" role="alert">
                <i class="bi bi-exclamation-triangle-fill"></i>
                ${this.escapeHtml(message)}
            </div>
        `;
    }

    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    capitalizeFirst(str) {
        return str.charAt(0).toUpperCase() + str.slice(1);
    }
}

// Inicializar quando o DOM estiver pronto
document.addEventListener('DOMContentLoaded', () => {
    new RelatoriosManager();
});
