<?php
#
# Controller para as funcionalidades do Usuário.
# Gerencia o dashboard do usuário, a edição de perfil (dados pessoais e senha)
# e as inscrições em modalidades esportivas.
#
namespace Application\Controller;

use Application\Core\Auth;

class UsuarioController extends BaseController
{
    public function dashboard()
    {
        Auth::protect();

        $userData = [
            'nome' => Auth::name(),
            'role' => Auth::role(),
            'tipo_usuario' => Auth::get('tipo_usuario_detalhado')
        ];

        $agendamentoRepository = $this->repository('AgendamentoRepository');
        $eventosComPresenca = $agendamentoRepository->findEventosComPresenca(Auth::id());

        view('usuario/dashboard', [
            'title' => 'Meu Painel',
            'user' => $userData,
            'eventos_presenca' => $eventosComPresenca
        ]);
    }

    public function perfil()
    {
        Auth::protect();

        try {
            $userId = Auth::id();
            $userRepository = $this->repository('UsuarioRepository');
            $cursoRepository = $this->repository('CursoRepository');
            $atleticaRepository = $this->repository('AtleticaRepository');

            $user = $userRepository->findById($userId);
            $cursos = $cursoRepository->findAll();

            if (!$user) {
                redirect('/dashboard');
            }

            // Buscar informações da atlética do curso do usuário
            $atleticaInfo = null;
            if ($user['curso_id']) {
                $atleticaInfo = $atleticaRepository->findAtleticaByCursoId($user['curso_id']);
            }

            view('usuario/perfil', [
                'title' => 'Editar Perfil',
                'user' => $user,
                'cursos' => $cursos,
                'atletica_info' => $atleticaInfo
            ]);
        } catch (\Exception $e) {
            $_SESSION['error_message'] = "Ocorreu um erro ao carregar seu perfil.";
            redirect('/dashboard');
        }
    }

    public function updatePerfil()
    {
        Auth::protect();
        $formType = $_POST['form_type'] ?? null;

        if ($formType === 'dados_pessoais') {
            $this->handleUpdateProfileData();
        } elseif ($formType === 'alterar_senha') {
            $this->handleUpdatePassword();
        } else {
            redirect('/perfil');
        }
    }

    private function handleUpdateProfileData()
    {
        if (empty($_POST['nome']) || empty($_POST['email']) || empty($_POST['data_nascimento'])) {
            $_SESSION['error_message'] = "Todos os campos são obrigatórios.";
            redirect('/perfil');
        }

        $data = [
            'nome' => trim($_POST['nome']),
            'email' => trim($_POST['email']),
            'data_nascimento' => trim($_POST['data_nascimento']),
            'curso_id' => $_POST['curso_id'] ?? null
        ];

        try {
            $userRepository = $this->repository('UsuarioRepository');
            $success = $userRepository->updateProfileData(Auth::id(), $data);

            if ($success) {
                $_SESSION['nome'] = $data['nome'];
                $_SESSION['success_message'] = "Perfil atualizado com sucesso!";
            } else {
                $_SESSION['error_message'] = "Não foi possível atualizar o perfil.";
            }
        } catch (\Exception $e) {
            if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                $_SESSION['error_message'] = "O e-mail informado já está em uso por outra conta.";
            } else {
                $_SESSION['error_message'] = "Ocorreu um erro ao atualizar o perfil.";
            }
        }

        redirect('/perfil');
    }

    private function handleUpdatePassword()
    {
        $senhaAtual = $_POST['senha_atual'] ?? '';
        $novaSenha = $_POST['nova_senha'] ?? '';
        $confirmarNovaSenha = $_POST['confirmar_nova_senha'] ?? '';

        if (empty($senhaAtual) || empty($novaSenha) || empty($confirmarNovaSenha)) {
            $_SESSION['error_message'] = "Todos os campos de senha são obrigatórios.";
            redirect('/perfil');
        }
        if (strlen($novaSenha) < 6) {
            $_SESSION['error_message'] = "A nova senha deve ter no mínimo 6 caracteres.";
            redirect('/perfil');
        }
        if ($novaSenha !== $confirmarNovaSenha) {
            $_SESSION['error_message'] = "A nova senha e a confirmação não coincidem.";
            redirect('/perfil');
        }

        try {
            $userRepository = $this->repository('UsuarioRepository');
            $currentHashedPassword = $userRepository->findPasswordHashById(Auth::id());

            if (!$currentHashedPassword || !password_verify($senhaAtual, $currentHashedPassword)) {
                $_SESSION['error_message'] = "A senha atual está incorreta.";
                redirect('/perfil');
            }

            $newHashedPassword = password_hash($novaSenha, PASSWORD_DEFAULT);
            $success = $userRepository->updatePassword(Auth::id(), $newHashedPassword);

            if ($success) {
                $_SESSION['success_message'] = "Senha alterada com sucesso!";
            } else {
                $_SESSION['error_message'] = "Não foi possível alterar a senha.";
            }
        } catch (\Exception $e) {
            $_SESSION['error_message'] = "Ocorreu um erro ao alterar a senha.";
        }

        redirect('/perfil');
    }

    public function showInscricoes()
    {
        Auth::protect();
        $userId = Auth::id();
        $atleticaId = Auth::get('atletica_id');

        if (Auth::get('tipo_usuario_detalhado') !== 'Membro das Atléticas' || !$atleticaId) {
            $_SESSION['error_message'] = "Você precisa ser membro de uma atlética para se inscrever em modalidades.";
            redirect('/dashboard');
        }

        $userRepo = $this->repository('UsuarioRepository');
        $modalidadeRepo = $this->repository('ModalidadeRepository');

        $minhasInscricoes = $userRepo->findInscricoesByUserId($userId);
        $todasModalidades = $modalidadeRepo->findAll();

        $inscritoIds = array_column($minhasInscricoes, 'modalidade_nome');
        $modalidadesDisponiveis = array_filter($todasModalidades, function($modalidade) use ($inscritoIds) {
            return !in_array($modalidade['nome'], $inscritoIds);
        });

        view('usuario/inscricoes', [
            'title' => 'Minhas Inscrições',
            'minhas_inscricoes' => $minhasInscricoes,
            'modalidades_disponiveis' => $modalidadesDisponiveis
        ]);
    }

    public function inscreverEmModalidade()
    {
        Auth::protect();
        $modalidadeId = (int)($_POST['modalidade_id'] ?? 0);
        $atleticaId = Auth::get('atletica_id');

        if ($modalidadeId > 0 && $atleticaId) {
            $userRepo = $this->repository('UsuarioRepository');
            $userRepo->createInscricaoModalidade(Auth::id(), $modalidadeId, $atleticaId);
            $_SESSION['success_message'] = "Inscrição enviada com sucesso! Aguarde a aprovação do admin da sua atlética.";
        }
        redirect('/inscricoes');
    }

    public function cancelarInscricao()
    {
        Auth::protect();
        $inscricaoId = (int)($_POST['inscricao_id'] ?? 0);
        if ($inscricaoId > 0) {
            $userRepo = $this->repository('UsuarioRepository');
            $userRepo->deleteInscricaoModalidade($inscricaoId, Auth::id());
            $_SESSION['success_message'] = "Inscrição cancelada com sucesso.";
        }
        redirect('/inscricoes');
    }

    public function solicitarEntradaAtletica()
    {
        Auth::protect();

        try {
            $userId = Auth::id();
            $userRepository = $this->repository('UsuarioRepository');

            // Atualizar status para 'pendente'
            $success = $userRepository->updateAtleticaJoinStatus($userId, 'pendente');

            if ($success) {
                $_SESSION['success_message'] = "Solicitação enviada com sucesso! Aguarde a aprovação do administrador da atlética.";
            } else {
                $_SESSION['error_message'] = "Erro ao enviar solicitação. Tente novamente.";
            }
        } catch (\Exception $e) {
            $_SESSION['error_message'] = "Ocorreu um erro ao processar sua solicitação.";
        }

        redirect('/perfil');
    }
}