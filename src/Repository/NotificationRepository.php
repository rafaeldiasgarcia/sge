<?php
#
# Repositório para a tabela 'notificacoes'.
# Gerencia a busca e a atualização de notificações para os usuários,
# incluindo a contagem de não lidas e a marcação como lida.
#
namespace Application\Repository;

use Application\Core\Connection;
use PDO;

class NotificationRepository
{
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
}