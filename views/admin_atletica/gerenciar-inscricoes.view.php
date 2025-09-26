<?php
#
# View para o Admin de Atlética gerenciar as inscrições de membros em modalidades.
# Permite aprovar ou recusar solicitações de inscrição.
#
?>
<h1>Gerenciar Inscrições em Modalidades</h1>
<p>Aprove ou recuse as candidaturas dos alunos para as modalidades da sua atlética.</p>

<?php if (isset($_SESSION['success_message'])): ?>
    <div class="alert alert-success"><?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?></div>
<?php endif; ?>

<div class="card mb-4">
    <div class="card-header"><h5 class="mb-0">Inscrições Pendentes</h5></div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table">
                <thead><tr><th>Aluno</th><th>Modalidade</th><th>Data</th><th>Ações</th></tr></thead>
                <tbody>
                <?php if (empty($pendentes)): ?>
                    <tr><td colspan="4" class="text-center">Nenhuma inscrição pendente.</td></tr>
                <?php else: ?>
                    <?php foreach($pendentes as $inscricao): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($inscricao['aluno_nome']); ?></td>
                            <td><?php echo htmlspecialchars($inscricao['modalidade_nome']); ?></td>
                            <td><?php echo date('d/m/Y H:i', strtotime($inscricao['data_inscricao'])); ?></td>
                            <td>
                                <form action="/admin/atletica/inscricoes/acao" method="post" class="d-inline">
                                    <input type="hidden" name="inscricao_id" value="<?php echo $inscricao['id']; ?>">
                                    <button type="submit" name="acao" value="aprovar" class="btn btn-success btn-sm">Aprovar</button>
                                    <button type="submit" name="acao" value="recusar" class="btn btn-danger btn-sm">Recusar</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header"><h5 class="mb-0">Alunos Aprovados nas Modalidades</h5></div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table">
                <thead><tr><th>Aluno</th><th>Modalidade</th><th>Ações</th></tr></thead>
                <tbody>
                <?php if (empty($aprovados)): ?>
                    <tr><td colspan="3" class="text-center">Nenhum aluno aprovado.</td></tr>
                <?php else: ?>
                    <?php foreach($aprovados as $aprovado): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($aprovado['aluno_nome']); ?></td>
                            <td><?php echo htmlspecialchars($aprovado['modalidade_nome']); ?></td>
                            <td>
                                <form action="/admin/atletica/inscricoes/acao" method="post" class="d-inline">
                                    <input type="hidden" name="inscricao_id" value="<?php echo $aprovado['id']; ?>">
                                    <button type="submit" name="acao" value="remover" class="btn btn-outline-danger btn-sm" onclick="return confirm('Tem certeza?')">Remover</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>