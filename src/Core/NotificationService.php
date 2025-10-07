<?php
#
# Serviço de Notificações.
# Centraliza a lógica de criação e envio de notificações para os usuários.
# Utilizado pelos Controllers para notificar sobre eventos importantes.
#
namespace Application\Core;

use Application\Repository\NotificationRepository;
use Application\Repository\AgendamentoRepository;
use Application\Repository\UsuarioRepository;

class NotificationService
{
    private $notificationRepo;
    private $agendamentoRepo;
    private $usuarioRepo;

    public function __construct()
    {
        $this->notificationRepo = new NotificationRepository();
        $this->agendamentoRepo = new AgendamentoRepository();
        $this->usuarioRepo = new UsuarioRepository();
    }

    /**
     * Notifica quando um agendamento é aprovado
     */
    public function notifyAgendamentoAprovado(int $agendamentoId): bool
    {
        $agendamento = $this->agendamentoRepo->findById($agendamentoId);
        if (!$agendamento) return false;

        $titulo = "Agendamento Aprovado! ✅";
        $mensagem = "Seu agendamento '{$agendamento['titulo']}' para o dia " .
                   date('d/m/Y', strtotime($agendamento['data_agendamento'])) .
                   " foi aprovado pelo administrador.";

        return $this->notificationRepo->create(
            $agendamento['usuario_id'],
            $titulo,
            $mensagem,
            'agendamento_aprovado',
            $agendamentoId
        );
    }

    /**
     * Notifica quando um agendamento é rejeitado
     */
    public function notifyAgendamentoRejeitado(int $agendamentoId, string $motivo = null): bool
    {
        $agendamento = $this->agendamentoRepo->findById($agendamentoId);
        if (!$agendamento) return false;

        $titulo = "Agendamento Rejeitado ❌";
        $mensagem = "Seu agendamento '{$agendamento['titulo']}' para o dia " .
                   date('d/m/Y', strtotime($agendamento['data_agendamento'])) .
                   " foi rejeitado.";

        if ($motivo) {
            $mensagem .= "\n\nMotivo: " . $motivo;
        }

        return $this->notificationRepo->create(
            $agendamento['usuario_id'],
            $titulo,
            $mensagem,
            'agendamento_rejeitado',
            $agendamentoId
        );
    }

    /**
     * Notifica quando um usuário marca presença em um evento
     */
    public function notifyPresencaConfirmada(int $userId, int $agendamentoId): bool
    {
        $agendamento = $this->agendamentoRepo->findById($agendamentoId);
        if (!$agendamento) return false;

        $titulo = "Presença Confirmada! ✅";
        $mensagem = "Você marcou presença no evento '{$agendamento['titulo']}' " .
                   "agendado para " . date('d/m/Y', strtotime($agendamento['data_agendamento'])) .
                   " às " . $this->formatPeriodo($agendamento['periodo']) . ".";

        return $this->notificationRepo->create(
            $userId,
            $titulo,
            $mensagem,
            'presenca_confirmada',
            $agendamentoId
        );
    }

    /**
     * Notifica lembretes de eventos (evento acontece hoje)
     */
    public function notifyLembreteEvento(int $agendamentoId): bool
    {
        $agendamento = $this->agendamentoRepo->findById($agendamentoId);
        if (!$agendamento) return false;

        // Buscar todos os usuários que marcaram presença no evento
        $presencas = $this->agendamentoRepo->getPresencasByAgendamento($agendamentoId);

        if (empty($presencas)) return true; // Não há presenças para notificar

        $titulo = "Evento Hoje! 📅";
        $mensagem = "Lembrete: O evento '{$agendamento['titulo']}' acontece hoje " .
                   "às " . $this->formatPeriodo($agendamento['periodo']) . ". " .
                   "Você confirmou presença!";

        $userIds = array_column($presencas, 'usuario_id');

        return $this->notificationRepo->createForMultipleUsers(
            $userIds,
            $titulo,
            $mensagem,
            'lembrete_evento',
            $agendamentoId
        );
    }

    /**
     * Envia notificações de lembrete para eventos do dia atual
     */
    public function sendDailyEventReminders(): int
    {
        $today = date('Y-m-d');
        $agendamentos = $this->agendamentoRepo->findByDate($today);

        $count = 0;
        foreach ($agendamentos as $agendamento) {
            if ($agendamento['status'] === 'aprovado') {
                if ($this->notifyLembreteEvento($agendamento['id'])) {
                    $count++;
                }
            }
        }

        return $count;
    }

    /**
     * Notifica agendamento cancelado
     */
    public function notifyAgendamentoCancelado(int $agendamentoId): bool
    {
        $agendamento = $this->agendamentoRepo->findById($agendamentoId);
        if (!$agendamento) return false;

        $titulo = "Evento Cancelado ⚠️";
        $mensagem = "O evento '{$agendamento['titulo']}' agendado para " .
                   date('d/m/Y', strtotime($agendamento['data_agendamento'])) .
                   " foi cancelado.";

        // Notificar o criador do evento
        $this->notificationRepo->create(
            $agendamento['usuario_id'],
            $titulo,
            $mensagem,
            'agendamento_cancelado',
            $agendamentoId
        );

        // Notificar todos que marcaram presença
        $presencas = $this->agendamentoRepo->getPresencasByAgendamento($agendamentoId);
        if (!empty($presencas)) {
            $userIds = array_column($presencas, 'usuario_id');
            $userIds = array_filter($userIds, function($id) use ($agendamento) {
                return $id != $agendamento['usuario_id']; // Não duplicar para o criador
            });

            if (!empty($userIds)) {
                $this->notificationRepo->createForMultipleUsers(
                    $userIds,
                    $titulo,
                    $mensagem,
                    'agendamento_cancelado',
                    $agendamentoId
                );
            }
        }

        return true;
    }

    /**
     * Notifica quando um agendamento é editado
     */
    public function notifyAgendamentoEditado(int $agendamentoId, string $statusAnterior): bool
    {
        $agendamento = $this->agendamentoRepo->findById($agendamentoId);
        if (!$agendamento) return false;

        // Notificar o Super Admin
        $superAdmins = $this->usuarioRepo->findSuperAdmins();

        $statusTexto = $statusAnterior === 'aprovado' ? 'aprovado' : 'pendente';
        $titulo = "⚠️ Agendamento Editado";
        $mensagem = "O agendamento '{$agendamento['titulo']}' (anteriormente {$statusTexto}) foi editado por {$agendamento['responsavel']} e retornou para análise.";

        $success = true;
        foreach ($superAdmins as $admin) {
            $result = $this->notificationRepo->create(
                $admin['id'],
                $titulo,
                $mensagem,
                'agendamento_editado',
                $agendamentoId
            );
            $success = $success && $result;
        }

        return $success;
    }

    /**
     * Notifica quando um agendamento aprovado é cancelado pelo admin
     */
    public function notifyAgendamentoCanceladoAdmin(int $agendamentoId, string $motivo): bool
    {
        $agendamento = $this->agendamentoRepo->findById($agendamentoId);
        if (!$agendamento) return false;

        $titulo = "Evento Cancelado pela Administração ⚠️";
        $mensagem = "O evento '{$agendamento['titulo']}' agendado para " .
                   date('d/m/Y', strtotime($agendamento['data_agendamento'])) .
                   " foi cancelado pela administração.\n\nMotivo: " . $motivo;

        // Notificar o criador do evento
        $this->notificationRepo->create(
            $agendamento['usuario_id'],
            $titulo,
            $mensagem,
            'agendamento_cancelado_admin',
            $agendamentoId
        );

        // Notificar todos que marcaram presença
        $presencas = $this->agendamentoRepo->getPresencasByAgendamento($agendamentoId);
        if (!empty($presencas)) {
            $userIds = array_column($presencas, 'usuario_id');
            $userIds = array_filter($userIds, function($id) use ($agendamento) {
                return $id != $agendamento['usuario_id']; // Não duplicar para o criador
            });

            if (!empty($userIds)) {
                $this->notificationRepo->createForMultipleUsers(
                    $userIds,
                    $titulo,
                    $mensagem,
                    'agendamento_cancelado_admin',
                    $agendamentoId
                );
            }
        }

        return true;
    }

    /**
     * Notifica quando um agendamento aprovado é alterado pelo admin
     */
    public function notifyAgendamentoAlterado(int $agendamentoId): bool
    {
        $agendamento = $this->agendamentoRepo->findById($agendamentoId);
        if (!$agendamento) return false;

        $titulo = "Evento Alterado pela Administração 📝";
        $mensagem = "O evento '{$agendamento['titulo']}' foi alterado pela administração. " .
                   "Nova data: " . date('d/m/Y', strtotime($agendamento['data_agendamento'])) .
                   " às " . $this->formatPeriodo($agendamento['periodo']) . ".";

        if (!empty($agendamento['observacoes_admin'])) {
            $mensagem .= "\n\nObservações: " . $agendamento['observacoes_admin'];
        }

        // Notificar o criador do evento
        $this->notificationRepo->create(
            $agendamento['usuario_id'],
            $titulo,
            $mensagem,
            'agendamento_alterado',
            $agendamentoId
        );

        // Notificar todos que marcaram presença
        $presencas = $this->agendamentoRepo->getPresencasByAgendamento($agendamentoId);
        if (!empty($presencas)) {
            $userIds = array_column($presencas, 'usuario_id');
            $userIds = array_filter($userIds, function($id) use ($agendamento) {
                return $id != $agendamento['usuario_id']; // Não duplicar para o criador
            });

            if (!empty($userIds)) {
                $this->notificationRepo->createForMultipleUsers(
                    $userIds,
                    $titulo,
                    $mensagem,
                    'agendamento_alterado',
                    $agendamentoId
                );
            }
        }

        return true;
    }

    /**
     * Formata o período para exibição
     */
    private function formatPeriodo(string $periodo): string
    {
        return $periodo === 'primeiro' ? '19:15-20:55' : '21:10-22:50';
    }

    /**
     * Notifica quando um aluno é aceito na atlética
     */
    public function notifyMembroAceito(int $usuarioId, string $nomeAtletica): bool
    {
        $titulo = "Bem-vindo à Atlética! 🎉";
        $mensagem = "Parabéns! Sua solicitação para se juntar à {$nomeAtletica} foi aprovada. " .
                   "Agora você é um membro oficial e pode participar das atividades e eventos!";

        return $this->notificationRepo->create(
            $usuarioId,
            $titulo,
            $mensagem,
            'info',
            null
        );
    }

    /**
     * Notifica quando um aluno é recusado na atlética
     */
    public function notifyMembroRecusado(int $usuarioId, string $nomeAtletica): bool
    {
        $titulo = "Solicitação Não Aprovada 😔";
        $mensagem = "Sua solicitação para se juntar à {$nomeAtletica} não foi aprovada desta vez. " .
                   "Você pode fazer uma nova solicitação no futuro.";

        return $this->notificationRepo->create(
            $usuarioId,
            $titulo,
            $mensagem,
            'aviso',
            null
        );
    }

    /**
     * Notifica quando um membro é removido da atlética
     */
    public function notifyMembroRemovido(int $usuarioId, string $nomeAtletica): bool
    {
        $titulo = "Remoção da Atlética ⚠️";
        $mensagem = "Você foi removido da {$nomeAtletica}. " .
                   "Se você tiver dúvidas, entre em contato com a administração da atlética.";

        return $this->notificationRepo->create(
            $usuarioId,
            $titulo,
            $mensagem,
            'aviso',
            null
        );
    }

    /**
     * Notifica quando um membro é promovido a administrador
     */
    public function notifyMembroPromovido(int $usuarioId, string $nomeAtletica): bool
    {
        $titulo = "Promoção a Administrador! 🚀";
        $mensagem = "Parabéns! Você foi promovido a Administrador da {$nomeAtletica}. " .
                   "Agora você tem permissões especiais para gerenciar membros, eventos e inscrições.";

        return $this->notificationRepo->create(
            $usuarioId,
            $titulo,
            $mensagem,
            'info',
            null
        );
    }

    /**
     * Notifica quando um administrador é rebaixado a membro comum
     */
    public function notifyAdminRebaixado(int $usuarioId, string $nomeAtletica): bool
    {
        $titulo = "Alteração de Permissões 📋";
        $mensagem = "Suas permissões de administrador da {$nomeAtletica} foram removidas. " .
                   "Você continua sendo um membro da atlética.";

        return $this->notificationRepo->create(
            $usuarioId,
            $titulo,
            $mensagem,
            'aviso',
            null
        );
    }

    /**
     * Limpa notificações antigas
     */
    public function cleanOldNotifications(int $days = 30): bool
    {
        return $this->notificationRepo->deleteOldNotifications($days);
    }
}
