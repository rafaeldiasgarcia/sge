<?php
#
# Script para envio de lembretes diários de eventos.
# Este script deve ser executado uma vez por dia (via cron job ou task scheduler)
# para enviar notificações de lembrete para usuários que marcaram presença em eventos do dia.
#
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../src/Core/helpers.php';

use Application\Core\NotificationService;

// Configurar timezone
date_default_timezone_set('America/Sao_Paulo');

try {
    $notificationService = new NotificationService();

    // Enviar lembretes para eventos do dia
    $count = $notificationService->sendDailyEventReminders();

    echo "[" . date('Y-m-d H:i:s') . "] Lembretes enviados para $count eventos.\n";

    // Limpar notificações antigas (mais de 30 dias)
    $cleaned = $notificationService->cleanOldNotifications(30);

    if ($cleaned) {
        echo "[" . date('Y-m-d H:i:s') . "] Notificações antigas foram limpas.\n";
    }

} catch (Exception $e) {
    echo "[" . date('Y-m-d H:i:s') . "] ERRO: " . $e->getMessage() . "\n";
    exit(1);
}
