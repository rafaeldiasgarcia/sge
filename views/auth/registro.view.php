<?php
/**
 * ============================================================================
 * VIEW: REGISTRO DE NOVOS USUÁRIOS
 * ============================================================================
 * 
 * Formulário completo de cadastro com campos dinâmicos baseados no tipo de
 * usuário (Aluno, Membro de Atlética, Professor, Comunidade Externa).
 * 
 * FUNCIONALIDADES:
 * - Cadastro para 4 tipos de usuários
 * - Campos dinâmicos via JavaScript (RA, Curso)
 * - Validação de data de nascimento (não aceita futuro)
 * - Máscara de telefone automática
 * - Validação de email institucional (@unifio.edu.br)
 * - Validação de senha e confirmação
 * - Preservação de dados em caso de erro
 * - Seleção de curso da lista do banco
 * 
 * TIPOS DE USUÁRIO:
 * - Aluno: requer RA + Curso + Email institucional
 * - Membro das Atléticas: requer RA + Curso + Email institucional
 * - Professor: requer RA + Curso + Email institucional
 * - Comunidade Externa: não requer RA nem Curso, aceita qualquer email
 * 
 * VALIDAÇÕES JAVASCRIPT:
 * - Data de nascimento não pode ser futura
 * - Telefone: formato (00) 00000-0000
 * - Email: institucional ou comum conforme tipo
 * - Campos obrigatórios dinâmicos
 * 
 * VARIÁVEIS RECEBIDAS:
 * @var array $cursos - Lista de cursos disponíveis para seleção
 * @var array $old    - Dados preservados após erro de validação
 * 
 * FLUXO:
 * 1. Usuário seleciona tipo de vínculo
 * 2. Campos relevantes aparecem dinamicamente
 * 3. Preenche formulário
 * 4. POST para /registro
 * 5. Se sucesso → redireciona para login
 * 6. Se erro → retorna com mensagem e dados preservados
 * 
 * CONTROLLER: AuthController::registro()
 * CSS: auth.css, default.css
 */
?>
<div class="auth-card-register">
  <h1 class="auth-title">Cadastre-se</h1>
  <p class="auth-subtitle">Cadastro para os jogos universitários da UNIFIO</p>

  <?php if (isset($_SESSION['error_message'])): ?>
    <div class="alert alert-danger alert-auth">
      <?= $_SESSION['error_message']; unset($_SESSION['error_message']); ?>
    </div>
  <?php endif; ?>

  <form action="/registro" method="post" class="auth-form-register">
    <!-- Vínculo -->
    <div class="mb-3">
      <label for="tipo_usuario_detalhado" class="form-label">Vínculo com a Instituição</label>
      <select name="tipo_usuario_detalhado" id="tipo_usuario_detalhado" class="form-select" required>
        <option value="" disabled <?= empty($old['tipo_usuario_detalhado']) ? 'selected' : ''; ?>>-- Selecione uma opção --</option>
        <option value="Aluno" <?= ($old['tipo_usuario_detalhado'] ?? '') === 'Aluno' ? 'selected' : ''; ?>>Aluno</option>
        <option value="Membro das Atléticas" <?= ($old['tipo_usuario_detalhado'] ?? '') === 'Membro das Atléticas' ? 'selected' : ''; ?>>Membro das Atléticas</option>
        <option value="Professor" <?= ($old['tipo_usuario_detalhado'] ?? '') === 'Professor' ? 'selected' : ''; ?>>Professor</option>
        <option value="Comunidade Externa" <?= ($old['tipo_usuario_detalhado'] ?? '') === 'Comunidade Externa' ? 'selected' : ''; ?>>Comunidade Externa</option>
      </select>
    </div>

    <!-- RA (condicional) -->
    <div id="campo_ra" class="mb-3" style="display:none;">
      <label for="ra" class="form-label">Matrícula (RA)</label>
      <input
        type="text" name="ra" id="ra" class="form-control"
        placeholder="000000"
        value="<?= htmlspecialchars($old['ra'] ?? ''); ?>"
        inputmode="numeric" maxlength="6" pattern="[0-9]{6}"
        title="O RA deve conter exatamente 6 números."
      >
    </div>

    <!-- Nome e Nascimento -->
    <div class="mb-3">
      <label for="nome" class="form-label">Nome Completo</label>
      <input type="text" name="nome" id="nome" class="form-control"
             value="<?= htmlspecialchars($old['nome'] ?? ''); ?>" required>
    </div>

    <div class="mb-3">
      <label for="data_nascimento" class="form-label">Data de Nascimento</label>
      <input type="date" name="data_nascimento" id="data_nascimento" class="form-control"
             value="<?= htmlspecialchars($old['data_nascimento'] ?? ''); ?>" required max="">
    </div>

    <!-- Email e Telefone -->
    <div class="mb-3">
      <label for="email" id="label_email" class="form-label">E-mail</label>
      <input type="email" name="email" id="email" class="form-control"
             placeholder="*******@unifio.edu.br"
             value="<?= htmlspecialchars($old['email'] ?? ''); ?>" required autocomplete="email">
    </div>

    <div class="mb-3">
      <label for="telefone" class="form-label">Telefone</label>
      <input type="tel" name="telefone" id="telefone" class="form-control"
             placeholder="(00) 00000-0000"
             value="<?= htmlspecialchars($old['telefone'] ?? ''); ?>" required maxlength="15">
    </div>

    <!-- Curso (condicional) -->
    <div id="campo_curso" class="mb-3" style="display:none;">
      <label for="curso_id" class="form-label">Qual seu curso?</label>
      <select name="curso_id" id="curso_id" class="form-select">
        <option value="">-- Selecione seu curso --</option>
        <?php if (isset($cursos)): foreach ($cursos as $curso): ?>
          <option value="<?= $curso['id']; ?>" <?= (isset($old['curso_id']) && $old['curso_id'] == $curso['id']) ? 'selected' : ''; ?>>
            <?= htmlspecialchars($curso['nome']); ?>
          </option>
        <?php endforeach; endif; ?>
      </select>
    </div>

    <!-- Senhas (com olho igual ao login) -->
    <div class="mb-3">
      <label for="senha" class="form-label">Senha</label>
      <div class="position-relative">
        <input type="password" name="senha" id="senha" class="form-control pe-5"
               placeholder="••••••••" required autocomplete="new-password">
        <button type="button"
                id="togglePassword"
                class="btn btn-link p-0 position-absolute top-50 end-0 translate-middle-y me-3"
                tabindex="-1" aria-label="Mostrar senha" title="Mostrar/ocultar senha">
          <i class="bi bi-eye fs-5"></i>
        </button>
      </div>
    </div>

    <div class="mb-3">
      <label for="confirmar_senha" class="form-label">Confirmar Senha</label>
      <div class="position-relative">
        <input type="password" name="confirmar_senha" id="confirmar_senha" class="form-control pe-5"
               placeholder="••••••••" required autocomplete="new-password">
        <button type="button"
                id="toggleConfirmPassword"
                class="btn btn-link p-0 position-absolute top-50 end-0 translate-middle-y me-3"
                tabindex="-1" aria-label="Mostrar confirmação de senha" title="Mostrar/ocultar confirmação">
          <i class="bi bi-eye fs-5"></i>
        </button>
      </div>
    </div>

    <button type="submit" class="btn btn-auth-primary">Cadastrar</button>

    <div class="auth-links-register">
      <a href="/login">Já tem uma conta? Faça login</a>
    </div>

    <div class="auth-help-text">
      Precisa de ajuda?<br>Entre em contato com a universidade
    </div>

    <div class="unifio-logo">
      <img src="/img/logo-unifio-azul.webp" alt="Logo UNIFIO">
    </div>
  </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
  // ===== Data de nascimento (não futura)
  const dataNascimentoInput = document.getElementById('data_nascimento');
  const hoje = new Date();
  dataNascimentoInput.setAttribute('max', hoje.toISOString().split('T')[0]);
  const validaData = (el) => {
    const sel = new Date(el.value);
    const hoje0 = new Date(); hoje0.setHours(0,0,0,0);
    el.setCustomValidity(sel > hoje0 ? 'A data de nascimento não pode ser uma data futura.' : '');
  };
  dataNascimentoInput.addEventListener('blur', e => validaData(e.target));
  dataNascimentoInput.addEventListener('input', e => validaData(e.target));

  // ===== Campos dinâmicos por tipo
  const tipoUsuarioSelect = document.getElementById('tipo_usuario_detalhado');
  const campoRa = document.getElementById('campo_ra');
  const inputRa = document.getElementById('ra');
  const campoCurso = document.getElementById('campo_curso');
  const emailInput = document.getElementById('email');
  const labelEmail = document.getElementById('label_email');

  function toggleFields() {
    const tipo = tipoUsuarioSelect.value;
    const exige = (tipo === 'Aluno' || tipo === 'Membro das Atléticas' || tipo === 'Professor');

    campoRa.style.display    = exige ? 'block' : 'none';
    inputRa.required         = !!exige;
    campoCurso.style.display = exige ? 'block' : 'none';

    if (exige) {
      labelEmail.textContent = 'E-mail institucional';
      emailInput.placeholder = 'Use seu e-mail @unifio.edu.br';
    } else if (tipo === 'Comunidade Externa') {
      labelEmail.textContent = 'E-mail';
      emailInput.placeholder = 'Seu e-mail';
    } else {
      labelEmail.textContent = 'E-mail';
      emailInput.placeholder = '*******@unifio.edu.br';
    }
  }
  tipoUsuarioSelect.addEventListener('change', toggleFields);
  toggleFields();

  // ===== Máscara de telefone
  const telefoneInput = document.getElementById('telefone');
  telefoneInput.addEventListener('input', function (e) {
    let v = e.target.value.replace(/\D/g, '').slice(0, 11);
    if (v.length === 0) { e.target.value = ''; return; }
    if (v.length <= 10) {
      v = v.replace(/^(\d{0,2})(\d{0,4})(\d{0,4}).*/, function(_, a, b, c){
        let r = ''; if (a) r += '('+a; if (a && a.length===2) r += ') ';
        if (b) r += b; if (c) r += '-'+c; return r;
      });
    } else {
      v = v.replace(/^(\d{0,2})(\d{0,5})(\d{0,4}).*/, function(_, a, b, c){
        let r=''; if (a) r+='('+a; if (a && a.length===2) r+=') ';
        if (b) r+=b; if (c) r+='-'+c; return r;
      });
    }
    e.target.value = v;
  });
  telefoneInput.addEventListener('blur', function (e) {
    const digits = e.target.value.replace(/\D/g, '');
    e.target.setCustomValidity(digits.length && digits.length !== 11 ? 'O telefone deve conter exatamente 11 dígitos.' : '');
  });

  // ===== Mostrar/Ocultar senha — MESMO markup/comportamento do login
  function wireToggle(btnId, inputId) {
    const btn = document.getElementById(btnId);
    const inp = document.getElementById(inputId);
    const icon = btn.querySelector('i');
    btn.addEventListener('click', () => {
      const isPass = inp.type === 'password';
      inp.type = isPass ? 'text' : 'password';
      icon.classList.toggle('bi-eye');
      icon.classList.toggle('bi-eye-slash');
      inp.focus();
    });
  }
  wireToggle('togglePassword', 'senha');
  wireToggle('toggleConfirmPassword', 'confirmar_senha');
});
</script>
