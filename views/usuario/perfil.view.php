<?php
/**
 * ============================================================================
 * VIEW: PERFIL DO USUÁRIO
 * ============================================================================
 * 
 * Página completa do perfil com informações pessoais, eventos e atlética.
 * Interface com abas para organizar diferentes seções de dados.
 * 
 * FUNCIONALIDADES:
 * - Visualizar informações pessoais e acadêmicas
 * - Ver eventos com presença confirmada (futuros e passados)
 * - Gerenciar vínculo com atlética
 * - Alterar senha via modal
 * - Solicitar/cancelar participação em atlética
 * - Avatar com iniciais
 * 
 * ABAS DISPONÍVEIS:
 * 1. Informações: dados pessoais e acadêmicos
 * 2. Meus Eventos: histórico de presenças confirmadas
 * 3. Atlética: gerenciamento de vínculo com atlética
 * 
 * VARIÁVEIS RECEBIDAS:
 * @var array $user          - Dados completos do usuário
 *                             [nome, email, telefone, ra, data_nascimento,
 *                              tipo_usuario_detalhado, curso_nome, role,
 *                              atletica_id, status_membro_atletica]
 * @var array $atletica_info - Informações da atlética (se vinculado)
 *                             [nome, id]
 * @var array $eventos       - Eventos com presença (futuros e passados)
 * @var array $atleticas     - Lista de atléticas disponíveis (para solicitação)
 * 
 * GERENCIAMENTO DE ATLÉTICA:
 * - Solicitar entrada: POST /usuario/solicitar-atletica
 * - Cancelar solicitação: POST /usuario/cancelar-solicitacao-atletica
 * - Sair da atlética: POST /usuario/sair-atletica
 * 
 * STATUS POSSÍVEIS:
 * - null: não vinculado
 * - 'pendente': aguardando aprovação
 * - 'membro': membro ativo
 * - 'recusado': solicitação recusada
 * 
 * CONTROLLER: UsuarioController::perfil()
 * CSS: usuario.css
 * JAVASCRIPT: Inline (tabs, modal de senha)
 */
?>
<div class="container mt-4">

  <?php if (isset($_SESSION['success_message'])): ?>
    <div class="alert alert-success d-flex align-items-center gap-2" role="alert">
      <i class="bi bi-check-circle"></i>
      <div><?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?></div>
    </div>
  <?php endif; ?>

  <?php if (isset($_SESSION['error_message'])): ?>
    <div class="alert alert-danger d-flex align-items-center gap-2" role="alert">
      <i class="bi bi-exclamation-triangle"></i>
      <div><?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?></div>
    </div>
  <?php endif; ?>

  <!-- Header do Perfil -->
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
        <h2 class="mb-1"><?php echo htmlspecialchars($user['nome'] ?? ''); ?></h2>
        <p class="mb-0"><?php echo htmlspecialchars($user['email'] ?? ''); ?></p>
        <p class="mb-0">Matrícula: <?php echo htmlspecialchars($user['ra'] ?? 'N/A'); ?></p>
        <p class="mb-0">Atlética: <?php echo htmlspecialchars($atletica_info['nome'] ?? 'Nenhuma'); ?></p>
      </div>
    </div>

    <div class="profile-header-buttons">
      <button class="btn-change-password" onclick="openPasswordModal()">Alterar Senha</button>
    </div>
  </div>

  <!-- Tabs de Navegação -->
  <div class="profile-tabs">
    <button class="profile-tab active" onclick="openTab(event, 'informacoes')">Informações</button>
    <button class="profile-tab" onclick="openTab(event, 'meus-eventos')">Meus Eventos</button>
    <button class="profile-tab" onclick="openTab(event, 'atletica')">Atlética</button>
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
          <span class="info-value"><?php echo htmlspecialchars(formatarTelefone($user['telefone'] ?? null) ?: 'Não informado'); ?></span>
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
          <span class="info-value">
            <?php
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
            ?>
            <button onclick="openTrocaCursoModal()" class="btn-trocar-curso" title="Solicitar troca de curso">
              <i class="bi bi-arrow-left-right"></i>
            </button>
          </span>
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
        <div class="alert alert-info d-flex align-items-center gap-2">
          <i class="bi bi-info-circle"></i>
          <span>Você ainda não confirmou presença em nenhum evento.</span>
        </div>
      <?php else: ?>
        <?php foreach ($meus_eventos as $evento): ?>
          <div class="event-card" data-event-id="<?php echo $evento['id']; ?>" style="cursor: pointer;">
            <div class="event-info">
              <h4 class="mb-1"><?php echo htmlspecialchars($evento['titulo']); ?></h4>
              <p class="mb-1">
                <?php echo date('d/m/Y', strtotime($evento['data_agendamento'])); ?> - <?php echo $evento['horario_periodo']; ?>
              </p>
              <span class="event-status <?php echo strtolower($evento['status']); ?>">
                <?php
                  $status_traducao = [
                    'aprovado'   => 'Confirmado',
                    'pendente'   => 'Pendente',
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
                <form action="/agenda/presenca" method="post" class="d-inline"
                      onsubmit="return confirm('Tem certeza que deseja cancelar sua presença neste evento?');">
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
    <div class="card shadow-sm border-0">
      <div class="card-header"><strong>Vínculo com Atlética</strong></div>
      <div class="card-body">
        <?php if (!$user['curso_id']): ?>
          <div class="alert alert-info d-flex align-items-center gap-2">
            <i class="bi bi-info-circle"></i>
            <div>
              <strong>Você não pode fazer parte de nenhuma atlética.</strong><br>
              Seu curso não tem uma atlética ainda, ou você não é um aluno.
            </div>
          </div>

        <?php elseif (!$atletica_info): ?>
          <div class="alert alert-secondary d-flex align-items-center gap-2">
            <i class="bi bi-info-circle"></i>
            <div>
              <strong>Seu curso não possui atlética</strong><br>
              O curso <strong>
              <?php
                $cursoNome = '';
                if (isset($cursos)) {
                  foreach ($cursos as $curso) {
                    if ($curso['id'] == $user['curso_id']) { $cursoNome = $curso['nome']; break; }
                  }
                }
                echo htmlspecialchars($cursoNome);
              ?>
              </strong> ainda não possui uma atlética associada.
            </div>
          </div>

        <?php elseif ($user['atletica_join_status'] === 'aprovado'): ?>
          <div class="alert alert-success">
            <i class="bi bi-check-circle-fill"></i>
            <strong>Você é membro da atlética!</strong><br>
            <strong>Atlética:</strong> <?php echo htmlspecialchars($atletica_info['nome']); ?><br>
            <small class="text-muted">Status: Membro ativo</small>
          </div>

          <?php if ($user['role'] === 'admin'): ?>
            <div class="alert alert-warning mt-3">
              <i class="bi bi-exclamation-triangle-fill"></i>
              <strong>Administradores não podem deixar a atlética</strong><br>
              Como você é administrador da atlética, não pode sair dela. Transfira a administração ou seja removido por um super admin.
            </div>
          <?php else: ?>
            <div class="mt-3">
              <form action="/perfil/sair-atletica" method="post"
                    onsubmit="return confirm('Tem certeza que deseja sair da atlética? Você voltará a ser um Aluno comum e perderá acesso às funcionalidades de membro.')">
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
          <div class="alert alert-warning">
            <i class="bi bi-clock-history"></i>
            <strong>Solicitação em análise</strong><br>
            Sua solicitação para entrar na <strong><?php echo htmlspecialchars($atletica_info['nome']); ?></strong> está sendo analisada pelo administrador da atlética.
            <br><small class="text-muted">Aguarde a resposta do administrador.</small>
          </div>

        <?php else: ?>
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
            <form action="/perfil/solicitar-atletica" method="post"
                  onsubmit="return confirm('Tem certeza que deseja solicitar entrada nesta atlética?')">
              <button type="submit" class="btn btn-primary">
                <i class="bi bi-person-plus-fill"></i> Solicitar Entrada na Atlética
              </button>
            </form>
            <small class="text-muted mt-2 d-block">
              Sua solicitação será enviada para o administrador da atlética para aprovação.
            </small>
          <?php else: ?>
            <div class="alert alert-info d-flex align-items-center gap-2">
              <i class="bi bi-info-circle"></i>
              <div>
                <strong>Não é possível solicitar entrada</strong><br>
                Apenas usuários do tipo "Aluno" podem solicitar entrada em atléticas manualmente.
              </div>
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
    <div class="modal-content modal-rounded">
      <div class="modal-header modal-header-blue">
        <h5 class="modal-title" id="modalAlterarSenhaLabel"><strong>REDEFINIR SENHA</strong></h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body modal-body-padded">
        <form action="/perfil" method="post" id="formAlterarSenha">
          <input type="hidden" name="form_type" value="alterar_senha">

          <div class="mb-3">
            <label for="senha_atual_modal" class="form-label label-strong">Senha atual</label>
            <input type="password" name="senha_atual" id="senha_atual_modal" class="form-control input-rounded" placeholder="Digite sua senha atual" required>
          </div>

          <div class="mb-3">
            <label for="nova_senha_modal" class="form-label label-strong">Nova senha</label>
            <input type="password" name="nova_senha" id="nova_senha_modal" class="form-control input-rounded" placeholder="Digite sua nova senha" required minlength="6">
          </div>

          <div class="mb-4">
            <label for="confirmar_nova_senha_modal" class="form-label label-strong">Confirmar senha</label>
            <input type="password" name="confirmar_nova_senha" id="confirmar_nova_senha_modal" class="form-control input-rounded" placeholder="Confirme sua nova senha" required>
          </div>

          <button type="submit" class="btn btn-orange w-100 btn-lg fw-semibold">Redefinir senha</button>
          <p class="text-center text-muted mt-3 mb-0 small">
            Precisa de ajuda? Entre em contato com a universidade
          </p>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- Modal de Solicitar Troca de Curso -->
<div class="modal fade" id="modalTrocarCurso" tabindex="-1" aria-labelledby="modalTrocarCursoLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content modal-rounded">
      <div class="modal-header modal-header-orange">
        <h5 class="modal-title" id="modalTrocarCursoLabel"><strong>SOLICITAR TROCA DE CURSO</strong></h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body modal-body-padded">
        <form action="/perfil/solicitar-troca-curso" method="post" id="formTrocarCurso">
          <div class="alert alert-info rounded-2 mb-3">
            <i class="bi bi-info-circle"></i>
            Sua solicitação será analisada pelo coordenador (super admin). Você receberá uma notificação com a resposta.
          </div>

          <div class="mb-3">
            <label for="curso_atual" class="form-label label-strong">Curso Atual</label>
            <input type="text" id="curso_atual" class="form-control input-rounded" value="<?php
              if (!empty($user['curso_id']) && isset($cursos)) {
                foreach ($cursos as $curso) {
                  if ($user['curso_id'] == $curso['id']) { echo htmlspecialchars($curso['nome']); break; }
                }
              } else {
                echo 'N/A';
              }
            ?>" disabled>
          </div>

          <div class="mb-3">
            <label for="curso_novo_id" class="form-label label-strong">Novo Curso Desejado <span class="text-danger">*</span></label>
            <select name="curso_novo_id" id="curso_novo_id" class="form-select input-rounded" required>
              <option value="">Selecione o curso desejado</option>
              <?php if (isset($cursos)): ?>
                <?php foreach ($cursos as $curso): ?>
                  <?php if ($curso['id'] != $user['curso_id']): ?>
                    <option value="<?php echo $curso['id']; ?>"><?php echo htmlspecialchars($curso['nome']); ?></option>
                  <?php endif; ?>
                <?php endforeach; ?>
              <?php endif; ?>
            </select>
          </div>

          <div class="mb-4">
            <label for="justificativa" class="form-label label-strong">Justificativa <span class="text-danger">*</span></label>
            <textarea name="justificativa" id="justificativa" class="form-control input-rounded" rows="5" placeholder="Explique o motivo da solicitação de troca de curso..." required></textarea>
            <small class="text-muted">Mínimo 50 caracteres</small>
          </div>

          <button type="submit" class="btn btn-orange w-100 btn-lg fw-semibold">Enviar Solicitação</button>
          <p class="text-center text-muted mt-3 mb-0 small">
            O coordenador analisará seu pedido em breve
          </p>
        </form>
      </div>
    </div>
  </div>
</div>