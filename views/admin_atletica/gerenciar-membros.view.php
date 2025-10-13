<?php
/**
 * ============================================================================
 * VIEW: GERENCIAR SOLICITAÇÕES DE MEMBROS
 * ============================================================================
 * 
 * Interface simplificada focada exclusivamente em aprovar ou recusar
 * solicitações de entrada na atlética (sem gerenciamento de membros ativos).
 * 
 * FUNCIONALIDADES:
 * - Visualizar todas as solicitações pendentes de entrada
 * - Aprovar solicitação: torna o aluno membro oficial da atlética
 * - Recusar solicitação: rejeita o pedido (aluno pode solicitar novamente)
 * - Badge com contador de solicitações pendentes
 * 
 * DIFERENÇA:
 * - Este arquivo foca apenas nas solicitações pendentes, sem incluir
 *   o gerenciamento completo de membros ativos
 * 
 * VARIÁVEIS RECEBIDAS:
 * @var array $pendentes - Lista de solicitações aguardando aprovação
 *                         [id, nome, curso_nome]
 * 
 * FLUXO DE APROVAÇÃO:
 * 1. Aluno solicita entrada na atlética
 * 2. Solicitação aparece nesta view
 * 3. Admin aprova → aluno vira membro com role 'usuario'
 * 4. Admin recusa → solicitação é rejeitada
 * 
 * AÇÕES DISPONÍVEIS:
 * - POST /admin/atletica/membros/acao
 *   Ações: aprovar, recusar
 * 
 * CONTROLLER: AdminAtleticaController::gerenciarMembros()
 */
?>

<h2>Gerenciar Solicitações de Membros</h2>
<p>Aprove ou recuse os pedidos de alunos para entrar na sua atlética.</p>

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

<div class="card">
    <div class="card-header">
        <strong>Solicitações Pendentes</strong>
        <span class="badge bg-warning ms-2"><?php echo count($pendentes ?? []); ?></span>
    </div>
    <div class="card-body">
        <?php if (empty($pendentes)): ?>
            <div class="text-center py-4">
                <i class="bi bi-inbox fs-1 text-muted"></i>
                <h5 class="text-muted mt-2">Nenhuma solicitação pendente</h5>
                <p class="text-muted">Quando alunos solicitarem entrada na sua atlética, eles aparecerão aqui.</p>
            </div>
        <?php else: ?>
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
                        <?php foreach($pendentes as $req): ?>
                            <tr>
                                <td>
                                    <strong><?php echo htmlspecialchars($req['nome']); ?></strong>
                                </td>
                                <td><?php echo htmlspecialchars($req['curso_nome']); ?></td>
                                <td>
                                    <form method="post" action="/admin/atletica/membros/acao" class="d-inline"
                                          onsubmit="return confirm('Tem certeza que deseja executar esta ação?')">
                                        <input type="hidden" name="aluno_id" value="<?php echo $req['id']; ?>">
                                        <div class="btn-group" role="group">
                                            <button type="submit" name="acao" value="aprovar" class="btn btn-sm btn-success">
                                                <i class="bi bi-check-circle"></i> Aprovar
                                            </button>
                                            <button type="submit" name="acao" value="recusar" class="btn btn-sm btn-danger">
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

<?php if (!empty($pendentes)): ?>
    <div class="mt-3">
        <div class="card">
            <div class="card-body">
                <h6><i class="bi bi-info-circle text-primary"></i> Informações Importantes</h6>
                <ul class="mb-0">
                    <li><strong>Aprovar:</strong> O aluno se tornará membro oficial da atlética e poderá se inscrever em modalidades.</li>
                    <li><strong>Recusar:</strong> A solicitação será rejeitada e o aluno poderá fazer uma nova solicitação no futuro.</li>
                </ul>
            </div>
        </div>
    </div>
<?php endif; ?>
