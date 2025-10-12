<?php
/**
 * ============================================================================
 * VIEW: DASHBOARD DO ADMINISTRADOR DE ATLÉTICA
 * ============================================================================
 * 
 * Painel central para administradores de atléticas gerenciarem membros e 
 * eventos esportivos.
 * 
 * FUNCIONALIDADES:
 * - Link para gerenciar solicitações de entrada na atlética
 * - Link para gerenciar participações em eventos esportivos
 * - Interface simplificada com cards de acesso rápido
 * 
 * ACESSÍVEL POR:
 * - Usuários com role 'admin' vinculados a uma atlética
 * 
 * NAVEGAÇÃO:
 * - /admin/atletica/inscricoes - Gerenciar membros e solicitações
 * - /admin/atletica/eventos - Gerenciar participações em eventos
 * 
 * CONTROLLER: AdminAtleticaController::dashboard()
 * 
 * @var array $_SESSION - Sessão do usuário com informações da atlética
 */
?>

<h1>Painel do Administrador da Atlética</h1>
<p>Gerencie as inscrições, equipes e atletas da sua atlética.</p>

<!-- ========================================================================
     CARDS DE ACESSO RÁPIDO
     ======================================================================== -->
<div class="row">
    <!-- Card: Gerenciar Inscrições e Membros -->
    <div class="col-md-6 mb-4">
        <div class="card h-100 border-primary">
            <div class="card-body">
                <h5 class="card-title">
                    <i class="bi bi-people-fill text-primary"></i> Gerenciar Inscrições e Membros
                </h5>
                <p class="card-text">
                    Aprove solicitações de entrada na atlética e gerencie os membros, suas permissões e status.
                </p>
                <a href="/admin/atletica/inscricoes" class="btn btn-primary">Gerenciar Inscrições</a>
            </div>
        </div>
    </div>

    <!-- Card: Gerenciar Participações em Eventos -->
    <div class="col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-body">
                <h5 class="card-title">Gerenciar Participações em Eventos</h5>
                <p class="card-text">
                    Inscreva membros da sua atlética nos eventos esportivos aprovados.
                </p>
                <a href="/admin/atletica/eventos" class="btn btn-primary">Gerenciar Eventos</a>
            </div>
        </div>
    </div>
</div>
