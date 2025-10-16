<?php
/**
 * ============================================================================
 * VIEW: EDITAR EVENTO
 * ============================================================================
 * 
 * Formulário de edição de evento com campos pré-preenchidos. Estrutura
 * idêntica ao formulário de agendamento mas permite modificar evento existente.
 * 
 * FUNCIONALIDADES:
 * - Editar todos os campos do evento (exceto se aprovado/rejeitado)
 * - Calendário para alterar data/período
 * - Campos dinâmicos conforme tipo de evento
 * - Mesmas validações do agendamento
 * - Restrições por role
 * 
 * PERMISSÕES:
 * - Usuário pode editar apenas seus próprios eventos
 * - Apenas eventos com status 'pendente' podem ser editados
 * - Coordenadores: apenas eventos não esportivos
 * - Eventos aprovados/rejeitados: apenas visualização
 * 
 * VARIÁVEIS RECEBIDAS:
 * @var array $evento       - Dados completos do evento a editar
 * @var array $modalidades  - Lista de esportes disponíveis
 * @var array $user         - Dados do usuário logado
 * @var  ...calendário...   - Variáveis do calendário
 * 
 * VALIDAÇÕES:
 * - Mesmas validações do formulário de agendamento
 * - Antecedência mínima de 4 dias
 * - Campos obrigatórios por tipo
 * 
 * FLUXO:
 * 1. Carregar dados do evento
 * 2. Preencher campos
 * 3. Usuário modifica
 * 4. POST para /editar-evento/{id}
 * 5. Volta a pendente se necessário
 * 
 * CONTROLLER: AgendamentoController::editarEvento()
 * JAVASCRIPT: calendar.js, event-form.js
 * CSS: calendar.css
 */
?>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0"><i class="bi bi-pencil-square"></i> Editar Evento</h4>
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
                            Você pode editar apenas <strong>Eventos Não Esportivos</strong> (Palestras, Workshops, Apresentações, etc).
                            Para eventos esportivos, entre em contato com a administração.
                        </div>
                    <?php endif; ?>

                    <?php if (isset($_SESSION['warning_message'])): ?>
                        <div class="alert alert-warning">
                            <i class="bi bi-exclamation-triangle"></i> <?php echo $_SESSION['warning_message']; unset($_SESSION['warning_message']); ?>
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

                    <form action="/agendamento/editar/<?php echo htmlspecialchars($evento['id']); ?>" method="post" enctype="multipart/form-data" id="agendamentoForm">
                        <input type="hidden" name="id" value="<?php echo htmlspecialchars($evento['id']); ?>">
                        <input type="hidden" name="data_agendamento" id="data_agendamento" value="<?php echo htmlspecialchars($evento['data_agendamento']); ?>">
                        <input type="hidden" name="periodo" id="periodo" value="<?php echo htmlspecialchars($evento['periodo']); ?>">

                        <div id="calendar-wrapper">
                            <?php
                            // Inclui a view parcial do calendário com a data pré-selecionada
                            $data_selecionada = $evento['data_agendamento'];
                            $periodo_selecionado = $evento['horario_periodo'];
                            require ROOT_PATH . '/views/_partials/calendar.php';
                            ?>
                        </div>

                        <div class="mb-4">
                            <strong>Horário Selecionado:</strong>
                            <span id="selecionado" class="text-primary fw-bold">
                                <?php
                                echo date('d/m/Y', strtotime($evento['data_agendamento'])) .
                                     ' - ' . $evento['horario_periodo'];
                                ?>
                            </span>
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
                                       value="<?php echo htmlspecialchars($evento['titulo']); ?>"
                                       placeholder="Ex: Treino de Futsal - Atlética X" required maxlength="255">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="tipo_agendamento" class="form-label">Categoria do Evento *</label>
                                <select name="tipo_agendamento" id="tipo_agendamento" class="form-select" required>
                                    <option value="">-- Selecione a categoria --</option>
                                    <option value="esportivo" <?php echo $evento['tipo_agendamento'] === 'esportivo' ? 'selected' : ''; ?>>
                                        Evento Esportivo (Treino/Campeonato)
                                    </option>
                                    <option value="nao_esportivo" <?php echo $evento['tipo_agendamento'] === 'nao_esportivo' ? 'selected' : ''; ?>>
                                        Evento Não Esportivo (Palestra/Workshop/etc)
                                    </option>
                                </select>
                            </div>
                        </div>

                        <div id="campos_esportivos" style="display: <?php echo $evento['tipo_agendamento'] === 'esportivo' ? 'block' : 'none'; ?>;">
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
                                        <option value="treino" <?php echo $evento['subtipo_evento'] === 'treino' ? 'selected' : ''; ?>>Treino</option>
                                        <option value="campeonato" <?php echo $evento['subtipo_evento'] === 'campeonato' ? 'selected' : ''; ?>>Campeonato</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="esporte_tipo" class="form-label">Esporte *</label>
                                    <select name="esporte_tipo" id="esporte_tipo" class="form-select">
                                        <option value="">-- Selecione --</option>
                                        <?php foreach ($modalidades as $modalidade): ?>
                                            <option value="<?php echo strtolower($modalidade['nome']); ?>"
                                                <?php echo strtolower($modalidade['nome']) === strtolower($evento['esporte_tipo']) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($modalidade['nome']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Possui materiais esportivos? *</label>
                                    <div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="possui_materiais" id="materiais_sim" value="1"
                                                <?php echo $evento['possui_materiais'] ? 'checked' : ''; ?>>
                                            <label class="form-check-label" for="materiais_sim">Sim</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="possui_materiais" id="materiais_nao" value="0"
                                                <?php echo !$evento['possui_materiais'] ? 'checked' : ''; ?>>
                                            <label class="form-check-label" for="materiais_nao">Não</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="estimativa_participantes_esp" class="form-label">Público Estimado *</label>
                                    <input type="number" name="estimativa_participantes" id="estimativa_participantes_esp"
                                           class="form-control" min="1" max="500" placeholder="Ex: 50"
                                           value="<?php echo htmlspecialchars($evento['estimativa_participantes'] ?? ''); ?>">
                                </div>
                                <div class="col-md-6 mb-3" id="campo_arbitro">
                                    <label for="arbitro_partida" class="form-label">Árbitro da Partida</label>
                                    <input type="text" name="arbitro_partida" id="arbitro_partida" class="form-control"
                                           value="<?php echo htmlspecialchars($evento['arbitro_partida'] ?? ''); ?>"
                                           placeholder="Nome do árbitro (opcional)">
                                </div>
                            </div>

                            <div class="row mb-4" id="campos_sem_materiais" style="display: <?php echo !$evento['possui_materiais'] ? 'block' : 'none'; ?>;">
                                <div class="col-12">
                                    <div class="alert alert-warning">
                                        <i class="bi bi-exclamation-triangle"></i> <strong>Atenção:</strong> Como você não possui os materiais, descreva o que será necessário.
                                    </div>
                                </div>
                                <div class="col-md-12 mb-3">
                                    <label for="materiais_necessarios" class="form-label">Materiais Necessários *</label>
                                    <textarea name="materiais_necessarios" id="materiais_necessarios" class="form-control" rows="3"
                                              placeholder="Descreva os materiais que serão necessários..."><?php echo htmlspecialchars($evento['materiais_necessarios'] ?? ''); ?></textarea>
                                </div>
                                <div class="col-md-12 mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="responsabiliza_devolucao"
                                               id="responsabiliza_devolucao" value="1"
                                               <?php echo $evento['responsabiliza_devolucao'] ? 'checked' : ''; ?>>
                                        <label class="form-check-label" for="responsabiliza_devolucao">
                                            <strong>Eu me responsabilizo pela devolução dos materiais *</strong>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-4">
                                <div class="col-md-12 mb-3">
                                    <label for="lista_participantes" class="form-label">Lista de Participantes (RAs) *</label>
                                    <textarea name="lista_participantes" id="lista_participantes" class="form-control" rows="4"
                                              placeholder="Digite os RAs dos participantes (um por linha)&#10;Ex:&#10;12345&#10;67890&#10;54321"><?php echo htmlspecialchars($evento['lista_participantes'] ?? ''); ?></textarea>
                                    <div class="form-text">Digite apenas os RAs dos participantes, um por linha. O sistema buscará automaticamente os nomes.</div>
                                </div>
                            </div>
                        </div>

                        <div id="campos_nao_esportivos" style="display: <?php echo $evento['tipo_agendamento'] === 'nao_esportivo' ? 'block' : 'none'; ?>;">
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h5 class="text-info border-bottom pb-2">
                                        <i class="bi bi-people"></i> Informações do Evento Não Esportivo
                                    </h5>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="subtipo_evento_nao_esp" class="form-label">Tipo de Evento *</label>
                                    <?php $subtipoSelecionado = $evento['subtipo_evento_nao_esp'] ?? ''; ?>
                                    <select name="subtipo_evento_nao_esp" id="subtipo_evento_nao_esp" class="form-select">
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
                                            <option value="<?php echo $valor; ?>"
                                                <?php echo $subtipoSelecionado === $valor ? 'selected' : ''; ?>>
                                                <?php echo $label; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="col-md-6 mb-3" id="campo_outro_tipo"
                                     style="display: <?php echo ($subtipoSelecionado === 'outro') ? 'block' : 'none'; ?>;">
                                    <label for="outro_tipo_evento" class="form-label">Qual o tipo do evento? *</label>
                                    <?php $outroValor = $evento['outro_tipo_evento'] ?? ($evento['esporte_tipo'] ?? ''); ?>
                                    <input type="text" name="outro_tipo_evento" id="outro_tipo_evento" class="form-control"
                                           value="<?php echo htmlspecialchars($outroValor); ?>"
                                           placeholder="Ex: Apresentação de TCC">
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="estimativa_participantes_nao_esp" class="form-label">Estimativa de Participantes *</label>
                                    <input type="number" name="estimativa_participantes" id="estimativa_participantes_nao_esp"
                                           class="form-control" min="1" max="500" placeholder="Ex: 100"
                                           value="<?php echo htmlspecialchars($evento['estimativa_participantes'] ?? ''); ?>">
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Evento aberto ao público? *</label>
                                    <div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="evento_aberto_publico"
                                                   id="publico_sim" value="1"
                                                   <?php echo $evento['evento_aberto_publico'] ? 'checked' : ''; ?>>
                                            <label class="form-check-label" for="publico_sim">Sim</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="evento_aberto_publico"
                                                   id="publico_nao" value="0"
                                                   <?php echo !$evento['evento_aberto_publico'] ? 'checked' : ''; ?>>
                                            <label class="form-check-label" for="publico_nao">Não (Fechado)</label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3" id="campo_publico_alvo"
                                     style="display: <?php echo !$evento['evento_aberto_publico'] ? 'block' : 'none'; ?>;">
                                    <label for="descricao_publico_alvo" class="form-label">Quem pode participar?</label>
                                    <input type="text" name="descricao_publico_alvo" id="descricao_publico_alvo"
                                           class="form-control"
                                           value="<?php echo htmlspecialchars($evento['descricao_publico_alvo'] ?? ''); ?>"
                                           placeholder="Ex: Alunos do curso de Engenharia">
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
                                <textarea name="infraestrutura_adicional" id="infraestrutura_adicional" class="form-control"
                                          rows="3" placeholder="Ex: som, palco, decoração, projetor..."><?php echo htmlspecialchars($evento['infraestrutura_adicional'] ?? ''); ?></textarea>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-12">
                                <label for="observacoes" class="form-label">Observações</label>
                                <textarea name="observacoes" id="observacoes" class="form-control" rows="3"
                                          placeholder="Informações adicionais sobre o evento..."><?php echo htmlspecialchars($evento['observacoes'] ?? ''); ?></textarea>
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
                            <a href="/meus-agendamentos" class="btn btn-secondary">
                                <i class="bi bi-arrow-left"></i> Voltar
                            </a>
                            <button type="submit" class="btn btn-primary" id="btnEnviarSolicitacao">
                                <i class="bi bi-save"></i> Salvar Alterações
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
