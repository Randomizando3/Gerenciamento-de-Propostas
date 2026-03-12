<?php

declare(strict_types=1);

return [
    'app_name' => 'Gerenciamento de Propostas',
    'timezone' => 'America/Sao_Paulo',
    'storage_path' => dirname(__DIR__) . DIRECTORY_SEPARATOR . 'storage',
    'db_path' => dirname(__DIR__) . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'database.sqlite',
    'default_admin' => [
        'name' => 'Administrador',
        'email' => 'admin@local',
        'password' => 'admin123',
    ],
    'status_labels' => [
        'draft' => 'Rascunho',
        'published' => 'Publicada',
        'viewed' => 'Visualizada',
        'signing' => 'Em assinatura',
        'signed' => 'Assinada',
    ],
    'discipline_labels' => [
        'eletrica' => 'Elétrica',
        'hidraulica' => 'Hidráulica',
        'esgoto' => 'Esgoto',
        'gas' => 'Gás',
        'especiais' => 'Especiais',
    ],
];
