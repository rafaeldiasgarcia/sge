<?php
#
# View para o Admin de Atlética gerenciar as solicitações de novos membros.
# Permite aprovar ou recusar pedidos de alunos para entrar na atlética.
#
?>
<h2>Gerenciar Solicitações de Membros</h2>
<p>Aprove ou recuse os pedidos de alunos para entrar na sua atlética.</p>

<?php if (isset($_SESSION['success_message'])): ?>
    <div class="alert alert-success"><?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?></div>
<?php endif; ?>

<div class="card">
    <div class="card-header">Solicitações Pendentes</div>
    <div class="card-body">
        <table class="table table-hover">
            <thead><tr><th>Aluno</th><th>Curso</th><th>Ações</th></tr></thead>
            <tbody>
            <?php if (empty($pendentes)): ?>
                <tr><td colspan="3" class="text-center">Nenhuma solicitação pendente.</td></tr>
            <?php else: ?>
                <?php foreach($pendentes as $req): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($req['nome']); ?></td>
                        <td><?php echo htmlspecialchars($req['curso_nome']); ?></td>
                        <td>
                            <form method="post" action="/admin/atletica/membros/acao" class="d-inline">
                                <input type="hidden" name="aluno_id" value="<?php echo $req['id']; ?>">
                                <button type="submit" name="acao" value="aprovar" class="btn btn-sm btn-success">Aprovar</button>
                                <button type="submit" name="acao" value="recusar" class="btn btn-sm btn-danger">Recusar</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>