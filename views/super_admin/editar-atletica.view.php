<?php
#
# View com o formulário para editar uma atlética existente.
# Usado pelo Super Administrador.
#
?>
<h2>Editando Atlética</h2>
<div class="card">
    <div class="card-body">
        <form method="post" action="/superadmin/atletica/editar">
            <input type="hidden" name="id" value="<?php echo $atletica['id']; ?>">
            <div class="mb-3">
                <label class="form-label">Nome da Atlética</label>
                <input type="text" name="nome" class="form-control" value="<?php echo htmlspecialchars($atletica['nome']); ?>" required>
            </div>
            <button type="submit" class="btn btn-success">Salvar Alterações</button>
            <a href="/superadmin/estrutura" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>
</div>