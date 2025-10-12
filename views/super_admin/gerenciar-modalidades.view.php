<?php
/**
 * VIEW: GERENCIAR MODALIDADES ESPORTIVAS (SUPER ADMIN)
 * CRUD de modalidades esportivas (adicionar, editar, excluir esportes).
 * CONTROLLER: SuperAdminController::gerenciarModalidades()
 */
?>
<h2>Gerenciar Modalidades</h2>
<p>Adicione, edite ou remova os tipos de esportes disponíveis para os eventos.</p>

<?php if (isset($_SESSION['success_message'])): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<div class="card mb-4">
    <div class="card-header">Adicionar Nova Modalidade</div>
    <div class="card-body">
        <form method="post" action="/superadmin/modalidades/criar" class="row g-3 align-items-center">
            <div class="col-md-10">
                <label for="nome" class="visually-hidden">Nome da Modalidade</label>
                <input type="text" name="nome" id="nome" class="form-control" placeholder="Nome da nova modalidade" required>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">Adicionar</button>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header">Modalidades Cadastradas</div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th class="text-end">Ações</th>
                </tr>
                </thead>
                <tbody>
                <?php if (empty($modalidades)): ?>
                    <tr>
                        <td colspan="3" class="text-center">Nenhuma modalidade cadastrada.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($modalidades as $row): ?>
                        <tr>
                            <td><?php echo $row['id']; ?></td>
                            <td><?php echo htmlspecialchars($row['nome']); ?></td>
                            <td class="text-end">
                                <a href="/superadmin/modalidade/editar?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-info">Editar</a>
                                <form method="post" action="/superadmin/modalidade/excluir" class="d-inline" onsubmit="return confirm('Tem certeza que deseja excluir esta modalidade?');">
                                    <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                    <button type="submit" class="btn btn-sm btn-danger">Excluir</button>
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