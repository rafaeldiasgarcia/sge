<?php
/**
 * Controller de Notificações - API JSON (NotificationController)
 * 
 * Fornece endpoints RESTful em formato JSON para o sistema de notificações.
 * Permite que o frontend busque e gerencie notificações sem recarregar a página.
 * 
 * Endpoints disponíveis:
 * 
 * GET /notifications/get
 * - Retorna as últimas notificações do usuário logado
 * - Inclui contagem de não lidas
 * - Formato: { notifications: [...], unread_count: N }
 * 
 * POST /notifications/mark-read
 * - Marca uma notificação específica como lida
 * - Parâmetro: notification_id
 * - Retorna: { success: true/false }
 * 
 * POST /notifications/mark-all-read
 * - Marca todas as notificações do usuário como lidas
 * - Retorna: { success: true/false }
 * 
 * Características:
 * - Respostas em JSON puro
 * - Requer autenticação (Auth::protect)
 * - Headers Content-Type: application/json
 * - Usado por AJAX no frontend (notifications.js)
 * 
 * Integração Frontend:
 * - public/js/notifications.js faz polling periódico
 * - Badge de contador atualizado em tempo real
 * - Dropdown de notificações atualizado automaticamente
 * 
 * @package Application\Controller
 */
namespace Application\Controller;

use Application\Core\Auth;

class NotificationController extends BaseController
{
    /**
     * Lê e valida o corpo JSON da requisição
     * Retorna sempre um array associativo (vazio em caso de erro de parse)
     */
    private function getJsonInput(): array
    {
        $raw = file_get_contents('php://input');
        if ($raw === false || $raw === '') {
            return [];
        }
        $decoded = json_decode($raw, true);
        return is_array($decoded) ? $decoded : [];
    }

    public function getNotifications()
    {
        // Guarda de autenticação padronizada
        $this->requireAuth();

        $repo = $this->repository('NotificationRepository');
        $userId = (int) Auth::id();
        $notifications = $repo->findByUserId($userId);
        $unreadCount = $repo->countUnread($userId);

        $this->jsonResponse([
            'success' => true,
            'notifications' => $notifications,
            'unreadCount' => $unreadCount
        ]);
    }

    public function markAsRead()
    {
        // Guarda de autenticação padronizada
        $this->requireAuth();

        $input = $this->getJsonInput();
        $repo = $this->repository('NotificationRepository');

        $userId = (int) Auth::id();
        $result = true;

        if (isset($input['notification_id'])) {
            $notificationId = (int) $input['notification_id'];
            if ($notificationId <= 0) {
                $this->jsonResponse(['success' => false, 'message' => 'notification_id inválido'], 400);
            }
            $result = $repo->markAsRead($notificationId, $userId);
        } else {
            $result = $repo->markAllAsRead($userId);
        }

        if (!$result) {
            $this->jsonResponse(['success' => false, 'message' => 'Não foi possível atualizar as notificações'], 500);
        }

        $this->jsonResponse(['success' => true]);
    }
}