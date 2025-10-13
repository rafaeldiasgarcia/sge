<?php
/**
 * VIEW: GERENCIAR ADMINISTRADORES (SUPER ADMIN)
 * Gerenciar admins de atléticas: promover usuários elegíveis e rebaixar admins.
 * CONTROLLER: SuperAdminController::gerenciarAdmins()
 */
?>
<h2>Gerenciar Administradores de Atléticas</h2>
<?php if (isset($_SESSION['success_message'])): ?>
    <div class="alert alert-success"><?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?></div>
<?php endif; ?>

<div class="card mb-4">
    <div class="card-body">
        <h5>Promover Membro para Admin</h5>
        <p class="text-muted">A lista abaixo mostra apenas usuários que são "Membro das Atléticas" e podem ser promovidos.</p>
        <form method="post" action="/superadmin/admins/promover" class="row g-3 align-items-center">
            <div class="col-md-10">
                <select name="aluno_id" class="form-select" required>
                    <option value="">-- Selecione o Membro para Promover --</option>
                    <?php foreach ($elegiveis as $row): ?>
                        <option value="<?php echo $row['id']; ?>"><?php echo htmlspecialchars($row['nome']) . ' (Atlética: ' . htmlspecialchars($row['atletica_nome']) . ')'; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2"><button type="submit" class="btn btn-primary w-100">Promover</button></div>
        </form>
    </div>
</div>

<h5>Admins Atuais</h5>
<table class="table table-striped">
    <thead><tr><th>Nome</th><th>Atlética Administrada</th><th>Ações</th></tr></thead>
    <tbody>
    <?php foreach ($admins as $row): ?>
        <tr>
            <td><?php echo htmlspecialchars($row['nome']); ?></td>
            <td><?php echo htmlspecialchars($row['atletica_nome']); ?></td>
            <td>
                <form method="post" action="/superadmin/admins/rebaixar" class="d-inline" onsubmit="return confirm('Tem certeza?');">
                    <input type="hidden" name="admin_id" value="<?php echo $row['id']; ?>">
                    <button type="submit" class="btn btn-sm btn-danger">Rebaixar para Usuário</button>
                </form>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>