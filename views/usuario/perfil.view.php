<?php
#
# View da página de Perfil do Usuário.
# Contém dois formulários: um para atualizar dados pessoais e outro
# para alterar a senha.
#
?>
<div class="container">
    <h2 class="mb-3">Meu Perfil</h2>
    <p class="text-muted mb-4">Gerencie suas informações pessoais, de acesso e seu vínculo com a atlética.</p>

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

    <div class="row">
        <div class="col-lg-7 mb-4">
            <div class="card">
                <div class="card-header"><strong>Dados Pessoais</strong></div>
                <div class="card-body">
                    <form action="/perfil" method="post">
                        <input type="hidden" name="form_type" value="dados_pessoais">
                        <div class="mb-3">
                            <label for="ra" class="form-label">RA</label>
                            <input type="text" id="ra" class="form-control" value="<?php echo htmlspecialchars($user['ra'] ?? ''); ?>" disabled>
                        </div>
                        <div class="mb-3">
                            <label for="nome" class="form-label">Nome Completo</label>
                            <input type="text" name="nome" id="nome" class="form-control" value="<?php echo htmlspecialchars($user['nome'] ?? ''); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" id="email" class="form-control" value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>" disabled>
                        </div>
                        <div class="mb-3">
                            <label for="data_nascimento" class="form-label">Data de Nascimento</label>
                            <input type="date" id="data_nascimento" class="form-control" value="<?php echo htmlspecialchars($user['data_nascimento'] ?? ''); ?>" disabled>
                        </div>
                        <?php if (!empty($user['ra'])): ?>
                            <div class="mb-3">
                                <label for="curso_id" class="form-label">Curso</label>
                                <input type="text" id="curso_id" class="form-control" value="<?php
                                    foreach ($cursos as $curso) {
                                        if ($user['curso_id'] == $curso['id']) {
                                            echo htmlspecialchars($curso['nome']);
                                            break;
                                        }
                                    }
                                ?>" disabled>
                            </div>
                        <?php endif; ?>
                        <button type="submit" class="btn btn-primary">Salvar Dados</button>
                    </form>
                </div>
            </div>

            <!-- Nova Seção: Vínculo com Atlética -->
            <div class="card mt-4">
                <div class="card-header"><strong>Vínculo com Atlética</strong></div>
                <div class="card-body">
                    <?php if (!$user['curso_id']): ?>
                        <!-- Usuário sem curso definido -->
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle"></i>
                            <strong>Você não pode fazer parte de nenhuma atletica.</strong><br>
                            Seu cusro não tem uma atletica ainda, ou você não é um aluno.
                        </div>

                    <?php elseif (!$atletica_info): ?>
                        <!-- Curso sem atlética -->
                        <div class="alert alert-secondary">
                            <i class="bi bi-info-circle"></i>
                            <strong>Seu curso não possui atlética</strong><br>
                            O curso <strong><?php
                                $cursoNome = '';
                                foreach ($cursos as $curso) {
                                    if ($curso['id'] == $user['curso_id']) {
                                        $cursoNome = $curso['nome'];
                                        break;
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

        <div class="col-lg-5">
            <div class="card">
                <div class="card-header"><strong>Alterar Senha</strong></div>
                <div class="card-body">
                    <form action="/perfil" method="post">
                        <input type="hidden" name="form_type" value="alterar_senha">
                        <div class="mb-3">
                            <label for="senha_atual" class="form-label">Senha Atual</label>
                            <input type="password" name="senha_atual" id="senha_atual" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="nova_senha" class="form-label">Nova Senha</label>
                            <input type="password" name="nova_senha" id="nova_senha" class="form-control" required minlength="6">
                        </div>
                        <div class="mb-3">
                            <label for="confirmar_nova_senha" class="form-label">Confirmar Nova Senha</label>
                            <input type="password" name="confirmar_nova_senha" id="confirmar_nova_senha" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-warning">Alterar Senha</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>