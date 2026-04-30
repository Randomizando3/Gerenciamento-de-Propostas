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
            'message' => 'Integração ZapSign não configurada. Cadastre API Key em Configurações.',
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
    $clientCompany = trim((string) ($proposal['client_company'] ?? ($proposalPayload['cliente_empresa'] ?? '')));
    $clientEmail = trim((string) ($proposal['client_email'] ?? ($proposalPayload['cliente_email'] ?? '')));
    $clientPhone = trim((string) ($proposal['client_phone'] ?? ($proposalPayload['cliente_telefone'] ?? '')));
    $signerName = $clientName !== '' ? $clientName : ($clientCompany !== '' ? $clientCompany : 'Cliente');
    $signerPayload = zapsign_build_signer_payload($signerName, $clientEmail, $clientPhone);

    if ($signerPayload === null) {
        return [
            'ok' => false,
            'message' => 'Para enviar ao ZapSign, preencha pelo menos o nome e um contato do cliente na proposta: e-mail ou telefone.',
        ];
    }

    $basePayload = [
        'name' => 'Proposta ' . (string) ($proposal['code'] ?? ''),
        'external_id' => 'proposal-' . (int) ($proposal['id'] ?? 0),
        'signers' => [$signerPayload],
    ];

    $endpoints = zapsign_doc_endpoints($baseUrl);
    $apiTokens = zapsign_token_candidates($apiKey);
    $contractUrl = proposal_acceptance_document_url($proposal, $proposalPayload);
    $strategies = [];
    $hasPublicContractUrl = $contractUrl !== null && zapsign_is_public_url($contractUrl);
    $contractHost = mb_strtolower(trim((string) parse_url((string) $contractUrl, PHP_URL_HOST)), 'UTF-8');
    $isLocalContractHost = in_array($contractHost, ['localhost', '127.0.0.1', '::1'], true);
    if ($hasPublicContractUrl) {
        // In produção, o ZapSign deve receber exatamente o mesmo documento
        // público exibido no aceite, sem fallback para um conteúdo alternativo.
        $strategies[] = [
            'mode' => 'url_pdf',
            'payload' => ['url_pdf' => $contractUrl],
        ];
    } elseif ($isLocalContractHost || $contractUrl === null) {
        $strategies[] = [
            'mode' => 'markdown_text',
            'payload' => ['markdown_text' => zapsign_markdown_from_proposal($proposal, $proposalPayload, $settings)],
        ];
    } else {
        return [
            'ok' => false,
            'message' => 'Não foi possível enviar o documento ao ZapSign. Configure a URL base pública em Configurações para gerar o documento de aceite.',
        ];
    }

    $errors = [];
    foreach ($strategies as $strategy) {
        $requestPayload = array_replace($basePayload, $strategy['payload']);
        $response = zapsign_post_json($endpoints, $apiTokens, $requestPayload);

        if ((int) ($response['http_code'] ?? 0) === 402) {
            return [
                'ok' => false,
                'message' => zapsign_human_error_message($response),
            ];
        }

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

    if ($hasPublicContractUrl && $errors === []) {
        $errors[] = 'Falha ao enviar o documento via URL pública.';
    }

    return [
        'ok' => false,
        'message' => 'Nao foi possivel criar o documento no ZapSign. ' . implode(' | ', $errors),
    ];
}

function zapsign_build_signer_payload(string $name, string $email, string $phone): ?array
{
    $signerName = trim($name);
    if ($signerName === '') {
        return null;
    }

    $normalizedEmail = trim($email);
    if ($normalizedEmail !== '' && filter_var($normalizedEmail, FILTER_VALIDATE_EMAIL) === false) {
        $normalizedEmail = '';
    }

    $phoneData = zapsign_normalize_phone($phone);
    $hasEmail = $normalizedEmail !== '';
    $hasPhone = $phoneData !== null;
    if (!$hasEmail && !$hasPhone) {
        return null;
    }

    $payload = [
        'name' => $signerName,
        'send_automatic_email' => false,
        'blank_email' => !$hasEmail,
        'blank_phone' => !$hasPhone,
    ];

    if ($hasEmail) {
        $payload['email'] = $normalizedEmail;
    }

    if ($hasPhone) {
        $payload['phone_country'] = $phoneData['country'];
        $payload['phone_number'] = $phoneData['number'];
    }

    return $payload;
}

function zapsign_normalize_phone(string $rawPhone): ?array
{
    $digits = preg_replace('/\D+/', '', $rawPhone) ?? '';
    if ($digits === '') {
        return null;
    }

    if (str_starts_with($digits, '55') && strlen($digits) > 11) {
        $digits = substr($digits, 2);
    }

    if (strlen($digits) < 10) {
        return null;
    }

    return [
        'country' => '55',
        'number' => $digits,
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
            'message' => 'Token ZapSign inválido, expirado ou sem permissão no ambiente selecionado.',
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
        $plainMessage = zapsign_extract_plain_error_message($response);
        return [
            'ok' => false,
            'http_code' => $httpCode,
            'message' => $httpCode >= 400
                ? 'HTTP ' . $httpCode . ($plainMessage !== '' ? ' - ' . $plainMessage : '.')
                : 'Resposta invalida da API ZapSign. HTTP ' . $httpCode . '.',
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

function zapsign_extract_plain_error_message(string $response): string
{
    $text = trim(strip_tags(html_entity_decode($response, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8')));
    if ($text === '') {
        return '';
    }

    $normalized = preg_replace('/\s+/u', ' ', $text);
    return trim((string) ($normalized ?? $text));
}

function zapsign_human_error_message(array $response): string
{
    $httpCode = (int) ($response['http_code'] ?? 0);
    $message = trim((string) ($response['message'] ?? 'Falha sem detalhe.'));
    $messageLower = mb_strtolower($message, 'UTF-8');

    if ($httpCode === 402 && str_contains($messageLower, 'plano de api')) {
        return 'Nao foi possivel criar o documento no ZapSign. A conta/token informados ainda nao possuem Plano de API ativo no ambiente de producao da ZapSign. Para testes, use token + endpoint sandbox; para producao, a conta precisa ter o Plano de API habilitado.';
    }

    return 'Nao foi possivel criar o documento no ZapSign. ' . $message;
}

function zapsign_markdown_from_proposal(array $proposal, array $payload, array $settings): string
{
    $acceptance = proposal_acceptance_render_data($proposal, $payload, $settings);
    $files = proposal_file_entries($payload);
    $scopeEntries = proposal_scope_entries($payload);
    $stages = proposal_timeline_entries($payload);
    $paymentSchedule = proposal_payment_schedule_entries($payload);
    $considerations = array_values(normalize_topic_lines($payload['consideracoes'] ?? []));
    $exclusions = array_values(normalize_topic_lines($payload['exclusoes'] ?? []));
    $publicUrl = proposal_public_url($proposal);
    $lines = [];
    $lines[] = '# ' . trim((string) ($acceptance['title'] ?? 'Documento de aceite'));
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
    $clientCompany = trim((string) ($payload['cliente_empresa'] ?? ''));
    if ($clientCompany !== '') {
        $lines[] = '- Empresa do cliente: ' . $clientCompany;
    }
    $clientCnpj = trim((string) ($payload['cliente_cnpj'] ?? ''));
    if ($clientCnpj !== '') {
        $lines[] = '- CNPJ do cliente: ' . $clientCnpj;
    }
    $obra = trim((string) ($payload['obra_nome'] ?? ''));
    if ($obra !== '') {
        $lines[] = '- Obra: ' . $obra;
    }
    $endereco = trim((string) ($payload['obra_endereco'] ?? ''));
    if ($endereco !== '') {
        $lines[] = '- Endereco: ' . $endereco;
    }

    $acceptanceMode = (string) ($acceptance['mode'] ?? 'contract');
    $documentBodyText = strip_html_to_plaintext((string) ($acceptance['body_html'] ?? ''));

    $lines[] = '';
    $lines[] = $acceptanceMode === 'summary' ? '## Resumo da proposta' : '## Termos do contrato';
    if ($documentBodyText !== '') {
        foreach (preg_split("/\n+/", $documentBodyText) ?: [] as $paragraph) {
            $paragraph = trim((string) $paragraph);
            if ($paragraph !== '') {
                $lines[] = $paragraph;
            }
        }
    } else {
        $lines[] = $acceptanceMode === 'summary'
            ? 'Resumo conforme configuração da proposta.'
            : 'Contrato conforme configuração da proposta.';
    }

    if ($acceptanceMode === 'summary') {
        $lines[] = '';
        $lines[] = 'Ao assinar este documento, as partes concordam com o resumo desta proposta comercial.';
        if ($publicUrl) {
            $lines[] = '';
            $lines[] = 'Link da proposta original: ' . $publicUrl;
        }
        return implode("\n", $lines);
    }

    $lines[] = '';
    $lines[] = '## Escopo da proposta';
    foreach ($scopeEntries as $entry) {
        $lines[] = '- ' . (string) ($entry['title'] ?? 'Disciplina');
        foreach ((array) ($entry['topics'] ?? []) as $topic) {
            $topic = trim((string) $topic);
            if ($topic !== '') {
                $lines[] = '  - ' . $topic;
            }
        }
        if (((array) ($entry['topics'] ?? [])) === [] && trim((string) ($entry['summary'] ?? '')) !== '') {
            $lines[] = '  - ' . trim((string) ($entry['summary'] ?? ''));
        }
    }

    if ($files !== []) {
        $lines[] = '';
        $lines[] = '## Arquivos recebidos';
        foreach ($files as $file) {
            $label = trim((string) ($file['item'] ?? '') . ' ' . (string) ($file['name'] ?? ''));
            $date = trim((string) ($file['date'] ?? ''));
            $lines[] = '- ' . trim($label . ($date !== '' ? ' - ' . $date : ''));
        }
    }

    if ($stages !== []) {
        $lines[] = '';
        $lines[] = '## Etapas e prazos';
        foreach ($stages as $stage) {
            $badge = trim((string) ($stage['badge'] ?? ''));
            $line = '- ' . trim((string) ($stage['name'] ?? 'Etapa'));
            if ($badge !== '') {
                $line .= ' (' . $badge . ')';
            }
            $lines[] = $line;
            $description = trim((string) ($stage['description'] ?? ''));
            if ($description !== '') {
                $lines[] = '  - ' . $description;
            }
        }
    }

    $total = proposal_total($payload);
    $lines[] = '';
    $lines[] = '## Valor total';
    $lines[] = '- ' . brl($total) . ' (' . currency_to_words_ptbr($total) . ')';

    if ($paymentSchedule !== []) {
        $lines[] = '';
        $lines[] = '## Forma de pagamento';
        foreach ($paymentSchedule as $entry) {
            if (($entry['type'] ?? 'line') === 'subtitle') {
                $lines[] = '- ' . trim((string) ($entry['label'] ?? 'Grupo'));
                continue;
            }
            $lines[] = '- ' . trim((string) ($entry['label'] ?? 'Parcela')) . ': ' . brl((float) ($entry['amount'] ?? 0));
        }
    }

    if ($considerations !== []) {
        $lines[] = '';
        $lines[] = '## Consideracoes';
        foreach ($considerations as $item) {
            $item = trim((string) $item);
            if ($item !== '') {
                $lines[] = '- ' . $item;
            }
        }
    }

    if ($exclusions !== []) {
        $lines[] = '';
        $lines[] = '## Itens fora do escopo';
        foreach ($exclusions as $item) {
            $item = trim((string) $item);
            if ($item !== '') {
                $lines[] = '- ' . $item;
            }
        }
    }

    if ($publicUrl) {
        $lines[] = '';
        $lines[] = '## Link da proposta original';
        $lines[] = $publicUrl;
    }

    $lines[] = '';
    $lines[] = 'Ao assinar este documento, as partes concordam com os termos deste contrato e com o escopo da proposta comercial.';

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
