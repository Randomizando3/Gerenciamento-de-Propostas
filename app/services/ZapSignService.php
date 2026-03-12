<?php

declare(strict_types=1);

function create_zapsign_document(array $proposal, array $settings): array
{
    $enabled = (int) ($settings['zapsign_enabled'] ?? 0) === 1;
    $apiKey = trim((string) ($settings['zapsign_api_key'] ?? ''));
    $baseUrl = rtrim((string) ($settings['zapsign_base_url'] ?? 'https://api.zapsign.com.br'), '/');

    if (!$enabled || $apiKey === '') {
        return [
            'ok' => false,
            'message' => 'Integracao ZapSign nao configurada. Cadastre API Key em Configuracoes.',
        ];
    }

    if (empty($proposal['token'])) {
        return [
            'ok' => false,
            'message' => 'Proposta precisa estar publicada para enviar ao ZapSign.',
        ];
    }

    $proposalPayload = is_array($proposal['payload'] ?? null) ? $proposal['payload'] : [];
    $clientName = trim((string) ($proposal['client_name'] ?? ($proposalPayload['cliente_nome'] ?? '')));
    $clientEmail = trim((string) ($proposal['client_email'] ?? ($proposalPayload['cliente_email'] ?? '')));

    $basePayload = [
        'name' => 'Proposta ' . (string) ($proposal['code'] ?? ''),
        'external_id' => 'proposal-' . (int) ($proposal['id'] ?? 0),
    ];
    if ($clientName !== '' && $clientEmail !== '') {
        $basePayload['signers'] = [
            [
                'name' => $clientName,
                'email' => $clientEmail,
            ],
        ];
    }

    $endpoints = zapsign_doc_endpoints($baseUrl);
    $apiTokens = zapsign_token_candidates($apiKey);
    $printUrl = proposal_print_url($proposal);
    $strategies = [];
    $hasPublicPrintUrl = $printUrl !== null && zapsign_is_public_url($printUrl);
    $printHost = mb_strtolower(trim((string) parse_url((string) $printUrl, PHP_URL_HOST)), 'UTF-8');
    $isLocalPrintHost = in_array($printHost, ['localhost', '127.0.0.1', '::1'], true);
    if ($hasPublicPrintUrl) {
        // Prioriza o mesmo documento do "Baixar PDF" para manter fidelidade visual.
        $strategies[] = [
            'mode' => 'url_pdf',
            'payload' => ['url_pdf' => $printUrl],
        ];
    } elseif ($isLocalPrintHost || $printUrl === null) {
        // Fallback somente quando nao existe URL publica do print (ex.: localhost/desenvolvimento).
        $strategies[] = [
            'mode' => 'markdown_text',
            'payload' => ['markdown_text' => zapsign_markdown_from_proposal($proposal, $proposalPayload)],
        ];
    } else {
        return [
            'ok' => false,
            'message' => 'Nao foi possivel enviar o PDF da proposta ao ZapSign. Configure a URL base publica em Configuracoes para usar o mesmo arquivo do botao "Baixar PDF".',
        ];
    }

    $errors = [];
    foreach ($strategies as $strategy) {
        $requestPayload = array_replace($basePayload, $strategy['payload']);
        $response = zapsign_post_json($endpoints, $apiTokens, $requestPayload);

        if ($response['ok']) {
            $json = $response['json'];
            $docId = (string) ($json['id'] ?? $json['token'] ?? '');
            $signUrl = (string) ($json['sign_url'] ?? $json['open_sign_url'] ?? '');
            if ($signUrl === '' && isset($json['signers'][0]) && is_array($json['signers'][0])) {
                $signUrl = (string) ($json['signers'][0]['sign_url'] ?? $json['signers'][0]['url'] ?? '');
            }

            if ($docId === '' && $signUrl === '') {
                $errors[] = 'Resposta sem id/sign_url no modo ' . $strategy['mode'] . '.';
                continue;
            }

            return [
                'ok' => true,
                'message' => 'Documento enviado ao ZapSign com sucesso.',
                'doc_id' => $docId,
                'sign_url' => $signUrl,
                'mode' => $strategy['mode'],
                'raw' => $json,
            ];
        }

        $errorMessage = (string) ($response['message'] ?? 'Falha sem detalhe.');
        if (str_contains(mb_strtolower($errorMessage, 'UTF-8'), 'token zapsign')) {
            $errors[] = $errorMessage;
        } else {
            $errors[] = '[' . $strategy['mode'] . '] ' . $errorMessage;
        }
    }

    $errors = array_values(array_unique(array_filter(array_map('trim', $errors))));

    if ($hasPublicPrintUrl && $errors === []) {
        $errors[] = 'Falha ao enviar PDF via URL publica da proposta.';
    }

    return [
        'ok' => false,
        'message' => 'Nao foi possivel criar o documento no ZapSign. ' . implode(' | ', $errors),
    ];
}

function zapsign_post_json(array $endpoints, array $apiTokens, array $payload): array
{
    $lastAuthError = null;
    foreach ($endpoints as $endpoint) {
        $allowInsecureTls = str_contains(mb_strtolower((string) $endpoint, 'UTF-8'), 'sandbox.api.zapsign.com.br');
        foreach ($apiTokens as $apiToken) {
            $attempts = [
                [
                    'name' => 'bearer',
                    'endpoint' => (string) $endpoint,
                    'headers' => [
                        'Content-Type: application/json',
                        'Authorization: Bearer ' . $apiToken,
                    ],
                ],
                [
                    'name' => 'query_api_token',
                    'endpoint' => zapsign_append_query((string) $endpoint, ['api_token' => $apiToken]),
                    'headers' => [
                        'Content-Type: application/json',
                    ],
                ],
                [
                    'name' => 'token',
                    'endpoint' => (string) $endpoint,
                    'headers' => [
                        'Content-Type: application/json',
                        'Authorization: Token ' . $apiToken,
                    ],
                ],
            ];

            foreach ($attempts as $attempt) {
                $result = zapsign_single_post(
                    (string) $attempt['endpoint'],
                    (array) $attempt['headers'],
                    $payload,
                    $allowInsecureTls
                );

                if (($result['ok'] ?? false) === true) {
                    return $result;
                }

                if (!zapsign_is_auth_error($result)) {
                    return $result;
                }

                $lastAuthError = $result;
            }
        }
    }

    if (is_array($lastAuthError)) {
        return [
            'ok' => false,
            'message' => 'Token ZapSign invalido, expirado ou sem permissao no ambiente selecionado.',
            'raw' => $lastAuthError['raw'] ?? null,
        ];
    }

    return ['ok' => false, 'message' => 'Falha de autenticacao com o ZapSign.'];
}

function zapsign_single_post(string $endpoint, array $headers, array $payload, bool $allowInsecureTls = false): array
{
    $ch = curl_init($endpoint);
    if ($ch === false) {
        return ['ok' => false, 'message' => 'Falha ao iniciar requisicao cURL.'];
    }

    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_HTTPHEADER => $headers,
        CURLOPT_POSTFIELDS => json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
        CURLOPT_TIMEOUT => 20,
        CURLOPT_FOLLOWLOCATION => true,
    ]);

    if ($allowInsecureTls) {
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    }

    $response = curl_exec($ch);
    $error = curl_error($ch);
    $httpCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if ($response === false) {
        return ['ok' => false, 'message' => 'Erro na requisicao ZapSign: ' . $error];
    }

    $json = json_decode($response, true);
    if (!is_array($json)) {
        return [
            'ok' => false,
            'http_code' => $httpCode,
            'message' => 'Resposta invalida da API ZapSign. HTTP ' . $httpCode . '.',
            'raw' => $response,
        ];
    }

    if ($httpCode >= 400) {
        $apiMessage = zapsign_extract_error_message($json);
        return [
            'ok' => false,
            'http_code' => $httpCode,
            'message' => 'HTTP ' . $httpCode . ($apiMessage !== '' ? ' - ' . $apiMessage : '.'),
            'raw' => $json,
        ];
    }

    return [
        'ok' => true,
        'http_code' => $httpCode,
        'json' => $json,
    ];
}

function zapsign_append_query(string $url, array $params): string
{
    $separator = str_contains($url, '?') ? '&' : '?';
    return $url . $separator . http_build_query($params);
}

function zapsign_doc_endpoints(string $baseUrl): array
{
    $baseUrl = rtrim(trim($baseUrl), '/');
    if ($baseUrl === '') {
        $baseUrl = 'https://sandbox.api.zapsign.com.br';
    }

    $hosts = [$baseUrl];
    $lower = mb_strtolower($baseUrl, 'UTF-8');
    if (str_contains($lower, 'sandbox.api.zapsign.com.br')) {
        $hosts[] = 'https://api.zapsign.com.br';
    } elseif (str_contains($lower, 'api.zapsign.com.br')) {
        $hosts[] = 'https://sandbox.api.zapsign.com.br';
    }

    $endpoints = [];
    foreach (array_unique($hosts) as $host) {
        $endpoints[] = rtrim($host, '/') . '/api/v1/docs/';
    }

    return $endpoints;
}

function zapsign_token_candidates(string $rawToken): array
{
    $rawToken = trim($rawToken);
    if ($rawToken === '') {
        return [];
    }

    $tokens = [$rawToken];
    if (preg_match_all('/[a-f0-9]{8}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{12}/i', $rawToken, $matches)) {
        foreach (($matches[0] ?? []) as $candidate) {
            if (is_string($candidate) && trim($candidate) !== '') {
                $tokens[] = trim($candidate);
            }
        }
    }

    return array_values(array_unique($tokens));
}

function zapsign_is_auth_error(array $response): bool
{
    $httpCode = (int) ($response['http_code'] ?? 0);
    $message = mb_strtolower(trim((string) ($response['message'] ?? '')), 'UTF-8');
    if ($httpCode === 401 || $httpCode === 403) {
        return true;
    }

    return str_contains($message, 'token') || str_contains($message, 'authorization');
}

function zapsign_extract_error_message(array $json): string
{
    $candidates = [
        $json['message'] ?? null,
        $json['detail'] ?? null,
        $json['error'] ?? null,
        $json['non_field_errors'] ?? null,
    ];

    foreach ($candidates as $candidate) {
        if (is_string($candidate) && trim($candidate) !== '') {
            return trim($candidate);
        }
        if (is_array($candidate) && isset($candidate[0]) && is_string($candidate[0])) {
            return trim((string) $candidate[0]);
        }
    }

    foreach ($json as $value) {
        if (is_string($value) && trim($value) !== '') {
            return trim($value);
        }
        if (is_array($value) && isset($value[0]) && is_string($value[0])) {
            return trim((string) $value[0]);
        }
    }

    return '';
}

function zapsign_markdown_from_proposal(array $proposal, array $payload): string
{
    $lines = [];
    $lines[] = '# Proposta Comercial';
    $lines[] = '';
    $lines[] = '- Codigo: ' . (string) ($proposal['code'] ?? ($payload['codigo_base'] ?? ''));
    $revision = trim((string) ($proposal['revision'] ?? ($payload['revisao'] ?? '')));
    if ($revision !== '' && $revision !== '00') {
        $lines[] = '- Revisao: ' . $revision;
    }
    $lines[] = '- Data: ' . date('d/m/Y');

    $client = trim((string) ($payload['cliente_nome'] ?? $proposal['client_name'] ?? ''));
    if ($client !== '') {
        $lines[] = '- Cliente: ' . $client;
    }
    $obra = trim((string) ($payload['obra_nome'] ?? ''));
    if ($obra !== '') {
        $lines[] = '- Obra: ' . $obra;
    }
    $endereco = trim((string) ($payload['obra_endereco'] ?? ''));
    if ($endereco !== '') {
        $lines[] = '- Endereco: ' . $endereco;
    }

    $lines[] = '';
    $lines[] = '## Escopo';
    $disciplinas = $payload['disciplinas'] ?? [];
    $valores = $payload['valores'] ?? [];
    $catalog = discipline_catalog();
    if (is_array($disciplinas) && $disciplinas !== []) {
        foreach ($disciplinas as $key) {
            if (!is_string($key)) {
                continue;
            }
            $nome = (string) ($catalog[$key]['nome'] ?? mb_convert_case($key, MB_CASE_TITLE, 'UTF-8'));
            $valor = brl((float) ($valores[$key] ?? 0));
            $lines[] = '- ' . $nome . ': ' . $valor;
        }
    } else {
        $lines[] = '- Escopo conforme proposta publicada.';
    }

    $total = proposal_total($payload);
    $lines[] = '';
    $lines[] = '## Valor total';
    $lines[] = '- ' . brl($total) . ' (' . currency_to_words_ptbr($total) . ')';

    $consideracoes = $payload['consideracoes'] ?? [];
    if (is_array($consideracoes) && $consideracoes !== []) {
        $lines[] = '';
        $lines[] = '## Consideracoes';
        foreach ($consideracoes as $item) {
            $item = trim((string) $item);
            if ($item !== '') {
                $lines[] = '- ' . $item;
            }
        }
    }

    $lines[] = '';
    $lines[] = 'Ao assinar este documento, as partes concordam com os termos da proposta comercial.';

    return implode("\n", $lines);
}

function zapsign_is_public_url(string $url): bool
{
    $parts = parse_url($url);
    if (!is_array($parts)) {
        return false;
    }
    $host = mb_strtolower(trim((string) ($parts['host'] ?? '')), 'UTF-8');
    if ($host === '') {
        return false;
    }

    if (in_array($host, ['localhost', '127.0.0.1', '::1'], true)) {
        return false;
    }

    if (str_ends_with($host, '.local') || str_ends_with($host, '.test') || str_ends_with($host, '.internal')) {
        return false;
    }

    if (filter_var($host, FILTER_VALIDATE_IP) !== false) {
        $isPublicIp = filter_var($host, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE);
        return $isPublicIp !== false;
    }

    return true;
}

function verify_zapsign_webhook_signature(string $rawBody, array $settings): bool
{
    $secret = trim((string) ($settings['zapsign_webhook_secret'] ?? ''));
    if ($secret === '') {
        return true;
    }

    $incoming = (string) ($_SERVER['HTTP_X_ZAPSIGN_SIGNATURE'] ?? '');
    if ($incoming === '') {
        return false;
    }

    $expected = hash_hmac('sha256', $rawBody, $secret);
    return hash_equals($expected, $incoming);
}
