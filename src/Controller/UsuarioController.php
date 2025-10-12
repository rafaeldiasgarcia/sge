<?php
/**
 * Controller do Usuário (UsuarioController)
 * 
 * Gerencia as funcionalidades disponíveis para usuários comuns autenticados,
 * incluindo dashboard, perfil, inscrições e gestão de atlética.
 * 
 * Funcionalidades principais:
 * - Dashboard personalizado com próximos eventos
 * - Visualização e edição de perfil
 * - Alteração de senha
 * - Gerenciamento de inscrições em modalidades esportivas
 * - Solicitação para entrar em atlética
 * - Saída de atlética
 * 
 * Dashboard exibe:
 * - Próximo evento esportivo com presença confirmada
 * - Próximo evento não-esportivo com presença confirmada
 * - Estatísticas de participação
 * - Links rápidos para ações comuns
 * 
 * Perfil permite editar:
 * - Nome completo
 * - E-mail
 * - Data de nascimento
 * - Telefone
 * - Curso
 * - Senha (com confirmação de senha antiga)
 * 
 * Inscrições em Modalidades:
 * - Ver modalidades disponíveis
 * - Inscrever-se em modalidades
 * - Cancelar inscrições (se pendentes)
 * - Status: pendente, aprovado, rejeitado
 * 
 * @package Application\Controller
 */
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
            'tipo_usuario' => Auth::get('tipo_usuario_detalhado'),
            'is_coordenador' => Auth::get('is_coordenador')
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
            $agendamentoRepository = $this->repository('AgendamentoRepository');

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

            // Buscar TODOS os eventos futuros com presença confirmada do usuário
            $meusEventos = $agendamentoRepository->findTodosEventosComPresencaFuturos($userId);

            view('usuario/perfil', [
                'title' => 'Editar Perfil',
                'user' => $user,
                'cursos' => $cursos,
                'atletica_info' => $atleticaInfo,
                'meus_eventos' => $meusEventos
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
        if (empty($_POST['nome'])) {
            $_SESSION['error_message'] = "O nome é obrigatório.";
            redirect('/perfil');
        }

        $data = [
            'nome' => trim($_POST['nome']),
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
            $_SESSION['error_message'] = "Ocorreu um erro ao atualizar o perfil.";
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
            'user' => $this->getUserData(),
            'inscricoes' => $minhasInscricoes
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

    public function sairAtletica()
    {
        Auth::protect();

        try {
            $userId = Auth::id();
            $userRepository = $this->repository('UsuarioRepository');

            // Buscar informações do usuário
            $user = $userRepository->findById($userId);

            if (!$user) {
                $_SESSION['error_message'] = "Usuário não encontrado.";
                redirect('/perfil');
                return;
            }

            // Verificar se é admin da atlética
            if ($user['role'] === 'admin') {
                $_SESSION['error_message'] = "Administradores não podem sair da atlética. Entre em contato com um super administrador.";
                redirect('/perfil');
                return;
            }

            // Verificar se está aprovado na atlética
            if ($user['atletica_join_status'] !== 'aprovado') {
                $_SESSION['error_message'] = "Você não é membro ativo de uma atlética.";
                redirect('/perfil');
                return;
            }

            // Atualizar status para NULL e remover atletica_id
            $success = $userRepository->sairDaAtletica($userId);

            if ($success) {
                // Atualizar sessão
                if (isset($_SESSION['atletica_id'])) {
                    unset($_SESSION['atletica_id']);
                }
                $_SESSION['tipo_usuario_detalhado'] = 'Aluno';
                $_SESSION['success_message'] = "Você saiu da atlética com sucesso. Seu status agora é 'Aluno'.";
            } else {
                $_SESSION['error_message'] = "Erro ao sair da atlética. Tente novamente.";
            }
        } catch (\Exception $e) {
            // Log do erro para debug
            error_log("Erro em sairAtletica: " . $e->getMessage() . " | Linha: " . $e->getLine());
            $_SESSION['error_message'] = "Ocorreu um erro ao processar sua solicitação: " . $e->getMessage();
        }

        redirect('/perfil');
    }
}