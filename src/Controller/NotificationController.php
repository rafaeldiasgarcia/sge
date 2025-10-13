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
    private function _jsonResponse($data)
    {
        header('Content-Type: application/json');
        echo json_encode($data);
        exit();
    }

    public function getNotifications()
    {
        if (!Auth::check()) {
            $this->_jsonResponse(['error' => 'Não autenticado']);
        }

        $repo = $this->repository('NotificationRepository');
        $notifications = $repo->findByUserId(Auth::id());
        $unreadCount = $repo->countUnread(Auth::id());

        $this->_jsonResponse([
            'success' => true,
            'notifications' => $notifications,
            'unreadCount' => $unreadCount
        ]);
    }

    public function markAsRead()
    {
        if (!Auth::check()) {
            $this->_jsonResponse(['error' => 'Não autenticado']);
        }

        $input = json_decode(file_get_contents('php://input'), true);
        $repo = $this->repository('NotificationRepository');

        if (isset($input['notification_id'])) {
            $repo->markAsRead((int)$input['notification_id'], Auth::id());
        } else {
            $repo->markAllAsRead(Auth::id());
        }

        $this->_jsonResponse(['success' => true]);
    }
}