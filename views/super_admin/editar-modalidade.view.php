<?php
/**
 * VIEW: EDITAR MODALIDADE (SUPER ADMIN)
 * Formulário para editar nome de modalidade esportiva.
 * CONTROLLER: SuperAdminController::editarModalidade()
 */
?>
<h2>Editando Modalidade</h2>
<div class="card">
    <div class="card-body">
        <form method="post" action="/superadmin/modalidade/editar">
            <input type="hidden" name="id" value="<?php echo $modalidade['id']; ?>">
            <div class="mb-3">
                <label class="form-label">Nome da Modalidade</label>
                <input type="text" name="nome" class="form-control" value="<?php echo htmlspecialchars($modalidade['nome']); ?>" required>
            </div>
            <button type="submit" class="btn btn-success">Salvar Alterações</button>
            <a href="/superadmin/modalidades" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>
</div>