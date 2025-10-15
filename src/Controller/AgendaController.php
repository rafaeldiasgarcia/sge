<?php
/**
 * Controller da Agenda (AgendaController)
 * 
 * Gerencia a visualização e interação com a agenda de eventos da quadra.
 * Exibe eventos aprovados em formato de calendário e permite que usuários
 * marquem presença.
 * 
 * Funcionalidades:
 * - Exibir eventos aprovados e finalizados no calendário
 * - Separar eventos por data e período (primeiro/segundo)
 * - Marcar/desmarcar presença em eventos
 * - Enviar notificações quando presença é confirmada
 * - Filtrar eventos por tipo (esportivo/não-esportivo)
 * - Exibir detalhes de eventos com atléticas confirmadas
 * 
 * Visualizações:
 * - Lista de eventos ordenados por data
 * - Contadores de presenças
 * - Informações de atléticas participantes
 * - Indicadores visuais de eventos com presença marcada
 * 
 * @package Application\Controller
 */
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
        // Agenda é pública - não requer autenticação
        // Apenas verifica se está logado para mostrar/ocultar botões

        try {
            $agendamentoRepo = $this->repository('AgendamentoRepository');

            // Atualizar eventos aprovados que já passaram para 'finalizado'
            $agendamentoRepo->updatePastEventsToFinalized();

            // Se não estiver logado, passa null como user_id
            $userId = Auth::check() ? Auth::id() : null;
            $eventos = $agendamentoRepo->findAgendaEvents($userId);

            $data_atual = date('Y-m-d');

            $eventos_futuros = array_filter($eventos, fn($e) => $e['data_agendamento'] >= $data_atual);
            $eventos_passados = array_filter($eventos, fn($e) => $e['data_agendamento'] < $data_atual);

            $eventos_futuros_esportivos = array_filter($eventos_futuros, fn($e) => $e['tipo_agendamento'] === 'esportivo');
            $eventos_futuros_nao_esportivos = array_filter($eventos_futuros, fn($e) => $e['tipo_agendamento'] === 'nao_esportivo');

            $eventos_passados_esportivos = array_filter($eventos_passados, fn($e) => $e['tipo_agendamento'] === 'esportivo');
            $eventos_passados_nao_esportivos = array_filter($eventos_passados, fn($e) => $e['tipo_agendamento'] === 'nao_esportivo');

            view('pages/agenda', [
                'title' => 'Agenda da Quadra',
                'user' => Auth::check() ? $this->getUserData() : null,
                'eventos' => $eventos,
                'eventos_futuros_esportivos' => $eventos_futuros_esportivos,
                'eventos_futuros_nao_esportivos' => $eventos_futuros_nao_esportivos,
                'eventos_passados_esportivos' => $eventos_passados_esportivos,
                'eventos_passados_nao_esportivos' => $eventos_passados_nao_esportivos,
                'eventos_passados' => $eventos_passados,
                'data_atual' => $data_atual,
                'role' => Auth::check() ? Auth::role() : null,
                'atletica_id' => Auth::check() ? Auth::get('atletica_id') : null,
                'tipo_usuario_detalhado' => Auth::check() ? Auth::get('tipo_usuario_detalhado') : null,
                'is_logged_in' => Auth::check(),
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
                    // Enviar notificação de presença confirmada (com tratamento de erro)
                    try {
                        if ($this->notificationService) {
                            $this->notificationService->notifyPresencaConfirmada(Auth::id(), $agendamentoId);
                        }
                    } catch (\Exception $notifError) {
                        error_log("Erro ao enviar notificação: " . $notifError->getMessage());
                        // Continua mesmo se a notificação falhar
                    }
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
                error_log("Erro ao processar presença: " . $e->getMessage());
                
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