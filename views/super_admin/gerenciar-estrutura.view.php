<?php
/**
 * VIEW: GERENCIAR ESTRUTURA ACADÊMICA (SUPER ADMIN)
 * CRUD completo de Cursos e Atléticas. Interface com abas.
 * CONTROLLER: SuperAdminController::gerenciarEstrutura()
 */
?>
<h2>Gerenciar Estrutura Acadêmica</h2>
<p>Gerencie os cursos e as atléticas que formam a base do sistema.</p>

<?php if (isset($_SESSION['success_message'])): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<div class="accordion" id="accordionEstrutura">
    <div class="accordion-item">
        <h2 class="accordion-header" id="headingCursos">
            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseCursos" aria-expanded="true" aria-controls="collapseCursos">
                <i class="bi bi-book-fill me-2"></i> Gerenciar Cursos
            </button>
        </h2>
        <div id="collapseCursos" class="accordion-collapse collapse show" aria-labelledby="headingCursos" data-bs-parent="#accordionEstrutura">
            <div class="accordion-body">
                <div class="card mb-4">
                    <div class="card-header">Adicionar Novo Curso</div>
                    <div class="card-body">
                        <form method="post" action="/superadmin/cursos/criar">
                            <div class="row g-3 align-items-end">
                                <div class="col-md-6">
                                    <label for="nome_curso" class="form-label">Nome do Curso</label>
                                    <input type="text" name="nome" id="nome_curso" class="form-control" required>
                                </div>
                                <div class="col-sm-4">
                                    <label for="atletica_id" class="form-label">Associar à Atlética (Opcional)</label>
                                    <select name="atletica_id" id="atletica_id" class="form-select">
                                        <option value="">Nenhuma</option>
                                        <?php foreach ($atleticas_disponiveis as $row): ?>
                                            <option value="<?php echo $row['id']; ?>"><?php echo htmlspecialchars($row['nome']); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <button type="submit" class="btn btn-primary w-100">Adicionar</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <h5>Cursos Cadastrados</h5>
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-dark">
                        <tr>
                            <th>Nome do Curso</th>
                            <th>Atlética Associada</th>
                            <th class="text-end">Ações</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($cursos as $row): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['nome']); ?></td>
                                <td><span class="badge bg-secondary"><?php echo htmlspecialchars($row['atletica_nome'] ?? 'Nenhuma'); ?></span></td>
                                <td class="text-end">
                                    <a href="/superadmin/curso/editar?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-info">Editar</a>
                                    <form method="post" action="/superadmin/curso/excluir" class="d-inline" onsubmit="return confirm('Tem certeza?');">
                                        <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                        <button type="submit" class="btn btn-sm btn-danger">Excluir</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="accordion-item">
        <h2 class="accordion-header" id="headingAtleticas">
            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseAtleticas" aria-expanded="false" aria-controls="collapseAtleticas">
                <i class="bi bi-shield-shaded me-2"></i> Gerenciar Atléticas
            </button>
        </h2>
        <div id="collapseAtleticas" class="accordion-collapse collapse" aria-labelledby="headingAtleticas" data-bs-parent="#accordionEstrutura">
            <div class="accordion-body">
                <div class="card mb-4">
                    <div class="card-header">Adicionar Nova Atlética</div>
                    <div class="card-body">
                        <form method="post" action="/superadmin/atleticas/criar">
                            <div class="input-group">
                                <input type="text" name="nome" class="form-control" placeholder="Nome da nova atlética" required>
                                <button type="submit" class="btn btn-primary">Adicionar</button>
                            </div>
                        </form>
                    </div>
                </div>

                <h5>Atléticas Cadastradas</h5>
                <ul class="list-group">
                    <?php foreach ($todas_atleticas as $row): ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <?php echo htmlspecialchars($row['nome']); ?>
                            <div class="btn-group" role="group">
                                <a href="/superadmin/atletica/editar?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-info">Editar</a>
                                <form method="post" action="/superadmin/atletica/excluir" class="d-inline" onsubmit="return confirm('Atenção! Excluir uma atlética irá desvincular cursos. Deseja continuar?');">
                                    <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                    <button type="submit" class="btn btn-sm btn-danger">Excluir</button>
                                </form>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>
</div>