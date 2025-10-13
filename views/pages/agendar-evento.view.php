<?php
/**
 * ============================================================================
 * VIEW: AGENDAR EVENTO NA QUADRA
 * ============================================================================
 * 
 * Formulário completo para solicitação de agendamento de eventos na quadra
 * esportiva com campos dinâmicos baseados no tipo de evento.
 * 
 * FUNCIONALIDADES:
 * - Calendário interativo para seleção de data/período
 * - Campos dinâmicos (esportivo vs não esportivo)
 * - Validações client-side
 * - Restrições por role (coordenadores só eventos não esportivos)
 * - Upload implícito via FormData
 * - Antecedência mínima de 4 dias
 * 
 * TIPOS DE EVENTO:
 * 1. ESPORTIVO:
 *    - Requer: esporte, subtipo (treino/campeonato), materiais, participantes (RAs)
 *    - Campos: árbitro, público estimado
 *    - Se sem materiais: descrição + checkbox de responsabilização
 * 
 * 2. NÃO ESPORTIVO:
 *    - Requer: subtipo (palestra/workshop/etc), público estimado
 *    - Campos: aberto ao público, infraestrutura adicional
 *    - Se fechado: descrição do público-alvo
 * 
 * RESTRIÇÕES DE ACESSO:
 * - Coordenadores: apenas eventos não esportivos
 * - Admins/SuperAdmins: todos os tipos
 * 
 * VALIDAÇÕES:
 * - Data/período obrigatórios (via calendário)
 * - Antecedência mínima de 4 dias
 * - Limite de 1 treino por esporte por semana (atlética)
 * - Campos obrigatórios conforme tipo
 * 
 * VARIÁVEIS RECEBIDAS:
 * @var array $modalidades - Lista de esportes disponíveis
 * @var array $user        - Dados do usuário logado (role, is_coordenador)
 * @var  ...calendário...  - Variáveis do calendário (ver calendar.php)
 * 
 * FLUXO:
 * 1. Usuário seleciona data/período no calendário
 * 2. Preenche tipo de evento
 * 3. Campos específicos aparecem dinamicamente
 * 4. Submit envia para /agendar-evento
 * 5. Aguarda aprovação do coordenador
 * 
 * CONTROLLER: AgendamentoController::criarAgendamento()
 * JAVASCRIPT: calendar.js, event-form.js
 * CSS: calendar.css
 */
?>
<!-- CSS específico para calendário -->
<link rel="stylesheet" href="/css/calendar.css">

<!-- JavaScript para interatividade -->
<script src="/js/calendar.js" defer></script>
<script src="/js/event-form.js" defer></script>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card shadow">
                <div class="card-header bg-success text-white">
                    <h4 class="mb-0"><i class="bi bi-calendar-plus"></i> Agendamento de Evento na Quadra</h4>
                </div>
                <div class="card-body">
                    <?php
                    // Verificar se o usuário é coordenador (mas não é admin ou superadmin)
                    $role = $user['role'] ?? 'usuario';
                    $is_coordenador = $user['is_coordenador'] ?? 0;
                    $isCoordenadorPuro = ($is_coordenador == 1) && ($role !== 'admin') && ($role !== 'superadmin');
                    ?>

                    <?php if ($isCoordenadorPuro): ?>
                        <div class="alert alert-warning border-warning">
                            <i class="bi bi-info-circle-fill"></i> <strong>Atenção Coordenadores:</strong>
                            Você pode agendar apenas <strong>Eventos Não Esportivos</strong> (Palestras, Workshops, Apresentações, etc).
                            Para eventos esportivos, entre em contato com a administração.
                        </div>
                    <?php endif; ?>

                    <?php if (isset($_SESSION['success_message'])): ?>
                        <div class="alert alert-success">
                            <i class="bi bi-check-circle"></i> <?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?>
                        </div>
                    <?php endif; ?>

                    <?php if (isset($_SESSION['error_message'])): ?>
                        <div class="alert alert-danger">
                            <i class="bi bi-exclamation-triangle"></i> <?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?>
                        </div>
                    <?php endif; ?>

                    <form action="/agendar-evento" method="post" enctype="multipart/form-data" id="agendamentoForm">
                        <input type="hidden" name="data_agendamento" id="data_agendamento">
                        <input type="hidden" name="periodo" id="periodo">

                        <div id="calendar-wrapper">
                            <?php
                            // Inclui a view parcial do calendário. As variáveis necessárias
                            // são passadas pelo controller que renderiza esta página.
                            require ROOT_PATH . '/views/_partials/calendar.php';
                            ?>
                        </div>

                        <div class="mb-4">
                            <strong>Horário Selecionado:</strong>
                            <span id="selecionado" class="text-primary fw-bold">Nenhum horário selecionado</span>
                        </div>

                        <div class="row mb-4" id="form-fields-wrapper">
                            <div class="col-12">
                                <h5 class="text-primary border-bottom pb-2">
                                    <i class="bi bi-info-circle"></i> Informações Básicas
                                </h5>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="titulo" class="form-label">Título do Evento *</label>
                                <input type="text" name="titulo" id="titulo" class="form-control"
                                       placeholder="Ex: Treino de Futsal - Atlética X" required maxlength="255">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="tipo_agendamento" class="form-label">Categoria do Evento *</label>
                                <select name="tipo_agendamento" id="tipo_agendamento" class="form-select" required>
                                    <option value="">-- Selecione a categoria --</option>
                                    <option value="esportivo">Evento Esportivo (Treino/Campeonato)</option>
                                    <option value="nao_esportivo">Evento Não Esportivo (Palestra/Workshop/etc)</option>
                                </select>
                            </div>

                            <div class="col-md-12 mb-3">
                                <label for="responsavel_evento" class="form-label">Responsável pelo Evento *</label>
                                <input type="text" name="responsavel_evento" id="responsavel_evento"
                                       class="form-control" placeholder="Nome completo do responsável" required>
                            </div>
                        </div>

                        <div id="campos_esportivos" style="display: none;">
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h5 class="text-success border-bottom pb-2">
                                        <i class="bi bi-trophy"></i> Informações do Evento Esportivo
                                    </h5>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="subtipo_evento" class="form-label">Tipo de Evento *</label>
                                    <select name="subtipo_evento" id="subtipo_evento" class="form-select">
                                        <option value="">-- Selecione --</option>
                                        <option value="treino">Treino</option>
                                        <option value="campeonato">Campeonato</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="esporte_tipo" class="form-label">Esporte *</label>
                                    <select name="esporte_tipo" id="esporte_tipo" class="form-select">
                                        <option value="">-- Selecione --</option>
                                        <?php foreach ($modalidades as $modalidade): ?>
                                            <option value="<?php echo strtolower($modalidade['nome']); ?>">
                                                <?php echo htmlspecialchars($modalidade['nome']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Possui materiais esportivos? *</label>
                                    <div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="possui_materiais" id="materiais_sim" value="1">
                                            <label class="form-check-label" for="materiais_sim">Sim</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="possui_materiais" id="materiais_nao" value="0">
                                            <label class="form-check-label" for="materiais_nao">Não</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="estimativa_participantes_esp" class="form-label">Público Estimado *</label>
                                    <input type="number" name="estimativa_participantes" id="estimativa_participantes_esp" class="form-control" min="1" max="500" placeholder="Ex: 50" required>
                                </div>
                                <div class="col-md-6 mb-3" id="campo_arbitro">
                                    <label for="arbitro_partida" class="form-label">Árbitro da Partida</label>
                                    <input type="text" name="arbitro_partida" id="arbitro_partida" class="form-control" placeholder="Nome do árbitro (opcional)">
                                </div>
                            </div>

                            <div class="row mb-4" id="campos_sem_materiais" style="display: none;">
                                <div class="col-12">
                                    <div class="alert alert-warning">
                                        <i class="bi bi-exclamation-triangle"></i> <strong>Atenção:</strong> Como você não possui os materiais, descreva o que será necessário.
                                    </div>
                                </div>
                                <div class="col-md-12 mb-3">
                                    <label for="materiais_necessarios" class="form-label">Materiais Necessários *</label>
                                    <textarea name="materiais_necessarios" id="materiais_necessarios" class="form-control" rows="3" placeholder="Descreva os materiais que serão necessários..."></textarea>
                                </div>
                                <div class="col-md-12 mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="responsabiliza_devolucao" id="responsabiliza_devolucao" value="1">
                                        <label class="form-check-label" for="responsabiliza_devolucao">
                                            <strong>Eu me responsabilizo pela devolução dos materiais *</strong>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-4">
                                <div class="col-md-12 mb-3">
                                    <label for="lista_participantes" class="form-label">Lista de Participantes (RAs) *</label>
                                    <textarea name="lista_participantes" id="lista_participantes" class="form-control" rows="4" placeholder="Digite os RAs dos participantes (um por linha)&#10;Ex:&#10;12345&#10;67890&#10;54321"></textarea>
                                    <div class="form-text">Digite apenas os RAs dos participantes, um por linha. O sistema buscará automaticamente os nomes.</div>
                                </div>
                            </div>
                        </div>

                        <div id="campos_nao_esportivos" style="display: none;">
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h5 class="text-info border-bottom pb-2">
                                        <i class="bi bi-people"></i> Informações do Evento Não Esportivo
                                    </h5>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="subtipo_evento_nao_esp" class="form-label">Tipo de Evento *</label>
                                    <select name="subtipo_evento_nao_esp" id="subtipo_evento_nao_esp" class="form-select" required>
                                        <option value="">-- Selecione --</option>
                                        <?php
                                        $tipos_evento = [
                                            'palestra' => 'Palestra',
                                            'workshop' => 'Workshop',
                                            'formatura' => 'Formatura',
                                            'seminario' => 'Seminário',
                                            'conferencia' => 'Conferência',
                                            'outro' => 'Outro'
                                        ];

                                        foreach ($tipos_evento as $valor => $label): ?>
                                            <option value="<?php echo $valor; ?>"><?php echo $label; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="col-md-6 mb-3" id="campo_outro_tipo" style="display: none;">
                                    <label for="outro_tipo_evento" class="form-label">Qual o tipo do evento? *</label>
                                    <input type="text" name="outro_tipo_evento" id="outro_tipo_evento" class="form-control"
                                        placeholder="Ex: Apresentação de TCC">
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="estimativa_participantes_nao_esp" class="form-label">Estimativa de Participantes *</label>
                                    <input type="number" name="estimativa_participantes" id="estimativa_participantes_nao_esp" class="form-control" min="1" max="500" placeholder="Ex: 100">
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Evento aberto ao público? *</label>
                                    <div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="evento_aberto_publico" id="publico_sim" value="1">
                                            <label class="form-check-label" for="publico_sim">Sim</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="evento_aberto_publico" id="publico_nao" value="0">
                                            <label class="form-check-label" for="publico_nao">Não (Fechado)</label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3" id="campo_publico_alvo" style="display: none;">
                                    <label for="descricao_publico_alvo" class="form-label">Quem pode participar?</label>
                                    <input type="text" name="descricao_publico_alvo" id="descricao_publico_alvo" class="form-control" placeholder="Ex: Alunos do curso de Engenharia">
                                </div>
                            </div>

                            <div class="col-md-12 mb-3">
                                <div class="alert alert-warning">
                                    <i class="bi bi-exclamation-triangle"></i>
                                    <strong>Importante:</strong> A supervisão do acesso é responsabilidade do organizador.
                                </div>
                            </div>
                            <div class="col-md-12 mb-3">
                                <label for="infraestrutura_adicional" class="form-label">Infraestrutura Adicional</label>
                                <textarea name="infraestrutura_adicional" id="infraestrutura_adicional" class="form-control" rows="3" placeholder="Ex: som, palco, decoração, projetor..."></textarea>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-12">
                                <label for="observacoes" class="form-label">Observações</label>
                                <textarea name="observacoes" id="observacoes" class="form-control" rows="3" placeholder="Informações adicionais sobre o evento..."></textarea>
                            </div>
                        </div>

                        <div class="alert alert-info">
                            <h6><i class="bi bi-info-circle"></i> Regras Importantes:</h6>
                            <ul class="mb-0">
                                <li>A seleção da data e período é feita pelo calendário acima.</li>
                                <li>Cada atlética pode agendar apenas 1 treino por esporte por semana.</li>
                                <li>A solicitação será analisada pelo Coordenador de Educação Física.</li>
                                <li>Você será notificado sobre a aprovação ou rejeição.</li>
                            </ul>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="/dashboard" class="btn btn-secondary">
                                <i class="bi bi-arrow-left"></i> Voltar
                            </a>
                            <button type="submit" class="btn btn-success" id="btnEnviarSolicitacao">
                                <i class="bi bi-send"></i> Enviar Solicitação
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
