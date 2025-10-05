<?php
#
# View do Dashboard do Admin de Atlética.
# Exibe estatísticas rápidas e links para as principais funcionalidades
# de gerenciamento da atlética.
#
?>
<h1>Painel do Administrador da Atlética</h1>
<p>Gerencie as inscrições, equipes e atletas da sua atlética.</p>

<div class="row">
    <div class="col-md-4 mb-4">
        <div class="card text-white bg-warning h-100">
            <div class="card-body">
                <h3 class="card-title"><?php echo $stats['eventos_confirmados']; ?></h3>
                <p class="card-text">Eventos Confirmados</p>
                <a href="/agenda" class="text-white stretched-link">Ver Agenda</a>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-4">
        <div class="card text-white bg-success h-100">
            <div class="card-body">
                <h3 class="card-title"><?php echo $stats['atletas_aprovados']; ?></h3>
                <p class="card-text">Atletas Aprovados</p>
                <a href="/admin/atletica/inscricoes" class="text-white stretched-link">Gerenciar Atletas</a>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-4">
        <div class="card text-white bg-info h-100">
            <div class="card-body">
                <h3 class="card-title"><?php echo $stats['membros_pendentes']; ?></h3>
                <p class="card-text">Solicitações de Entrada</p>
                <a href="/admin/atletica/membros" class="text-white stretched-link">Aceitar Solicitações</a>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-body">
                <h5 class="card-title">Gerenciar Inscrições</h5>
                <p class="card-text">Aprove ou recuse as candidaturas dos alunos para as modalidades da sua atlética.</p>
                <a href="/admin/atletica/inscricoes" class="btn btn-primary">Ver Inscrições</a>
            </div>
        </div>
    </div>

    <div class="col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-body">
                <h5 class="card-title">Gerenciar Participações em Eventos</h5>
                <p class="card-text">Inscreva membros da sua atlética nos eventos esportivos aprovados.</p>
                <a href="/admin/atletica/eventos" class="btn btn-primary">Gerenciar Eventos</a>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6 mb-4">
        <div class="card h-100 border-primary">
            <div class="card-body">
                <h5 class="card-title"><i class="bi bi-people-fill text-primary"></i> Gerenciar Membros da Atlética</h5>
                <p class="card-text">Visualize todos os membros da sua atlética e gerencie seus status e permissões.</p>
                <a href="/admin/atletica/gerenciar-membros" class="btn btn-primary">Gerenciar Membros</a>
            </div>
        </div>
    </div>
</div>