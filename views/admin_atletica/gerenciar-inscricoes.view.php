<?php
/**
 * ============================================================================
 * VIEW: GERENCIAR INSCRIÇÕES E MEMBROS (ADMIN ATLÉTICA)
 * ============================================================================
 * 
 * Interface completa para administradores de atléticas gerenciarem o ciclo 
 * completo de membros: desde a aprovação de solicitações até a gestão de 
 * permissões e remoção.
 * 
 * ESTRUTURA:
 * - Duas abas (tabs) principais:
 *   1. Solicitações de Entrada: aprovar/recusar pedidos
 *   2. Membros da Atlética: gerenciar membros ativos
 * 
 * FUNCIONALIDADES - ABA SOLICITAÇÕES:
 * - Visualizar todas as solicitações pendentes
 * - Aprovar solicitação: torna o aluno membro oficial
 * - Recusar solicitação: rejeita o pedido (pode solicitar novamente)
 * - Badge com contador de solicitações pendentes
 * 
 * FUNCIONALIDADES - ABA MEMBROS:
 * - Listar todos os membros da atlética com seus perfis
 * - Promover membro a administrador
 * - Rebaixar administrador a membro comum
 * - Remover membro da atlética (ação irreversível)
 * - Proteção: não permite modificar o próprio perfil
 * 
 * VARIÁVEIS RECEBIDAS:
 * @var array $solicitacoes_pendentes - Solicitações aguardando aprovação
 *                                      [id, nome, curso_nome]
 * @var array $membros                - Membros ativos da atlética
 *                                      [id, nome, email, ra, curso_nome, role]
 * 
 * HIERARQUIA DE ROLES:
 * - 'usuario': membro comum da atlética
 * - 'admin': administrador da atlética (acesso a este painel)
 * 
 * AÇÕES DISPONÍVEIS:
 * - POST /admin/atletica/membros/acao (aprovar, recusar)
 * - POST /admin/atletica/gerenciar-membros/acao (promover_admin, rebaixar_admin, remover_atletica)
 * 
 * CONTROLLER: AdminAtleticaController::gerenciarInscricoes()
 */
?>

<h1>Gerenciar Inscrições e Membros</h1>
<p>Aprove solicitações de entrada na atlética e gerencie os membros, suas permissões e status.</p>

<!-- Mensagens de feedback -->
<?php if (isset($_SESSION['success_message'])): ?>
    <div class="alert alert-success">
        <?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?>
    </div>
<?php endif; ?>

<?php if (isset($_SESSION['error_message'])): ?>
    <div class="alert alert-danger">
        <?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?>
    </div>
<?php endif; ?>

<!-- ========================================================================
     NAVEGAÇÃO DE ABAS (TABS)
     Duas abas principais: Solicitações e Membros
     ======================================================================== -->
<ul class="nav nav-tabs mb-4" id="inscricoesTab" role="tablist">
    <!-- Aba 1: Solicitações de Entrada na Atlética -->
    <li class="nav-item" role="presentation">
        <button class="nav-link active" id="solicitacoes-tab" 
                data-bs-toggle="tab" data-bs-target="#solicitacoes" 
                type="button" role="tab">
            <i class="bi bi-person-plus"></i> Solicitações de Entrada
            <?php if (!empty($solicitacoes_pendentes)): ?>
                <span class="badge bg-warning text-dark ms-1">
                    <?php echo count($solicitacoes_pendentes); ?>
                </span>
            <?php endif; ?>
        </button>
    </li>
    
    <!-- Aba 2: Membros da Atlética -->
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="membros-tab" 
                data-bs-toggle="tab" data-bs-target="#membros" 
                type="button" role="tab">
            <i class="bi bi-people-fill"></i> Membros da Atlética
            <?php if (!empty($membros)): ?>
                <span class="badge bg-primary ms-1">
                    <?php echo count($membros); ?>
                </span>
            <?php endif; ?>
        </button>
    </li>
</ul>

<!-- ========================================================================
     CONTEÚDO DAS ABAS
     ======================================================================== -->
<div class="tab-content" id="inscricoesTabContent">

    <!-- ========================================================================
         ABA 1: SOLICITAÇÕES DE ENTRADA NA ATLÉTICA
         Exibe pedidos de alunos para entrar na atlética e permite aprovar/recusar
         ======================================================================== -->
    <div class="tab-pane fade show active" id="solicitacoes" role="tabpanel">
        <div class="card">
            <div class="card-header">
                <strong>Solicitações Pendentes de Entrada na Atlética</strong>
                <span class="badge bg-warning ms-2">
                    <?php echo count($solicitacoes_pendentes ?? []); ?>
                </span>
            </div>
            <div class="card-body">
                <?php if (empty($solicitacoes_pendentes)): ?>
                    <!-- Estado vazio: Nenhuma solicitação pendente -->
                    <div class="text-center py-4">
                        <i class="bi bi-inbox fs-1 text-muted"></i>
                        <h5 class="text-muted mt-2">Nenhuma solicitação pendente</h5>
                        <p class="text-muted">
                            Quando alunos solicitarem entrada na sua atlética, eles aparecerão aqui.
                        </p>
                    </div>
                <?php else: ?>
                    <!-- Tabela de solicitações pendentes -->
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th><i class="bi bi-person"></i> Aluno</th>
                                    <th><i class="bi bi-book"></i> Curso</th>
                                    <th><i class="bi bi-tools"></i> Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($solicitacoes_pendentes as $req): ?>
                                    <tr>
                                        <td>
                                            <strong><?php echo htmlspecialchars($req['nome']); ?></strong>
                                        </td>
                                        <td><?php echo htmlspecialchars($req['curso_nome']); ?></td>
                                        <td>
                                            <!-- Formulário de ações: Aprovar ou Recusar -->
                                            <form method="post" 
                                                  action="/admin/atletica/membros/acao" 
                                                  class="d-inline"
                                                  onsubmit="return confirm('Tem certeza que deseja executar esta ação?')">
                                                <input type="hidden" name="aluno_id" 
                                                       value="<?php echo $req['id']; ?>">
                                                <div class="btn-group" role="group">
                                                    <button type="submit" name="acao" value="aprovar" 
                                                            class="btn btn-sm btn-success">
                                                        <i class="bi bi-check-circle"></i> Aprovar
                                                    </button>
                                                    <button type="submit" name="acao" value="recusar" 
                                                            class="btn btn-sm btn-danger">
                                                        <i class="bi bi-x-circle"></i> Recusar
                                                    </button>
                                                </div>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <?php if (!empty($solicitacoes_pendentes)): ?>
            <!-- Card informativo sobre as ações -->
            <div class="mt-3">
                <div class="card border-info">
                    <div class="card-body">
                        <h6><i class="bi bi-info-circle text-info"></i> Informações Importantes</h6>
                        <ul class="mb-0">
                            <li><strong>Aprovar:</strong> O aluno se tornará membro oficial da atlética e poderá se inscrever em modalidades.</li>
                            <li><strong>Recusar:</strong> A solicitação será rejeitada e o aluno poderá fazer uma nova solicitação no futuro.</li>
                        </ul>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- ========================================================================
         ABA 2: MEMBROS DA ATLÉTICA
         Lista todos os membros com opções de gerenciamento de permissões
         ======================================================================== -->
    <div class="tab-pane fade" id="membros" role="tabpanel">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="bi bi-people-fill text-primary"></i> Todos os Membros da Atlética
                    <span class="badge bg-primary ms-2"><?php echo count($membros ?? []); ?></span>
                </h5>
            </div>
            <div class="card-body">
                <?php if (empty($membros)): ?>
                    <!-- Estado vazio: Nenhum membro encontrado -->
                    <div class="text-center py-4">
                        <i class="bi bi-people fs-1 text-muted"></i>
                        <h5 class="text-muted mt-2">Nenhum membro encontrado</h5>
                        <p class="text-muted">
                            Quando houver membros aprovados na atlética, eles aparecerão aqui.
                        </p>
                    </div>
                <?php else: ?>
                    <!-- Tabela de membros da atlética -->
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th><i class="bi bi-person"></i> Membro</th>
                                    <th><i class="bi bi-envelope"></i> Email</th>
                                    <th><i class="bi bi-book"></i> Curso</th>
                                    <th><i class="bi bi-shield"></i> Perfil</th>
                                    <th><i class="bi bi-tools"></i> Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($membros as $membro): ?>
                                    <tr>
                                        <!-- Coluna: Nome e RA do membro -->
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <?php if ($membro['role'] === 'admin'): ?>
                                                    <i class="bi bi-shield-fill-check text-warning me-2" 
                                                       title="Administrador"></i>
                                                <?php else: ?>
                                                    <i class="bi bi-person-circle text-primary me-2" 
                                                       title="Membro"></i>
                                                <?php endif; ?>
                                                <div>
                                                    <strong><?php echo htmlspecialchars($membro['nome']); ?></strong>
                                                    <?php if ($membro['ra']): ?>
                                                        <br><small class="text-muted">
                                                            RA: <?php echo htmlspecialchars($membro['ra']); ?>
                                                        </small>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </td>
                                        
                                        <!-- Coluna: Email -->
                                        <td>
                                            <small><?php echo htmlspecialchars($membro['email']); ?></small>
                                        </td>
                                        
                                        <!-- Coluna: Curso -->
                                        <td>
                                            <small class="text-muted">
                                                <?php echo htmlspecialchars($membro['curso_nome'] ?? 'Não definido'); ?>
                                            </small>
                                        </td>
                                        
                                        <!-- Coluna: Badge de perfil (Admin ou Membro) -->
                                        <td>
                                            <?php if ($membro['role'] === 'admin'): ?>
                                                <span class="badge bg-warning text-dark">
                                                    <i class="bi bi-shield-fill"></i> Administrador
                                                </span>
                                            <?php else: ?>
                                                <span class="badge bg-primary">
                                                    <i class="bi bi-person"></i> Membro
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        
                                        <!-- Coluna: Ações de gerenciamento -->
                                        <td>
                                            <?php if ($membro['id'] != $_SESSION['id']): // Não permite modificar a si mesmo ?>
                                                <div class="btn-group" role="group">
                                                    <?php if ($membro['role'] === 'usuario'): ?>
                                                        <!-- Botão: Promover Membro a Administrador -->
                                                        <form method="post" 
                                                              action="/admin/atletica/gerenciar-membros/acao" 
                                                              class="d-inline">
                                                            <input type="hidden" name="membro_id" 
                                                                   value="<?php echo $membro['id']; ?>">
                                                            <button type="submit" name="acao" value="promover_admin"
                                                                    class="btn btn-sm btn-outline-warning"
                                                                    onclick="return confirm('Tem certeza que deseja promover este membro a Administrador da Atlética?')"
                                                                    title="Promover a Administrador">
                                                                <i class="bi bi-arrow-up-circle"></i> Promover
                                                            </button>
                                                        </form>
                                                    <?php elseif ($membro['role'] === 'admin'): ?>
                                                        <!-- Botão: Rebaixar Administrador a Membro -->
                                                        <form method="post" 
                                                              action="/admin/atletica/gerenciar-membros/acao" 
                                                              class="d-inline">
                                                            <input type="hidden" name="membro_id" 
                                                                   value="<?php echo $membro['id']; ?>">
                                                            <button type="submit" name="acao" value="rebaixar_admin"
                                                                    class="btn btn-sm btn-outline-secondary"
                                                                    onclick="return confirm('Tem certeza que deseja rebaixar este administrador a membro comum?')"
                                                                    title="Rebaixar a Membro">
                                                                <i class="bi bi-arrow-down-circle"></i> Rebaixar
                                                            </button>
                                                        </form>
                                                    <?php endif; ?>

                                                    <!-- Botão: Remover da Atlética (Ação Irreversível) -->
                                                    <form method="post" 
                                                          action="/admin/atletica/gerenciar-membros/acao" 
                                                          class="d-inline">
                                                        <input type="hidden" name="membro_id" 
                                                               value="<?php echo $membro['id']; ?>">
                                                        <button type="submit" name="acao" value="remover_atletica"
                                                                class="btn btn-sm btn-outline-danger"
                                                                onclick="return confirm('ATENÇÃO: Esta ação removerá o membro da atlética permanentemente. Tem certeza?')"
                                                                title="Remover da Atlética">
                                                            <i class="bi bi-person-x"></i> Remover
                                                        </button>
                                                    </form>
                                                </div>
                                            <?php else: ?>
                                                <!-- Badge indicando que é o próprio usuário logado -->
                                                <span class="badge bg-info">
                                                    <i class="bi bi-person-badge"></i> Você
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <?php if (!empty($membros)): ?>
            <!-- Card informativo sobre gerenciamento de permissões -->
            <div class="mt-3">
                <div class="card border-warning">
                    <div class="card-body">
                        <h6 class="card-title text-warning">
                            <i class="bi bi-info-circle"></i> Informações sobre Administradores
                        </h6>
                        <ul class="mb-0 small">
                            <li><strong>Promover:</strong> O membro se tornará administrador da atlética com as mesmas permissões que você.</li>
                            <li><strong>Rebaixar:</strong> O administrador se tornará um membro comum, perdendo as permissões administrativas.</li>
                        </ul>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

</div>