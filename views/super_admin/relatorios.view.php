<?php
#
# View da página de Relatórios do Super Admin.
# Contém os formulários para gerar diferentes tipos de relatórios e
# exibe os resultados na mesma página.
#
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

<?php if ($dados_relatorio): ?>
<div class="card mt-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4>Resultado do Relatório</h4>
        <form action="/superadmin/relatorios/imprimir" method="post" target="_blank">
            <input type="hidden" name="tipo_relatorio" value="<?php echo htmlspecialchars($dados_relatorio['tipo']); ?>">
            <?php if ($dados_relatorio['tipo'] === 'periodo'): ?>
                <input type="hidden" name="data_inicio" value="<?php echo htmlspecialchars($dados_relatorio['periodo']['inicio']); ?>">
                <input type="hidden" name="data_fim" value="<?php echo htmlspecialchars($dados_relatorio['periodo']['fim']); ?>">
            <?php elseif ($dados_relatorio['tipo'] === 'evento_especifico'): ?>
                <input type="hidden" name="evento_id" value="<?php echo htmlspecialchars($dados_relatorio['evento']['id']); ?>">
            <?php elseif ($dados_relatorio['tipo'] === 'usuario'): ?>
                <input type="hidden" name="usuario_id" value="<?php echo htmlspecialchars($dados_relatorio['usuario']['id']); ?>">
            <?php endif; ?>
            <button type="submit" class="btn btn-secondary"><i class="bi bi-printer-fill"></i> Imprimir / Salvar PDF</button>
        </form>
    </div>
    <div class="card-body">
        <?php if ($dados_relatorio['tipo'] === 'periodo'): $stats = $dados_relatorio['estatisticas']; ?>
            <h5>Resumo do Período (<?php echo date('d/m/Y', strtotime($dados_relatorio['periodo']['inicio'])); ?> a <?php echo date('d/m/Y', strtotime($dados_relatorio['periodo']['fim'])); ?>)</h5>
            <ul class="list-group">
                <li class="list-group-item d-flex justify-content-between align-items-center">Total de Eventos no Período <span class="badge bg-primary rounded-pill"><?php echo $stats['total_eventos']; ?></span></li>
                <li class="list-group-item d-flex justify-content-between align-items-center">Eventos Aprovados <span class="badge bg-success rounded-pill"><?php echo $stats['eventos_aprovados']; ?></span></li>
                <li class="list-group-item d-flex justify-content-between align-items-center">Público Estimado (soma inicial) <span class="badge bg-secondary rounded-pill"><?php echo $stats['total_pessoas_estimadas'] ?? 0; ?></span></li>
                <li class="list-group-item d-flex justify-content-between align-items-center">Total de Presenças Confirmadas <span class="badge bg-info rounded-pill"><?php echo $stats['total_presencas'] ?? 0; ?></span></li>
            </ul>
        <?php endif; ?>

        <?php if ($dados_relatorio['tipo'] === 'evento_especifico'): ?>
            <h5>Detalhes do Evento: <?php echo htmlspecialchars($dados_relatorio['evento']['titulo']); ?></h5>
            <ul class="list-group">
                <li class="list-group-item"><strong>Responsável:</strong> <?php echo htmlspecialchars($dados_relatorio['evento']['responsavel']); ?></li>
                <li class="list-group-item"><strong>Público Estimado:</strong> <?php echo $dados_relatorio['evento']['estimativa_participantes'] ?? 0; ?></li>
                <li class="list-group-item"><strong>Presenças Confirmadas:</strong> <?php echo $dados_relatorio['evento']['total_presencas']; ?></li>
                <?php if (!empty($dados_relatorio['evento']['participantes_formatados'])): ?>
                    <li class="list-group-item">
                        <strong>Lista de Participantes:</strong>
                        <div class="mt-2">
                            <?php foreach ($dados_relatorio['evento']['participantes_formatados'] as $participante): ?>
                                <span class="badge bg-info me-1"><?php echo htmlspecialchars($participante); ?></span>
                            <?php endforeach; ?>
                        </div>
                    </li>
                <?php endif; ?>
            </ul>
        <?php endif; ?>

        <?php if ($dados_relatorio['tipo'] === 'usuario'): ?>
            <h5>Atividades de: <?php echo htmlspecialchars($dados_relatorio['usuario']['nome']); ?></h5>
            <ul class="list-group">
                <li class="list-group-item"><strong>Agendamentos Criados:</strong> <?php echo count($dados_relatorio['agendamentos']); ?></li>
                <li class="list-group-item"><strong>Presenças Marcadas:</strong> <?php echo count($dados_relatorio['presencas']); ?></li>
            </ul>
        <?php endif; ?>
    </div>
</div>
<?php endif; ?>