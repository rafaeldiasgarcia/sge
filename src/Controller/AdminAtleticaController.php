<?php
/**
 * Controller do Administrador de Atlética (AdminAtleticaController)
 * 
 * Gerencia as funcionalidades administrativas específicas de cada atlética.
 * Admins de atlética têm permissões limitadas apenas à sua própria atlética.
 * 
 * Funcionalidades principais:
 * 
 * 1. Dashboard:
 *    - Estatísticas da atlética (membros, atletas, eventos)
 *    - Pendências aguardando aprovação
 *    - Links rápidos para ações comuns
 * 
 * 2. Gerenciamento de Membros:
 *    - Listar solicitações pendentes de entrada
 *    - Aprovar novos membros
 *    - Recusar solicitações
 *    - Listar membros ativos
 *    - Promover membros a administradores
 *    - Rebaixar administradores a membros
 *    - Remover membros da atlética
 * 
 * 3. Inscrições em Modalidades:
 *    - Listar inscrições pendentes
 *    - Aprovar inscrições em modalidades esportivas
 *    - Rejeitar inscrições
 *    - Ver inscrições aprovadas por modalidade
 * 
 * 4. Gerenciamento de Eventos:
 *    - Ver eventos futuros esportivos
 *    - Gerenciar inscrições de atletas em eventos
 *    - Adicionar atletas aos eventos
 *    - Remover atletas dos eventos
 * 
 * Restrições:
 * - Admin só pode gerenciar sua própria atlética
 * - Não pode promover/rebaixar super admins
 * - Não pode aprovar agendamentos (apenas super admin)
 * 
 * Fluxo de Aprovação de Membros:
 * 1. Aluno solicita entrada (atletica_join_status = 'pendente')
 * 2. Admin visualiza na lista de pendentes
 * 3. Admin aprova ou recusa
 * 4. Se aprovado: tipo_usuario_detalhado = 'Membro das Atléticas'
 * 5. Aluno recebe notificação do resultado
 * 
 * Todas as ações são protegidas pelo middleware Auth::protectAdmin()
 * 
 * @package Application\Controller
 */
namespace Application\Controller;

use Application\Core\Auth;
use Application\Core\NotificationService;

class AdminAtleticaController extends BaseController
{
    private $notificationService;

    public function __construct()
    {
        $this->notificationService = new NotificationService();
    }

    public function dashboard()
    {
        Auth::protectAdmin();
        $atleticaId = Auth::get('atletica_id');

        // Verificar se o usuário tem uma atlética associada
        if (!$atleticaId) {
            $_SESSION['error_message'] = "Usuário não possui uma atlética associada. Entre em contato com o administrador do sistema.";
            redirect('/dashboard');
            return;
        }

        $adminRepo = $this->repository('AdminAtleticaRepository');

        $stats = [
            'atletas_aprovados' => $adminRepo->countAtletasAprovados($atleticaId),
            'membros_pendentes' => $adminRepo->countMembrosPendentes($atleticaId)
        ];

        view('admin_atletica/dashboard', [
            'title' => 'Painel da Atlética',
            'user' => $this->getUserData(),
            'stats' => $stats
        ]);
    }

    public function gerenciarMembros()
    {
        Auth::protectAdmin();
        $atleticaId = Auth::get('atletica_id');

        if (!$atleticaId) {
            $_SESSION['error_message'] = "Usuário não possui uma atlética associada. Entre em contato com o administrador do sistema.";
            redirect('/dashboard');
            return;
        }

        $adminRepo = $this->repository('AdminAtleticaRepository');
        $pendentes = $adminRepo->findMembrosPendentes($atleticaId);


        view('admin_atletica/gerenciar-membros', [
            'title' => 'Gerenciar Membros',
            'user' => $this->getUserData(),
            'pendentes' => $pendentes
        ]);
    }

    public function handleMembroAction()
    {
        Auth::protectAdmin();
        $atleticaId = Auth::get('atletica_id');

        if (!$atleticaId) {
            $_SESSION['error_message'] = "Usuário não possui uma atlética associada.";
            redirect('/dashboard');
            return;
        }

        $alunoId = (int)($_POST['aluno_id'] ?? 0);
        $action = $_POST['acao'] ?? '';
        $adminRepo = $this->repository('AdminAtleticaRepository');

        // Buscar informações da atlética para as notificações
        $atletica = $adminRepo->getAtleticaById($atleticaId);
        $nomeAtletica = $atletica ? $atletica['nome'] : 'Atlética';

        if ($action === 'aprovar') {
            $adminRepo->aprovarMembro($alunoId, $atleticaId);
            $_SESSION['success_message'] = "Aluno aprovado e adicionado à atlética!";
            
            // Enviar notificação de aceite
            $this->notificationService->notifyMembroAceito($alunoId, $nomeAtletica);
        } elseif ($action === 'recusar') {
            $adminRepo->recusarMembro($alunoId);
            $_SESSION['success_message'] = "Solicitação recusada.";
            
            // Enviar notificação de recusa
            $this->notificationService->notifyMembroRecusado($alunoId, $nomeAtletica);
        }
        redirect('/admin/atletica/inscricoes');
    }

    public function gerenciarInscricoes()
    {
        Auth::protectAdmin();
        $atleticaId = Auth::get('atletica_id');

        if (!$atleticaId) {
            $_SESSION['error_message'] = "Usuário não possui uma atlética associada. Entre em contato com o administrador do sistema.";
            redirect('/dashboard');
            return;
        }

        $adminRepo = $this->repository('AdminAtleticaRepository');
        view('admin_atletica/gerenciar-inscricoes', [
            'title' => 'Gerenciar Inscrições e Membros',
            'user' => $this->getUserData(),
            'solicitacoes_pendentes' => $adminRepo->findMembrosPendentes($atleticaId),
            'membros' => $adminRepo->findMembrosAtletica($atleticaId)
        ]);
    }

    public function handleInscricaoAction()
    {
        Auth::protectAdmin();
        $inscricaoId = (int)($_POST['inscricao_id'] ?? 0);
        $action = $_POST['acao'] ?? ''; // aprovar, recusar, remover
        $adminRepo = $this->repository('AdminAtleticaRepository');

        if ($action === 'aprovar') $adminRepo->updateStatusInscricao($inscricaoId, 'aprovado');
        if ($action === 'recusar') $adminRepo->updateStatusInscricao($inscricaoId, 'recusado');
        if ($action === 'remover') $adminRepo->updateStatusInscricao($inscricaoId, 'recusado');

        $_SESSION['success_message'] = "Status da inscrição atualizado.";
        redirect('/admin/atletica/inscricoes');
    }

    public function gerenciarEventos()
    {
        Auth::protectAdmin();
        $atleticaId = Auth::get('atletica_id');

        if (!$atleticaId) {
            $_SESSION['error_message'] = "Usuário não possui uma atlética associada. Entre em contato com o administrador do sistema.";
            redirect('/dashboard');
            return;
        }

        $agendamentoRepo = $this->repository('AgendamentoRepository');
        $eventos = $agendamentoRepo->findFutureEsportivoEvents();

        $adminRepo = $this->repository('AdminAtleticaRepository');
        foreach ($eventos as $key => $evento) {
            $eventos[$key]['inscritos'] = $adminRepo->findAlunosInscritosEmEvento($evento['id'], $atleticaId);
            $eventos[$key]['disponiveis'] = $adminRepo->findMembrosDisponiveisParaEvento($evento['id'], $atleticaId);
        }

        view('admin_atletica/gerenciar-eventos', [
            'title' => 'Gerenciar Participações em Eventos',
            'user' => $this->getUserData(),
            'eventos' => $eventos
        ]);
    }

    public function inscreverEmEvento()
    {
        Auth::protectAdmin();
        $atleticaId = Auth::get('atletica_id');

        if (!$atleticaId) {
            $_SESSION['error_message'] = "Usuário não possui uma atlética associada.";
            redirect('/dashboard');
            return;
        }

        $alunoId = (int)($_POST['aluno_id'] ?? 0);
        $eventoId = (int)($_POST['evento_id'] ?? 0);
        $adminRepo = $this->repository('AdminAtleticaRepository');
        $adminRepo->inscreverAlunoEmEvento($alunoId, $eventoId, $atleticaId);
        $_SESSION['success_message'] = "Aluno inscrito com sucesso no evento!";
        redirect('/admin/atletica/eventos?open_evento=' . $eventoId);
    }

    public function removerDeEvento()
    {
        Auth::protectAdmin();
        $inscricaoId = (int)($_POST['inscricao_id'] ?? 0);
        $eventoId = (int)($_POST['evento_id'] ?? 0);
        $adminRepo = $this->repository('AdminAtleticaRepository');
        $adminRepo->removerInscricaoDeEvento($inscricaoId);
        $_SESSION['success_message'] = "Inscrição removida com sucesso!";
        redirect('/admin/atletica/eventos?open_evento=' . $eventoId);
    }

    public function gerenciarMembrosAtletica()
    {
        Auth::protectAdmin();
        $atleticaId = Auth::get('atletica_id');

        if (!$atleticaId) {
            $_SESSION['error_message'] = "Usuário não possui uma atlética associada. Entre em contato com o administrador do sistema.";
            redirect('/dashboard');
            return;
        }

        $adminRepo = $this->repository('AdminAtleticaRepository');
        $membros = $adminRepo->findMembrosAtletica($atleticaId);

        view('admin_atletica/gerenciar-membros-atletica', [
            'title' => 'Gerenciar Membros da Atlética',
            'user' => $this->getUserData(),
            'membros' => $membros
        ]);
    }

    public function handleMembroAtleticaAction()
    {
        Auth::protectAdmin();
        $atleticaId = Auth::get('atletica_id');

        if (!$atleticaId) {
            $_SESSION['error_message'] = "Usuário não possui uma atlética associada.";
            redirect('/dashboard');
            return;
        }

        $membroId = (int)($_POST['membro_id'] ?? 0);
        $action = $_POST['acao'] ?? '';
        $adminRepo = $this->repository('AdminAtleticaRepository');

        // Buscar informações da atlética para as notificações
        $atletica = $adminRepo->getAtleticaById($atleticaId);
        $nomeAtletica = $atletica ? $atletica['nome'] : 'Atlética';

        switch ($action) {
            case 'promover_admin':
                $success = $adminRepo->promoverMembroAAdmin($membroId, $atleticaId);
                if ($success) {
                    $_SESSION['success_message'] = "Membro promovido a Administrador da Atlética com sucesso!";
                    
                    // Enviar notificação de promoção
                    $this->notificationService->notifyMembroPromovido($membroId, $nomeAtletica);
                } else {
                    $_SESSION['error_message'] = "Erro ao promover membro.";
                }
                break;

            case 'rebaixar_admin':
                $success = $adminRepo->rebaixarAdmin($membroId);
                if ($success) {
                    $_SESSION['success_message'] = "Administrador rebaixado a membro comum com sucesso!";
                    
                    // Enviar notificação de rebaixamento
                    $this->notificationService->notifyAdminRebaixado($membroId, $nomeAtletica);
                } else {
                    $_SESSION['error_message'] = "Erro ao rebaixar administrador.";
                }
                break;

            case 'remover_atletica':
                $success = $adminRepo->removerMembroAtletica($membroId);
                if ($success) {
                    $_SESSION['success_message'] = "Membro removido da atlética com sucesso!";
                    
                    // Enviar notificação de remoção
                    $this->notificationService->notifyMembroRemovido($membroId, $nomeAtletica);
                } else {
                    $_SESSION['error_message'] = "Erro ao remover membro da atlética.";
                }
                break;

            default:
                $_SESSION['error_message'] = "Ação inválida.";
                break;
        }

        redirect('/admin/atletica/inscricoes');
    }
}