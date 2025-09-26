<?php
#
# Controller para a página da Agenda.
# Responsável por buscar os eventos aprovados no banco de dados,
# separá-los por data e tipo, e exibi-los na view. Também processa
# as ações de marcar/desmarcar presença.
#
namespace Application\Controller;

use Application\Core\Auth;

class AgendaController extends BaseController
{
    public function index()
    {
        Auth::protect();

        try {
            $agendamentoRepo = $this->repository('AgendamentoRepository');
            $eventos = $agendamentoRepo->findAgendaEvents(Auth::id());

            $data_atual = date('Y-m-d');

            $eventos_futuros = array_filter($eventos, fn($e) => $e['data_agendamento'] >= $data_atual);
            $eventos_passados = array_filter($eventos, fn($e) => $e['data_agendamento'] < $data_atual);

            $eventos_futuros_esportivos = array_filter($eventos_futuros, fn($e) => $e['tipo_agendamento'] === 'esportivo');
            $eventos_futuros_nao_esportivos = array_filter($eventos_futuros, fn($e) => $e['tipo_agendamento'] === 'nao_esportivo');

            $eventos_passados_esportivos = array_filter($eventos_passados, fn($e) => $e['tipo_agendamento'] === 'esportivo');
            $eventos_passados_nao_esportivos = array_filter($eventos_passados, fn($e) => $e['tipo_agendamento'] === 'nao_esportivo');

            view('pages/agenda', [
                'title' => 'Agenda da Quadra',
                'eventos' => $eventos,
                'eventos_futuros_esportivos' => $eventos_futuros_esportivos,
                'eventos_futuros_nao_esportivos' => $eventos_futuros_nao_esportivos,
                'eventos_passados_esportivos' => $eventos_passados_esportivos,
                'eventos_passados_nao_esportivos' => $eventos_passados_nao_esportivos,
                'eventos_passados' => $eventos_passados,
                'data_atual' => $data_atual,
                'role' => Auth::role(),
                'atletica_id' => Auth::get('atletica_id')
            ]);
        } catch (\Exception $e) {
            die("Erro ao carregar a agenda: " . $e->getMessage());
        }
    }

    public function handlePresenca()
    {
        Auth::protect();

        $agendamentoId = (int)($_POST['agendamento_id'] ?? 0);
        $action = $_POST['action'] ?? '';

        if ($agendamentoId > 0 && in_array($action, ['marcar', 'desmarcar'])) {
            try {
                $agendamentoRepo = $this->repository('AgendamentoRepository');

                if ($action === 'marcar') {
                    $agendamentoRepo->marcarPresenca(Auth::id(), $agendamentoId);
                } elseif ($action === 'desmarcar') {
                    $agendamentoRepo->desmarcarPresenca(Auth::id(), $agendamentoId);
                }
            } catch (\Exception $e) {
                $_SESSION['error_message'] = "Ocorreu um erro ao processar sua solicitação.";
            }
        }

        redirect('/agenda');
    }
}