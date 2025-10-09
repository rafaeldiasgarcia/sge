<?php
#
# View da página de Perfil do Usuário.
# Contém informações do usuário divididas em abas: Informações, Meus Eventos, Histórico e Configurações.
#
?>
<head>
    <link rel="stylesheet" href="/css/usuario.css">
</head>

<div class="container mt-4">
    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="alert alert-success">
            <i class="bi bi-check-circle"></i> <?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['error_message'])): ?>
        <div class="alert alert-danger">
            <i class="bi bi-exclamation-triangle"></i> <?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?>
        </div>
    <?php endif; ?>

    <!-- Card do Header do Perfil -->
    <div class="profile-header-card">
        <div class="profile-header-left">
            <div class="profile-avatar">
                <?php 
                    $iniciais = '';
                    $nome_completo = $user['nome'] ?? '';
                    $partes_nome = explode(' ', trim($nome_completo));
                    if (count($partes_nome) > 0) {
                        $iniciais = strtoupper(substr($partes_nome[0], 0, 1));
                    }
                    echo htmlspecialchars($iniciais);
                ?>
                <div class="profile-avatar-badge">
                    <i class="bi bi-gear-fill"></i>
                </div>
            </div>
            <div class="profile-info">
                <h2><?php echo htmlspecialchars($user['nome'] ?? ''); ?></h2>
                <p><?php echo htmlspecialchars($user['email'] ?? ''); ?></p>
                <p>Matrícula: <?php echo htmlspecialchars($user['ra'] ?? 'N/A'); ?></p>
                <p>Atlética: <?php echo htmlspecialchars($atletica_info['nome'] ?? 'Nenhuma'); ?></p>
            </div>
        </div>
        <div class="profile-header-buttons">
            <button class="btn-edit-profile" onclick="">Editar Perfil</button>
            <button class="btn-change-password" onclick="openPasswordModal()">Alterar Senha</button>
        </div>
    </div>

    <!-- Tabs de Navegação -->
    <div class="profile-tabs">
        <button class="profile-tab active" onclick="openTab('informacoes')">Informações</button>
        <button class="profile-tab" onclick="openTab('meus-eventos')">Meus Eventos</button>
        <button class="profile-tab" onclick="openTab('atletica')">Atlética</button>
    </div>

    <!-- Conteúdo das Abas -->
    
    <!-- Aba Informações -->
    <div id="informacoes" class="profile-tab-content active">
        <div class="info-section">
            <!-- Coluna Esquerda: Informações Pessoais -->
            <div class="info-card">
                <h3><i class="bi bi-person-fill"></i> Informações Pessoais</h3>
                <div class="info-item">
                    <span class="info-label">Nome Completo:</span>
                    <span class="info-value"><?php echo htmlspecialchars($user['nome'] ?? ''); ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Email:</span>
                    <span class="info-value"><?php echo htmlspecialchars($user['email'] ?? ''); ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Telefone:</span>
                    <span class="info-value"><?php echo htmlspecialchars($user['telefone'] ?? '(14) 99999-9999'); ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Data de Nascimento:</span>
                    <span class="info-value"><?php echo !empty($user['data_nascimento']) ? date('d/m/Y', strtotime($user['data_nascimento'])) : 'N/A'; ?></span>
                </div>
            </div>

            <!-- Coluna Direita: Informações Acadêmicas -->
            <div class="info-card">
                <h3><i class="bi bi-mortarboard-fill"></i> Informações Acadêmicas</h3>
                <div class="info-item">
                    <span class="info-label">Matrícula:</span>
                    <span class="info-value"><?php echo htmlspecialchars($user['ra'] ?? 'N/A'); ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Curso:</span>
                    <span class="info-value"><?php
                        if (!empty($user['curso_id']) && isset($cursos)) {
                            foreach ($cursos as $curso) {
                                if ($user['curso_id'] == $curso['id']) {
                                    echo htmlspecialchars($curso['nome']);
                                    break;
                                }
                            }
                        } else {
                            echo 'N/A';
                        }
                    ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Atlética:</span>
                    <span class="info-value"><?php echo htmlspecialchars($atletica_info['nome'] ?? 'Nenhuma'); ?></span>
                </div>
            </div>
        </div>
    </div>

    <!-- Aba Meus Eventos -->
    <div id="meus-eventos" class="profile-tab-content">
        <div class="events-list">
            <h3>Meus Eventos Inscritos</h3>
            
            <?php if (empty($meus_eventos)): ?>
                <div class="alert alert-info">
                    <i class="bi bi-info-circle"></i>
                    Você ainda não confirmou presença em nenhum evento.
                </div>
            <?php else: ?>
                <?php foreach ($meus_eventos as $evento): ?>
                    <div class="event-card" data-event-id="<?php echo $evento['id']; ?>" style="cursor: pointer;">
                        <div class="event-info">
                            <h4><?php echo htmlspecialchars($evento['titulo']); ?></h4>
                            <p>
                                <?php echo date('d/m/Y', strtotime($evento['data_agendamento'])); ?> - <?php echo $evento['horario_periodo']; ?>
                            </p>
                            <span class="event-status <?php echo strtolower($evento['status']); ?>">
                                <?php 
                                    $status_traducao = [
                                        'aprovado' => 'Confirmado',
                                        'pendente' => 'Pendente',
                                        'finalizado' => 'Finalizado'
                                    ];
                                    echo $status_traducao[$evento['status']] ?? ucfirst($evento['status']);
                                ?>
                            </span>
                        </div>
                        <div class="event-actions" onclick="event.stopPropagation();">
                            <button class="btn-details" onclick="document.querySelector('[data-event-id=&quot;<?php echo $evento['id']; ?>&quot;]').click();">
                                Ver Detalhes
                            </button>
                            <?php if ($evento['status'] === 'aprovado'): ?>
                                <form action="/agenda/presenca" method="post" style="display: inline;" onsubmit="return confirm('Tem certeza que deseja cancelar sua presença neste evento?');">
                                    <input type="hidden" name="agendamento_id" value="<?php echo $evento['id']; ?>">
                                    <input type="hidden" name="action" value="desmarcar">
                                    <input type="hidden" name="redirect_to" value="/perfil">
                                    <button type="submit" class="btn-cancel">Cancelar Presença</button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- Aba Atlética -->
    <div id="atletica" class="profile-tab-content">
        <div class="card">
            <div class="card-header"><strong>Vínculo com Atlética</strong></div>
            <div class="card-body">
                <?php if (!$user['curso_id']): ?>
                    <!-- Usuário sem curso definido -->
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i>
                        <strong>Você não pode fazer parte de nenhuma atlética.</strong><br>
                        Seu curso não tem uma atlética ainda, ou você não é um aluno.
                    </div>

                <?php elseif (!$atletica_info): ?>
                    <!-- Curso sem atlética -->
                    <div class="alert alert-secondary">
                        <i class="bi bi-info-circle"></i>
                        <strong>Seu curso não possui atlética</strong><br>
                        O curso <strong><?php
                            $cursoNome = '';
                            if (isset($cursos)) {
                                foreach ($cursos as $curso) {
                                    if ($curso['id'] == $user['curso_id']) {
                                        $cursoNome = $curso['nome'];
                                        break;
                                    }
                                }
                            }
                            echo htmlspecialchars($cursoNome);
                        ?></strong> ainda não possui uma atlética associada.
                    </div>

                <?php elseif ($user['atletica_join_status'] === 'aprovado'): ?>
                    <!-- Usuário já é membro da atlética -->
                    <div class="alert alert-success">
                        <i class="bi bi-check-circle-fill"></i>
                        <strong>Você é membro da atlética!</strong><br>
                        <strong>Atlética:</strong> <?php echo htmlspecialchars($atletica_info['nome']); ?><br>
                        <small class="text-muted">Status: Membro ativo</small>
                    </div>

                    <?php if ($user['role'] === 'admin'): ?>
                        <!-- Admin da atlética não pode sair -->
                        <div class="alert alert-warning mt-3">
                            <i class="bi bi-exclamation-triangle-fill"></i>
                            <strong>Administradores não podem deixar a atlética</strong><br>
                            Como você é administrador da atlética, não pode sair dela.
                            Para sair, primeiro você precisa passar a administração para outro membro ou
                            ser removido por um super administrador.
                        </div>
                    <?php else: ?>
                        <!-- Membro comum pode sair -->
                        <div class="mt-3">
                            <form action="/perfil/sair-atletica" method="post" onsubmit="return confirm('Tem certeza que deseja sair da atlética? Você voltará a ser um Aluno comum e perderá acesso às funcionalidades de membro.')">
                                <button type="submit" class="btn btn-danger">
                                    <i class="bi bi-box-arrow-right"></i> Sair da Atlética
                                </button>
                            </form>
                            <small class="text-muted d-block mt-2">
                                Ao sair, seu status voltará para "Aluno" e você precisará solicitar entrada novamente se quiser voltar.
                            </small>
                        </div>
                    <?php endif; ?>

                <?php elseif ($user['atletica_join_status'] === 'pendente'): ?>
                    <!-- Solicitação pendente (para Membros das Atléticas que se cadastraram) -->
                    <div class="alert alert-warning">
                        <i class="bi bi-clock-history"></i>
                        <strong>Solicitação em análise</strong><br>
                        Sua solicitação para entrar na <strong><?php echo htmlspecialchars($atletica_info['nome']); ?></strong>
                        está sendo analisada pelo administrador da atlética.
                        <br><small class="text-muted">Aguarde a resposta do administrador.</small>
                    </div>

                <?php else: ?>
                    <!-- Usuário pode solicitar entrada (apenas se for Aluno ou não faz parte) -->
                    <div class="mb-3">
                        <h6><i class="bi bi-people-fill text-primary"></i> Atlética Disponível</h6>
                        <p class="mb-2">
                            <strong>Atlética:</strong> <?php echo htmlspecialchars($atletica_info['nome']); ?><br>
                            <small class="text-muted">Baseado no seu curso atual</small>
                        </p>
                        <p class="mb-3">
                            <strong>Status atual:</strong>
                            <span class="badge bg-secondary">Não é membro</span>
                        </p>
                    </div>

                    <?php if ($user['tipo_usuario_detalhado'] === 'Aluno'): ?>
                        <!-- Apenas alunos podem solicitar manualmente -->
                        <form action="/perfil/solicitar-atletica" method="post" onsubmit="return confirm('Tem certeza que deseja solicitar entrada nesta atlética?')">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-person-plus-fill"></i> Solicitar Entrada na Atlética
                            </button>
                        </form>

                        <small class="text-muted mt-2 d-block">
                            Sua solicitação será enviada para o administrador da atlética para aprovação.
                        </small>
                    <?php else: ?>
                        <!-- Outros tipos de usuário não podem solicitar -->
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle"></i>
                            <strong>Não é possível solicitar entrada</strong><br>
                            Apenas usuários do tipo "Aluno" podem solicitar entrada em atléticas manualmente.
                            Usuários "Membro das Atléticas" são enviados automaticamente para aprovação durante o cadastro.
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>



</div>

<!-- Modal de Alterar Senha -->
<div class="modal fade" id="modalAlterarSenha" tabindex="-1" aria-labelledby="modalAlterarSenhaLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 12px; border: none;">
            <div class="modal-header" style="background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%); color: white; border-radius: 12px 12px 0 0;">
                <h5 class="modal-title" id="modalAlterarSenhaLabel"><strong>REDEFINIR SENHA</strong></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="padding: 30px;">
                <form action="/perfil" method="post" id="formAlterarSenha">
                    <input type="hidden" name="form_type" value="alterar_senha">
                    
                    <div class="mb-3">
                        <label for="senha_atual_modal" class="form-label" style="font-weight: 600; color: #333;">Senha atual</label>
                        <input type="password" name="senha_atual" id="senha_atual_modal" class="form-control" placeholder="Digite sua senha atual" required style="border-radius: 8px; padding: 12px;">
                    </div>
                    
                    <div class="mb-3">
                        <label for="nova_senha_modal" class="form-label" style="font-weight: 600; color: #333;">Nova senha</label>
                        <input type="password" name="nova_senha" id="nova_senha_modal" class="form-control" placeholder="Digite sua nova senha" required minlength="6" style="border-radius: 8px; padding: 12px;">
                    </div>
                    
                    <div class="mb-4">
                        <label for="confirmar_nova_senha_modal" class="form-label" style="font-weight: 600; color: #333;">Confirmar senha</label>
                        <input type="password" name="confirmar_nova_senha" id="confirmar_nova_senha_modal" class="form-control" placeholder="Confirme sua nova senha" required style="border-radius: 8px; padding: 12px;">
                    </div>
                    
                    <button type="submit" class="btn w-100" style="background: #f97316; color: white; border: none; padding: 12px; border-radius: 8px; font-weight: 600; font-size: 16px;">
                        Redefinir senha
                    </button>
                    
                    <p class="text-center text-muted mt-3 mb-0" style="font-size: 14px;">
                        Precisa de ajuda? Entre em contato com a universidade
                    </p>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="/js/event-popup.js"></script>
<script>
// JavaScript para controlar as abas
function openTab(tabName) {
    // Esconder todos os conteúdos das abas
    const tabContents = document.querySelectorAll('.profile-tab-content');
    tabContents.forEach(content => {
        content.classList.remove('active');
    });
    
    // Remover a classe active de todos os botões
    const tabs = document.querySelectorAll('.profile-tab');
    tabs.forEach(tab => {
        tab.classList.remove('active');
    });
    
    // Mostrar o conteúdo da aba selecionada
    document.getElementById(tabName).classList.add('active');
    
    // Adicionar classe active ao botão clicado
    event.target.classList.add('active');
}

// Função para abrir o modal de alterar senha
function openPasswordModal() {
    const modal = new bootstrap.Modal(document.getElementById('modalAlterarSenha'));
    modal.show();
}
</script>