<?php
#
# Controller para o gerenciamento de Agendamentos.
# Lida com a exibição do formulário, criação, edição, listagem e
# cancelamento de solicitações de agendamento por parte dos usuários.
# Também fornece o HTML parcial do calendário para requisições AJAX.
#
namespace Application\Controller;

use Application\Core\Auth;
use Application\Repository\AgendamentoRepository;

class AgendamentoController extends BaseController
{
    private function _checkSchedulingPermission()
    {
        $tipo_usuario = Auth::get('tipo_usuario_detalhado');
        $role = Auth::role();
        $can_schedule = ($tipo_usuario === 'Professor') || ($role === 'superadmin') || ($role === 'admin' && $tipo_usuario === 'Membro das Atléticas');
        if (!$can_schedule) {
            http_response_code(403);
            die('Acesso negado. Você não tem permissão para gerenciar agendamentos.');
        }
    }

    public function showForm()
    {
        Auth::protect();
        $this->_checkSchedulingPermission();
        $modalidadeRepo = $this->repository('ModalidadeRepository');

        // Lógica para carregar os dados do calendário para a view principal
        $mesParam = date('Y-m');
        $inicio = new \DateTime($mesParam . '-01');
        $fim = (clone $inicio)->modify('last day of this month');
        $agendamentoRepo = $this->repository('AgendamentoRepository');
        $rows = $agendamentoRepo->findOcupacaoPorMes($inicio->format('Y-m-d'), $fim->format('Y-m-d'));
        $ocupado = [];
        foreach ($rows as $r) {
            $periodoCalendario = ($r['periodo'] === 'primeiro') ? 'P1' : 'P2';
            $ocupado[$r['data_agendamento']][$periodoCalendario] = true;
        }

        view('pages/agendar-evento', [
            'title' => 'Agendar Evento na Quadra',
            'modalidades' => $modalidadeRepo->findAll(),
            'inicio' => $inicio,
            'diasNoMes' => (int)$inicio->format('t'),
            'primeiroW' => (int)(clone $inicio)->modify('first day of this month')->format('w'),
            'ocupado' => $ocupado,
            'prevMes' => (clone $inicio)->modify('-1 month')->format('Y-m'),
            'nextMes' => (clone $inicio)->modify('+1 month')->format('Y-m')
        ]);
    }

    public function create()
    {
        Auth::protect();
        $this->_checkSchedulingPermission();

        $requiredFields = ['titulo', 'tipo_agendamento', 'data_agendamento', 'periodo', 'responsavel_evento'];
        foreach ($requiredFields as $field) {
            if (empty($_POST[$field])) {
                $_SESSION['error_message'] = "Preencha todos os campos obrigatórios.";
                redirect('/agendar-evento');
            }
        }

        $dataEvento = $_POST['data_agendamento'];
        $hoje = new \DateTime();
        $dataEventoObj = new \DateTime($dataEvento);
        $diferencaDias = $hoje->diff($dataEventoObj)->days;

        $subtipo = $_POST['subtipo_evento'] ?? '';
        if ($subtipo !== 'campeonato' && $diferencaDias < 4) {
            $_SESSION['error_message'] = "A data deve ser com pelo menos 4 dias de antecedência (exceto campeonatos).";
            redirect('/agendar-evento');
        }

        $agendamentoRepo = $this->repository('AgendamentoRepository');

        if ($agendamentoRepo->isSlotOccupied($dataEvento, $_POST['periodo'])) {
            $_SESSION['error_message'] = "Este horário já está reservado!";
            redirect('/agendar-evento');
        }

        if ($_POST['tipo_agendamento'] === 'esportivo' && $subtipo === 'treino') {
            $atleticaId = Auth::get('atletica_id');
            $esporte = $_POST['esporte_tipo'];

            if ($atleticaId && $agendamentoRepo->verificaTreinoSemanal($atleticaId, $esporte, $dataEvento)) {
                $_SESSION['error_message'] = "Sua atlética já possui um treino de {$esporte} agendado nesta semana.";
                redirect('/agendar-evento');
            }
        }

        $data = [
            'usuario_id' => Auth::id(),
            'titulo' => trim($_POST['titulo']),
            'tipo_agendamento' => $_POST['tipo_agendamento'],
            'subtipo_evento' => $subtipo,
            'data_agendamento' => $dataEvento,
            'periodo' => $_POST['periodo'],
            'responsavel_evento' => trim($_POST['responsavel_evento']),
            'observacoes' => trim($_POST['observacoes'] ?? '')
        ];

        if ($_POST['tipo_agendamento'] === 'esportivo') {
            $data = array_merge($data, [
                'esporte_tipo' => $_POST['esporte_tipo'],
                'possui_materiais' => isset($_POST['possui_materiais']) ? (int)$_POST['possui_materiais'] : null,
                'materiais_necessarios' => trim($_POST['materiais_necessarios'] ?? ''),
                'responsabiliza_devolucao' => isset($_POST['responsabiliza_devolucao']) ? 1 : 0,
                'lista_participantes' => trim($_POST['lista_participantes'] ?? ''),
                'arbitro_partida' => trim($_POST['arbitro_partida'] ?? '')
            ]);

            if (empty($_POST['esporte_tipo']) || empty($_POST['lista_participantes'])) {
                $_SESSION['error_message'] = "Preencha todos os campos obrigatórios para eventos esportivos.";
                redirect('/agendar-evento');
            }

            $listaRAs = array_filter(array_map('trim', explode("\n", $_POST['lista_participantes'])));
            if (!empty($listaRAs)) {
                $userRepo = $this->repository('UsuarioRepository');
                $rasInexistentes = $userRepo->findRAsInexistentes($listaRAs);

                if (!empty($rasInexistentes)) {
                    $_SESSION['error_message'] = "Os seguintes RAs não foram encontrados no sistema: " . implode(', ', $rasInexistentes);
                    redirect('/agendar-evento');
                }
            }

            if (isset($_POST['possui_materiais']) && $_POST['possui_materiais'] === '0') {
                if (empty($_POST['materiais_necessarios']) || !isset($_POST['responsabiliza_devolucao'])) {
                    $_SESSION['error_message'] = "Quando não possui materiais, é obrigatório descrever os materiais necessários e aceitar a responsabilização.";
                    redirect('/agendar-evento');
                }
            }
        } else {
            $data = array_merge($data, [
                'estimativa_participantes' => (int)($_POST['estimativa_participantes'] ?? 0),
                'evento_aberto_publico' => isset($_POST['evento_aberto_publico']) ? (int)$_POST['evento_aberto_publico'] : null,
                'descricao_publico_alvo' => trim($_POST['descricao_publico_alvo'] ?? ''),
                'infraestrutura_adicional' => trim($_POST['infraestrutura_adicional'] ?? '')
            ]);

            if (empty($_POST['estimativa_participantes']) || $_POST['estimativa_participantes'] <= 0) {
                $_SESSION['error_message'] = "Informe uma estimativa válida de participantes.";
                redirect('/agendar-evento');
            }
        }

        if ($agendamentoRepo->createAgendamento($data)) {
            $_SESSION['success_message'] = "Solicitação enviada com sucesso! Aguarde aprovação do Coordenador de Educação Física.";
            redirect('/meus-agendamentos');
        } else {
            $_SESSION['error_message'] = "Erro ao processar a solicitação. Tente novamente.";
            redirect('/agendar-evento');
        }
    }

    public function showMeusAgendamentos()
    {
        Auth::protect();
        $this->_checkSchedulingPermission();
        $agendamentoRepo = $this->repository('AgendamentoRepository');
        $agendamentos = $agendamentoRepo->findByUserId(Auth::id());
        view('pages/meus-agendamentos', [
            'title' => 'Meus Agendamentos',
            'agendamentos' => $agendamentos
        ]);
    }

    public function showEditForm()
    {
        Auth::protect();
        $this->_checkSchedulingPermission();
        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) redirect('/meus-agendamentos');

        $agendamentoRepo = $this->repository('AgendamentoRepository');
        $agendamento = $agendamentoRepo->findByIdAndUserId($id, Auth::id());

        if (!$agendamento) {
            $_SESSION['error_message'] = "Agendamento não encontrado ou você não tem permissão para editá-lo.";
            redirect('/meus-agendamentos');
        }

        view('pages/editar-agendamento', [
            'title' => 'Editar Agendamento',
            'agendamento' => $agendamento
        ]);
    }

    public function update()
    {
        Auth::protect();
        $this->_checkSchedulingPermission();
        $id = (int)($_POST['id'] ?? 0);
        $data = [
            'titulo' => trim($_POST['titulo'] ?? ''),
            'data_agendamento' => $_POST['data_agendamento'] ?? '',
            'periodo' => $_POST['periodo'] ?? '',
            'descricao' => trim($_POST['descricao'] ?? '')
        ];

        if ($id <= 0 || empty($data['titulo']) || empty($data['data_agendamento']) || empty($data['periodo'])) {
            $_SESSION['error_message'] = "Dados inválidos.";
            redirect('/meus-agendamentos');
        }

        $agendamentoRepo = $this->repository('AgendamentoRepository');
        $agendamentoRepo->updateAgendamento($id, Auth::id(), $data);
        $_SESSION['success_message'] = "Agendamento atualizado e reenviado para aprovação!";
        redirect('/meus-agendamentos');
    }

    public function getCalendarPartial()
    {
        $mesParam = $_GET['mes'] ?? date('Y-m');
        try {
            $inicio = new \DateTime($mesParam . '-01');
        } catch (\Throwable $e) {
            $inicio = new \DateTime('first day of this month');
        }

        $fim = (clone $inicio)->modify('last day of this month');
        $agendamentoRepo = $this->repository('AgendamentoRepository');
        $rows = $agendamentoRepo->findOcupacaoPorMes($inicio->format('Y-m-d'), $fim->format('Y-m-d'));

        $ocupado = [];
        foreach ($rows as $r) {
            $periodoCalendario = ($r['periodo'] === 'primeiro') ? 'P1' : 'P2';
            $ocupado[$r['data_agendamento']][$periodoCalendario] = true;
        }

        $data = [
            'inicio' => $inicio,
            'diasNoMes' => (int)$inicio->format('t'),
            'primeiroW' => (int)(clone $inicio)->modify('first day of this month')->format('w'),
            'ocupado' => $ocupado,
            'prevMes' => (clone $inicio)->modify('-1 month')->format('Y-m'),
            'nextMes' => (clone $inicio)->modify('+1 month')->format('Y-m')
        ];

        extract($data);
        require ROOT_PATH . '/views/_partials/calendar.php';
    }

    public function cancel()
    {
        Auth::protect();
        $this->_checkSchedulingPermission();
        $id = (int)($_POST['id'] ?? 0);
        if ($id <= 0) redirect('/meus-agendamentos');

        $agendamentoRepo = $this->repository('AgendamentoRepository');
        $agendamentoRepo->cancelAgendamento($id, Auth::id());
        $_SESSION['success_message'] = "Agendamento cancelado com sucesso.";
        redirect('/meus-agendamentos');
    }
}