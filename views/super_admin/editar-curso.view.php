<?php
/**
 * VIEW: EDITAR CURSO (SUPER ADMIN)
 * Formulário para editar nome do curso e associação com atlética.
 * CONTROLLER: SuperAdminController::editarCurso()
 */
?>
<h2>Editando Curso</h2>
<div class="card">
    <div class="card-body">
        <form method="post" action="/superadmin/curso/editar">
            <input type="hidden" name="id" value="<?php echo $curso['id']; ?>">
            <div class="mb-3">
                <label class="form-label">Nome do Curso</label>
                <input type="text" name="nome" class="form-control" value="<?php echo htmlspecialchars($curso['nome']); ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Associar à Atlética</label>
                <select name="atletica_id" class="form-select">
                    <option value="">Nenhuma</option>
                    <?php foreach ($atleticas as $a): ?>
                        <option value="<?php echo $a['id']; ?>" <?php if($curso['atletica_id'] == $a['id']) echo 'selected'; ?>><?php echo htmlspecialchars($a['nome']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="btn btn-success">Salvar Alterações</button>
            <a href="/superadmin/estrutura" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>
</div>