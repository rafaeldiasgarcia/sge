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
 * - Professor: requer Curso + Email institucional (sem RA)
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

<!-- Card expandido de registro -->
<div class="auth-card-register">
    <h1 class="auth-title">Cadastre-se</h1>
    <p class="auth-subtitle">Cadastro para os jogos universitários da UNIFIO</p>

    <!-- Mensagens de erro -->
    <?php if (isset($_SESSION['error_message'])): ?>
        <div class="alert alert-danger alert-auth">
            <?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?>
        </div>
    <?php endif; ?>

    <!-- Formulário de registro -->
    <form action="/registro" method="post" class="auth-form-register">
        <!-- Linha 1: Vínculo e RA (condicional) -->
        <div class="row"> 
            <!-- Campo: Tipo de vínculo (controla exibição de outros campos) -->
            <div class="mb-3">
                <label for="tipo_usuario_detalhado" class="form-label">Vínculo com a Instituição</label>
                <select name="tipo_usuario_detalhado" id="tipo_usuario_detalhado" class="form-select" required>
                    <option value="" disabled <?php echo empty($old['tipo_usuario_detalhado']) ? 'selected' : ''; ?>>
                        -- Selecione uma opção --
                    </option>
                    <option value="Aluno" <?php echo ($old['tipo_usuario_detalhado'] ?? '') === 'Aluno' ? 'selected' : ''; ?>>
                        Aluno
                    </option>
                    <option value="Membro das Atléticas" <?php echo ($old['tipo_usuario_detalhado'] ?? '') === 'Membro das Atléticas' ? 'selected' : ''; ?>>
                        Membro das Atléticas
                    </option>
                    <option value="Professor" <?php echo ($old['tipo_usuario_detalhado'] ?? '') === 'Professor' ? 'selected' : ''; ?>>
                        Professor
                    </option>
                    <option value="Comunidade Externa" <?php echo ($old['tipo_usuario_detalhado'] ?? '') === 'Comunidade Externa' ? 'selected' : ''; ?>>
                        Comunidade Externa
                    </option>
                </select>
            </div>

            <!-- Campo condicional: RA (apenas para Aluno e Membro de Atlética) -->
            <div id="campo_ra" class="mb-3" style="display:none;">
                <label for="ra" class="form-label">Matrícula (RA)</label>
                <input type="text" name="ra" id="ra" class="form-control" 
                       placeholder="00000" 
                       value="<?php echo htmlspecialchars($old['ra'] ?? ''); ?>" 
                       inputmode="numeric" maxlength="6" pattern="[0-9]{6}" 
                       title="O RA deve conter exatamente 6 números.">
            </div>
        </div>

        <!-- Linha 2: Nome e Data de Nascimento -->
        <div class="row">
            <!-- Campo: Nome completo -->
            <div class="mb-3">
                <label for="nome" class="form-label">Nome Completo</label>
                <input type="text" name="nome" id="nome" class="form-control" 
                       value="<?php echo htmlspecialchars($old['nome'] ?? ''); ?>" required>
            </div>
            
            <!-- Campo: Data de nascimento (com validação JS) -->
            <div class="mb-3">
                <label for="data_nascimento" class="form-label">Data de Nascimento</label>
                <input type="date" name="data_nascimento" id="data_nascimento" class="form-control" 
                       value="<?php echo htmlspecialchars($old['data_nascimento'] ?? ''); ?>" 
                       required max="">
            </div>
        </div>

        <!-- Linha 3: Email e Telefone -->
        <div class="row">
            <!-- Campo: Email (label dinâmico via JS) -->
            <div class="mb-3">
                <label for="email" class="form-label" id="label_email">E-mail</label>
                <input type="email" name="email" id="email" class="form-control" 
                       placeholder="*******@unifio.edu.br" 
                       value="<?php echo htmlspecialchars($old['email'] ?? ''); ?>" required>
            </div>

            <!-- Campo: Telefone (com máscara JS) -->
            <div class="mb-3">
                <label for="telefone" class="form-label">Telefone</label>
                <input type="tel" name="telefone" id="telefone" class="form-control" 
                       placeholder="(00) 00000-0000" 
                       value="<?php echo htmlspecialchars($old['telefone'] ?? ''); ?>" 
                       required maxlength="15">
            </div>
        </div>

        <!-- Campo condicional: Curso (para Aluno, Membro de Atlética e Professor) -->
        <div id="campo_curso" class="mb-3" style="display:none;">
            <label for="curso_id" class="form-label">Qual seu curso?</label>
            <select name="curso_id" id="curso_id" class="form-select">
                <option value="">-- Selecione seu curso --</option>
                <?php if (isset($cursos)): foreach ($cursos as $curso): ?>
                    <option value="<?php echo $curso['id']; ?>" 
                            <?php echo (isset($old['curso_id']) && $old['curso_id'] == $curso['id']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($curso['nome']); ?>
                    </option>
                <?php endforeach; endif; ?>
            </select>
        </div>

        <!-- Linha 4: Senha e Confirmação -->
        <div class="row">
            <!-- Campo: Senha -->
            <div class="mb-3">
                <label for="senha" class="form-label">Senha</label>
                <input type="password" name="senha" id="senha" class="form-control" 
                       placeholder="••••••••" required>
            </div>

            <!-- Campo: Confirmar senha -->
            <div class="mb-3">
                <label for="confirmar_senha" class="form-label">Confirmar Senha</label>
                <input type="password" name="confirmar_senha" id="confirmar_senha" 
                       class="form-control" placeholder="••••••••" required>
            </div>
        </div>

        <!-- Botão: Cadastrar -->
        <button type="submit" class="btn btn-auth-primary">Cadastrar</button>

        <!-- Link: Já tem conta? -->
        <div class="auth-links-register">
            <a href="/login">Já tem uma conta? Faça login</a>
        </div>

        <!-- Texto de ajuda -->
        <div class="auth-help-text">
            Precisa de ajuda?<br>
            Entre em contato com a universidade
        </div>

        <!-- Logo institucional -->
        <div class="unifio-logo">
            <img src="/img/logo-unifio-azul.webp" alt="Logo UNIFIO">
        </div>
    </form>
</div>

<!-- ========================================================================
     JAVASCRIPT: VALIDAÇÕES E COMPORTAMENTOS DINÂMICOS
     ======================================================================== -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // ====================================================================
        // VALIDAÇÃO DE DATA DE NASCIMENTO
        // Impede que o usuário selecione datas futuras
        // ====================================================================
        const dataNascimentoInput = document.getElementById('data_nascimento');
        const hoje = new Date();
        const dataMaxima = hoje.toISOString().split('T')[0];
        
        // Define data máxima como hoje
        dataNascimentoInput.setAttribute('max', dataMaxima);

        // Validação ao perder foco do campo
        dataNascimentoInput.addEventListener('blur', function(e) {
            const dataSelecionada = new Date(e.target.value);
            const dataHoje = new Date();
            dataHoje.setHours(0, 0, 0, 0); // Zera horas para comparar apenas data
            
            if (dataSelecionada > dataHoje) {
                e.target.setCustomValidity('A data de nascimento não pode ser uma data futura.');
                e.target.reportValidity();
            } else {
                e.target.setCustomValidity('');
            }
        });

        // Validação em tempo real durante digitação
        dataNascimentoInput.addEventListener('input', function(e) {
            const dataSelecionada = new Date(e.target.value);
            const dataHoje = new Date();
            dataHoje.setHours(0, 0, 0, 0);
            
            if (dataSelecionada > dataHoje) {
                e.target.setCustomValidity('A data de nascimento não pode ser uma data futura.');
            } else {
                e.target.setCustomValidity('');
            }
        });

        // ====================================================================
        // CAMPOS DINÂMICOS BASEADOS NO TIPO DE USUÁRIO
        // Mostra/oculta RA e Curso conforme o vínculo selecionado
        // ====================================================================
        const tipoUsuarioSelect = document.getElementById('tipo_usuario_detalhado');
        const campoRa = document.getElementById('campo_ra');
        const inputRa = document.getElementById('ra');
        const campoCurso = document.getElementById('campo_curso');
        const emailInput = document.getElementById('email');
        const labelEmail = document.getElementById('label_email');

        /**
         * Exibe/oculta campos baseado no tipo de usuário selecionado
         * 
         * Lógica:
         * - Aluno/Membro Atlética: exige RA + Curso + Email institucional
         * - Professor: exige Curso + Email institucional (sem RA)
         * - Comunidade Externa: não exige RA nem Curso, email livre
         */
        function toggleFields() {
            const tipo = tipoUsuarioSelect.value;

            if (tipo === 'Aluno' || tipo === 'Membro das Atléticas') {
                // Aluno e Membro: requer RA e Curso
                campoRa.style.display = 'block';
                inputRa.required = true;
                campoCurso.style.display = 'block';
            } else if (tipo === 'Professor') {
                // Professor: requer apenas Curso (sem RA)
                campoRa.style.display = 'none';
                inputRa.required = false;
                campoCurso.style.display = 'block';
            } else {
                // Comunidade Externa: não requer RA nem Curso
                campoRa.style.display = 'none';
                inputRa.required = false;
                campoCurso.style.display = 'none';
            }

            // Atualizar label e placeholder do email conforme tipo de usuário
            if (tipo === 'Aluno' || tipo === 'Membro das Atléticas' || tipo === 'Professor') {
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

        // Atualiza campos ao mudar seleção
        tipoUsuarioSelect.addEventListener('change', toggleFields);
        
        // Executa ao carregar para manter estado se houver erro
        toggleFields();

        // ====================================================================
        // MÁSCARA DE TELEFONE
        // Formata automaticamente para (00) 00000-0000
        // ====================================================================
        const telefoneInput = document.getElementById('telefone');
        
        telefoneInput.addEventListener('input', function(e) {
            // Remove tudo que não é dígito
            let value = e.target.value.replace(/\D/g, '');
            
            // Limita a 11 dígitos (DDD + 9 dígitos)
            if (value.length > 11) {
                value = value.substring(0, 11);
            }
            
            // Aplica a máscara (00) 00000-0000
            if (value.length >= 1) {
                value = value.replace(/^(\d{0,2})(\d{0,5})(\d{0,4}).*/, function(match, p1, p2, p3) {
                    let result = '';
                    if (p1) result += '(' + p1;
                    if (p1.length === 2) result += ')';
                    if (p2) result += p2;
                    if (p2.length === 5 && p3) result += '-' + p3;
                    return result;
                });
            }
            
            e.target.value = value;
        });

        // Valida quantidade de dígitos ao perder foco
        telefoneInput.addEventListener('blur', function(e) {
            const value = e.target.value.replace(/\D/g, '');
            if (value.length > 0 && value.length !== 11) {
                e.target.setCustomValidity('O telefone deve conter exatamente 11 dígitos.');
            } else {
                e.target.setCustomValidity('');
            }
        });
    });
</script>