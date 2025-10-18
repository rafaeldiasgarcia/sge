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
    /**
     * Guarda local: exige que o usuário seja membro de atlética
     * Retorna o atletica_id válido ou redireciona com mensagem
     */
    private function requireMembroAtleticaOrRedirect(string $redirectPath = '/dashboard'): int
    {
        Auth::protect();
        $atleticaId = (int)(Auth::get('atletica_id') ?? 0);
        $tipoDetalhado = Auth::get('tipo_usuario_detalhado');
        if ($tipoDetalhado !== 'Membro das Atléticas' || $atleticaId <= 0) {
            $_SESSION['error_message'] = "Você precisa ser membro de uma atlética para se inscrever em modalidades.";
            redirect($redirectPath);
        }
        return $atleticaId;
    }

    public function dashboard()
    {
        $this->requireAuth();

        $userData = [
            'nome' => Auth::name(),
            'role' => Auth::role(),
            'tipo_usuario' => Auth::get('tipo_usuario_detalhado'),
            'is_coordenador' => Auth::get('is_coordenador')
        ];

        $agendamentoRepository = $this->repository('AgendamentoRepository');
        $eventosComPresenca = $agendamentoRepository->findEventosComPresenca(Auth::id());

        view('usuario/dashboard', [
            'title' => 'Meu Painel - UNIFIO',
            'user' => $userData,
            'eventos_presenca' => $eventosComPresenca,
            'additional_scripts' => ['/js/event-popup.js']
        ]);
    }

    public function perfil()
    {
        $this->requireAuth();

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
                'title' => 'Editar Perfil - UNIFIO',
                'user' => $user,
                'cursos' => $cursos,
                'atletica_info' => $atleticaInfo,
                'meus_eventos' => $meusEventos,
                'additional_scripts' => ['/js/event-popup.js', '/js/perfil-page.js']
            ]);
        } catch (\Exception $e) {
            $this->setErrorAndRedirect("Ocorreu um erro ao carregar seu perfil.", '/dashboard');
        }
    }

    public function updatePerfil()
    {
        $this->requireAuth();
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
            $this->setErrorAndRedirect("O nome é obrigatório.", '/perfil');
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
            $this->setErrorAndRedirect("Todos os campos de senha são obrigatórios.", '/perfil');
        }
        if (strlen($novaSenha) < 6) {
            $this->setErrorAndRedirect("A nova senha deve ter no mínimo 6 caracteres.", '/perfil');
        }
        if ($novaSenha !== $confirmarNovaSenha) {
            $this->setErrorAndRedirect("A nova senha e a confirmação não coincidem.", '/perfil');
        }

        try {
            $userRepository = $this->repository('UsuarioRepository');
            $currentHashedPassword = $userRepository->findPasswordHashById(Auth::id());

            if (!$currentHashedPassword || !password_verify($senhaAtual, $currentHashedPassword)) {
                $this->setErrorAndRedirect("A senha atual está incorreta.", '/perfil');
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
        $this->requireAuth();
        $userId = Auth::id();
        $atleticaId = $this->requireMembroAtleticaOrRedirect('/dashboard');

        $userRepo = $this->repository('UsuarioRepository');
        $modalidadeRepo = $this->repository('ModalidadeRepository');

        $minhasInscricoes = $userRepo->findInscricoesByUserId($userId);
        $todasModalidades = $modalidadeRepo->findAll();

        $inscritoIds = array_column($minhasInscricoes, 'modalidade_nome');
        $modalidadesDisponiveis = array_filter($todasModalidades, function($modalidade) use ($inscritoIds) {
            return !in_array($modalidade['nome'], $inscritoIds);
        });

        view('usuario/inscricoes', [
            'title' => 'Minhas Inscrições - UNIFIO',
            'user' => $this->getUserData(),
            'inscricoes' => $minhasInscricoes
        ]);
    }

    public function inscreverEmModalidade()
    {
        $this->requireAuth();
        $modalidadeId = (int)($_POST['modalidade_id'] ?? 0);
        $atleticaId = $this->requireMembroAtleticaOrRedirect('/inscricoes');

        if ($modalidadeId > 0 && $atleticaId) {
            $userRepo = $this->repository('UsuarioRepository');
            $userRepo->createInscricaoModalidade(Auth::id(), $modalidadeId, $atleticaId);
            $_SESSION['success_message'] = "Inscrição enviada com sucesso! Aguarde a aprovação do admin da sua atlética.";
        }
        redirect('/inscricoes');
    }

    public function cancelarInscricao()
    {
        $this->requireAuth();
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
        $this->requireAuth();

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
        $this->requireAuth();

        try {
            $userId = Auth::id();
            $userRepository = $this->repository('UsuarioRepository');

            // Buscar informações do usuário
            $user = $userRepository->findById($userId);

            if (!$user) {
                $this->setErrorAndRedirect("Usuário não encontrado.", '/perfil');
                return;
            }

            // Verificar se é admin da atlética
            if ($user['role'] === 'admin') {
                $this->setErrorAndRedirect("Administradores não podem sair da atlética. Entre em contato com um super administrador.", '/perfil');
                return;
            }

            // Verificar se está aprovado na atlética
            if ($user['atletica_join_status'] !== 'aprovado') {
                $this->setErrorAndRedirect("Você não é membro ativo de uma atlética.", '/perfil');
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

    public function solicitarTrocaCurso()
    {
        $this->requireAuth();

        try {
            $userId = Auth::id();
            $cursoNovoId = (int)($_POST['curso_novo_id'] ?? 0);
            $justificativa = trim($_POST['justificativa'] ?? '');

            // Validações
            if ($cursoNovoId <= 0) {
                $this->setErrorAndRedirect("Por favor, selecione um curso válido.", '/perfil');
                return;
            }

            if (strlen($justificativa) < 50) {
                $this->setErrorAndRedirect("A justificativa deve ter no mínimo 50 caracteres.", '/perfil');
                return;
            }

            $userRepository = $this->repository('UsuarioRepository');
            $solicitacaoRepository = $this->repository('SolicitacaoTrocaCursoRepository');

            // Verificar se já tem solicitação pendente
            if ($solicitacaoRepository->hasSolicitacaoPendente($userId)) {
                $this->setErrorAndRedirect("Você já possui uma solicitação de troca de curso pendente. Aguarde a resposta do coordenador.", '/perfil');
                return;
            }

            // Buscar curso atual do usuário
            $user = $userRepository->findById($userId);
            $cursoAtualId = $user['curso_id'] ?? null;

            // Verificar se o curso novo é diferente do atual
            if ($cursoAtualId && $cursoAtualId == $cursoNovoId) {
                $this->setErrorAndRedirect("O curso selecionado é o mesmo que seu curso atual.", '/perfil');
                return;
            }

            // Criar a solicitação
            $success = $solicitacaoRepository->create($userId, $cursoAtualId, $cursoNovoId, $justificativa);

            if ($success) {
                $_SESSION['success_message'] = "Solicitação enviada com sucesso! O coordenador analisará seu pedido e você receberá uma notificação com a resposta.";
                
                // Notificar todos os super admins sobre a nova solicitação
                $this->notificarSuperAdminsNovaSolicitacao($userId);
            } else {
                $_SESSION['error_message'] = "Erro ao enviar solicitação. Tente novamente.";
            }
        } catch (\Exception $e) {
            error_log("Erro em solicitarTrocaCurso: " . $e->getMessage());
            $_SESSION['error_message'] = "Ocorreu um erro ao processar sua solicitação.";
        }

        redirect('/perfil');
    }

    /**
     * Notifica todos os super admins sobre uma nova solicitação de troca de curso
     * 
     * @param int $usuarioId ID do usuário que solicitou a troca
     * @return void
     */
    private function notificarSuperAdminsNovaSolicitacao(int $usuarioId): void
    {
        try {
            $userRepository = $this->repository('UsuarioRepository');
            $notificationRepository = $this->repository('NotificationRepository');

            // Buscar informações do usuário solicitante
            $usuario = $userRepository->findById($usuarioId);
            
            if (!$usuario) {
                return;
            }

            // Buscar todos os super admins
            $superAdmins = $userRepository->findSuperAdmins();

            // Criar notificação para cada super admin
            $titulo = '🔔 Nova Solicitação de Troca de Curso';
            $mensagem = "O aluno " . $usuario['nome'] . " (RA: " . ($usuario['ra'] ?? 'N/A') . ") solicitou uma troca de curso. Acesse 'Gerenciar Usuários' para analisar o pedido.";

            foreach ($superAdmins as $admin) {
                $notificationRepository->create(
                    $admin['id'],
                    $titulo,
                    $mensagem,
                    'sistema'
                );
            }
        } catch (\Exception $e) {
            error_log("Erro ao notificar super admins: " . $e->getMessage());
            // Não interrompe o fluxo principal se falhar a notificação
        }
    }
}