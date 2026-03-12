<?php

declare(strict_types=1);

require __DIR__ . '/helpers.php';
require __DIR__ . '/db.php';
require __DIR__ . '/services/ProposalService.php';
require __DIR__ . '/services/ZapSignService.php';
require __DIR__ . '/controllers/AuthController.php';
require __DIR__ . '/controllers/AdminController.php';
require __DIR__ . '/controllers/PublicController.php';
require __DIR__ . '/controllers/TrackingController.php';
require __DIR__ . '/controllers/WebhookController.php';

date_default_timezone_set(app_config('timezone', 'America/Sao_Paulo'));

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

init_storage();
