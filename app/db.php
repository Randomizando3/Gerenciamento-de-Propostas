<?php

declare(strict_types=1);

function db_file_path(): string
{
    return app_config('storage_path') . DIRECTORY_SEPARATOR . 'database.json';
}

function init_storage(): void
{
    $path = db_file_path();
    $dir = dirname($path);
    if (!is_dir($dir) && !@mkdir($dir, 0775, true) && !is_dir($dir)) {
        throw new RuntimeException('Nao foi possivel criar o diretorio de armazenamento: ' . $dir);
    }
    if (!is_writable($dir)) {
        @chmod($dir, 0775);
    }
    if (!is_writable($dir)) {
        throw new RuntimeException('Sem permissao de escrita no diretorio storage. Ajuste owner/permissoes para o usuario do PHP.');
    }

    $state = &db_state();
    if (!file_exists($path)) {
        db_persist($state);
    }
}

function &db_state(): array
{
    static $state = null;
    if (is_array($state)) {
        return $state;
    }

    $path = db_file_path();
    if (!file_exists($path)) {
        $state = db_empty_state();
        db_seed_defaults($state);
        return $state;
    }

    $raw = file_get_contents($path);
    $decoded = is_string($raw) ? json_decode($raw, true) : null;
    if (!is_array($decoded)) {
        $state = db_empty_state();
        db_seed_defaults($state);
        return $state;
    }

    $state = array_replace_recursive(db_empty_state(), $decoded);
    db_sync_counters($state);
    db_seed_defaults($state);
    return $state;
}

function db_empty_state(): array
{
    return [
        '_meta' => [
            'counters' => [
                'admins' => 0,
                'settings' => 0,
                'proposals' => 0,
                'proposal_models' => 0,
                'proposal_views' => 0,
                'proposal_events' => 0,
                'webhooks' => 0,
            ],
            'updated_at' => now_iso(),
        ],
        'admins' => [],
        'settings' => [],
        'proposals' => [],
        'proposal_models' => [],
        'proposal_views' => [],
        'proposal_events' => [],
        'webhooks' => [],
    ];
}

function db_seed_defaults(array &$state): void
{
    if (count($state['admins']) === 0) {
        $admin = app_config('default_admin');
        db_insert('admins', [
            'name' => $admin['name'],
            'email' => mb_strtolower($admin['email'], 'UTF-8'),
            'password_hash' => password_hash($admin['password'], PASSWORD_DEFAULT),
            'role' => 'admin',
            'is_active' => 1,
            'created_at' => now_iso(),
            'updated_at' => now_iso(),
        ]);
    }

    if (count($state['settings']) === 0) {
        $defaults = default_settings_values();
        $defaults['updated_at'] = now_iso();
        db_insert('settings', $defaults);
    }
}

function db_sync_counters(array &$state): void
{
    $tables = ['admins', 'settings', 'proposals', 'proposal_models', 'proposal_views', 'proposal_events', 'webhooks'];
    foreach ($tables as $table) {
        $maxId = 0;
        foreach (($state[$table] ?? []) as $row) {
            $id = (int) ($row['id'] ?? 0);
            if ($id > $maxId) {
                $maxId = $id;
            }
        }
        if (($state['_meta']['counters'][$table] ?? 0) < $maxId) {
            $state['_meta']['counters'][$table] = $maxId;
        }
    }
}

function db_persist(array $state): void
{
    $state['_meta']['updated_at'] = now_iso();
    $json = json_encode($state, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    if (!is_string($json)) {
        throw new RuntimeException('Falha ao serializar o banco local em JSON.');
    }

    $path = db_file_path();
    $dir = dirname($path);
    if (!is_dir($dir) && !@mkdir($dir, 0775, true) && !is_dir($dir)) {
        throw new RuntimeException('Nao foi possivel criar o diretorio de armazenamento: ' . $dir);
    }
    if (!is_writable($dir)) {
        @chmod($dir, 0775);
    }
    if (file_exists($path) && !is_writable($path)) {
        @chmod($path, 0664);
    }
    if (!is_writable($dir) || (file_exists($path) && !is_writable($path))) {
        throw new RuntimeException('Sem permissao para gravar em storage/database.json. Ajuste owner/permissoes no servidor.');
    }

    db_create_backup_snapshot($path);
    db_write_file_atomic($path, $json);
}

function db_create_backup_snapshot(string $path): void
{
    if (!file_exists($path)) {
        return;
    }

    $backupDir = app_config('storage_path') . DIRECTORY_SEPARATOR . 'backups';
    if (!is_dir($backupDir) && !@mkdir($backupDir, 0775, true) && !is_dir($backupDir)) {
        return;
    }

    $timestamp = (new DateTimeImmutable('now'))->format('Ymd-His-v');
    $backupPath = $backupDir . DIRECTORY_SEPARATOR . 'database-' . $timestamp . '.json';
    @copy($path, $backupPath);

    $files = glob($backupDir . DIRECTORY_SEPARATOR . 'database-*.json');
    if (!is_array($files) || count($files) <= 50) {
        return;
    }

    usort(
        $files,
        static fn (string $a, string $b): int => filemtime($b) <=> filemtime($a)
    );

    foreach (array_slice($files, 50) as $oldFile) {
        @unlink($oldFile);
    }
}

function db_write_file_atomic(string $path, string $content): void
{
    $tmpPath = $path . '.tmp';
    $bytes = @file_put_contents($tmpPath, $content, LOCK_EX);
    if ($bytes === false) {
        throw new RuntimeException('Falha ao gravar arquivo temporario do banco: ' . $tmpPath);
    }

    if (!@rename($tmpPath, $path)) {
        @unlink($tmpPath);
        throw new RuntimeException('Falha ao salvar storage/database.json. Verifique permissao de escrita.');
    }

    @chmod($path, 0664);
}

function db_commit(): void
{
    $state = db_state();
    db_persist($state);
}

function db_all(string $table): array
{
    $state = db_state();
    return $state[$table] ?? [];
}

function db_find(string $table, int $id): ?array
{
    $rows = db_all($table);
    foreach ($rows as $row) {
        if ((int) ($row['id'] ?? 0) === $id) {
            return $row;
        }
    }
    return null;
}

function db_where(string $table, callable $predicate): array
{
    $rows = db_all($table);
    $filtered = [];
    foreach ($rows as $row) {
        if ($predicate($row)) {
            $filtered[] = $row;
        }
    }
    return $filtered;
}

function db_first(string $table, callable $predicate): ?array
{
    $rows = db_all($table);
    foreach ($rows as $row) {
        if ($predicate($row)) {
            return $row;
        }
    }
    return null;
}

function db_next_id(string $table): int
{
    $state = &db_state();
    if (!isset($state['_meta']['counters'][$table])) {
        $state['_meta']['counters'][$table] = 0;
    }
    $state['_meta']['counters'][$table] += 1;
    return (int) $state['_meta']['counters'][$table];
}

function db_insert(string $table, array $data): array
{
    $state = &db_state();
    $data['id'] = db_next_id($table);
    $state[$table][] = $data;
    db_persist($state);
    return $data;
}

function db_update(string $table, int $id, array $changes): ?array
{
    $state = &db_state();
    if (!isset($state[$table]) || !is_array($state[$table])) {
        return null;
    }

    foreach ($state[$table] as $index => $row) {
        if ((int) ($row['id'] ?? 0) !== $id) {
            continue;
        }

        $updated = array_replace($row, $changes);
        $state[$table][$index] = $updated;
        db_persist($state);
        return $updated;
    }

    return null;
}

function db_replace_rows(string $table, array $rows): void
{
    $state = &db_state();
    $state[$table] = array_values($rows);
    db_persist($state);
}

function find_admin_by_email(string $email): ?array
{
    $email = mb_strtolower(trim($email), 'UTF-8');
    return db_first('admins', static fn (array $row): bool => mb_strtolower((string) ($row['email'] ?? ''), 'UTF-8') === $email);
}

function list_admin_users(): array
{
    $admins = db_all('admins');
    usort(
        $admins,
        static fn (array $a, array $b): int => strcmp((string) ($a['name'] ?? ''), (string) ($b['name'] ?? ''))
    );

    foreach ($admins as &$admin) {
        if (!array_key_exists('is_active', $admin)) {
            $admin['is_active'] = 1;
        }
        $role = mb_strtolower(trim((string) ($admin['role'] ?? '')), 'UTF-8');
        $admin['role'] = in_array($role, ['admin', 'editor'], true) ? $role : 'admin';
    }
    unset($admin);

    return $admins;
}

function create_admin_user(string $name, string $email, string $password, string $role = 'editor'): array
{
    $normalizedEmail = mb_strtolower(trim($email), 'UTF-8');
    $normalizedRole = mb_strtolower(trim($role), 'UTF-8');
    if (!in_array($normalizedRole, ['admin', 'editor'], true)) {
        $normalizedRole = 'editor';
    }

    if (find_admin_by_email($normalizedEmail)) {
        throw new InvalidArgumentException('Já existe um usuário com este e-mail.');
    }

    $now = now_iso();
    return db_insert('admins', [
        'name' => trim($name),
        'email' => $normalizedEmail,
        'password_hash' => password_hash($password, PASSWORD_DEFAULT),
        'role' => $normalizedRole,
        'is_active' => 1,
        'created_at' => $now,
        'updated_at' => $now,
    ]);
}

function delete_admin_user(int $id): bool
{
    $rows = db_all('admins');
    $filtered = [];
    $deleted = false;

    foreach ($rows as $row) {
        if ((int) ($row['id'] ?? 0) === $id) {
            $deleted = true;
            continue;
        }
        $filtered[] = $row;
    }

    if (!$deleted) {
        return false;
    }

    db_replace_rows('admins', $filtered);
    return true;
}

function get_settings_row(): array
{
    $settings = db_all('settings');
    if (count($settings) === 0) {
        $created = db_insert('settings', ['updated_at' => now_iso()]);
        return $created;
    }
    return $settings[0];
}

function save_settings_row(array $changes): array
{
    $current = get_settings_row();
    $changes['updated_at'] = now_iso();
    return db_update('settings', (int) $current['id'], $changes) ?? $current;
}

function list_proposal_models(): array
{
    $rows = db_all('proposal_models');
    usort(
        $rows,
        static fn (array $a, array $b): int => strcmp((string) ($a['name'] ?? ''), (string) ($b['name'] ?? ''))
    );

    return $rows;
}

function get_proposal_model(int $id): ?array
{
    $row = db_find('proposal_models', $id);
    if (!$row) {
        return null;
    }

    $payload = json_decode((string) ($row['payload_json'] ?? '{}'), true);
    $row['payload'] = is_array($payload) ? $payload : [];
    return $row;
}

function save_proposal_model_record(?int $id, string $name, string $description, array $payload, ?array $actor = null): array
{
    $now = now_iso();
    $actorId = (int) ($actor['id'] ?? 0);
    $actorName = trim((string) ($actor['name'] ?? ''));
    $payloadJson = json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    if (!is_string($payloadJson)) {
        $payloadJson = '{}';
    }

    if ($id === null) {
        return db_insert('proposal_models', [
            'name' => $name,
            'description' => $description,
            'payload_json' => $payloadJson,
            'created_by_admin_id' => $actorId > 0 ? $actorId : null,
            'created_by_admin_name' => $actorName,
            'updated_by_admin_id' => $actorId > 0 ? $actorId : null,
            'updated_by_admin_name' => $actorName,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }

    $existing = db_find('proposal_models', $id);
    if (!$existing) {
        return save_proposal_model_record(null, $name, $description, $payload, $actor);
    }

    return db_update('proposal_models', $id, [
        'name' => $name,
        'description' => $description,
        'payload_json' => $payloadJson,
        'updated_by_admin_id' => $actorId > 0 ? $actorId : ($existing['updated_by_admin_id'] ?? null),
        'updated_by_admin_name' => $actorName !== '' ? $actorName : (string) ($existing['updated_by_admin_name'] ?? ''),
        'updated_at' => $now,
    ]) ?? $existing;
}

function delete_proposal_model_record(int $id): bool
{
    $rows = db_all('proposal_models');
    $filtered = [];
    $deleted = false;

    foreach ($rows as $row) {
        if ((int) ($row['id'] ?? 0) === $id) {
            $deleted = true;
            continue;
        }
        $filtered[] = $row;
    }

    if (!$deleted) {
        return false;
    }

    db_replace_rows('proposal_models', $filtered);
    return true;
}
