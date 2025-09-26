<?php
#
# View do Dashboard do Usuário.
# É a página inicial para usuários logados, mostrando atalhos para as
# principais funcionalidades e uma lista de eventos onde ele marcou presença.
#
?>
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1>Bem-vindo, <?php echo htmlspecialchars($user['nome']); ?>!</h1>
            <p class="text-muted">Este é o seu painel de controle.</p>
        </div>
        <div>
        <span class="badge bg-primary fs-6">
            Perfil: <?php echo htmlspecialchars(ucfirst($user['role'])); ?>
        </span>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="card h-100 shadow-sm">
                <div class="card-body d-flex flex-column">
                    <h5 class="card-title"><i class="bi bi-calendar-check-fill text-primary"></i> Agenda da Quadra</h5>
                    <p class="card-text">Confira os próximos eventos e marque sua presença.</p>
                    <a href="/agenda" class="btn btn-primary mt-auto">Ver Agenda</a>
                </div>
            </div>
        </div>

        <?php if ($user['tipo_usuario'] === 'Professor'): ?>
            <div class="col-md-6 mb-4">
                <div class="card h-100 shadow-sm border-warning">
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title"><i class="bi bi-building text-warning"></i> Aluguel da Quadra</h5>
                        <p class="card-text">Solicite o uso da quadra esportiva para suas atividades acadêmicas.</p>
                        <a href="/agendar-evento" class="btn btn-warning mt-auto">Solicitar Aluguel</a>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <?php
        $tipo_usuario = $user['tipo_usuario'] ?? '';
        $role = $user['role'] ?? '';
        $can_schedule = ($tipo_usuario === 'Professor') || ($role === 'superadmin') || ($role === 'admin' && $tipo_usuario === 'Membro das Atléticas');

        if ($can_schedule): ?>
            <div class="col-md-6 mb-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title"><i class="bi bi-journal-text text-success"></i> Meus Agendamentos</h5>
                        <p class="card-text">Acompanhe o status das suas solicitações de uso da quadra.</p>
                        <a href="/meus-agendamentos" class="btn btn-success mt-auto">Ver Solicitações</a>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <div class="row mt-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="bi bi-calendar-check"></i> Meus Eventos (Presença Marcada)</h5>
                </div>
                <div class="card-body">
                    <?php if (empty($eventos_presenca)): ?>
                        <div class="text-center text-muted py-4">
                            <i class="bi bi-calendar-x fs-1 text-muted"></i>
                            <p class="mt-2">Você ainda não marcou presença em nenhum evento.</p>
                            <a href="/agenda" class="btn btn-outline-primary">Ver Agenda de Eventos</a>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Evento</th>
                                        <th>Data</th>
                                        <th>Período</th>
                                        <th>Responsável</th>
                                        <th>Tipo</th>
                                        <th>Atlética</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($eventos_presenca as $evento): ?>
                                        <tr>
                                            <td>
                                                <strong><?php echo htmlspecialchars($evento['titulo']); ?></strong>
                                                <?php if ($evento['esporte_tipo']): ?>
                                                    <br><small class="text-muted"><?php echo htmlspecialchars($evento['esporte_tipo']); ?></small>
                                                <?php endif; ?>
                                            </td>
                                            <td><?php echo date('d/m/Y', strtotime($evento['data_agendamento'])); ?></td>
                                            <td><span class="badge bg-secondary"><?php echo ucfirst($evento['periodo']); ?></span></td>
                                            <td><?php echo htmlspecialchars($evento['responsavel']); ?></td>
                                            <td><span class="badge <?php echo $evento['tipo_agendamento'] === 'esportivo' ? 'bg-success' : 'bg-primary'; ?>"><?php echo ucfirst($evento['tipo_agendamento']); ?></span></td>
                                            <td><?php echo $evento['atletica_nome'] ? htmlspecialchars($evento['atletica_nome']) : '-'; ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>