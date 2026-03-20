<?php

declare(strict_types=1);

// Apache FallbackResource may preserve the original 404 status code from the
// rewritten URL. Reset it before handing off to the application router.
http_response_code(200);

require __DIR__ . '/router.php';
