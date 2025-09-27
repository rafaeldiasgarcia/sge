<?php
#
# Controller para o Painel do Administrador da Atlética.
# Gerencia todas as ações relacionadas à administração de uma atlética específica,
# como aprovar membros, gerenciar inscrições e eventos.
#
namespace Application\Controller;

use Application\Core\Auth;

class AdminAtleticaController extends BaseController
{
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
            'eventos_confirmados' => 0, // Esta contagem é mais complexa, deixaremos para depois
            'atletas_aprovados' => $adminRepo->countAtletasAprovados($atleticaId),
            'membros_pendentes' => $adminRepo->countMembrosPendentes($atleticaId)
        ];

        view('admin_atletica/dashboard', [
            'title' => 'Painel da Atlética',
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

        if ($action === 'aprovar') {
            $adminRepo->aprovarMembro($alunoId, $atleticaId);
            $_SESSION['success_message'] = "Aluno aprovado e adicionado à atlética!";
        } elseif ($action === 'recusar') {
            $adminRepo->recusarMembro($alunoId);
            $_SESSION['success_message'] = "Solicitação recusada.";
        }
        redirect('/admin/atletica/membros');
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
            'title' => 'Gerenciar Inscrições',
            'pendentes' => $adminRepo->findInscricoesPendentes($atleticaId),
            'aprovados' => $adminRepo->findInscricoesAprovadas($atleticaId)
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

        switch ($action) {
            case 'promover_admin':
                $success = $adminRepo->promoverMembroAAdmin($membroId, $atleticaId);
                if ($success) {
                    $_SESSION['success_message'] = "Membro promovido a Administrador da Atlética com sucesso!";
                } else {
                    $_SESSION['error_message'] = "Erro ao promover membro.";
                }
                break;

            case 'rebaixar_admin':
                $success = $adminRepo->rebaixarAdmin($membroId);
                if ($success) {
                    $_SESSION['success_message'] = "Administrador rebaixado a membro comum com sucesso!";
                } else {
                    $_SESSION['error_message'] = "Erro ao rebaixar administrador.";
                }
                break;

            case 'remover_atletica':
                $success = $adminRepo->removerMembroAtletica($membroId);
                if ($success) {
                    $_SESSION['success_message'] = "Membro removido da atlética com sucesso!";
                } else {
                    $_SESSION['error_message'] = "Erro ao remover membro da atlética.";
                }
                break;

            default:
                $_SESSION['error_message'] = "Ação inválida.";
                break;
        }

        redirect('/admin/atletica/gerenciar-membros');
    }
}