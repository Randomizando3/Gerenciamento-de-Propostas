<?php

declare(strict_types=1);

$publicDir = __DIR__ . DIRECTORY_SEPARATOR . 'public';
$requestUri = $_SERVER['REQUEST_URI'] ?? '/';
$path = parse_url($requestUri, PHP_URL_PATH);

if (is_string($path)) {
    $filePath = realpath($publicDir . DIRECTORY_SEPARATOR . ltrim($path, '/'));
    if ($filePath !== false && str_starts_with($filePath, realpath($publicDir)) && is_file($filePath)) {
        $extension = mb_strtolower((string) pathinfo($filePath, PATHINFO_EXTENSION), 'UTF-8');
        if ($extension === 'php') {
            require $filePath;
            return true;
        }
        $mimeTypes = [
            'css' => 'text/css; charset=UTF-8',
            'js' => 'application/javascript; charset=UTF-8',
            'json' => 'application/json; charset=UTF-8',
            'txt' => 'text/plain; charset=UTF-8',
            'map' => 'application/json; charset=UTF-8',
            'png' => 'image/png',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'webp' => 'image/webp',
            'svg' => 'image/svg+xml',
            'gif' => 'image/gif',
            'ico' => 'image/x-icon',
            'woff' => 'font/woff',
            'woff2' => 'font/woff2',
            'ttf' => 'font/ttf',
            'otf' => 'font/otf',
        ];
        $contentType = $mimeTypes[$extension] ?? 'application/octet-stream';
        header('Content-Type: ' . $contentType);
        header('Content-Length: ' . (string) filesize($filePath));
        header('Cache-Control: public, max-age=3600');
        readfile($filePath);
        return true;
    }
}

require $publicDir . DIRECTORY_SEPARATOR . 'index.php';
