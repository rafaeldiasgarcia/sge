<?php
#
# Controller de Autenticação.
# Gerencia todo o fluxo de autenticação de usuários: login, verificação em
# duas etapas (simulada), logout, registro e a funcionalidade de
# recuperação de senha.
#
namespace Application\Controller;

class AuthController extends BaseController
{
    public function showLoginForm()
    {
        view('auth/login', [
            'title' => 'Login - SGE UNIFIO'
        ]);
    }

    public function login()
    {
        $email = $_POST['email'] ?? '';
        $senha = $_POST['senha'] ?? '';

        if (empty($email) || empty($senha)) {
            $_SESSION['error_message'] = "Por favor, preencha e-mail e senha.";
            redirect('/login');
        }

        try {
            $userRepository = $this->repository('UsuarioRepository');
            $user = $userRepository->findByEmail($email);

            if ($user && password_verify($senha, $user['senha'])) {
                if ($user['role'] === 'superadmin') {
                    $this->createSession($user);
                    redirect('/');
                }

                // Gerar código como string para evitar problemas de tipo
                $code = (string) rand(100000, 999999);
                $expires = date('Y-m-d H:i:s', strtotime('+15 minutes'));

                // Verificar se a atualização foi bem-sucedida
                $updateSuccess = $userRepository->updateLoginCode($user['id'], $code, $expires);

                if (!$updateSuccess) {
                    $_SESSION['error_message'] = "Erro ao gerar código de verificação. Tente novamente.";
                    redirect('/login');
                }

                $_SESSION['login_email'] = $user['email'];
                $_SESSION['login_code_simulado'] = $code;
                $_SESSION['verification_code'] = $code; // Para exibir na tela de verificação

                redirect('/login/verify');
            } else {
                $_SESSION['error_message'] = "E-mail ou senha inválidos.";
                redirect('/login');
            }
        } catch (\Exception $e) {
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
        $code = $_POST['code'] ?? '';

        if (empty($email) || empty($code)) {
            redirect('/login');
        }

        try {
            $userRepository = $this->repository('UsuarioRepository');
            $user = $userRepository->findUserByLoginCode($email, $code);

            if ($user) {
                $userRepository->clearLoginCode($user['id']);
                unset($_SESSION['login_email'], $_SESSION['login_code_simulado'], $_SESSION['verification_code']);
                $this->createSession($user);
                redirect('/');
            } else {
                $_SESSION['error_message'] = "Código inválido ou expirado. Tente novamente.";
                redirect('/login/verify');
            }
        } catch (\Exception $e) {
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

        if ($user['role'] === 'admin') {
            $_SESSION['atletica_id'] = $user['atletica_id'];
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
            view('auth/registro', [
                'title' => 'Criar Conta - SGE UNIFIO',
                'cursos' => $cursos
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
            'senha' => $_POST['senha'] ?? '',
            'confirmar_senha' => $_POST['confirmar_senha'] ?? '',
            'curso_id' => !empty($_POST['curso_id']) ? (int)$_POST['curso_id'] : null,
            'ra' => trim($_POST['ra'] ?? null)
        ];

        $errors = [];
        if (empty($data['nome'])) $errors[] = "O nome é obrigatório.";
        if (empty($data['email'])) $errors[] = "O e-mail é obrigatório.";
        if (strlen($data['senha']) < 6) $errors[] = "A senha deve ter no mínimo 6 caracteres.";
        if ($data['senha'] !== $data['confirmar_senha']) $errors[] = "As senhas não coincidem.";

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
            redirect('/registro');
        }

        $data['atletica_id'] = null;
        $data['atletica_join_status'] = 'none';
        if ($data['curso_id']) {
            $cursoRepository = $this->repository('CursoRepository');
            $data['atletica_id'] = $cursoRepository->findAtleticaIdByCursoId($data['curso_id']);
            if ($data['atletica_id']) {
                $data['atletica_join_status'] = 'pendente';
            }
        }

        $data['senha'] = password_hash($data['senha'], PASSWORD_DEFAULT);
        $data['role'] = 'usuario';

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

            // SIMULAÇÃO DE ENVIO DE E-MAIL
            // Em um ambiente real, você usaria uma biblioteca como PHPMailer aqui.
            $baseUrl = getenv('APP_URL') ?: "http://" . $_SERVER['HTTP_HOST'];
            $recoveryLink = $baseUrl . "/redefinir-senha?token=" . $token;
            $_SESSION['success_message'] = "Se um usuário com este e-mail existir, um link de recuperação foi enviado.<br><br><strong>[AMBIENTE DE TESTE]</strong><br>Seu link é: <a href='{$recoveryLink}'>{$recoveryLink}</a>";
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
            die('Token de redefinição não fornecido.');
        }

        $userRepo = $this->repository('UsuarioRepository');
        $user = $userRepo->findUserByResetToken($token);

        if (!$user) {
            $_SESSION['error_message'] = "Token inválido ou expirado. Por favor, solicite um novo link de recuperação.";
            redirect('/esqueci-senha');
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

        if (empty($token) || empty($novaSenha) || empty($confirmarNovaSenha)) {
            $_SESSION['error_message'] = "Todos os campos são obrigatórios.";
            redirect('/redefinir-senha?token=' . $token);
        }
        if (strlen($novaSenha) < 6) {
            $_SESSION['error_message'] = "A nova senha deve ter no mínimo 6 caracteres.";
            redirect('/redefinir-senha?token=' . $token);
        }
        if ($novaSenha !== $confirmarNovaSenha) {
            $_SESSION['error_message'] = "As senhas não coincidem.";
            redirect('/redefinir-senha?token=' . $token);
        }

        $userRepo = $this->repository('UsuarioRepository');
        $user = $userRepo->findUserByResetToken($token);

        if (!$user) {
            $_SESSION['error_message'] = "Token inválido ou expirado. Por favor, solicite um novo link de recuperação.";
            redirect('/esqueci-senha');
        }

        $newHashedPassword = password_hash($novaSenha, PASSWORD_DEFAULT);
        $success = $userRepo->updatePasswordAndClearToken($user['id'], $newHashedPassword);

        if ($success) {
            $_SESSION['success_message'] = "Senha redefinida com sucesso! Você já pode fazer o login.";
            redirect('/login');
        } else {
            $_SESSION['error_message'] = "Ocorreu um erro ao redefinir sua senha. Tente novamente.";
            redirect('/redefinir-senha?token=' . $token);
        }
    }
}

