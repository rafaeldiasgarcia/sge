<?php
#
# Controller para o gerenciamento de Agendamentos.
# Lida com a exibição do formulário, criação, edição, listagem e
# cancelamento de solicitações de agendamento por parte dos usuários.
# Também fornece o HTML parcial do calendário para requisições AJAX.
#
namespace Application\Controller;

use Application\Core\Auth;
use Application\Core\NotificationService;
use Application\Repository\AgendamentoRepository;

class AgendamentoController extends BaseController
{
    private $notificationService;

    public function __construct()
    {
        $this->notificationService = new NotificationService();
    }

    private function _checkSchedulingPermission()
    {
        $tipo_usuario = Auth::get('tipo_usuario_detalhado');
        $role = Auth::role();
        $can_schedule = ($tipo_usuario === 'Professor') || ($role === 'superadmin') || ($role === 'admin');
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
            'user' => $this->getUserData(),
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
        
        // Verifica se a data já passou
        if ($dataEventoObj < $hoje) {
            $_SESSION['error_message'] = "Não é possível agendar eventos em datas que já passaram.";
            redirect('/agendar-evento');
        }
        
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

        // Verifica limite semanal para eventos esportivos
        if ($_POST['tipo_agendamento'] === 'esportivo') {
            $userId = Auth::id();
            $esporteTipo = $_POST['esporte_tipo'] ?? '';
            if (empty($esporteTipo)) {
                $_SESSION['error_message'] = "O tipo de esporte é obrigatório para eventos esportivos.";
                redirect('/agendar-evento');
            }
            if ($agendamentoRepo->hasUserSportEventInWeek($userId, $dataEvento, $esporteTipo)) {
                $_SESSION['error_message'] = "Você já possui um evento de {$esporteTipo} agendado nesta semana. Limite de 1 evento por semana para cada tipo de esporte.";
                redirect('/agendar-evento');
            }
        }

        $dados = [
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
            $data = array_merge($dados, [
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
            $data = array_merge($dados, [
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

        // Atualizar eventos aprovados que já passaram para 'finalizado'
        $agendamentoRepo->updatePastEventsToFinalized();

        $agendamentos = $agendamentoRepo->findByUserId(Auth::id());
        view('pages/meus-agendamentos', [
            'title' => 'Meus Agendamentos',
            'user' => $this->getUserData(),
            'agendamentos' => $agendamentos
        ]);
    }

    public function showEditForm($id)
    {
        Auth::protect();
        $this->_checkSchedulingPermission();

        $agendamentoRepo = $this->repository('AgendamentoRepository');
        $modalidadeRepo = $this->repository('ModalidadeRepository');

        // Buscar o agendamento
        $evento = $agendamentoRepo->findById($id);

        // Verificar se o evento existe e pertence ao usuário
        if (!$evento || $evento['usuario_id'] !== Auth::id()) {
            $_SESSION['error_message'] = "Agendamento não encontrado ou sem permissão para editar.";
            redirect('/meus-agendamentos');
        }

        // Verificar se o evento já passou ou foi finalizado
        $dataEvento = new \DateTime($evento['data_agendamento']);
        $hoje = new \DateTime();
        if ($dataEvento < $hoje || $evento['status'] === 'finalizado') {
            $_SESSION['error_message'] = "Não é possível editar eventos que já passaram ou foram finalizados.";
            redirect('/meus-agendamentos');
        }

        // Lógica para carregar os dados do calendário
        $mesParam = $dataEvento->format('Y-m');
        $inicio = new \DateTime($mesParam . '-01');
        $fim = (clone $inicio)->modify('last day of this month');
        $rows = $agendamentoRepo->findOcupacaoPorMes($inicio->format('Y-m-d'), $fim->format('Y-m-d'));
        $ocupado = [];
        foreach ($rows as $r) {
            if (isset($r['id']) && $r['id'] != $id) {
                $periodoCalendario = ($r['periodo'] === 'primeiro') ? 'P1' : 'P2';
                $ocupado[$r['data_agendamento']][$periodoCalendario] = true;
            }
        }

        // Mensagem diferente dependendo se é aprovado ou pendente
        if ($evento['status'] === 'aprovado') {
            $_SESSION['warning_message'] = "Atenção: Este evento já foi aprovado. Ao editá-lo, ele voltará para análise e precisará ser aprovado novamente pelo Coordenador.";
        } else {
            $_SESSION['warning_message'] = "Atenção: Ao editar o evento, ele voltará para análise e precisará ser aprovado novamente pelo Coordenador.";
        }

        view('pages/editar-evento', [
            'title' => 'Editar Evento',
            'user' => $this->getUserData(),
            'evento' => $evento,
            'modalidades' => $modalidadeRepo->findAll(),
            'inicio' => $inicio,
            'diasNoMes' => (int)$inicio->format('t'),
            'primeiroW' => (int)(clone $inicio)->modify('first day of this month')->format('w'),
            'ocupado' => $ocupado,
            'prevMes' => (clone $inicio)->modify('-1 month')->format('Y-m'),
            'nextMes' => (clone $inicio)->modify('+1 month')->format('Y-m')
        ]);
    }

    public function update($id)
    {
        Auth::protect();
        $this->_checkSchedulingPermission();

        $agendamentoRepo = $this->repository('AgendamentoRepository');
        $agendamentoAtual = $agendamentoRepo->findByIdAndUserId($id, Auth::id());

        if (!$agendamentoAtual) {
            $_SESSION['error_message'] = "Agendamento não encontrado.";
            redirect('/meus-agendamentos');
        }

        // Dados básicos
        $data = [
            'titulo' => trim($_POST['titulo'] ?? ''),
            'tipo_agendamento' => $_POST['tipo_agendamento'] ?? '',
            'data_agendamento' => $_POST['data_agendamento'] ?? '',
            'periodo' => $_POST['periodo'] ?? '',
            'observacoes' => trim($_POST['observacoes'] ?? '')
        ];

        if (empty($data['titulo']) || empty($data['tipo_agendamento']) ||
            empty($data['data_agendamento']) || empty($data['periodo'])) {
            $_SESSION['error_message'] = "Todos os campos obrigatórios precisam ser preenchidos.";
            redirect("/agendamento/editar/$id");
        }

        // Dados específicos por tipo de evento
        if ($data['tipo_agendamento'] === 'esportivo') {
            $data = array_merge($data, [
                'subtipo_evento' => $_POST['subtipo_evento'] ?? null,
                'esporte_tipo' => $_POST['esporte_tipo'] ?? null,
                'possui_materiais' => isset($_POST['possui_materiais']) ? (int)$_POST['possui_materiais'] : null,
                'materiais_necessarios' => trim($_POST['materiais_necessarios'] ?? ''),
                'responsabiliza_devolucao' => isset($_POST['responsabiliza_devolucao']) ? 1 : 0,
                'lista_participantes' => trim($_POST['lista_participantes'] ?? ''),
                'arbitro_partida' => trim($_POST['arbitro_partida'] ?? '')
            ]);
        } else {
            $data = array_merge($data, [
                'subtipo_evento_nao_esp' => $_POST['subtipo_evento_nao_esp'] ?? null,
                'outro_tipo_evento' => $_POST['subtipo_evento_nao_esp'] === 'outro' ? trim($_POST['outro_tipo_evento'] ?? '') : null,
                'estimativa_participantes' => (int)($_POST['estimativa_participantes'] ?? 0),
                'evento_aberto_publico' => isset($_POST['evento_aberto_publico']) ? (int)$_POST['evento_aberto_publico'] : null,
                'descricao_publico_alvo' => trim($_POST['descricao_publico_alvo'] ?? ''),
                'infraestrutura_adicional' => trim($_POST['infraestrutura_adicional'] ?? '')
            ]);
        }

        // Validação da data (mínimo 4 dias de antecedência) - MAS permite se for a mesma data
        $hoje = new \DateTime();
        $dataEvento = new \DateTime($data['data_agendamento']);
        $dataOriginal = new \DateTime($agendamentoAtual['data_agendamento']);
        $diferencaDias = $hoje->diff($dataEvento)->days;

        $subtipo = $data['tipo_agendamento'] === 'esportivo' ?
                  ($data['subtipo_evento'] ?? '') :
                  ($data['subtipo_evento_nao_esp'] ?? '');

        // Só valida os 4 dias se a data foi alterada
        if ($data['data_agendamento'] !== $agendamentoAtual['data_agendamento']) {
            if ($subtipo !== 'campeonato' && $diferencaDias < 4) {
                $_SESSION['error_message'] = "A nova data deve ser com pelo menos 4 dias de antecedência (exceto campeonatos).";
                redirect("/agendamento/editar/$id");
            }
        }

        // Se for evento esportivo, verifica a limitação semanal
        if ($data['tipo_agendamento'] === 'esportivo') {
            $userId = Auth::id();
            $novaData = $data['data_agendamento'];

            // Só verifica se a data está sendo alterada
            if ($novaData !== $agendamentoAtual['data_agendamento']) {
                if ($agendamentoRepo->hasUserSportEventInWeek($userId, $novaData, $data['esporte_tipo'])) {
                    $_SESSION['error_message'] = "Você já possui um evento de {$data['esporte_tipo']} agendado nesta semana. Escolha outra data.";
                    redirect("/agendamento/editar/$id");
                }
            }
        }

        // Marcar que foi editado
        $data['foi_editado'] = true;
        $data['data_edicao'] = date('Y-m-d H:i:s');

        if ($agendamentoRepo->updateAgendamento($id, Auth::id(), $data)) {
            // Enviar notificação ao super admin sobre a edição
            $this->notificationService->notifyAgendamentoEditado($id, $agendamentoAtual['status']);

            $_SESSION['success_message'] = "Agendamento atualizado com sucesso! Aguarde a nova aprovação do Coordenador de Educação Física.";
            redirect('/meus-agendamentos');
        } else {
            $_SESSION['error_message'] = "Erro ao atualizar o agendamento.";
            redirect("/agendamento/editar/$id");
        }
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
        $id = (int)($_POST['agendamento_id'] ?? 0);
        if ($id <= 0) {
            $_SESSION['error_message'] = "Agendamento inválido.";
            redirect('/meus-agendamentos');
        }

        $agendamentoRepo = $this->repository('AgendamentoRepository');
        $agendamento = $agendamentoRepo->findByIdAndUserId($id, Auth::id());

        if (!$agendamento || !in_array($agendamento['status'], ['aprovado', 'pendente'])) {
            $_SESSION['error_message'] = "Agendamento não pode ser cancelado.";
            redirect('/meus-agendamentos');
        }

        if ($agendamentoRepo->cancelAgendamento($id, Auth::id())) {
            // Enviar notificações de cancelamento
            $this->notificationService->notifyAgendamentoCancelado($id);
            $_SESSION['success_message'] = "Agendamento cancelado com sucesso!";
        } else {
            $_SESSION['error_message'] = "Erro ao cancelar o agendamento.";
        }

        redirect('/meus-agendamentos');
    }

    public function getEventDetails()
    {
        // Desabilitar qualquer buffer de saída anterior
        if (ob_get_level()) {
            ob_end_clean();
        }

        // Iniciar novo buffer
        ob_start();

        // Verificar autenticação sem redirecionamento
        if (!Auth::check()) {
            header('Content-Type: application/json; charset=utf-8');
            http_response_code(401);
            echo json_encode(['error' => 'Não autenticado']);
            ob_end_flush();
            exit;
        }

        header('Content-Type: application/json; charset=utf-8');

        try {
            $id = (int)($_GET['id'] ?? 0);

            if ($id <= 0) {
                http_response_code(400);
                echo json_encode(['error' => 'ID inválido', 'id_recebido' => $id]);
                ob_end_flush();
                exit;
            }

            $agendamentoRepo = $this->repository('AgendamentoRepository');
            $evento = $agendamentoRepo->findByIdWithDetails($id);

            if (!$evento) {
                http_response_code(404);
                echo json_encode(['error' => 'Evento não encontrado', 'id' => $id]);
                ob_end_flush();
                exit;
            }

            // Buscar lista de presenças
            try {
                $presencas = $agendamentoRepo->getPresencasByAgendamento($id);
                $evento['presencas'] = is_array($presencas) ? $presencas : [];
            } catch (\Exception $e) {
                // Se houver erro ao buscar presenças, continuar sem elas
                $evento['presencas'] = [];
            }

            http_response_code(200);
            echo json_encode($evento, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            ob_end_flush();
            exit;

        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode([
                'error' => 'Erro interno do servidor',
                'message' => $e->getMessage()
            ], JSON_UNESCAPED_UNICODE);
            ob_end_flush();
            exit;
        }
    }
}
