<?php

declare(strict_types=1);

require dirname(__DIR__, 2) . '/app/bootstrap.php';

if (request_method() !== 'POST') {
    json_response([
        'ok' => false,
        'message' => 'Metodo nao permitido. Use POST.',
    ], 405);
}

webhook_zapsign();
