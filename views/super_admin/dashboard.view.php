<?php
/**
 * ============================================================================
 * VIEW: DASHBOARD DO SUPER ADMINISTRADOR
 * ============================================================================
 * 
 * Painel principal com acesso a todas as funcionalidades administrativas do
 * sistema. Interface com cards organizados por categoria.
 * 
 * SEÇÕES DISPONÍVEIS:
 * - Agendamentos: solicitar, visualizar próprios, aprovar/recusar todos
 * - Usuários: gerenciar todos os usuários e coordenadores
 * - Estrutura: gerenciar atléticas, cursos e modalidades
 * - Notificações: enviar notificações globais
 * - Relatórios: gerar relatórios do sistema
 * 
 * CONTROLLER: SuperAdminController::dashboard()
 */
?>
<h1>Painel do Super Administrador</h1>
<p>Acesso total para gerenciamento da estrutura e dos usuários do sistema.</p>

<div class="row">
    <div class="col-md-4 mb-3">
        <div class="card h-100 border-warning border-2">
            <div class="card-body">
                <h5 class="card-title"><i class="bi bi-calendar-plus"></i> Solicitar Agendamento</h5>
                <p>Solicite o uso da quadra esportiva.</p>
                <a href="/agendar-evento" class="btn btn-warning">Solicitar Aluguel</a>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-3">
        <div class="card h-100 border-info border-2">
            <div class="card-body">
                <h5 class="card-title"><i class="bi bi-journal-text"></i> Meus Agendamentos</h5>
                <p>Acompanhe suas solicitações de uso da quadra.</p>
                <a href="/meus-agendamentos" class="btn btn-info">Ver Solicitações</a>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-3"><div class="card h-100 border-success border-2"><div class="card-body"><h5 class="card-title">Aprovar Agendamentos</h5><p>Aprove ou recuse os pedidos de uso da quadra.</p><a href="/superadmin/agendamentos" class="btn btn-success">Acessar</a></div></div></div>
    <div class="col-md-4 mb-3"><div class="card h-100 border-primary border-2"><div class="card-body"><h5 class="card-title">Gerenciar Usuários</h5><p>Visualize e edite todos os usuários do sistema.</p><a href="/superadmin/usuarios" class="btn btn-primary">Acessar</a></div></div></div>
    <div class="col-md-4 mb-3"><div class="card h-100"><div class="card-body"><h5 class="card-title">Gerenciar Admins</h5><p>Promova alunos a administradores.</p><a href="/superadmin/admins" class="btn btn-secondary">Acessar</a></div></div></div>
    <div class="col-md-4 mb-3">
        <div class="card h-100 border-warning border-2">
            <div class="card-body">
                <h5 class="card-title">Notificação Global</h5>
                <p>Envie notificações para todos os usuários da plataforma.</p>
                <a href="/superadmin/notificacao-global" class="btn btn-warning">Enviar</a>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-3">
        <div class="card h-100">
            <div class="card-body">
                <h5 class="card-title">Estrutura Acadêmica</h5>
                <p>Gerencie os cursos e as atléticas do sistema.</p>
                <a href="/superadmin/estrutura" class="btn btn-secondary">Acessar</a>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-3"><div class="card h-100"><div class="card-body"><h5 class="card-title">Gerenciar Modalidades</h5><p>Adicione os esportes disponíveis para os eventos.</p><a href="/superadmin/modalidades" class="btn btn-secondary">Acessar</a></div></div></div>
    <div class="col-md-4 mb-3"><div class="card h-100"><div class="card-body"><h5 class="card-title">Relatórios</h5><p>Visualize relatórios detalhados do sistema.</p><a href="/superadmin/relatorios" class="btn btn-secondary">Acessar</a></div></div></div>
</div>