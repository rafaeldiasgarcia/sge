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
    <div class="col-md-6 mb-4">
        <div class="card h-100 border-primary">
            <div class="card-body">
                <h5 class="card-title"><i class="bi bi-people-fill text-primary"></i> Gerenciar Inscrições e Membros</h5>
                <p class="card-text">Aprove solicitações de entrada na atlética e gerencie os membros, suas permissões e status.</p>
                <a href="/admin/atletica/inscricoes" class="btn btn-primary">Gerenciar Inscrições</a>
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
