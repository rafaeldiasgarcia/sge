<?php
/**
 * Controller de Agendamentos (AgendamentoController)
 * 
 * Gerencia todo o ciclo de vida dos agendamentos de eventos na quadra,
 * desde a criação até o cancelamento. Implementa regras de negócio
 * complexas de validação e disponibilidade.
 * 
 * Funcionalidades principais:
 * - Criação de agendamentos (esportivos e não-esportivos)
 * - Edição de agendamentos pendentes
 * - Cancelamento de agendamentos
 * - Listagem de agendamentos do usuário
 * - Verificação de disponibilidade de horários
 * - Validação de regras de negócio (limites semanais, conflitos)
 * - Envio de notificações sobre mudanças de status
 * - Geração de calendário AJAX para visualização
 * 
 * Regras de Negócio implementadas:
 * - Apenas 1 agendamento aprovado por horário/data
 * - Usuários podem agendar no máximo 1 evento esportivo por modalidade por semana
 * - Atléticas podem ter no máximo 1 evento por semana (esportivo ou não esportivo)
 * - Super Admin é imune a todas as restrições de agendamento
 * - Agendamentos editados retornam para status 'pendente'
 * - Super Admin deve aprovar todos os agendamentos
 * 
 * Tipos de Agendamento:
 * - Esportivo: Treinos, Jogos, Campeonatos
 * - Não Esportivo: Eventos Culturais, Reuniões, Outros
 * 
 * @package Application\Controller
 */
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
        $role = Auth::role();
        $is_coordenador = Auth::get('is_coordenador');
        $can_schedule = ($role === 'superadmin') || ($role === 'admin') || ($is_coordenador == 1);
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
            'nextMes' => (clone $inicio)->modify('+1 month')->format('Y-m'),
            'isCampeonato' => false // Será atualizado via JavaScript
        ]);
    }

    public function create()
    {
        Auth::protect();
        $this->_checkSchedulingPermission();

        // Função auxiliar para salvar dados do formulário em sessão
        $saveFormData = function($errorMessage) {
            $_SESSION['form_data'] = $_POST;
            $_SESSION['error_message'] = $errorMessage;
            redirect('/agendar-evento');
        };

        $requiredFields = ['titulo', 'tipo_agendamento', 'data_agendamento', 'periodo', 'responsavel_evento'];
        foreach ($requiredFields as $field) {
            if (empty($_POST[$field])) {
                $saveFormData("Preencha todos os campos obrigatórios.");
            }
        }

        // Coordenadores só podem criar eventos não esportivos
        $role = Auth::role();
        $is_coordenador = Auth::get('is_coordenador');
        if ($is_coordenador == 1 && $role !== 'superadmin' && $role !== 'admin') {
            if ($_POST['tipo_agendamento'] === 'esportivo') {
                $saveFormData("Coordenadores só podem criar eventos NÃO ESPORTIVOS (Palestras, Workshops, etc). Para eventos esportivos, entre em contato com a administração.");
            }
        }

        $dataEvento = $_POST['data_agendamento'];
        $hoje = new \DateTime();
        $dataEventoObj = new \DateTime($dataEvento);
        
        // Verifica se a data já passou
        if ($dataEventoObj < $hoje) {
            $saveFormData("Não é possível agendar eventos em datas que já passaram.");
        }
        
        $diferencaDias = $hoje->diff($dataEventoObj)->days;

        $subtipo = $_POST['subtipo_evento'] ?? '';
        $isCampeonato = ($_POST['tipo_agendamento'] === 'esportivo' && $subtipo === 'campeonato');
        
        // Para campeonatos: SEM NENHUMA restrição de data
        // Para outros eventos: aplicar restrições normais
        if (!$isCampeonato) {
            if ($diferencaDias < 4) {
                $saveFormData("A data deve ser com pelo menos 4 dias de antecedência (exceto campeonatos).");
            }
            if ($diferencaDias > 30) {
                $saveFormData("A data não pode ser agendada com mais de 1 mês de antecedência.");
            }
        }
        // Se for campeonato, não aplica NENHUMA restrição de data

        $agendamentoRepo = $this->repository('AgendamentoRepository');

        // Para campeonatos: pode ocupar qualquer horário (mesmo ocupado)
        // Para outros eventos: verificar se o slot está livre
        if (!$isCampeonato && $agendamentoRepo->isSlotOccupied($dataEvento, $_POST['periodo'])) {
            $saveFormData("Este horário já está reservado!");
        }
        // Se for campeonato, pode ocupar qualquer horário

        // Super Admin é imune a todas as restrições de agendamento
        $isSuperAdmin = Auth::role() === 'superadmin';
        
        if (!$isSuperAdmin) {
            // Verifica limite semanal por atlética (todos os tipos de evento)
            $atleticaId = Auth::get('atletica_id');
            if ($atleticaId) {
                if ($agendamentoRepo->hasAtleticaEventInWeek($atleticaId, $dataEvento)) {
                    $saveFormData("Sua atlética já possui um evento agendado nesta semana. Limite de 1 evento por semana por atlética.");
                }
            }

            // Verifica limite semanal para eventos esportivos (por modalidade)
            if ($_POST['tipo_agendamento'] === 'esportivo') {
                $userId = Auth::id();
                $esporteTipo = $_POST['esporte_tipo'] ?? '';
                
                if (empty($esporteTipo)) {
                    $saveFormData("O tipo de esporte é obrigatório para eventos esportivos.");
                }
                
                // Verifica se o usuário já tem um evento do mesmo tipo de esporte na semana
                if ($agendamentoRepo->hasUserSportEventInWeek($userId, $dataEvento, $esporteTipo)) {
                    $saveFormData("Você já possui um evento de {$esporteTipo} agendado nesta semana. Limite de 1 evento por semana para cada tipo de esporte.");
                }
            }
        } else {
            // Para Super Admin, apenas valida se o tipo de esporte é obrigatório
            if ($_POST['tipo_agendamento'] === 'esportivo') {
                $esporteTipo = $_POST['esporte_tipo'] ?? '';
                if (empty($esporteTipo)) {
                    $saveFormData("O tipo de esporte é obrigatório para eventos esportivos.");
                }
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
                $saveFormData("Preencha todos os campos obrigatórios para eventos esportivos.");
            }

            $listaRAs = array_filter(array_map('trim', explode("\n", $_POST['lista_participantes'])));
            if (!empty($listaRAs)) {
                $userRepo = $this->repository('UsuarioRepository');
                $rasInexistentes = $userRepo->findRAsInexistentes($listaRAs);

                if (!empty($rasInexistentes)) {
                    $saveFormData("Os seguintes RAs não foram encontrados no sistema: " . implode(', ', $rasInexistentes));
                }
            }

            if (isset($_POST['possui_materiais']) && $_POST['possui_materiais'] === '0') {
                if (empty($_POST['materiais_necessarios']) || !isset($_POST['responsabiliza_devolucao'])) {
                    $saveFormData("Quando não possui materiais, é obrigatório descrever os materiais necessários e aceitar a responsabilização.");
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
                $saveFormData("Informe uma estimativa válida de participantes.");
            }
        }

        if ($agendamentoRepo->createAgendamento($data)) {
            // Limpar dados do formulário da sessão em caso de sucesso
            unset($_SESSION['form_data']);
            $_SESSION['success_message'] = "Solicitação enviada com sucesso! Aguarde aprovação do Coordenador de Educação Física.";
            redirect('/meus-agendamentos');
        } else {
            $saveFormData("Erro ao processar a solicitação. Tente novamente.");
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

        // Coordenadores só podem criar eventos não esportivos
        $role = Auth::role();
        $is_coordenador = Auth::get('is_coordenador');
        if ($is_coordenador == 1 && $role !== 'superadmin' && $role !== 'admin') {
            if ($data['tipo_agendamento'] === 'esportivo') {
                $_SESSION['error_message'] = "Coordenadores só podem criar eventos NÃO ESPORTIVOS (Palestras, Workshops, etc). Para eventos esportivos, entre em contato com a administração.";
                redirect("/agendamento/editar/$id");
            }
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
            // Para campeonatos: SEM NENHUMA restrição de data
            // Para outros eventos: aplicar restrições normais
            if ($subtipo !== 'campeonato') {
                if ($diferencaDias < 4) {
                    $_SESSION['error_message'] = "A nova data deve ser com pelo menos 4 dias de antecedência (exceto campeonatos).";
                    redirect("/agendamento/editar/$id");
                }
                if ($diferencaDias > 30) {
                    $_SESSION['error_message'] = "A nova data não pode ser agendada com mais de 1 mês de antecedência.";
                    redirect("/agendamento/editar/$id");
                }
            }
            // Se for campeonato, não aplica NENHUMA restrição de data
        }

        // Super Admin é imune a todas as restrições de agendamento
        $isSuperAdmin = Auth::role() === 'superadmin';
        $novaData = $data['data_agendamento'];

        // Só verifica se a data está sendo alterada e se não for Super Admin
        if ($novaData !== $agendamentoAtual['data_agendamento'] && !$isSuperAdmin) {
            // Verifica limitação semanal por atlética (todos os tipos de evento)
            $atleticaId = Auth::get('atletica_id');
            
            // Verifica se a atlética já tem um evento na semana
            if ($atleticaId) {
                if ($agendamentoRepo->hasAtleticaEventInWeek($atleticaId, $novaData)) {
                    $_SESSION['error_message'] = "Sua atlética já possui um evento agendado nesta semana. Limite de 1 evento por semana por atlética.";
                    redirect("/agendamento/editar/$id");
                }
            }
            
            // Se for evento esportivo, verifica a limitação por modalidade
            if ($data['tipo_agendamento'] === 'esportivo') {
                $userId = Auth::id();
                
                // Verifica se o usuário já tem um evento do mesmo tipo de esporte na semana
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
            
            // Enviar notificação ao usuário confirmando a edição
            $notificationRepo = $this->repository('NotificationRepository');
            $notificationRepo->create(
                Auth::id(),
                'Evento Editado com Sucesso',
                "Seu evento '{$data['titulo']}' foi editado e enviado para análise do Coordenador de Educação Física. Você será notificado sobre a aprovação.",
                'info',
                $id
            );

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
        $isCampeonato = isset($_GET['is_campeonato']) && $_GET['is_campeonato'] === 'true';
        
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
            'nextMes' => (clone $inicio)->modify('+1 month')->format('Y-m'),
            'isCampeonato' => $isCampeonato
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

        // Verificar se usuário está logado para mensagem customizada
        if (!Auth::check()) {
            header('Content-Type: application/json; charset=utf-8');
            http_response_code(401);
            echo json_encode([
                'error' => 'not_authenticated',
                'message' => 'Faça login para ver mais detalhes sobre este evento',
                'login_url' => '/login'
            ]);
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

            // Determinar nível de acesso do usuário
            $role = Auth::role();
            $isCoordenador = Auth::get('is_coordenador') == 1;
            $tipoUsuario = Auth::get('tipo_usuario_detalhado');
            
            // Permissão total: Superadmin ou Professor coordenador
            $hasFullAccess = ($role === 'superadmin') || 
                           ($tipoUsuario === 'Professor' && $isCoordenador);
            
            // Permissão limitada: Admin de atlética ou usuário comum
            $hasLimitedAccess = ($role === 'admin') || ($role === 'usuario');

            // Buscar lista de presenças APENAS para quem tem acesso total
            if ($hasFullAccess) {
                try {
                    $presencas = $agendamentoRepo->getPresencasByAgendamento($id);
                    $evento['presencas'] = is_array($presencas) ? $presencas : [];
                } catch (\Exception $e) {
                    $evento['presencas'] = [];
                }
            } else {
                // Admin de atlética e usuário comum NÃO veem lista de presenças
                $evento['presencas'] = [];
            }

            // Filtrar campos sensíveis para usuários com acesso limitado
            if ($hasLimitedAccess && !$hasFullAccess) {
                // Remover dados sensíveis do solicitante
                unset($evento['criador_telefone']);
                unset($evento['criador_email']);
                unset($evento['criador_tipo']);
                
                // Remover informações administrativas
                unset($evento['lista_participantes']);
                unset($evento['estimativa_participantes']);
                unset($evento['materiais_necessarios']);
                unset($evento['possui_materiais']);
            }

            // Adicionar informação de permissão ao retorno
            $evento['user_permission_level'] = $hasFullAccess ? 'full' : 'limited';
            $evento['user_role'] = $role;

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
