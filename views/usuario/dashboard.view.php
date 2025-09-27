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
            <h1>Bem-vindo, <?php
                $nomeCompleto = htmlspecialchars($user['nome']);
                $primeiroNome = explode(' ', $nomeCompleto)[0];
                echo $primeiroNome;
            ?>!</h1>
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
                    <h5 class="mb-0"><i class="bi bi-calendar-check"></i> Próximos Eventos (Presença Marcada)</h5>
                </div>
                <div class="card-body">
                    <?php if (empty($eventos_presenca)): ?>
                        <div class="text-center text-muted py-4">
                            <i class="bi bi-calendar-x fs-1 text-muted"></i>
                            <p class="mt-2">Você ainda não marcou presença em nenhum evento futuro.</p>
                            <a href="/agenda" class="btn btn-outline-primary">Ver Agenda de Eventos</a>
                        </div>
                    <?php else: ?>
                        <div class="row">
                            <!-- Eventos Esportivos -->
                            <div class="col-md-6">
                                <h6><i class="bi bi-trophy text-success"></i> Eventos Esportivos</h6>
                                <?php
                                $eventosEsportivos = array_filter($eventos_presenca, function($evento) {
                                    return $evento['tipo_agendamento'] === 'esportivo';
                                });
                                ?>
                                <?php if (empty($eventosEsportivos)): ?>
                                    <div class="text-center text-muted py-3">
                                        <p class="mb-0">Nenhum evento esportivo confirmado</p>
                                    </div>
                                <?php else: ?>
                                    <?php foreach ($eventosEsportivos as $evento): ?>
                                        <div class="card mb-3 border-success">
                                            <div class="card-body">
                                                <h6 class="card-title text-success"><?php echo htmlspecialchars($evento['titulo']); ?></h6>
                                                <?php if ($evento['esporte_tipo']): ?>
                                                    <small class="text-muted d-block"><?php echo htmlspecialchars($evento['esporte_tipo']); ?></small>
                                                <?php endif; ?>
                                                <p class="card-text mb-2">
                                                    <strong>Data:</strong> <?php echo date('d/m/Y', strtotime($evento['data_agendamento'])); ?><br>
                                                    <strong>Horário:</strong> <?php echo htmlspecialchars($evento['horario_periodo']); ?><br>
                                                    <strong>Responsável:</strong> <?php echo htmlspecialchars($evento['responsavel']); ?>
                                                </p>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>

                            <!-- Eventos Não Esportivos -->
                            <div class="col-md-6">
                                <h6><i class="bi bi-calendar-event text-primary"></i> Eventos Não Esportivos</h6>
                                <?php
                                $eventosNaoEsportivos = array_filter($eventos_presenca, function($evento) {
                                    return $evento['tipo_agendamento'] !== 'esportivo';
                                });
                                ?>
                                <?php if (empty($eventosNaoEsportivos)): ?>
                                    <div class="text-center text-muted py-3">
                                        <p class="mb-0">Nenhum evento não esportivo confirmado</p>
                                    </div>
                                <?php else: ?>
                                    <?php foreach ($eventosNaoEsportivos as $evento): ?>
                                        <div class="card mb-3 border-primary">
                                            <div class="card-body">
                                                <h6 class="card-title text-primary"><?php echo htmlspecialchars($evento['titulo']); ?></h6>
                                                <p class="card-text mb-2">
                                                    <strong>Data:</strong> <?php echo date('d/m/Y', strtotime($evento['data_agendamento'])); ?><br>
                                                    <strong>Horário:</strong> <?php echo htmlspecialchars($evento['horario_periodo']); ?><br>
                                                    <strong>Responsável:</strong> <?php echo htmlspecialchars($evento['responsavel']); ?>
                                                </p>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="text-center mt-3">
                            <a href="/agenda" class="btn btn-outline-primary">
                                <i class="bi bi-calendar3"></i> Ver Agenda Completa
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>