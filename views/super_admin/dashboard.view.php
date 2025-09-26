<?php
#
# View do Dashboard do Super Administrador.
# Apresenta um menu com links para todas as seções de gerenciamento
# de alto nível do sistema.
#
?>
<h1>Painel do Super Administrador</h1>
<p>Acesso total para gerenciamento da estrutura e dos usuários do sistema.</p>

<div class="row">
    <div class="col-md-4 mb-3"><div class="card h-100 border-success border-2"><div class="card-body"><h5 class="card-title">Aprovar Agendamentos</h5><p>Aprove ou recuse os pedidos de uso da quadra.</p><a href="/superadmin/agendamentos" class="btn btn-success">Acessar</a></div></div></div>
    <div class="col-md-4 mb-3"><div class="card h-100 border-primary border-2"><div class="card-body"><h5 class="card-title">Gerenciar Usuários</h5><p>Visualize e edite todos os usuários do sistema.</p><a href="/superadmin/usuarios" class="btn btn-primary">Acessar</a></div></div></div>
    <div class="col-md-4 mb-3"><div class="card h-100"><div class="card-body"><h5 class="card-title">Gerenciar Admins</h5><p>Promova alunos a administradores.</p><a href="/superadmin/admins" class="btn btn-secondary">Acessar</a></div></div></div>
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
</div>