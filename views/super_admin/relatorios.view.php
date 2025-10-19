<?php
/**
 * VIEW: RELATÓRIOS DO SISTEMA (SUPER ADMIN)
 * Interface para gerar e visualizar relatórios detalhados sobre eventos,
 * períodos e atividades de usuários. Permite impressão e exportação.
 * CONTROLLER: SuperAdminController::relatorios()
 */
?>
<h1>Relatórios do Sistema</h1>
<p>Gere relatórios detalhados sobre eventos, períodos e atividades de usuários.</p>

<?php if (isset($_SESSION['error_message'])): ?>
    <div class="alert alert-danger"><?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?></div>
<?php endif; ?>

<div class="row">
    <div class="col-lg-4 mb-4">
        <div class="card h-100">
            <div class="card-header">Relatório por Período</div>
            <div class="card-body d-flex flex-column">
                <form action="/superadmin/relatorios" method="post" class="flex-grow-1 d-flex flex-column">
                    <input type="hidden" name="tipo_relatorio" value="periodo">
                    <div class="mb-3"><label for="data_inicio" class="form-label">Data Início</label><input type="date" name="data_inicio" id="data_inicio" class="form-control" required></div>
                    <div class="mb-3"><label for="data_fim" class="form-label">Data Fim</label><input type="date" name="data_fim" id="data_fim" class="form-control" required></div>
                    <button type="submit" class="btn btn-primary w-100 mt-auto">Gerar</button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-lg-4 mb-4">
        <div class="card h-100">
            <div class="card-header">Relatório de Evento Específico</div>
            <div class="card-body d-flex flex-column">
                <form action="/superadmin/relatorios" method="post" class="flex-grow-1 d-flex flex-column">
                    <input type="hidden" name="tipo_relatorio" value="evento_especifico">
                    <div class="mb-3 flex-grow-1">
                        <label for="evento_id" class="form-label">Selecione o Evento</label>
                        <select name="evento_id" id="evento_id" class="form-select" required>
                            <option value="">-- Escolha um evento --</option>
                            <?php foreach ($eventos as $evento): ?>
                                <option value="<?php echo $evento['id']; ?>"><?php echo htmlspecialchars($evento['titulo']) . ' (' . date('d/m/Y', strtotime($evento['data_agendamento'])) . ')'; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 mt-auto">Gerar</button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-lg-4 mb-4">
        <div class="card h-100">
            <div class="card-header">Relatório por Usuário</div>
            <div class="card-body d-flex flex-column">
                <form action="/superadmin/relatorios" method="post" class="flex-grow-1 d-flex flex-column">
                    <input type="hidden" name="tipo_relatorio" value="usuario">
                    <div class="mb-3 flex-grow-1">
                        <label for="usuario_id" class="form-label">Selecione o Usuário</label>
                        <select name="usuario_id" id="usuario_id" class="form-select" required>
                            <option value="">-- Escolha um usuário --</option>
                            <?php foreach ($usuarios as $usuario): ?>
                                <option value="<?php echo $usuario['id']; ?>"><?php echo htmlspecialchars($usuario['nome']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 mt-auto">Gerar</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Container para resultados dos relatórios (será preenchido via AJAX) -->
<div id="relatorio-results"></div>

<!-- Script para gerenciar relatórios via AJAX -->
<script src="/js/modules/super_admin/relatorios.js"></script>