<?php

declare(strict_types=1);

function app_config(?string $key = null, mixed $default = null): mixed
{
    static $config;

    if ($config === null) {
        $config = require __DIR__ . '/config.php';
    }

    if ($key === null) {
        return $config;
    }

    return $config[$key] ?? $default;
}

function now_iso(): string
{
    return (new DateTimeImmutable('now'))->format('Y-m-d H:i:s');
}

function h(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function base_path(string $path = ''): string
{
    $root = dirname(__DIR__);
    if ($path === '') {
        return $root;
    }

    return $root . DIRECTORY_SEPARATOR . ltrim($path, '\\/');
}

function app_url(string $path = ''): string
{
    $path = '/' . ltrim($path, '/');
    $settings = load_settings();
    if (!empty($settings['base_url'])) {
        return rtrim((string) $settings['base_url'], '/') . $path;
    }

    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $forwardedProto = trim((string) ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? ''));
    if ($forwardedProto !== '') {
        $protoParts = explode(',', $forwardedProto);
        $firstProto = mb_strtolower(trim((string) ($protoParts[0] ?? '')), 'UTF-8');
        if (in_array($firstProto, ['http', 'https'], true)) {
            $scheme = $firstProto;
        }
    }

    $host = trim((string) ($_SERVER['HTTP_HOST'] ?? ''));
    $forwardedHost = trim((string) ($_SERVER['HTTP_X_FORWARDED_HOST'] ?? ''));
    if ($forwardedHost !== '') {
        $hostParts = explode(',', $forwardedHost);
        $forwardedFirst = trim((string) ($hostParts[0] ?? ''));
        if ($forwardedFirst !== '') {
            $host = $forwardedFirst;
        }
    }
    if ($host === '') {
        $host = 'localhost:9898';
    }

    return sprintf('%s://%s%s', $scheme, $host, $path);
}

function redirect(string $path): never
{
    if (!str_starts_with($path, 'http')) {
        $path = '/' . ltrim($path, '/');
    }
    header('Location: ' . $path);
    exit;
}

function json_response(array $payload, int $status = 200): never
{
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

function request_path(): string
{
    $uri = $_SERVER['REQUEST_URI'] ?? '/';
    $path = parse_url($uri, PHP_URL_PATH);
    if (!is_string($path) || $path === '') {
        return '/';
    }
    return $path;
}

function request_method(): string
{
    return strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
}

function old_input(string $key, mixed $default = ''): mixed
{
    return $_SESSION['_old'][$key] ?? $default;
}

function flash_old(array $data): void
{
    $_SESSION['_old'] = $data;
}

function clear_old(): void
{
    unset($_SESSION['_old']);
}

function flash(string $type, string $message): void
{
    $_SESSION['_flash'][] = ['type' => $type, 'message' => $message];
}

function get_flash_messages(): array
{
    $messages = $_SESSION['_flash'] ?? [];
    unset($_SESSION['_flash']);
    return $messages;
}

function csrf_token(): string
{
    if (empty($_SESSION['_csrf'])) {
        $_SESSION['_csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['_csrf'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="_csrf" value="' . h(csrf_token()) . '">';
}

function verify_csrf_or_die(): void
{
    $token = $_POST['_csrf'] ?? '';
    if (!is_string($token) || !hash_equals(csrf_token(), $token)) {
        http_response_code(419);
        exit('Token CSRF inválido.');
    }
}

function is_logged_in(): bool
{
    return isset($_SESSION['admin_id']);
}

function require_auth(): void
{
    if (!is_logged_in()) {
        flash('error', 'Faça login para continuar.');
        redirect('/admin/login');
    }

    if (current_admin() === null) {
        logout_admin();
        flash('error', 'Sessão inválida. Faça login novamente.');
        redirect('/admin/login');
    }
}

function current_admin(): ?array
{
    if (!is_logged_in()) {
        return null;
    }
    $admin = db_find('admins', (int) $_SESSION['admin_id']);
    if (!$admin) {
        return null;
    }
    $roleRaw = mb_strtolower(trim((string) ($admin['role'] ?? '')), 'UTF-8');
    $role = in_array($roleRaw, ['admin', 'editor'], true) ? $roleRaw : 'admin';

    return [
        'id' => $admin['id'],
        'name' => $admin['name'],
        'email' => $admin['email'],
        'role' => $role,
    ];
}

function is_admin_user(?array $admin = null): bool
{
    $admin = $admin ?? current_admin();
    if (!$admin) {
        return false;
    }
    return mb_strtolower((string) ($admin['role'] ?? ''), 'UTF-8') === 'admin';
}

function require_admin(): void
{
    require_auth();
    if (!is_admin_user()) {
        flash('error', 'Acesso restrito a administradores.');
        redirect('/admin');
    }
}

function set_admin_session(array $admin): void
{
    $_SESSION['admin_id'] = (int) $admin['id'];
}

function logout_admin(): void
{
    unset($_SESSION['admin_id']);
}

function render(string $view, array $data = [], string $layout = 'admin'): void
{
    $viewFile = __DIR__ . '/views/' . $view . '.php';
    if (!file_exists($viewFile)) {
        http_response_code(500);
        echo 'View não encontrada: ' . h($view);
        return;
    }

    $flashes = get_flash_messages();
    extract($data, EXTR_SKIP);

    if ($layout === 'none') {
        require $viewFile;
        return;
    }

    $layoutFile = __DIR__ . '/views/partials/' . $layout . '_layout.php';
    if (!file_exists($layoutFile)) {
        http_response_code(500);
        echo 'Layout não encontrado.';
        return;
    }

    require $layoutFile;
}

function request_input(string $key, mixed $default = null): mixed
{
    return $_POST[$key] ?? $_GET[$key] ?? $default;
}

function to_float(mixed $value): float
{
    if (is_numeric($value)) {
        return (float) $value;
    }

    if (!is_string($value)) {
        return 0.0;
    }

    $normalized = str_replace(['R$', '.', ' '], '', trim($value));
    $normalized = str_replace(',', '.', $normalized);
    if (!is_numeric($normalized)) {
        return 0.0;
    }

    return (float) $normalized;
}

function brl(float $value): string
{
    return 'R$ ' . number_format($value, 2, ',', '.');
}

function client_ip(): string
{
    $keys = ['HTTP_CF_CONNECTING_IP', 'HTTP_X_FORWARDED_FOR', 'REMOTE_ADDR'];
    foreach ($keys as $key) {
        if (!empty($_SERVER[$key])) {
            $value = (string) $_SERVER[$key];
            if ($key === 'HTTP_X_FORWARDED_FOR') {
                $parts = explode(',', $value);
                return trim($parts[0]);
            }
            return $value;
        }
    }
    return '0.0.0.0';
}

function detect_device(string $userAgent): string
{
    $ua = mb_strtolower($userAgent, 'UTF-8');
    if (str_contains($ua, 'tablet') || str_contains($ua, 'ipad')) {
        return 'tablet';
    }
    if (str_contains($ua, 'mobile') || str_contains($ua, 'android') || str_contains($ua, 'iphone')) {
        return 'mobile';
    }
    return 'desktop';
}

function load_settings(): array
{
    return array_replace(default_settings_values(), get_settings_row());
}

function default_settings_values(): array
{
    return [
        'clarity_enabled' => 1,
        'clarity_project_id' => 'vuev12yagy',
        'clarity_export_endpoint' => 'https://www.clarity.ms/export-data/api/v1/project-live-insights',
        'clarity_export_token' => '',
        'zapsign_enabled' => 1,
        'zapsign_api_key' => '',
        'zapsign_webhook_secret' => '',
        'zapsign_base_url' => 'https://sandbox.api.zapsign.com.br',
        'base_url' => '',
        'company_name' => 'Complementare Projetos',
        'company_phone' => '(21) 3264-2475',
        'company_website' => 'https://www.cprojetos.com.br',
        'company_instagram' => 'https://instagram.com/complementareprojetos',
        'company_address' => 'Rio de Janeiro/RJ',
        'company_bank_name' => 'Banco Inter (077)',
        'company_bank_agency' => '0001',
        'company_bank_account' => '3375106-4',
        'company_bank_favored' => 'Complementare Projetos de Instalações LTDA-EPP',
        'company_bank_cnpj' => '23.012.176/0001-69',
        'company_bank_pix_key' => '23.012.176/0001-69',
        'company_bank_pix_key_type' => 'CNPJ',
        'accept_terms_title' => 'CONTRATO DE PRESTA��O DE SERVI�OS DE PROJETOS DE ENGENHARIA',
        'accept_terms_html' => '',
        'accept_terms_checkbox_text' => 'Li e concordo com os termos e condições apresentados acima. Autorizo o início dos trabalhos conforme proposta comercial {{PROPOSTA_NUM}}.',
    ];
}

function status_label(string $status): string
{
    $labels = app_config('status_labels', []);
    return $labels[$status] ?? $status;
}

function random_token(int $length = 32): string
{
    return bin2hex(random_bytes((int) ceil($length / 2)));
}

function parse_json_body(): array
{
    $raw = file_get_contents('php://input');
    if (!is_string($raw) || trim($raw) === '') {
        return [];
    }
    $data = json_decode($raw, true);
    return is_array($data) ? $data : [];
}

function clamp(float|int $value, float|int $min, float|int $max): float|int
{
    if ($value < $min) {
        return $min;
    }
    if ($value > $max) {
        return $max;
    }
    return $value;
}

function number_to_words_ptbr(int $number): string
{
    $units = [
        0 => 'zero',
        1 => 'um',
        2 => 'dois',
        3 => 'três',
        4 => 'quatro',
        5 => 'cinco',
        6 => 'seis',
        7 => 'sete',
        8 => 'oito',
        9 => 'nove',
        10 => 'dez',
        11 => 'onze',
        12 => 'doze',
        13 => 'treze',
        14 => 'quatorze',
        15 => 'quinze',
        16 => 'dezesseis',
        17 => 'dezessete',
        18 => 'dezoito',
        19 => 'dezenove',
    ];

    $tens = [
        20 => 'vinte',
        30 => 'trinta',
        40 => 'quarenta',
        50 => 'cinquenta',
        60 => 'sessenta',
        70 => 'setenta',
        80 => 'oitenta',
        90 => 'noventa',
    ];

    $hundreds = [
        100 => 'cem',
        200 => 'duzentos',
        300 => 'trezentos',
        400 => 'quatrocentos',
        500 => 'quinhentos',
        600 => 'seiscentos',
        700 => 'setecentos',
        800 => 'oitocentos',
        900 => 'novecentos',
    ];

    if ($number < 20) {
        return $units[$number];
    }

    if ($number < 100) {
        $ten = (int) (floor($number / 10) * 10);
        $rest = $number % 10;
        return $rest === 0 ? $tens[$ten] : $tens[$ten] . ' e ' . number_to_words_ptbr($rest);
    }

    if ($number < 1000) {
        if ($number === 100) {
            return $hundreds[100];
        }
        $hundred = (int) (floor($number / 100) * 100);
        $rest = $number % 100;
        if ($hundred === 100) {
            return 'cento e ' . number_to_words_ptbr($rest);
        }
        return $rest === 0 ? $hundreds[$hundred] : $hundreds[$hundred] . ' e ' . number_to_words_ptbr($rest);
    }

    if ($number < 1000000) {
        $thousand = (int) floor($number / 1000);
        $rest = $number % 1000;
        $thousandText = $thousand === 1 ? 'mil' : number_to_words_ptbr($thousand) . ' mil';
        if ($rest === 0) {
            return $thousandText;
        }
        $connector = $rest < 100 ? ' e ' : ' ';
        return $thousandText . $connector . number_to_words_ptbr($rest);
    }

    if ($number < 1000000000) {
        $million = (int) floor($number / 1000000);
        $rest = $number % 1000000;
        $millionText = $million === 1 ? 'um milhão' : number_to_words_ptbr($million) . ' milhões';
        if ($rest === 0) {
            return $millionText;
        }
        $connector = $rest < 100 ? ' e ' : ' ';
        return $millionText . $connector . number_to_words_ptbr($rest);
    }

    return (string) $number;
}

function currency_to_words_ptbr(float $value): string
{
    $intValue = (int) floor($value);
    $cents = (int) round(($value - $intValue) * 100);

    $intText = match (true) {
        $intValue === 0 => 'zero reais',
        $intValue === 1 => 'um real',
        default => number_to_words_ptbr($intValue) . ' reais',
    };

    if ($cents === 0) {
        return $intText;
    }

    $centText = $cents === 1 ? 'um centavo' : number_to_words_ptbr($cents) . ' centavos';
    return $intText . ' e ' . $centText;
}

function parse_multilines(string $value): array
{
    $lines = preg_split('/\r\n|\r|\n/', trim($value));
    if (!$lines) {
        return [];
    }
    $clean = [];
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line !== '') {
            $clean[] = $line;
        }
    }
    return $clean;
}



