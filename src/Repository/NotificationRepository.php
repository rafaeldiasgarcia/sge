<?php
/**
 * Repositório de Notificações (NotificationRepository)
 * 
 * Camada de acesso a dados para a tabela 'notificacoes'.
 * Gerencia o sistema de notificações do aplicativo, permitindo que
 * usuários recebam alertas sobre eventos importantes.
 * 
 * Responsabilidades:
 * - Criar notificações individuais ou em massa
 * - Buscar notificações de um usuário
 * - Marcar notificações como lidas
 * - Contar notificações não lidas
 * - Criar notificações globais (para todos os usuários)
 * - Limpar notificações antigas
 * 
 * Tipos de notificações:
 * - 'agendamento_aprovado': Agendamento foi aprovado
 * - 'agendamento_rejeitado': Agendamento foi rejeitado
 * - 'agendamento_cancelado': Evento foi cancelado
 * - 'presenca_confirmada': Presença foi marcada
 * - 'lembrete_evento': Lembrete de evento próximo
 * - 'info': Informação geral
 * - 'aviso': Aviso importante
 * - 'sistema': Notificação do sistema
 * 
 * @package Application\Repository
 */
namespace Application\Repository;

use Application\Core\Connection;
use PDO;

class NotificationRepository
{
    /** @var PDO Instância da conexão PDO */
    private $pdo;

    public function __construct()
    {
        $this->pdo = Connection::getInstance();
    }

    public function findByUserId(int $userId, int $limit = 10): array
    {
        $sql = "SELECT id, titulo, mensagem, tipo, data_criacao, lida
                FROM notificacoes
                WHERE usuario_id = :user_id 
                ORDER BY data_criacao DESC
                LIMIT :limit_val";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':limit_val', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function countUnread(int $userId): int
    {
        $sql = "SELECT COUNT(id) FROM notificacoes WHERE usuario_id = :user_id AND lida = 0";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();
        return (int)$stmt->fetchColumn();
    }

    public function markAsRead(int $notificationId, int $userId): bool
    {
        $sql = "UPDATE notificacoes SET lida = 1 WHERE id = :id AND usuario_id = :user_id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id', $notificationId, PDO::PARAM_INT);
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function markAllAsRead(int $userId): bool
    {
        $sql = "UPDATE notificacoes SET lida = 1 WHERE usuario_id = :user_id AND lida = 0";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function create(int $userId, string $titulo, string $mensagem, string $tipo, int $agendamentoId = null): bool
    {
        $sql = "INSERT INTO notificacoes (usuario_id, titulo, mensagem, tipo, agendamento_id) 
                VALUES (:user_id, :titulo, :mensagem, :tipo, :agendamento_id)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':titulo', $titulo, PDO::PARAM_STR);
        $stmt->bindValue(':mensagem', $mensagem, PDO::PARAM_STR);
        $stmt->bindValue(':tipo', $tipo, PDO::PARAM_STR);
        $stmt->bindValue(':agendamento_id', $agendamentoId, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function createForMultipleUsers(array $userIds, string $titulo, string $mensagem, string $tipo, int $agendamentoId = null): bool
    {
        $placeholders = str_repeat('(?, ?, ?, ?, ?),', count($userIds));
        $placeholders = rtrim($placeholders, ',');

        $sql = "INSERT INTO notificacoes (usuario_id, titulo, mensagem, tipo, agendamento_id) VALUES $placeholders";
        $stmt = $this->pdo->prepare($sql);

        $values = [];
        foreach ($userIds as $userId) {
            $values[] = $userId;
            $values[] = $titulo;
            $values[] = $mensagem;
            $values[] = $tipo;
            $values[] = $agendamentoId;
        }

        return $stmt->execute($values);
    }

    public function createGlobalNotification(string $titulo, string $mensagem, string $tipo = 'sistema'): bool
    {
        // Buscar todos os usuários (removida condição WHERE ativo = 1 pois a coluna não existe)
        $sql = "SELECT id FROM usuarios";
        $stmt = $this->pdo->query($sql);
        $userIds = $stmt->fetchAll(PDO::FETCH_COLUMN);

        if (empty($userIds)) {
            return false;
        }

        // Usar o método existente para criar notificações para múltiplos usuários
        return $this->createForMultipleUsers($userIds, $titulo, $mensagem, $tipo);
    }

    public function deleteOldNotifications(int $days = 30): bool
    {
        $sql = "DELETE FROM notificacoes WHERE data_criacao < DATE_SUB(NOW(), INTERVAL :days DAY)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':days', $days, PDO::PARAM_INT);
        return $stmt->execute();
    }
}