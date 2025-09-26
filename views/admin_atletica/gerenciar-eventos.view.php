<?php
#
# View para o Admin de Atlética gerenciar a participação de seus membros em eventos.
# Permite inscrever e remover membros de eventos esportivos já aprovados.
#
?>
<h2>Gerenciar Participações em Eventos Esportivos</h2>
<p>Inscreva membros da sua atlética nos eventos esportivos aprovados.</p>

<?php if (isset($_SESSION['success_message'])): ?>
    <div class="alert alert-success"><?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?></div>
<?php endif; ?>

<div class="accordion" id="accordionEventos">
    <?php if (empty($eventos)): ?>
        <div class="card"><div class="card-body text-center py-5"><h5 class="text-muted">Nenhum evento esportivo disponível.</h5></div></div>
    <?php else: ?>
        <?php foreach($eventos as $evento): ?>
            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-<?php echo $evento['id']; ?>">
                        <strong><?php echo htmlspecialchars($evento['titulo']); ?></strong>
                        <span class="badge bg-primary ms-2"><?php echo htmlspecialchars($evento['esporte_tipo']); ?></span>
                        <span class="text-muted ms-auto"><?php echo date('d/m/Y', strtotime($evento['data_agendamento'])); ?></span>
                    </button>
                </h2>
                <div id="collapse-<?php echo $evento['id']; ?>" class="accordion-collapse collapse" data-bs-parent="#accordionEventos">
                    <div class="accordion-body">
                        <div class="row">
                            <div class="col-md-7">
                                <h5>Alunos Inscritos</h5>
                                <?php if (empty($evento['inscritos'])): ?>
                                    <p class="text-muted">Nenhum aluno inscrito neste evento ainda.</p>
                                <?php else: ?>
                                    <table class="table table-sm table-striped">
                                        <thead><tr><th>Nome</th><th>RA</th><th>Ação</th></tr></thead>
                                        <tbody>
                                        <?php foreach($evento['inscritos'] as $inscrito): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($inscrito['nome']); ?></td>
                                                <td><?php echo htmlspecialchars($inscrito['ra']); ?></td>
                                                <td>
                                                    <form method="post" action="/admin/atletica/eventos/remover" onsubmit="return confirm('Tem certeza?')">
                                                        <input type="hidden" name="inscricao_id" value="<?php echo $inscrito['inscricao_id']; ?>">
                                                        <input type="hidden" name="evento_id" value="<?php echo $evento['id']; ?>">
                                                        <button type="submit" class="btn btn-sm btn-outline-danger">Remover</button>
                                                    </form>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-5 border-start">
                                <h5>Inscrever Membros</h5>
                                <?php if (empty($evento['disponiveis'])): ?>
                                    <p class="text-muted">Todos os membros já estão inscritos.</p>
                                <?php else: ?>
                                    <?php foreach($evento['disponiveis'] as $aluno): ?>
                                        <div class="d-flex justify-content-between align-items-center mb-2 p-2 border rounded">
                                            <span><?php echo htmlspecialchars($aluno['nome']); ?></span>
                                            <form method="post" action="/admin/atletica/eventos/inscrever">
                                                <input type="hidden" name="aluno_id" value="<?php echo $aluno['id']; ?>">
                                                <input type="hidden" name="evento_id" value="<?php echo $evento['id']; ?>">
                                                <button type="submit" class="btn btn-success btn-sm">Inscrever</button>
                                            </form>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>