<?php
/**
 * Controller de Autenticação (AuthController)
 * 
 * Gerencia todo o fluxo de autenticação e autorização de usuários no sistema.
 * Implementa autenticação em duas etapas (2FA) via e-mail e recuperação de senha.
 * 
 * Funcionalidades principais:
 * - Login com verificação de e-mail e senha
 * - Autenticação em dois fatores (2FA) via código por e-mail
 * - Registro de novos usuários
 * - Recuperação de senha via e-mail
 * - Redefinição de senha com token de segurança
 * - Logout e limpeza de sessão
 * 
 * Fluxo de Login:
 * 1. Usuário informa e-mail e senha
 * 2. Sistema verifica credenciais
 * 3. Para não-superadmins: Gera código de 6 dígitos e envia por e-mail
 * 4. Usuário informa código recebido
 * 5. Sistema valida código e cria sessão
 * 6. Superadmins fazem login direto (sem 2FA)
 * 
 * Fluxo de Recuperação de Senha:
 * 1. Usuário informa e-mail
 * 2. Sistema gera token único e envia link por e-mail
 * 3. Token válido por 1 hora
 * 4. Usuário clica no link e define nova senha
 * 5. Sistema valida token, atualiza senha e limpa token
 * 
 * Segurança:
 * - Senhas armazenadas com hash (password_hash do PHP)
 * - Tokens de redefinição únicos e com prazo de validade
 * - Códigos 2FA expiram em 15 minutos
 * - Mensagens de erro genéricas para não expor se e-mail existe
 * 
 * @package Application\Controller
 */
namespace Application\Controller;

class AuthController extends BaseController
{
    /**
     * Exibe o formulário de login
     * 
     * Renderiza a view de login com o título da página.
     * Se já houver usuário logado, poderia redirecionar para dashboard.
     * 
     * @return void
     */
    public function showLoginForm()
    {
        view('auth/login', [
            'title' => 'Login - SGE UNIFIO'
        ]);
    }

    /**
     * Processa o login do usuário (1ª etapa)
     * 
     * Fluxo:
     * 1. Valida presença de e-mail e senha
     * 2. Busca usuário no banco pelo e-mail
     * 3. Verifica se a senha está correta usando password_verify
     * 4. Se superadmin: faz login direto
     * 5. Se não-superadmin: gera código 2FA e envia por e-mail
     * 
     * Códigos de verificação:
     * - São números aleatórios de 6 dígitos
     * - Expiram em 15 minutos
     * - São armazenados na tabela usuarios
     * - São enviados por e-mail via EmailService
     * 
     * @return void Redireciona para tela de verificação ou dashboard
     */
    public function login()
    {
        // Coleta credenciais do formulário
        $email = $_POST['email'] ?? '';
        $senha = $_POST['senha'] ?? '';

        // Validação básica
        if (empty($email) || empty($senha)) {
            $_SESSION['error_message'] = "Por favor, preencha e-mail e senha.";
            redirect('/login');
        }

        try {
            $userRepository = $this->repository('UsuarioRepository');
            $user = $userRepository->findByEmail($email);

            // Verifica se usuário existe
            // Mensagem genérica para não expor se e-mail existe no sistema
            if (!$user) {
                $_SESSION['error_message'] = "E-mail ou senha inválidos.";
                redirect('/login');
                return;
            }

            // Verifica se a senha está correta
            // password_verify compara a senha informada com o hash armazenado
            if (!password_verify($senha, $user['senha'])) {
                $_SESSION['error_message'] = "E-mail ou senha inválidos.";
                redirect('/login');
                return;
            }

            // Superadmins fazem login direto sem 2FA (para facilitar administração)
            if ($user['role'] === 'superadmin') {
                $this->createSession($user);
                redirect('/');
                return;
            }

            // Para usuários comuns e admins: gerar código 2FA
            // Gera código aleatório de 6 dígitos
            $code = (string) rand(100000, 999999);
            // Define expiração em 15 minutos
            $expires = date('Y-m-d H:i:s', strtotime('+15 minutes'));

            // Limpa qualquer código anterior do usuário
            $userRepository->clearLoginCode($user['id']);

            // Salva o novo código no banco
            $updateSuccess = $userRepository->updateLoginCode($user['id'], $code, $expires);

            if (!$updateSuccess) {
                $_SESSION['error_message'] = "Erro ao gerar código de verificação. Tente novamente.";
                redirect('/login');
                return;
            }

            // Envia código por e-mail usando EmailService
            $emailService = new \Application\Core\EmailService();
            $emailSent = $emailService->sendVerificationCode($user['email'], $user['nome'], $code);

            if (!$emailSent) {
                error_log("Falha ao enviar email para {$user['email']}.");
                $_SESSION['error_message'] = "Erro ao enviar código de verificação. Tente novamente.";
                redirect('/login');
                return;
            }

            // Log de sucesso (útil para debug)
            error_log("Email enviado com sucesso para {$user['email']}. Código: {$code}");
            
            // Armazena e-mail na sessão temporariamente para a próxima etapa
            $_SESSION['login_email'] = $user['email'];

            // Redireciona para tela de verificação do código
            redirect('/login/verify');

        } catch (\Exception $e) {
            // Captura qualquer erro inesperado e loga
            error_log("Erro no login: " . $e->getMessage());
            $_SESSION['error_message'] = "Ocorreu um erro no sistema. Tente novamente.";
            redirect('/login');
        }
    }

    public function showVerifyForm()
    {
        if (!isset($_SESSION['login_email'])) {
            redirect('/login');
        }
        view('auth/login-verify', ['title' => 'Verificação de Acesso - SGE UNIFIO']);
    }

    public function verifyCode()
    {
        $email = $_SESSION['login_email'] ?? null;
        $code = trim($_POST['code'] ?? '');

        error_log("=== Debug Verificação de Código ===");
        error_log("Email na sessão: " . ($email ?? 'não definido'));
        error_log("Código recebido: " . $code);

        if (empty($email) || empty($code)) {
            error_log("Email ou código vazios");
            $_SESSION['error_message'] = "Dados de verificação inválidos. Por favor, faça login novamente.";
            redirect('/login');
            return;
        }

        try {
            $userRepository = $this->repository('UsuarioRepository');
            $user = $userRepository->findByEmail($email);

            if (!$user) {
                error_log("Usuário não encontrado para o email: $email");
                $_SESSION['error_message'] = "Usuário não encontrado. Por favor, faça login novamente.";
                redirect('/login');
                return;
            }

            // Debug: Ver o que tem no banco ANTES de verificar
            error_log("=== ANTES DA VERIFICAÇÃO ===");
            error_log("Código digitado: '{$code}' (length: " . strlen($code) . ")");
            $debugUser = $userRepository->findByEmail($email);
            error_log("Código no banco: '" . ($debugUser['login_code'] ?? 'NULL') . "' (length: " . strlen($debugUser['login_code'] ?? '') . ")");
            error_log("Expira em: " . ($debugUser['login_code_expires'] ?? 'NULL'));
            error_log("Hora atual: " . date('Y-m-d H:i:s'));
            
            // Verifica no banco de dados
            $dbUser = $userRepository->findUserByLoginCode($email, $code);
            if ($dbUser) {
                error_log("✓ Código do banco de dados válido!");
                $userRepository->clearLoginCode($user['id']);
                unset($_SESSION['login_email'], $_SESSION['verification_code_debug']);
                $this->createSession($dbUser);
                redirect('/');
                return;
            }

            error_log("✗ Código inválido ou expirado para o email: $email");
            $_SESSION['error_message'] = "Código inválido ou expirado. Tente novamente.";
            redirect('/login/verify');

        } catch (\Exception $e) {
            error_log("Erro na verificação do código: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            $_SESSION['error_message'] = "Ocorreu um erro no sistema. Tente novamente.";
            redirect('/login');
        }
    }

    private function createSession(array $user)
    {
        session_regenerate_id(true);
        $_SESSION['loggedin'] = true;
        $_SESSION['id'] = $user['id'];
        $_SESSION['nome'] = $user['nome'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['tipo_usuario_detalhado'] = $user['tipo_usuario_detalhado'];
        $_SESSION['curso_id'] = $user['curso_id'];
        $_SESSION['is_coordenador'] = $user['is_coordenador'] ?? 0;

        if ($user['role'] === 'admin') {
            // Se atletica_id não estiver presente, buscar no banco de dados
            if (empty($user['atletica_id'])) {
                $userRepo = $this->repository('UsuarioRepository');
                $userDetails = $userRepo->findById($user['id']);
                $_SESSION['atletica_id'] = $userDetails['atletica_id'] ?? null;
            } else {
                $_SESSION['atletica_id'] = $user['atletica_id'];
            }
        }
    }

    public function logout()
    {
        $_SESSION = [];
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        session_destroy();
        session_start();
        $_SESSION['success_message'] = "Você saiu com segurança.";
        redirect('/login');
    }

    public function showRegistrationForm()
    {
        try {
            $cursoRepository = $this->repository('CursoRepository');
            $cursos = $cursoRepository->findAll();
            
            // Recuperar dados antigos se houver erro
            $oldInput = $_SESSION['old_input'] ?? [];
            unset($_SESSION['old_input']); // Limpar após usar
            
            view('auth/registro', [
                'title' => 'Criar Conta - SGE UNIFIO',
                'cursos' => $cursos,
                'old' => $oldInput
            ]);
        } catch (\Exception $e) {
            die('Não foi possível carregar a página de registro. Erro no banco de dados.');
        }
    }

    public function register()
    {
        $data = [
            'nome' => trim($_POST['nome'] ?? ''),
            'tipo_usuario_detalhado' => trim($_POST['tipo_usuario_detalhado'] ?? ''),
            'data_nascimento' => trim($_POST['data_nascimento'] ?? ''),
            'email' => trim($_POST['email'] ?? ''),
            'telefone' => trim($_POST['telefone'] ?? ''),
            'senha' => $_POST['senha'] ?? '',
            'confirmar_senha' => $_POST['confirmar_senha'] ?? '',
            'curso_id' => !empty($_POST['curso_id']) ? (int)$_POST['curso_id'] : null,
            'ra' => !empty(trim($_POST['ra'] ?? '')) ? trim($_POST['ra']) : null
        ];

        $errors = [];
        if (empty($data['nome'])) $errors[] = "O nome é obrigatório.";
        if (empty($data['email'])) $errors[] = "O e-mail é obrigatório.";
        if (strlen($data['senha']) < 6) $errors[] = "A senha deve ter no mínimo 6 caracteres.";
        if ($data['senha'] !== $data['confirmar_senha']) $errors[] = "As senhas não coincidem.";
        
        // Validação do telefone - obrigatório e deve ter 11 dígitos no formato (00)00000-0000
        if (empty($data['telefone'])) {
            $errors[] = "O telefone é obrigatório.";
        } else {
            // Remove caracteres não numéricos para validar
            $telefone_numeros = preg_replace('/[^0-9]/', '', $data['telefone']);
            if (strlen($telefone_numeros) !== 11) {
                $errors[] = "O telefone deve conter exatamente 11 dígitos no formato (00)00000-0000.";
            } else {
                // Salvar apenas os números no banco de dados
                $data['telefone'] = $telefone_numeros;
            }
        }

        $email_domain = substr(strrchr($data['email'], "@"), 1);
        if ($data['tipo_usuario_detalhado'] != 'Comunidade Externa' && !in_array($email_domain, ['unifio.edu.br', 'fio.edu.br'])) {
            $errors[] = "Para este tipo de vínculo, é obrigatório o uso de um e-mail institucional UNIFIO.";
        }

        if (in_array($data['tipo_usuario_detalhado'], ['Aluno', 'Membro das Atléticas'])) {
            if (empty($data['ra']) || !preg_match('/^[0-9]{6}$/', $data['ra'])) {
                $errors[] = "O RA/Matrícula deve conter exatamente 6 números.";
            }
        } else {
            $data['ra'] = null;
        }

        if (!empty($errors)) {
            $_SESSION['error_message'] = implode('<br>', $errors);
            $_SESSION['old_input'] = $data; // Salvar os dados preenchidos
            unset($_SESSION['old_input']['senha']); // Não manter a senha por segurança
            unset($_SESSION['old_input']['confirmar_senha']);
            redirect('/registro');
        }

        $data['atletica_id'] = null;
        $data['atletica_join_status'] = 'none'; // Por padrão, ninguém vai para pending

        if ($data['curso_id']) {
            $cursoRepository = $this->repository('CursoRepository');
            $atletica_do_curso = $cursoRepository->findAtleticaIdByCursoId($data['curso_id']);

            if ($atletica_do_curso) {
                // Apenas "Membro das Atléticas" vai direto para pending (aprovação do admin)
                if ($data['tipo_usuario_detalhado'] === 'Membro das Atléticas') {
                    $data['atletica_join_status'] = 'pendente';
                }
                // "Aluno" fica como 'none' - terá que solicitar manualmente no perfil
            }
        }

        $data['senha'] = password_hash($data['senha'], PASSWORD_DEFAULT);
        $data['role'] = 'usuario';
        
        // Professores automaticamente são coordenadores (podem agendar eventos não esportivos)
        $data['is_coordenador'] = ($data['tipo_usuario_detalhado'] === 'Professor') ? 1 : 0;

        try {
            $userRepository = $this->repository('UsuarioRepository');
            $userRepository->createUser($data);
            $_SESSION['success_message'] = "Cadastro realizado com sucesso! Faça seu login.";
            redirect('/login');
        } catch (\PDOException $e) {
            if ($e->getCode() == '23000') {
                $_SESSION['error_message'] = "O e-mail ou RA informado já está cadastrado.";
            } else {
                $_SESSION['error_message'] = "Ocorreu um erro ao realizar o cadastro. Tente novamente.";
            }
            // Salvar os dados preenchidos (exceto senha)
            $oldData = $data;
            unset($oldData['senha']);
            $_SESSION['old_input'] = $oldData;
            redirect('/registro');
        }
    }

    public function showForgotPasswordForm()
    {
        view('auth/esqueci-senha', ['title' => 'Recuperar Senha']);
    }

    public function sendRecoveryLink()
    {
        $email = $_POST['email'] ?? '';
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['error_message'] = "Por favor, insira um e-mail válido.";
            redirect('/esqueci-senha');
        }

        $userRepo = $this->repository('UsuarioRepository');
        $user = $userRepo->findByEmail($email);

        if ($user) {
            $token = bin2hex(random_bytes(32));
            $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));
            $userRepo->updateResetToken($user['id'], $token, $expires);

            // Enviar link de recuperação por e-mail usando PHPMailer
            $emailService = new \Application\Core\EmailService();
            $emailSent = $emailService->sendPasswordRecoveryLink($user['email'], $user['nome'], $token);

            if (!$emailSent) {
                // Se o e-mail não foi enviado, exibe o link na tela como fallback
                $recoveryLink = "/redefinir-senha?token=" . $token;
                $_SESSION['recovery_link'] = $recoveryLink;
                error_log("Falha ao enviar email de recuperação para {$user['email']}. Exibindo link na tela.");
            }
            
            $_SESSION['success_message'] = "Se um usuário com este e-mail existir, um link de recuperação foi enviado.";
        } else {
            // Mensagem genérica para não confirmar se um e-mail existe ou não.
            $_SESSION['success_message'] = "Se um usuário com este e-mail existir, um link de recuperação foi enviado.";
        }

        redirect('/esqueci-senha');
    }

    public function showResetPasswordForm()
    {
        $token = $_GET['token'] ?? '';

        if (empty($token)) {
            $_SESSION['error_message'] = "Token de redefinição não fornecido. Por favor, solicite um novo link de recuperação.";
            redirect('/esqueci-senha');
            return;
        }

        $userRepo = $this->repository('UsuarioRepository');
        $user = $userRepo->findUserByResetToken($token);

        if (!$user) {
            $_SESSION['error_message'] = "Token inválido ou expirado. Por favor, solicite um novo link de recuperação.";
            redirect('/esqueci-senha');
            return;
        }

        view('auth/redefinir-senha', [
            'title' => 'Redefinir Senha',
            'token' => $token
        ]);
    }

    public function resetPassword()
    {
        $token = $_POST['token'] ?? '';
        $novaSenha = $_POST['nova_senha'] ?? '';
        $confirmarNovaSenha = $_POST['confirmar_nova_senha'] ?? '';

        if (empty($token)) {
            $_SESSION['error_message'] = "Token não fornecido. Por favor, solicite um novo link de recuperação.";
            redirect('/esqueci-senha');
            return;
        }

        // Validar o token primeiro
        $userRepo = $this->repository('UsuarioRepository');
        $user = $userRepo->findUserByResetToken($token);

        if (!$user) {
            $_SESSION['error_message'] = "Token inválido ou expirado. Por favor, solicite um novo link de recuperação.";
            redirect('/esqueci-senha');
            return;
        }

        // Validações dos campos
        if (empty($novaSenha) || empty($confirmarNovaSenha)) {
            $_SESSION['error_message'] = "Todos os campos são obrigatórios.";
            redirect('/redefinir-senha?token=' . urlencode($token));
            return;
        }

        if (strlen($novaSenha) < 6) {
            $_SESSION['error_message'] = "A nova senha deve ter no mínimo 6 caracteres.";
            redirect('/redefinir-senha?token=' . urlencode($token));
            return;
        }

        if ($novaSenha !== $confirmarNovaSenha) {
            $_SESSION['error_message'] = "As senhas não coincidem.";
            redirect('/redefinir-senha?token=' . urlencode($token));
            return;
        }

        // Atualizar a senha
        $newHashedPassword = password_hash($novaSenha, PASSWORD_DEFAULT);
        $success = $userRepo->updatePasswordAndClearToken($user['id'], $newHashedPassword);

        if ($success) {
            $_SESSION['success_message'] = "Senha redefinida com sucesso! Você já pode fazer o login.";
            redirect('/login');
        } else {
            $_SESSION['error_message'] = "Ocorreu um erro ao redefinir sua senha. Tente novamente.";
            redirect('/redefinir-senha?token=' . urlencode($token));
        }
    }
}
