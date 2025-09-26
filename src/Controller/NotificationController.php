<?php
#
# Controller para as Notificações (API).
# Fornece endpoints JSON para o frontend buscar notificações em tempo real
# e marcá-las como lidas, sem a necessidade de recarregar a página.
#
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