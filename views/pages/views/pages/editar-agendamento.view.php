<?php
#
# View com o formulário para editar um agendamento existente.
# Permite que o solicitante altere os dados de uma solicitação que ainda
# está com o status 'pendente'.
#
?>
<h2>Editando Agendamento</h2>
<p>Ajuste as informações da sua solicitação. Após salvar, ela voltará para o status "Pendente" e precisará de nova aprovação.</p>

<div class="card">
    <div class="card-body">
        <form action="/agendamento/editar" method="post">
            <input type="hidden" name="id" value="<?php echo htmlspecialchars($agendamento['id']); ?>">

            <div class="mb-3">
                <label for="titulo" class="form-label">Título do Evento</label>
                <input type="text" name="titulo" id="titulo" class="form-control" value="<?php echo htmlspecialchars($agendamento['titulo']); ?>" required>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="data_agendamento" class="form-label">Data</label>
                    <input type="date" name="data_agendamento" id="data_agendamento" class="form-control" value="<?php echo htmlspecialchars($agendamento['data_agendamento']); ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="periodo" class="form-label">Período</label>
                    <select name="periodo" id="periodo" class="form-select" required>
                        <option value="primeiro" <?php if($agendamento['periodo'] == 'primeiro') echo 'selected'; ?>>1º Período (19:15 - 20:55)</option>
                        <option value="segundo" <?php if($agendamento['periodo'] == 'segundo') echo 'selected'; ?>>2º Período (21:10 - 22:50)</option>
                        <option value="manha" <?php if($agendamento['periodo'] == 'manha') echo 'selected'; ?>>Manhã</option>
                        <option value="tarde" <?php if($agendamento['periodo'] == 'tarde') echo 'selected'; ?>>Tarde</option>
                        <option value="noite" <?php if($agendamento['periodo'] == 'noite') echo 'selected'; ?>>Noite</option>
                    </select>
                </div>
            </div>
            <div class="mb-3">
                <label for="descricao" class="form-label">Breve Descrição (Opcional)</label>
                <textarea name="descricao" id="descricao" class="form-control" rows="3"><?php echo htmlspecialchars($agendamento['descricao'] ?? ''); ?></textarea>
            </div>

            <button type="submit" class="btn btn-success">Salvar e Reenviar para Aprovação</button>
            <a href="/meus-agendamentos" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>
</div>