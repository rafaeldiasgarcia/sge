<?php
#
# Controller para a página da Agenda.
# Responsável por buscar os eventos aprovados no banco de dados,
# separá-los por data e tipo, e exibi-los na view. Também processa
# as ações de marcar/desmarcar presença.
#
namespace Application\Controller;

use Application\Core\Auth;
use Application\Core\NotificationService;

class AgendaController extends BaseController
{
    private $notificationService;

    public function __construct()
    {
        try {
            $this->notificationService = new NotificationService();
        } catch (Exception $e) {
            // Fallback se houver problema com NotificationService
            $this->notificationService = null;
        }
    }

    public function index()
    {
        Auth::protect();

        try {
            $agendamentoRepo = $this->repository('AgendamentoRepository');

            // Atualizar eventos aprovados que já passaram para 'finalizado'
            $agendamentoRepo->updatePastEventsToFinalized();

            $eventos = $agendamentoRepo->findAgendaEvents(Auth::id());

            $data_atual = date('Y-m-d');

            $eventos_futuros = array_filter($eventos, fn($e) => $e['data_agendamento'] >= $data_atual);
            $eventos_passados = array_filter($eventos, fn($e) => $e['data_agendamento'] < $data_atual);

            $eventos_futuros_esportivos = array_filter($eventos_futuros, fn($e) => $e['tipo_agendamento'] === 'esportivo');
            $eventos_futuros_nao_esportivos = array_filter($eventos_futuros, fn($e) => $e['tipo_agendamento'] === 'nao_esportivo');

            $eventos_passados_esportivos = array_filter($eventos_passados, fn($e) => $e['tipo_agendamento'] === 'esportivo');
            $eventos_passados_nao_esportivos = array_filter($eventos_passados, fn($e) => $e['tipo_agendamento'] === 'nao_esportivo');

            // DEBUG: Ver quantos eventos passados temos
            error_log("=== DEBUG AGENDA ===");
            error_log("Data atual: " . $data_atual);
            error_log("Total de eventos: " . count($eventos));
            error_log("Eventos futuros: " . count($eventos_futuros));
            error_log("Eventos passados: " . count($eventos_passados));
            error_log("Eventos passados esportivos: " . count($eventos_passados_esportivos));
            error_log("Eventos passados não esportivos: " . count($eventos_passados_nao_esportivos));

            if (count($eventos_passados) > 0) {
                error_log("Primeiros 3 eventos passados:");
                foreach (array_slice($eventos_passados, 0, 3) as $ev) {
                    error_log("  - ID: " . $ev['id'] . " | Data: " . $ev['data_agendamento'] . " | Título: " . $ev['titulo']);
                }
            }

            view('pages/agenda', [
                'title' => 'Agenda da Quadra',
                'user' => $this->getUserData(),
                'eventos' => $eventos,
                'eventos_futuros_esportivos' => $eventos_futuros_esportivos,
                'eventos_futuros_nao_esportivos' => $eventos_futuros_nao_esportivos,
                'eventos_passados_esportivos' => $eventos_passados_esportivos,
                'eventos_passados_nao_esportivos' => $eventos_passados_nao_esportivos,
                'eventos_passados' => $eventos_passados,
                'data_atual' => $data_atual,
                'role' => Auth::role(),
                'atletica_id' => Auth::get('atletica_id'),
                'tipo_usuario_detalhado' => Auth::get('tipo_usuario_detalhado'),
                'additional_styles' => ['/css/usuario.css', '/css/agenda.css']
            ]);
        } catch (\Exception $e) {
            die("Erro ao carregar a agenda: " . $e->getMessage());
        }
    }

    public function handlePresenca()
    {
        Auth::protect();


        // Verificar se é uma requisição AJAX
        $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
                  strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';

        // Processar dados do $_POST (funciona tanto para AJAX com FormData quanto para formulários normais)
        $agendamentoId = (int)($_POST['agendamento_id'] ?? 0);
        $action = $_POST['action'] ?? '';

        if ($agendamentoId > 0 && in_array($action, ['marcar', 'desmarcar'])) {
            try {
                $agendamentoRepo = $this->repository('AgendamentoRepository');

                if ($action === 'marcar') {
                    $agendamentoRepo->marcarPresenca(Auth::id(), $agendamentoId);
                    // Enviar notificação de presença confirmada
                    $this->notificationService->notifyPresencaConfirmada(Auth::id(), $agendamentoId);
                } elseif ($action === 'desmarcar') {
                    $agendamentoRepo->desmarcarPresenca(Auth::id(), $agendamentoId);
                }

                // Resposta para AJAX
                if ($isAjax) {
                    header('Content-Type: application/json');
                    echo json_encode(['success' => true, 'action' => $action]);
                    return;
                }

                $_SESSION['success_message'] = $action === 'marcar' ?
                    'Presença marcada com sucesso!' :
                    'Presença desmarcada com sucesso!';

            } catch (\Exception $e) {
                // Resposta para AJAX em caso de erro
                if ($isAjax) {
                    header('Content-Type: application/json');
                    echo json_encode(['success' => false, 'message' => 'Erro ao processar solicitação']);
                    return;
                }

                $_SESSION['error_message'] = "Ocorreu um erro ao processar sua solicitação.";
            }
        } else {
            // Resposta para AJAX em caso de dados inválidos
            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Dados inválidos']);
                return;
            }

            $_SESSION['error_message'] = "Dados inválidos.";
        }

        // Redirect apenas para requisições não-AJAX
        if (!$isAjax) {
            // Verificar se há um redirect_to especificado, senão usar referer ou agenda
            $redirectTo = $_POST['redirect_to'] ?? null;
            if (!$redirectTo) {
                $referer = $_SERVER['HTTP_REFERER'] ?? '/agenda';
                $redirectTo = (strpos($referer, '/perfil') !== false) ? '/perfil' : '/agenda';
            }
            redirect($redirectTo);
        }
    }
}