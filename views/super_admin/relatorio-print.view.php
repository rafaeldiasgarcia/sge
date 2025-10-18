<?php
/**
 * VIEW: RELATÓRIO PARA IMPRESSÃO (SUPER ADMIN)
 * Layout simplificado sem navegação, otimizado para impressão e exportação PDF.
 * CONTROLLER: SuperAdminController::relatorioPrint()
 */
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title ?? 'Relatório SGE'; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #fff; }
        .container { max-width: 900px; }
        .report-header { border-bottom: 2px solid #dee2e6; padding-bottom: 1rem; margin-bottom: 2rem; }
        .table { font-size: 0.9rem; }
        .summary-card { background-color: #f8f9fa; border: 1px solid #dee2e6; }
        @media print { .no-print { display: none; } body { margin: 0; padding: 0; } }
    </style>
</head>
<body>
<div class="container my-5">
    <div class="report-header text-center">
        <h2>UNIFIO</h2>
        <p class="lead">Relatório do Sistema de Gerenciamento de Eventos</p>
    </div>

    <?php if ($dados_relatorio): ?>
        <?php if ($dados_relatorio['tipo'] === 'periodo'): $stats = $dados_relatorio['estatisticas']; ?>
            <h4>Relatório Geral por Período</h4>
            <p class="text-muted"><?php echo date('d/m/Y', strtotime($dados_relatorio['periodo']['inicio'])); ?> a <?php echo date('d/m/Y', strtotime($dados_relatorio['periodo']['fim'])); ?></p>
            <div class="row g-3 mb-4">
                <div class="col-md-4"><div class="summary-card p-3 rounded text-center"><h5><?php echo $stats['total_eventos']; ?></h5><small class="text-muted">Eventos Totais</small></div></div>
                <div class="col-md-4"><div class="summary-card p-3 rounded text-center"><h5><?php echo $stats['total_pessoas_estimadas'] ?? 0; ?></h5><small class="text-muted">Público Previsto (soma)</small></div></div>
                <div class="col-md-4"><div class="summary-card p-3 rounded text-center"><h5><?php echo $stats['total_presencas'] ?? 0; ?></h5><small class="text-muted">Presenças Confirmadas</small></div></div>
            </div>
            <h6>Lista de Eventos no Período</h6>
            <table class="table table-striped table-bordered">
                <thead><tr><th>Data</th><th>Título</th><th>Tipo</th><th>Responsável</th><th>Público Previsto</th><th>Presenças</th></tr></thead>
                <tbody>
                <?php foreach ($dados_relatorio['eventos_lista'] as $evento): ?>
                    <tr>
                        <td><?php echo date('d/m/Y', strtotime($evento['data_agendamento'])); ?></td>
                        <td><?php echo htmlspecialchars($evento['titulo']); ?></td>
                        <td>
                            <?php 
                            echo ucfirst($evento['tipo_agendamento']);
                            if ($evento['tipo_agendamento'] === 'esportivo' && !empty($evento['esporte_tipo'])) {
                                echo ' - ' . htmlspecialchars($evento['esporte_tipo']);
                            }
                            ?>
                        </td>
                        <td><?php echo htmlspecialchars($evento['responsavel']); ?></td>
                        <td><?php echo $evento['estimativa_participantes'] ?? 0; ?></td>
                        <td><?php echo $evento['total_presencas']; ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>

        <?php if ($dados_relatorio['tipo'] === 'evento_especifico'): $evento = $dados_relatorio['evento']; ?>
            <h4>Relatório de Evento Específico</h4>
            <div class="summary-card p-3 rounded mb-4">
                <h5><?php echo htmlspecialchars($evento['titulo']); ?></h5>
                <p class="mb-1">
                    <strong>Data:</strong> <?php echo date('d/m/Y', strtotime($evento['data_agendamento'])); ?> | 
                    <strong>Tipo:</strong> <?php echo ucfirst($evento['tipo_agendamento']); ?><?php if ($evento['tipo_agendamento'] === 'esportivo' && !empty($evento['esporte_tipo'])): ?> - <?php echo htmlspecialchars($evento['esporte_tipo']); ?><?php endif; ?> | 
                    <strong>Responsável:</strong> <?php echo htmlspecialchars($evento['responsavel']); ?>
                </p>
                <p class="mb-0"><strong>Público Previsto:</strong> <?php echo $evento['estimativa_participantes'] ?? 0; ?> | <strong class="text-success">Presenças Confirmadas:</strong> <?php echo $evento['total_presencas']; ?></p>
                
                <?php if ($evento['tipo_agendamento'] === 'esportivo' && isset($evento['possui_materiais'])): ?>
                    <hr class="my-2">
                    <p class="mb-0">
                        <strong>Materiais:</strong>
                        <?php if ($evento['possui_materiais'] == 1): ?>
                            <span class="badge bg-success">✓ Possui materiais próprios</span>
                        <?php else: ?>
                            <span class="badge bg-warning text-dark">⚠ Não possui materiais próprios</span>
                        <?php endif; ?>
                    </p>
                    <?php if ($evento['possui_materiais'] == 0 && !empty($evento['materiais_necessarios'])): ?>
                        <p class="mb-0 mt-2">
                            <strong>Materiais Necessários/Utilizados:</strong><br>
                            <small style="white-space: pre-line; display: block; background: #f8f9fa; padding: 8px; border: 1px solid #dee2e6; border-radius: 4px; margin-top: 5px;"><?php echo htmlspecialchars($evento['materiais_necessarios']); ?></small>
                        </p>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
            <h6>Lista de Presenças Confirmadas</h6>
            <table class="table table-striped table-bordered">
                <thead><tr><th>Nome</th><th>Email</th><th>RA</th></tr></thead>
                <tbody>
                <?php if (empty($dados_relatorio['presencas'])): ?>
                    <tr><td colspan="3">Nenhuma presença registrada.</td></tr>
                <?php else: ?>
                    <?php foreach ($dados_relatorio['presencas'] as $p): ?>
                        <tr><td><?php echo htmlspecialchars($p['nome']); ?></td><td><?php echo htmlspecialchars($p['email']); ?></td><td><?php echo htmlspecialchars($p['ra'] ?? 'N/A'); ?></td></tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        <?php endif; ?>

        <?php if ($dados_relatorio['tipo'] === 'usuario'): $usuario = $dados_relatorio['usuario']; ?>
            <h4>Relatório de Atividades do Usuário</h4>
            <div class="summary-card p-3 rounded mb-4">
                <h5><?php echo htmlspecialchars($usuario['nome']); ?></h5>
                <p class="mb-1"><strong>Email:</strong> <?php echo htmlspecialchars($usuario['email']); ?> | <strong>RA:</strong> <?php echo htmlspecialchars($usuario['ra'] ?? 'N/A'); ?></p>
                <p class="mb-0"><strong>Agendamentos Criados:</strong> <?php echo count($dados_relatorio['agendamentos']); ?> | <strong class="text-success">Presenças Marcadas:</strong> <?php echo count($dados_relatorio['presencas']); ?></p>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <h6>Agendamentos Criados (<?php echo count($dados_relatorio['agendamentos']); ?>)</h6>
                    <table class="table table-sm table-striped table-bordered">
                        <thead><tr><th>Título</th><th>Tipo</th><th>Data</th><th>Status</th></tr></thead>
                        <tbody>
                        <?php if(empty($dados_relatorio['agendamentos'])): ?><tr><td colspan="4">Nenhum.</td></tr><?php endif; ?>
                        <?php foreach ($dados_relatorio['agendamentos'] as $ag): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($ag['titulo']); ?></td>
                                <td>
                                    <?php 
                                    echo ucfirst($ag['tipo_agendamento'] ?? 'N/A');
                                    if (!empty($ag['esporte_tipo']) && $ag['tipo_agendamento'] === 'esportivo') {
                                        echo '<br><small>' . htmlspecialchars($ag['esporte_tipo']) . '</small>';
                                    }
                                    ?>
                                </td>
                                <td><?php echo date('d/m/Y', strtotime($ag['data_agendamento'])); ?></td>
                                <td><?php echo ucfirst($ag['status']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <div class="col-md-6">
                    <h6>Presenças Marcadas (<?php echo count($dados_relatorio['presencas']); ?>)</h6>
                    <table class="table table-sm table-striped table-bordered">
                        <thead><tr><th>Evento</th><th>Data</th></tr></thead>
                        <tbody>
                        <?php if(empty($dados_relatorio['presencas'])): ?><tr><td colspan="2">Nenhuma.</td></tr><?php endif; ?>
                        <?php foreach ($dados_relatorio['presencas'] as $pr): ?>
                            <tr><td><?php echo htmlspecialchars($pr['titulo']); ?></td><td><?php echo date('d/m/Y', strtotime($pr['data_agendamento'])); ?></td></tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endif; ?>
    <?php endif; ?>

    <hr class="mt-5">
    <p class="text-center text-muted small">Relatório gerado em <?php echo date('d/m/Y \à\s H:i:s'); ?></p>
</div>
<script>window.onload = () => window.print();</script>
</body>
</html>