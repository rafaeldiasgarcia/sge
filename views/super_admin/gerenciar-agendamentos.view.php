<?php
#
# View para o Super Admin aprovar ou rejeitar solicitações de agendamento.
# Lista todas as solicitações com status 'pendente'.
#
?>
<h2>Aprovar Agendamentos da Quadra</h2>
<p>Gerencie as solicitações de uso da quadra feitas pelos usuários.</p>

<?php if (isset($_SESSION['success_message'])): ?>
    <div class="alert alert-success"><?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?></div>
<?php endif; ?>
<?php if (isset($_SESSION['error_message'])): ?>
    <div class="alert alert-danger"><?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?></div>
<?php endif; ?>

<div class="card">
    <div class="card-header">Solicitações Pendentes</div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                <tr>
                    <th>Solicitante</th>
                    <th>Título</th>
                    <th>Data</th>
                    <th>Ações</th>
                </tr>
                </thead>
                <tbody>
                <?php if (empty($pendentes)): ?>
                    <tr><td colspan="4" class="text-center">Nenhuma solicitação pendente.</td></tr>
                <?php else: ?>
                    <?php foreach ($pendentes as $req): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($req['solicitante']); ?></td>
                            <td><?php echo htmlspecialchars($req['titulo']); ?><br><small class="text-muted"><?php echo htmlspecialchars($req['periodo']); ?></small></td>
                            <td><?php echo date('d/m/Y', strtotime($req['data_agendamento'])); ?></td>
                            <td>
                                <form method="post" action="/superadmin/agendamentos/aprovar" class="d-inline">
                                    <input type="hidden" name="id" value="<?php echo $req['id']; ?>">
                                    <button type="submit" class="btn btn-sm btn-success">Aprovar</button>
                                </form>
                                <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal" data-id="<?php echo $req['id']; ?>" data-titulo="<?php echo htmlspecialchars($req['titulo']); ?>">
                                    Rejeitar
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Rejeitar Agendamento</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="post" action="/superadmin/agendamentos/rejeitar">
                <div class="modal-body">
                    <p>Você está rejeitando o evento: <strong id="modal-evento-titulo"></strong></p>
                    <input type="hidden" name="id" id="modal-agendamento-id">
                    <div class="mb-3">
                        <label for="motivo_rejeicao" class="form-label">Motivo da Rejeição (Obrigatório)</label>
                        <textarea name="motivo_rejeicao" id="motivo_rejeicao" class="form-control" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger">Confirmar Rejeição</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        var rejectModal = document.getElementById('rejectModal');
        rejectModal.addEventListener('show.bs.modal', function (event) {
            var button = event.relatedTarget;
            var agendamentoId = button.getAttribute('data-id');
            var agendamentoTitulo = button.getAttribute('data-titulo');
            var modalTitle = rejectModal.querySelector('#modal-evento-titulo');
            var modalInputId = rejectModal.querySelector('#modal-agendamento-id');
            modalTitle.textContent = agendamentoTitulo;
            modalInputId.value = agendamentoId;
        });
    });
</script>