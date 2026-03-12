<?php

declare(strict_types=1);

function default_proposal_payload(): array
{
    $today = new DateTimeImmutable('today');
    $proposalCode = 'P' . $today->format('ymd') . '-' . random_int(100, 999);

    return [
        'codigo_base' => $proposalCode,
        'revisao' => '00',
        'titulo' => 'Proposta Comercial',
        'data_proposta' => $today->format('Y-m-d'),
        'cliente_nome' => '',
        'cliente_empresa' => '',
        'cliente_cnpj' => '',
        'cliente_email' => '',
        'cliente_telefone' => '',
        'cliente_endereco' => '',
        'cliente_cidade' => '',
        'cliente_uf' => '',
        'cliente_cep' => '',
        'obra_nome' => '',
        'obra_endereco' => '',
        'obra_cidade' => '',
        'obra_uf' => '',
        'finalidade_obra' => '',
        'descricao_objeto' => '',
        'prazo_dias' => 7,
        'validade_dias' => 10,
        'disciplinas' => ['eletrica', 'hidraulica', 'esgoto'],
        'valores' => [
            'eletrica' => 0,
            'hidraulica' => 0,
            'esgoto' => 0,
            'gas' => 0,
            'especiais' => 0,
        ],
        'disciplinas_custom' => [],
        'valores_custom' => [],
        'etapas' => [
            ['nome' => 'Sinal e kick-off', 'percentual' => 20, 'descricao' => 'Assinatura e início'],
            ['nome' => 'Projeto básico', 'percentual' => 30, 'descricao' => 'Entrega preliminar'],
            ['nome' => 'Projeto executivo', 'percentual' => 30, 'descricao' => 'Entrega executiva'],
            ['nome' => 'Aprovação final', 'percentual' => 20, 'descricao' => 'Encerramento'],
        ],
        'arquivos' => [
            ['item' => 'ARQ-01', 'nome' => 'Memorial descritivo', 'rev' => '00', 'link' => ''],
            ['item' => 'ARQ-02', 'nome' => 'Projeto básico', 'rev' => '00', 'link' => ''],
            ['item' => 'ARQ-03', 'nome' => 'Projeto executivo', 'rev' => '00', 'link' => ''],
        ],
        'consideracoes' => [
            'A aprovação em concessionárias depende dos prazos de análise dos órgãos.',
            'Escopo considera somente disciplinas selecionadas nesta proposta.',
            'Alterações após aceite podem gerar custos adicionais.',
        ],
        'exclusoes' => [
            'Execução física da obra.',
            'Fornecimento de materiais e equipamentos.',
            'Taxas e emolumentos de concessionárias.',
        ],
        'pagamento_cartao_ativo' => false,
        'pagamento_cartao_titulo' => 'Pagamento por cartao',
        'pagamento_cartao_descricao' => 'Use o link seguro para pagar no cartao de credito.',
        'pagamento_cartao_link' => '',
        'pagamento_cartao_botao' => 'Pagar no cartao',
        'pagamento_boleto_ativo' => false,
        'pagamento_boleto_titulo' => 'Pagamento por boleto',
        'pagamento_boleto_descricao' => 'Use o link para emitir ou visualizar o boleto.',
        'pagamento_boleto_link' => '',
        'pagamento_boleto_botao' => 'Abrir boleto',
        'observacoes' => '',
        'zapsign_sign_url' => '',
    ];
}

function discipline_catalog(): array
{
    return [
        'eletrica' => [
            'nome' => 'Elétrica',
            'icone' => '/assets/img/icon-eletrica.png',
            'descricao' => 'Projeto de instalações elétricas, cargas, quadros e detalhamentos técnicos.',
        ],
        'hidraulica' => [
            'nome' => 'Hidráulica',
            'icone' => '/assets/img/icon-hidraulica.png',
            'descricao' => 'Projeto de água fria/quente, distribuição, barrilete e pontos de consumo.',
        ],
        'esgoto' => [
            'nome' => 'Esgoto',
            'icone' => '/assets/img/icon-esgoto.png',
            'descricao' => 'Projeto sanitário, ventilação e encaminhamento para rede pública.',
        ],
        'gas' => [
            'nome' => 'Gás',
            'icone' => '/assets/img/icon-gas.png',
            'descricao' => 'Projeto de gás conforme normas aplicáveis e segurança operacional.',
        ],
        'especiais' => [
            'nome' => 'Especiais',
            'icone' => '/assets/img/icon-especiais.png',
            'descricao' => 'Soluções especiais como SPDA, incêndio e sistemas complementares.',
        ],
    ];
}

function normalize_proposal_payload(array $input, ?array $base = null): array
{
    $defaults = default_proposal_payload();
    $payload = $base ? array_replace_recursive($defaults, $base) : $defaults;

    $stringFields = [
        'codigo_base',
        'revisao',
        'titulo',
        'data_proposta',
        'cliente_nome',
        'cliente_empresa',
        'cliente_cnpj',
        'cliente_email',
        'cliente_telefone',
        'cliente_endereco',
        'cliente_cidade',
        'cliente_uf',
        'cliente_cep',
        'obra_nome',
        'obra_endereco',
        'obra_cidade',
        'obra_uf',
        'finalidade_obra',
        'descricao_objeto',
        'pagamento_cartao_titulo',
        'pagamento_cartao_descricao',
        'pagamento_cartao_link',
        'pagamento_cartao_botao',
        'pagamento_boleto_titulo',
        'pagamento_boleto_descricao',
        'pagamento_boleto_link',
        'pagamento_boleto_botao',
        'observacoes',
        'zapsign_sign_url',
    ];

    foreach ($stringFields as $field) {
        if (array_key_exists($field, $input)) {
            $payload[$field] = trim((string) $input[$field]);
        }
    }

    $payload['pagamento_cartao_ativo'] = isset($input['pagamento_cartao_ativo']) && (string) $input['pagamento_cartao_ativo'] !== '0';
    $payload['pagamento_boleto_ativo'] = isset($input['pagamento_boleto_ativo']) && (string) $input['pagamento_boleto_ativo'] !== '0';

    $payload['prazo_dias'] = (int) clamp((int) ($input['prazo_dias'] ?? $payload['prazo_dias']), 1, 365);
    $payload['validade_dias'] = (int) clamp((int) ($input['validade_dias'] ?? $payload['validade_dias']), 1, 365);

    $catalog = discipline_catalog();
    $disciplines = $input['disciplinas'] ?? $payload['disciplinas'];
    if (!is_array($disciplines)) {
        $disciplines = [];
    }
    $disciplines = array_values(array_filter($disciplines, static fn ($value) => isset($catalog[$value])));
    $payload['disciplinas'] = array_values(array_unique($disciplines));

    $values = [];
    foreach (array_keys($catalog) as $key) {
        $raw = $input['valores'][$key] ?? $payload['valores'][$key] ?? 0;
        $values[$key] = round(to_float($raw), 2);
    }
    $payload['valores'] = $values;

    $customDisciplinesInput = $input['disciplinas_custom'] ?? $payload['disciplinas_custom'] ?? [];
    if (!is_array($customDisciplinesInput)) {
        $customDisciplinesInput = [];
    }
    $customDisciplines = [];
    $customValues = [];
    foreach ($customDisciplinesInput as $index => $item) {
        if (!is_array($item)) {
            continue;
        }
        $nome = trim((string) ($item['nome'] ?? ''));
        if ($nome === '') {
            continue;
        }
        $keyRaw = trim((string) ($item['key'] ?? ''));
        $key = normalize_custom_discipline_key($keyRaw !== '' ? $keyRaw : $nome);
        if ($key === '') {
            $key = 'custom-' . ((int) $index + 1);
        }
        $descricao = trim((string) ($item['descricao'] ?? ''));
        $icone = trim((string) ($item['icone'] ?? ''));
        $valor = round(to_float($item['valor'] ?? 0), 2);
        $ativa = isset($item['ativa']) && (string) $item['ativa'] !== '0';

        $customDisciplines[] = [
            'key' => $key,
            'nome' => $nome,
            'descricao' => $descricao,
            'icone' => $icone,
            'valor' => $valor,
            'ativa' => $ativa,
        ];
        $customValues[$key] = $valor;
    }
    $payload['disciplinas_custom'] = array_slice($customDisciplines, 0, 24);
    $payload['valores_custom'] = $customValues;

    if ($payload['disciplinas'] === []) {
        $hasActiveCustom = false;
        foreach ($payload['disciplinas_custom'] as $customItem) {
            if ((bool) ($customItem['ativa'] ?? false)) {
                $hasActiveCustom = true;
                break;
            }
        }
        if (!$hasActiveCustom) {
            $payload['disciplinas'] = ['eletrica'];
        }
    }

    $stages = $input['etapas'] ?? $payload['etapas'];
    if (!is_array($stages)) {
        $stages = [];
    }
    $normalizedStages = [];
    foreach ($stages as $stage) {
        if (!is_array($stage)) {
            continue;
        }
        $name = trim((string) ($stage['nome'] ?? ''));
        $percentual = (int) clamp((int) ($stage['percentual'] ?? 0), 0, 100);
        $descricao = trim((string) ($stage['descricao'] ?? ''));
        if ($name === '') {
            continue;
        }
        $normalizedStages[] = ['nome' => $name, 'percentual' => $percentual, 'descricao' => $descricao];
    }
    if ($normalizedStages === []) {
        $normalizedStages = $payload['etapas'];
    }
    $payload['etapas'] = array_slice($normalizedStages, 0, 24);

    $files = $input['arquivos'] ?? $payload['arquivos'];
    if (!is_array($files)) {
        $files = [];
    }
    $normalizedFiles = [];
    foreach ($files as $file) {
        if (!is_array($file)) {
            continue;
        }
        $item = trim((string) ($file['item'] ?? ''));
        $nome = trim((string) ($file['nome'] ?? ''));
        $rev = trim((string) ($file['rev'] ?? ''));
        $link = trim((string) ($file['link'] ?? ''));
        if ($item === '' && $nome === '') {
            continue;
        }
        $normalizedFiles[] = [
            'item' => $item !== '' ? $item : 'ARQ',
            'nome' => $nome !== '' ? $nome : 'Arquivo',
            'rev' => $rev,
            'link' => $link,
        ];
    }
    $payload['arquivos'] = array_slice($normalizedFiles, 0, 40);

    $consideracoesInput = $input['consideracoes'] ?? $payload['consideracoes'];
    if (is_string($consideracoesInput)) {
        $consideracoesInput = parse_multilines($consideracoesInput);
    }
    if (!is_array($consideracoesInput)) {
        $consideracoesInput = [];
    }
    $consideracoes = [];
    foreach ($consideracoesInput as $line) {
        $line = trim((string) $line);
        if ($line !== '') {
            $consideracoes[] = $line;
        }
    }
    $payload['consideracoes'] = array_slice($consideracoes, 0, 20);

    $exclusoesInput = $input['exclusoes'] ?? $payload['exclusoes'];
    if (is_string($exclusoesInput)) {
        $exclusoesInput = parse_multilines($exclusoesInput);
    }
    if (!is_array($exclusoesInput)) {
        $exclusoesInput = [];
    }
    $exclusoes = [];
    foreach ($exclusoesInput as $line) {
        $line = trim((string) $line);
        if ($line !== '') {
            $exclusoes[] = $line;
        }
    }
    $payload['exclusoes'] = array_slice($exclusoes, 0, 40);

    return $payload;
}

function proposal_total(array $payload): float
{
    $total = 0.0;
    $baseDisciplines = $payload['disciplinas'] ?? [];
    if (!is_array($baseDisciplines)) {
        $baseDisciplines = [];
    }
    foreach ($baseDisciplines as $disciplineKey) {
        $total += (float) ($payload['valores'][$disciplineKey] ?? 0);
    }

    $customDisciplines = $payload['disciplinas_custom'] ?? [];
    if (!is_array($customDisciplines)) {
        $customDisciplines = [];
    }
    foreach ($customDisciplines as $customItem) {
        if (!is_array($customItem) || !(bool) ($customItem['ativa'] ?? false)) {
            continue;
        }
        $total += (float) ($customItem['valor'] ?? 0);
    }

    return round($total, 2);
}

function save_proposal(array $payload, ?int $id = null, ?array $actor = null): int
{
    $now = now_iso();
    $total = proposal_total($payload);
    $totalWords = currency_to_words_ptbr($total);
    $payloadJson = json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    $actorId = (int) ($actor['id'] ?? 0);
    $actorName = trim((string) ($actor['name'] ?? ''));

    if ($id === null) {
        $row = db_insert('proposals', [
            'code' => $payload['codigo_base'],
            'revision' => $payload['revisao'],
            'status' => 'draft',
            'title' => $payload['titulo'],
            'client_name' => $payload['cliente_nome'],
            'client_company' => $payload['cliente_empresa'],
            'client_cnpj' => $payload['cliente_cnpj'],
            'client_email' => $payload['cliente_email'],
            'client_phone' => $payload['cliente_telefone'],
            'obra_nome' => $payload['obra_nome'],
            'obra_endereco' => $payload['obra_endereco'],
            'obra_cidade' => $payload['obra_cidade'],
            'obra_uf' => mb_strtoupper($payload['obra_uf'], 'UTF-8'),
            'prazo_dias' => $payload['prazo_dias'],
            'validade_dias' => $payload['validade_dias'],
            'total_value' => $total,
            'total_value_extenso' => $totalWords,
            'payload_json' => $payloadJson,
            'token' => null,
            'zapsign_doc_id' => '',
            'zapsign_sign_url' => $payload['zapsign_sign_url'],
            'created_by_admin_id' => $actorId > 0 ? $actorId : null,
            'created_by_admin_name' => $actorName,
            'last_edited_by_admin_id' => $actorId > 0 ? $actorId : null,
            'last_edited_by_admin_name' => $actorName,
            'edited_after_create' => 0,
            'published_at' => null,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        return (int) $row['id'];
    }

    $existing = db_find('proposals', $id);
    if (!$existing) {
        return save_proposal($payload, null, $actor);
    }

    $lastEditedById = $actorId > 0
        ? $actorId
        : (int) ($existing['last_edited_by_admin_id'] ?? ($existing['created_by_admin_id'] ?? 0));
    $lastEditedByName = $actorName !== ''
        ? $actorName
        : trim((string) ($existing['last_edited_by_admin_name'] ?? ($existing['created_by_admin_name'] ?? '')));
    $createdById = (int) ($existing['created_by_admin_id'] ?? 0);
    $createdByName = trim((string) ($existing['created_by_admin_name'] ?? ''));
    if ($createdById <= 0 && $actorId > 0) {
        $createdById = $actorId;
    }
    if ($createdByName === '' && $actorName !== '') {
        $createdByName = $actorName;
    }

    db_update('proposals', $id, [
        'code' => $payload['codigo_base'],
        'revision' => $payload['revisao'],
        'title' => $payload['titulo'],
        'client_name' => $payload['cliente_nome'],
        'client_company' => $payload['cliente_empresa'],
        'client_cnpj' => $payload['cliente_cnpj'],
        'client_email' => $payload['cliente_email'],
        'client_phone' => $payload['cliente_telefone'],
        'obra_nome' => $payload['obra_nome'],
        'obra_endereco' => $payload['obra_endereco'],
        'obra_cidade' => $payload['obra_cidade'],
        'obra_uf' => mb_strtoupper($payload['obra_uf'], 'UTF-8'),
        'prazo_dias' => $payload['prazo_dias'],
        'validade_dias' => $payload['validade_dias'],
        'total_value' => $total,
        'total_value_extenso' => $totalWords,
        'payload_json' => $payloadJson,
        'zapsign_sign_url' => $payload['zapsign_sign_url'],
        'created_by_admin_id' => $createdById > 0 ? $createdById : null,
        'created_by_admin_name' => $createdByName,
        'last_edited_by_admin_id' => $lastEditedById > 0 ? $lastEditedById : null,
        'last_edited_by_admin_name' => $lastEditedByName,
        'edited_after_create' => 1,
        'updated_at' => $now,
    ]);

    return $id;
}

function get_proposal_by_id(int $id): ?array
{
    $proposal = db_find('proposals', $id);
    if (!$proposal) {
        return null;
    }
    $proposal = normalize_proposal_audit_fields($proposal);
    $proposal['payload'] = decode_payload($proposal['payload_json']);
    return $proposal;
}

function get_proposal_by_token(string $token): ?array
{
    $proposal = db_first('proposals', static fn (array $row): bool => (string) ($row['token'] ?? '') === $token);
    if (!$proposal) {
        return null;
    }
    $proposal = normalize_proposal_audit_fields($proposal);
    $proposal['payload'] = decode_payload($proposal['payload_json']);
    return $proposal;
}

function decode_payload(string $payloadJson): array
{
    $payload = json_decode($payloadJson, true);
    if (!is_array($payload)) {
        return default_proposal_payload();
    }
    return normalize_proposal_payload($payload, $payload);
}

function list_proposals_with_metrics(): array
{
    $rows = db_all('proposals');
    $allViews = db_all('proposal_views');

    $viewMap = [];
    foreach ($allViews as $view) {
        $proposalId = (int) ($view['proposal_id'] ?? 0);
        if (!isset($viewMap[$proposalId])) {
            $viewMap[$proposalId] = [
                'total_views' => 0,
                'ips' => [],
                'sum_time' => 0.0,
                'max_scroll' => 0.0,
                'last_view_at' => null,
            ];
        }

        $viewMap[$proposalId]['total_views'] += 1;
        $viewMap[$proposalId]['sum_time'] += (float) ($view['total_time_seconds'] ?? 0);
        $viewMap[$proposalId]['ips'][(string) ($view['ip'] ?? '')] = true;
        $viewMap[$proposalId]['max_scroll'] = max($viewMap[$proposalId]['max_scroll'], (float) ($view['max_scroll'] ?? 0));
        $lastSeen = (string) ($view['last_seen_at'] ?? '');
        if ($lastSeen !== '' && ($viewMap[$proposalId]['last_view_at'] === null || $lastSeen > $viewMap[$proposalId]['last_view_at'])) {
            $viewMap[$proposalId]['last_view_at'] = $lastSeen;
        }
    }

    foreach ($rows as &$row) {
        $row = normalize_proposal_audit_fields($row);
        $row['payload'] = decode_payload($row['payload_json']);
        $metrics = $viewMap[(int) $row['id']] ?? null;
        if ($metrics === null) {
            $row['total_views'] = 0;
            $row['unique_ips'] = 0;
            $row['avg_time'] = 0;
            $row['max_scroll'] = 0;
            $row['last_view_at'] = null;
        } else {
            $totalViews = max(1, (int) $metrics['total_views']);
            $row['total_views'] = (int) $metrics['total_views'];
            $row['unique_ips'] = count($metrics['ips']);
            $row['avg_time'] = $metrics['sum_time'] / $totalViews;
            $row['max_scroll'] = $metrics['max_scroll'];
            $row['last_view_at'] = $metrics['last_view_at'];
        }
    }
    unset($row);

    usort(
        $rows,
        static fn (array $a, array $b): int => strcmp((string) ($b['updated_at'] ?? ''), (string) ($a['updated_at'] ?? ''))
    );

    return $rows;
}

function publish_proposal(int $id): ?array
{
    $proposal = get_proposal_by_id($id);
    if (!$proposal) {
        return null;
    }

    $token = $proposal['token'] ?: random_token(24);
    $now = now_iso();

    db_update('proposals', $id, [
        'token' => $token,
        'status' => 'published',
        'published_at' => $now,
        'updated_at' => $now,
    ]);

    return get_proposal_by_id($id);
}

function duplicate_proposal(int $id, ?array $actor = null): ?int
{
    $source = get_proposal_by_id($id);
    if (!$source) {
        return null;
    }

    $payload = $source['payload'];
    $payload['codigo_base'] = $payload['codigo_base'] . '-C' . random_int(10, 99);
    $payload['revisao'] = '00';

    $newId = save_proposal($payload, null, $actor);

    return $newId;
}

function normalize_proposal_audit_fields(array $proposal): array
{
    $createdById = (int) ($proposal['created_by_admin_id'] ?? 0);
    $createdByName = trim((string) ($proposal['created_by_admin_name'] ?? ''));
    if ($createdByName === '' && $createdById > 0) {
        $admin = db_find('admins', $createdById);
        $createdByName = trim((string) ($admin['name'] ?? ''));
    }

    $lastEditedById = (int) ($proposal['last_edited_by_admin_id'] ?? 0);
    $lastEditedByName = trim((string) ($proposal['last_edited_by_admin_name'] ?? ''));
    if ($lastEditedByName === '' && $lastEditedById > 0) {
        $admin = db_find('admins', $lastEditedById);
        $lastEditedByName = trim((string) ($admin['name'] ?? ''));
    }
    if ($lastEditedByName === '') {
        $lastEditedByName = $createdByName;
    }

    $editedFlag = $proposal['edited_after_create'] ?? null;
    if ($editedFlag === null || $editedFlag === '') {
        $editedFlag = 0;
    }

    $proposal['created_by_admin_id'] = $createdById > 0 ? $createdById : null;
    $proposal['created_by_admin_name'] = $createdByName !== '' ? $createdByName : 'Não informado';
    $proposal['last_edited_by_admin_id'] = $lastEditedById > 0 ? $lastEditedById : null;
    $proposal['last_edited_by_admin_name'] = $lastEditedByName;
    $proposal['edited_after_create'] = (int) $editedFlag === 1 ? 1 : 0;

    return $proposal;
}

function proposal_public_url(array $proposal): ?string
{
    if (empty($proposal['token'])) {
        return null;
    }

    return app_url('/p/' . $proposal['token']);
}

function proposal_print_url(array $proposal): ?string
{
    if (empty($proposal['token'])) {
        return null;
    }

    return app_url('/p/' . $proposal['token'] . '/print');
}

function get_dashboard_stats(): array
{
    $totals = [
        'total_proposals' => 0,
        'published' => 0,
        'signed' => 0,
        'views' => 0,
        'avg_scroll' => 0,
    ];

    $proposals = db_all('proposals');
    $views = db_all('proposal_views');

    $totals['total_proposals'] = count($proposals);
    foreach ($proposals as $proposal) {
        $status = (string) ($proposal['status'] ?? 'draft');
        if (in_array($status, ['published', 'viewed', 'signing', 'signed'], true)) {
            $totals['published'] += 1;
        }
        if ($status === 'signed') {
            $totals['signed'] += 1;
        }
    }

    $totals['views'] = count($views);
    if ($totals['views'] > 0) {
        $sumScroll = 0.0;
        foreach ($views as $view) {
            $sumScroll += (float) ($view['max_scroll'] ?? 0);
        }
        $totals['avg_scroll'] = round($sumScroll / $totals['views'], 1);
    }

    return $totals;
}

function get_proposal_metrics(int $proposalId): array
{
    $stats = [
        'views' => 0,
        'avg_time_seconds' => 0,
        'max_scroll' => 0,
        'avg_scroll' => 0,
        'downloads' => 0,
        'sign_clicks' => 0,
        'accepted' => 0,
        'sections' => [],
        'devices' => ['desktop' => 0, 'mobile' => 0, 'tablet' => 0],
    ];

    $views = db_where('proposal_views', static fn (array $row): bool => (int) ($row['proposal_id'] ?? 0) === $proposalId);
    $stats['views'] = count($views);
    if ($stats['views'] > 0) {
        $sumTime = 0.0;
        $sumScroll = 0.0;
        foreach ($views as $view) {
            $sumTime += (float) ($view['total_time_seconds'] ?? 0);
            $scroll = (float) ($view['max_scroll'] ?? 0);
            $sumScroll += $scroll;
            $stats['max_scroll'] = max($stats['max_scroll'], $scroll);

            if (!empty($view['downloaded_pdf_at'])) {
                $stats['downloads'] += 1;
            }
            if (!empty($view['clicked_sign_at'])) {
                $stats['sign_clicks'] += 1;
            }
            if (!empty($view['accepted_at'])) {
                $stats['accepted'] += 1;
            }

            $device = (string) ($view['device'] ?? '');
            if (isset($stats['devices'][$device])) {
                $stats['devices'][$device] += 1;
            }
        }
        $stats['avg_time_seconds'] = (int) round($sumTime / $stats['views']);
        $stats['avg_scroll'] = $sumScroll / $stats['views'];
    }

    $sections = [];
    foreach ($views as $view) {
        $times = json_decode((string) $view['section_times_json'], true);
        if (!is_array($times)) {
            continue;
        }
        foreach ($times as $key => $seconds) {
            if (!isset($sections[$key])) {
                $sections[$key] = 0;
            }
            $sections[$key] += (int) $seconds;
        }
    }
    arsort($sections);
    $stats['sections'] = $sections;

    return $stats;
}

function update_proposal_record(int $proposalId, array $changes): ?array
{
    return db_update('proposals', $proposalId, $changes);
}

function render_proposal_template_html(array $proposal, array $payload, array $settings, bool $previewMode = false): string
{
    $templatePath = base_path('propostabase.html');
    if (!file_exists($templatePath)) {
        return '<h1>Template de proposta não encontrado.</h1>';
    }

    $template = file_get_contents($templatePath);
    if (!is_string($template) || trim($template) === '') {
        return '<h1>Template de proposta vazio.</h1>';
    }
    $template = apply_clarity_head_script($template, $settings);

    $template = str_replace(
        [
            "fonts/Ethnocentric-Regular.otf",
            "fonts/Conthrax-SemiBold.otf",
            "logo-complementare.png",
            "empresa.png",
        ],
        [
            "/assets/fonts/ethnocentric.otf",
            "/assets/fonts/conthrax-sb.otf",
            "/assets/img/logo-complementare.png",
            "/assets/img/empresa.png",
        ],
        $template
    );

    $headPatch = '<style>.nav-logo img{filter:brightness(0) saturate(100%) invert(23%) sepia(40%) saturate(755%) hue-rotate(151deg) brightness(93%) contrast(90%);} .hero .nav-logo img{filter:none;}</style>';
    $template = str_replace('</head>', $headPatch . '</head>', $template);

    $replaceMap = build_proposal_placeholder_map($proposal, $payload, $settings);
    foreach ($replaceMap as $key => $value) {
        $template = str_replace('{{' . $key . '}}', $value, $template);
    }
    $template = str_replace('{ANO}', (string) date('Y'), $template);
    $template = preg_replace('/\{\{[A-Z0-9_]+\}\}/', '', $template) ?? $template;
    $template = apply_accept_terms_customization($template, $settings, $replaceMap);

    $template = preg_replace_callback(
        '/<section class="section section-teal" id="exclusoes">.*?<\/section>/si',
        static function (array $matches): string {
            $section = $matches[0];
            $section = preg_replace(
                '/<div class="exclusion-line[^"]*"[^>]*>\s*<div class="exclusion-letter">[^<]*<\/div>\s*<div class="exclusion-text">\s*<\/div>\s*<\/div>/si',
                '',
                $section
            ) ?? $section;

            if (!preg_match('/<div class="exclusion-line[^"]*"[^>]*>/si', $section)) {
                return '';
            }
            return $section;
        },
        $template
    ) ?? $template;

    $token = (string) ($proposal['token'] ?? '');
    $downloadUrl = $token !== '' ? '/p/' . $token . '/print' : '#';
    $signUrl = $token !== '' ? '/p/' . $token . '/sign' : '#';
    $downloadTarget = $previewMode ? '' : ' target="_blank"';

    $template = preg_replace(
        '/<a href=\"#\" class=\"download-button\">/i',
        '<a href="' . h($downloadUrl) . '" class="download-button" id="download-pdf-button"' . $downloadTarget . '>',
        $template,
        1
    ) ?? $template;

    $template = preg_replace(
        '/<button class=\"accept-button\" onclick=\"openModal\(\)\">/i',
        '<button class="accept-button" id="accept-proposal-button" onclick="openModal()">',
        $template,
        1
    ) ?? $template;

    $selectedDisciplines = $payload['disciplinas'] ?? [];
    if (!is_array($selectedDisciplines)) {
        $selectedDisciplines = [];
    }
    $selectedDisciplines = array_values(array_unique(array_filter($selectedDisciplines, 'is_string')));

    $customDisciplines = [];
    $customInput = $payload['disciplinas_custom'] ?? [];
    if (is_array($customInput)) {
        foreach ($customInput as $item) {
            if (!is_array($item)) {
                continue;
            }
            $nome = trim((string) ($item['nome'] ?? ''));
            if ($nome === '') {
                continue;
            }
            $customDisciplines[] = [
                'key' => (string) ($item['key'] ?? ''),
                'nome' => $nome,
                'descricao' => trim((string) ($item['descricao'] ?? '')),
                'icone' => trim((string) ($item['icone'] ?? '')),
                'valor' => (float) ($item['valor'] ?? 0),
                'ativa' => (bool) ($item['ativa'] ?? false),
            ];
        }
    }

    $timelineStages = [];
    $stageInput = $payload['etapas'] ?? [];
    if (is_array($stageInput)) {
        foreach ($stageInput as $index => $item) {
            if (!is_array($item)) {
                continue;
            }
            $nome = trim((string) ($item['nome'] ?? ''));
            if ($nome === '') {
                continue;
            }
            $pct = (int) clamp((int) ($item['percentual'] ?? 0), 0, 100);
            $badge = $pct > 0 ? $pct . '%' : 'Etapa';
            $timelineStages[] = [
                'nome' => $nome,
                'descricao' => trim((string) ($item['descricao'] ?? '')),
                'badge' => $badge,
                'ordem' => ((int) $index + 1) . 'a',
            ];
        }
    }
    if ($timelineStages === []) {
        $timelineStages = [
            ['nome' => 'Concepção', 'descricao' => 'Definição inicial do escopo.', 'badge' => '10 dias', 'ordem' => '1ª'],
            ['nome' => 'Estudo Preliminar', 'descricao' => 'Diretrizes e validação inicial.', 'badge' => '15 dias', 'ordem' => '2ª'],
            ['nome' => 'Projeto Básico', 'descricao' => 'Consolidação técnica.', 'badge' => '10 dias', 'ordem' => '3ª'],
            ['nome' => 'Projeto Executivo', 'descricao' => 'Entrega final do detalhamento.', 'badge' => 'Final', 'ordem' => '4ª'],
        ];
    }

    $runtimeScript = proposal_runtime_script(
        (string) $token,
        $downloadUrl,
        $signUrl,
        $selectedDisciplines,
        $customDisciplines,
        $timelineStages,
        $previewMode,
        $settings
    );

    if ($previewMode) {
        $previewBar = '<div style="position:sticky;top:0;z-index:99999;background:#ffe79f;color:#5f4600;padding:8px 12px;font:700 12px/1.2 Arial,sans-serif;text-align:center;border-bottom:1px solid #e9cc78;">Pré-visualização interna - tracking e redirecionamento de assinatura desativados.</div>';
        $template = str_replace('<body>', '<body>' . $previewBar, $template);
    }

    return str_replace('</body>', $runtimeScript . '</body>', $template);
}

function acceptance_terms_variable_catalog(): array
{
    $sampleProposal = [
        'code' => 'P000000-000',
        'revision' => '00',
    ];
    $keys = array_keys(build_proposal_placeholder_map($sampleProposal, default_proposal_payload(), default_settings_values()));

    $knownDescriptions = [
        'PROPOSTA_NUM' => 'Código da proposta (ex: P260311-293)',
        'CODIGO_BASE' => 'Código base da proposta',
        'REVISAO' => 'Revisão atual (vazio quando 00)',
        'NOME_CLIENTE' => 'Nome do cliente',
        'CONTRATANTE_RAZAO' => 'Razão/nome do contratante',
        'CONTRATANTE_CNPJ' => 'CNPJ do contratante',
        'CONTRATANTE_EMAIL' => 'E-mail do contratante',
        'CONTRATANTE_ENDERECO' => 'Endereço do contratante',
        'CONTRATANTE_MUNICIPIO' => 'Cidade do contratante',
        'CONTRATANTE_UF' => 'UF do contratante',
        'CONTRATANTE_CEP' => 'CEP do contratante',
        'OBRA' => 'Nome da obra',
        'OBRA_ENDERECO_COMPLETO' => 'Endereço completo da obra',
        'DATA_PROPOSTA' => 'Data da proposta',
        'DATA_ASSINATURA' => 'Data de assinatura',
        'PRAZO_DIAS' => 'Prazo em dias',
        'VALIDADE_PROPOSTA' => 'Validade da proposta',
        'VALOR_TOTAL' => 'Valor total formatado (R$)',
        'VALOR_TOTAL_EXTENSO' => 'Valor por extenso',
        'PRECO_TOTAL' => 'Valor total numérico para contrato',
        'BANK_NAME' => 'Banco para pagamento via PIX',
        'BANK_AGENCY' => 'Agencia bancaria',
        'BANK_ACCOUNT' => 'Conta corrente bancaria',
        'BANK_FAVORED' => 'Favorecido da conta',
        'BANK_CNPJ' => 'CNPJ vinculado ao pagamento',
        'BANK_PIX_KEY' => 'Chave PIX',
        'BANK_PIX_KEY_TYPE' => 'Tipo da chave PIX',
        'PAYMENT_CARD_LINK' => 'Link de pagamento no cartao',
        'PAYMENT_BOLETO_LINK' => 'Link para emissao de boleto',
        'FORMA_PAGAMENTO_BASE' => 'Texto base da forma de pagamento',
        'FORMA_PAGAMENTO_EXTRA' => 'Texto extra com links adicionais',
    ];

    $catalog = [];
    foreach ($keys as $key) {
        $catalog[$key] = $knownDescriptions[$key] ?? 'Valor dinâmico da proposta.';
    }
    ksort($catalog);

    return $catalog;
}

function apply_accept_terms_customization(string $template, array $settings, array $replaceMap): string
{
    $tokens = [];
    foreach ($replaceMap as $key => $value) {
        $tokens['{{' . $key . '}}'] = (string) $value;
    }

    $renderTokens = static function (string $content) use ($tokens): string {
        return strtr($content, $tokens);
    };

    $titleTpl = trim((string) ($settings['accept_terms_title'] ?? ''));
    if ($titleTpl !== '') {
        $title = $renderTokens(h($titleTpl));
        $template = preg_replace_callback(
            '/(<div class="modal-header">\s*<h2>)(.*?)(<\/h2>)/si',
            static fn (array $m): string => $m[1] . $title . $m[3],
            $template,
            1
        ) ?? $template;
    }

    $checkboxDefault = (string) (default_settings_values()['accept_terms_checkbox_text'] ?? '');
    $checkboxTpl = trim((string) ($settings['accept_terms_checkbox_text'] ?? $checkboxDefault));
    if ($checkboxTpl === '') {
        $checkboxTpl = $checkboxDefault;
    }
    if ($checkboxTpl !== '') {
        $checkboxText = $renderTokens(h($checkboxTpl));
        $template = preg_replace_callback(
            '/(<label for="agree-terms">)(.*?)(<\/label>)/si',
            static fn (array $m): string => $m[1] . $checkboxText . $m[3],
            $template,
            1
        ) ?? $template;
    }

    $termsTpl = trim((string) ($settings['accept_terms_html'] ?? ''));
    if ($termsTpl !== '') {
        $hasHtmlTags = preg_match('/<[^>]+>/', $termsTpl) === 1;
        if ($hasHtmlTags) {
            $termsHtml = $renderTokens($termsTpl);
        } else {
            $termsHtml = '<p>' . nl2br($renderTokens(h($termsTpl))) . '</p>';
        }

        $template = preg_replace_callback(
            '/(<div class="contract-content[^"]*>\s*<div class="contract-body">)(.*?)(<\/div>\s*<\/div>)/si',
            static fn (array $m): string => $m[1] . $termsHtml . $m[3],
            $template,
            1
        ) ?? $template;
    }

    return $template;
}

function apply_clarity_head_script(string $template, array $settings): string
{
    $template = preg_replace(
        '/<script type="text\/javascript">\s*\(function\(c,l,a,r,i,t,y\)\{.*?clarity\.ms\/tag\/"\+i;.*?\}\)\(window,\s*document,\s*"clarity",\s*"script",\s*".*?"\);\s*<\/script>/si',
        '',
        $template
    ) ?? $template;

    $enabled = (int) ($settings['clarity_enabled'] ?? 0) === 1;
    $projectId = trim((string) ($settings['clarity_project_id'] ?? ''));
    if (!$enabled || $projectId === '') {
        return $template;
    }

    $projectIdJs = json_encode($projectId, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    if (!is_string($projectIdJs) || $projectIdJs === '') {
        return $template;
    }

    $script = <<<HTML
<script type="text/javascript">
    (function(c,l,a,r,i,t,y){
        c[a]=c[a]||function(){(c[a].q=c[a].q||[]).push(arguments)};
        t=l.createElement(r);t.async=1;t.src="https://www.clarity.ms/tag/"+i;
        y=l.getElementsByTagName(r)[0];y.parentNode.insertBefore(t,y);
    })(window, document, "clarity", "script", {$projectIdJs});
</script>
HTML;

    return str_replace('</head>', $script . PHP_EOL . '</head>', $template);
}

function build_proposal_placeholder_map(array $proposal, array $payload, array $settings): array
{
    $code = (string) ($payload['codigo_base'] ?? ($proposal['code'] ?? 'P-000'));
    $revRaw = trim((string) ($payload['revisao'] ?? ($proposal['revision'] ?? '00')));
    $rev = normalize_revision_for_display($revRaw);
    $revBadge = $rev !== '' ? ' - Revisao ' . $rev : '';
    $revBadgeUpper = $rev !== '' ? ' - REVISAO ' . $rev : '';
    $revTitle = $rev !== '' ? ' - Revisao ' . $rev : '';
    $revContract = $rev !== '' ? '(Revisao ' . $rev . ')' : '';
    $clientName = trim((string) ($payload['cliente_nome'] ?? ''));
    $clientCompany = trim((string) ($payload['cliente_empresa'] ?? ''));
    $clientDisplay = $clientName !== '' ? $clientName : ($clientCompany !== '' ? $clientCompany : 'Cliente');
    $proposalDate = parse_ymd((string) ($payload['data_proposta'] ?? date('Y-m-d')));

    $prazoDias = (int) ($payload['prazo_dias'] ?? 7);
    $validadeDias = (int) ($payload['validade_dias'] ?? 10);
    $prazoInicio = $proposalDate;
    $prazoFim = $proposalDate->modify('+' . max(0, $prazoDias) . ' days');
    $vigenciaInicio = $proposalDate;
    $vigenciaFim = $proposalDate->modify('+' . max(0, $validadeDias) . ' days');

    $disciplines = $payload['disciplinas'] ?? [];
    if (!is_array($disciplines)) {
        $disciplines = [];
    }
    $discSet = array_fill_keys($disciplines, true);

    $total = proposal_total($payload);
    $values = $payload['valores'] ?? [];
    if (!is_array($values)) {
        $values = [];
    }

    $formatDiscipline = static function (string $key) use ($discSet, $values): string {
        if (!isset($discSet[$key])) {
            return 'Não incluso';
        }
        return brl((float) ($values[$key] ?? 0.0));
    };
    $formatDisciplineNumberOnly = static function (string $key) use ($discSet, $values): string {
        if (!isset($discSet[$key])) {
            return '0,00';
        }
        return number_format((float) ($values[$key] ?? 0.0), 2, ',', '.');
    };

    $etapas = $payload['etapas'] ?? [];
    if (!is_array($etapas)) {
        $etapas = [];
    }
    $etapaPct = static function (int $index) use ($etapas): string {
        $pct = (int) ($etapas[$index]['percentual'] ?? 0);
        return $pct . '%';
    };
    $etapaVal = static function (int $index, float $total) use ($etapas): string {
        $pct = (int) ($etapas[$index]['percentual'] ?? 0);
        return brl(round($total * $pct / 100, 2));
    };

    $arquivos = $payload['arquivos'] ?? [];
    if (!is_array($arquivos)) {
        $arquivos = [];
    }
    $getArquivo = static function (int $index, string $field) use ($arquivos): string {
        return (string) ($arquivos[$index][$field] ?? '');
    };
    $getArquivoRev = static function (int $index) use ($arquivos): string {
        $rev = trim((string) ($arquivos[$index]['rev'] ?? ''));
        return $rev === '00' ? '' : $rev;
    };

    $exclusoes = $payload['exclusoes'] ?? [];
    if (!is_array($exclusoes)) {
        $exclusoes = [];
    }
    $excl = static function (int $index) use ($exclusoes): string {
        return (string) ($exclusoes[$index] ?? '');
    };

    $precoTotalNumber = number_format($total, 2, ',', '.');
    $precoTotalExtenso = number_to_words_ptbr((int) floor($total));

    $consideracoes = $payload['consideracoes'] ?? [];
    if (!is_array($consideracoes)) {
        $consideracoes = [];
    }
    $flowText = 'Concepção > Estudo preliminar > Projeto básico > Projeto executivo > Entrega final';
    $cronogramaParts = [];
    foreach ($etapas as $item) {
        if (!is_array($item)) {
            continue;
        }
        $cronogramaParts[] = (string) ($item['nome'] ?? 'Etapa') . ' (' . ((int) ($item['percentual'] ?? 0)) . '%)';
    }
    $cronogramaText = implode(' | ', $cronogramaParts);
    if (trim($cronogramaText) === '') {
        $cronogramaText = $flowText;
    }

    $bankName = trim((string) ($settings['company_bank_name'] ?? ''));
    $bankAgency = trim((string) ($settings['company_bank_agency'] ?? ''));
    $bankAccount = trim((string) ($settings['company_bank_account'] ?? ''));
    $bankFavored = trim((string) ($settings['company_bank_favored'] ?? ''));
    $bankCnpj = trim((string) ($settings['company_bank_cnpj'] ?? ''));
    $bankPixKey = trim((string) ($settings['company_bank_pix_key'] ?? ''));
    $bankPixType = trim((string) ($settings['company_bank_pix_key_type'] ?? 'CNPJ'));
    if ($bankName === '') {
        $bankName = 'Banco Inter (077)';
    }
    if ($bankAgency === '') {
        $bankAgency = '0001';
    }
    if ($bankAccount === '') {
        $bankAccount = '3375106-4';
    }
    if ($bankFavored === '') {
        $bankFavored = 'Complementare Projetos de Instalacoes LTDA-EPP';
    }
    if ($bankCnpj === '') {
        $bankCnpj = '23.012.176/0001-69';
    }
    if ($bankPixKey === '') {
        $bankPixKey = $bankCnpj;
    }
    if ($bankPixType === '') {
        $bankPixType = 'CNPJ';
    }

    $cardLink = trim((string) ($payload['pagamento_cartao_link'] ?? ''));
    $boletoLink = trim((string) ($payload['pagamento_boleto_link'] ?? ''));
    $cardEnabled = !empty($payload['pagamento_cartao_ativo']);
    $boletoEnabled = !empty($payload['pagamento_boleto_ativo']);
    $paymentMethodsCount = 1 + ($cardEnabled ? 1 : 0) + ($boletoEnabled ? 1 : 0);

    $paymentBaseText = 'o pagamento podera ser realizado por transferencia eletronica (PIX), por meio da chave '
        . $bankPixKey
        . ' (' . $bankPixType . '), enderecada ao '
        . $bankName
        . ', agencia '
        . $bankAgency
        . ', conta corrente n. '
        . $bankAccount;

    $paymentExtraParts = [];
    if ($cardEnabled && $cardLink !== '') {
        $paymentExtraParts[] = 'Tambem e possivel pagar por cartao no link: ' . $cardLink . '.';
    }
    if ($boletoEnabled && $boletoLink !== '') {
        $paymentExtraParts[] = 'Tambem e possivel pagar por boleto no link: ' . $boletoLink . '.';
    }
    $paymentExtraText = implode(' ', $paymentExtraParts);

    $map = [
        'CODIGO_BASE' => $code,
        'REVISAO' => $rev,
        'REVISAO_BADGE' => $revBadge,
        'REVISAO_BADGE_UPPER' => $revBadgeUpper,
        'REVISAO_TITULO' => $revTitle,
        'REVISAO_CONTRATO' => $revContract,
        'PROPOSTA_NUM' => $code,
        'PROPOSTA_REV' => $rev,
        'NOME_CLIENTE' => $clientDisplay,
        'CONTRATANTE_RAZAO' => $clientCompany !== '' ? $clientCompany : $clientDisplay,
        'CONTRATANTE_CNPJ' => (string) ($payload['cliente_cnpj'] ?? ''),
        'CONTRATANTE_EMAIL' => (string) ($payload['cliente_email'] ?? ''),
        'CONTRATANTE_ENDERECO' => (string) ($payload['cliente_endereco'] ?? ''),
        'CONTRATANTE_MUNICIPIO' => (string) ($payload['cliente_cidade'] ?? ''),
        'CONTRATANTE_UF' => (string) ($payload['cliente_uf'] ?? ''),
        'CONTRATANTE_CEP' => (string) ($payload['cliente_cep'] ?? ''),
        'OBRA' => (string) ($payload['obra_nome'] ?? ''),
        'OBRA_CIDADE' => (string) ($payload['obra_cidade'] ?? ''),
        'OBRA_ENDERECO_RESUMIDO' => (string) ($payload['obra_endereco'] ?? ''),
        'OBRA_ENDERECO_COMPLETO' => trim((string) ($payload['obra_endereco'] ?? '') . ' - ' . (string) ($payload['obra_cidade'] ?? '') . '/' . (string) ($payload['obra_uf'] ?? '')),
        'ENDERECO' => (string) ($payload['obra_endereco'] ?? ''),
        'OBJETO_OBRA_DESCRICAO' => (string) ($payload['descricao_objeto'] ?? ''),
        'FINALIDADE_OBRA' => (string) ($payload['finalidade_obra'] ?? ''),
        'VALOR_TOTAL' => brl($total),
        'VALOR_TOTAL_EXTENSO' => '(' . currency_to_words_ptbr($total) . ')',
        'VALOR_ELETRICA' => $formatDiscipline('eletrica'),
        'VALOR_ESPECIAIS' => $formatDiscipline('especiais'),
        'VALOR_HIDRAULICA' => $formatDiscipline('hidraulica'),
        'VALOR_ESGOTO' => $formatDiscipline('esgoto'),
        'VALOR_GAS' => $formatDiscipline('gas'),
        'PRECO_TOTAL' => $precoTotalNumber,
        'PRECO_TOTAL_EXTENSO' => $precoTotalExtenso,
        'PRECO_ELETRICA' => $formatDisciplineNumberOnly('eletrica'),
        'PRECO_ESPECIAIS' => $formatDisciplineNumberOnly('especiais'),
        'PRECO_HIDRAULICA' => $formatDisciplineNumberOnly('hidraulica'),
        'PRECO_ESGOTO_PLUVIAL' => $formatDisciplineNumberOnly('esgoto'),
        'PRECO_GAS' => $formatDisciplineNumberOnly('gas'),
        'PRAZO_DIAS' => (string) $prazoDias,
        'PRAZO_DIAS_EXTENSO' => number_to_words_ptbr($prazoDias),
        'PRAZO_INICIO' => $prazoInicio->format('d/m/Y'),
        'PRAZO_FIM' => $prazoFim->format('d/m/Y'),
        'VIGENCIA_DIAS' => (string) $validadeDias,
        'VIGENCIA_DIAS_EXTENSO' => number_to_words_ptbr($validadeDias),
        'VIGENCIA_INICIO' => $vigenciaInicio->format('d/m/Y'),
        'VIGENCIA_FIM' => $vigenciaFim->format('d/m/Y'),
        'VALIDADE_PROPOSTA' => (string) $validadeDias . ' dias',
        'DATA_EMISSAO' => $proposalDate->format('d/m/Y'),
        'DATA_PROPOSTA' => $proposalDate->format('d/m/Y'),
        'DATA_ARQUIVOS' => $proposalDate->format('d/m/Y'),
        'DATA_EMAIL_ARQUIVOS' => $proposalDate->format('d/m/Y'),
        'DATA_ASSINATURA' => date('d/m/Y'),
        'ANO' => (string) date('Y'),
        'CRONOGRAMA_ESQUEMA' => $cronogramaText,
        'INSERIR_FLUXOGRAMA_PRESTACAO_SERVICOS' => $flowText,
        'BANK_NAME' => $bankName,
        'BANK_AGENCY' => $bankAgency,
        'BANK_ACCOUNT' => $bankAccount,
        'BANK_FAVORED' => $bankFavored,
        'BANK_CNPJ' => $bankCnpj,
        'BANK_PIX_KEY' => $bankPixKey,
        'BANK_PIX_KEY_TYPE' => $bankPixType,
        'BANK_PIX_COPY_LABEL' => 'Copiar chave PIX (' . $bankPixType . ')',
        'PAYMENT_METHODS_COUNT' => (string) $paymentMethodsCount,
        'PAYMENT_CARD_ENABLED' => $cardEnabled ? '1' : '0',
        'PAYMENT_CARD_TITLE' => (string) ($payload['pagamento_cartao_titulo'] ?? 'Pagamento por cartao'),
        'PAYMENT_CARD_DESC' => (string) ($payload['pagamento_cartao_descricao'] ?? ''),
        'PAYMENT_CARD_LINK' => $cardLink,
        'PAYMENT_CARD_BUTTON' => (string) ($payload['pagamento_cartao_botao'] ?? 'Pagar no cartao'),
        'PAYMENT_BOLETO_ENABLED' => $boletoEnabled ? '1' : '0',
        'PAYMENT_BOLETO_TITLE' => (string) ($payload['pagamento_boleto_titulo'] ?? 'Pagamento por boleto'),
        'PAYMENT_BOLETO_DESC' => (string) ($payload['pagamento_boleto_descricao'] ?? ''),
        'PAYMENT_BOLETO_LINK' => $boletoLink,
        'PAYMENT_BOLETO_BUTTON' => (string) ($payload['pagamento_boleto_botao'] ?? 'Abrir boleto'),
        'FORMA_PAGAMENTO_BASE' => $paymentBaseText,
        'FORMA_PAGAMENTO_EXTRA' => $paymentExtraText,
        'PGTO_SINAL' => brl(round($total * 0.20, 2)),
        'PGTO_BASICO_ELETRICO_ESPECIAIS' => brl(round($total * 0.30, 2)),
        'PGTO_BASICO_HIDROSSANITARIO' => brl(round($total * 0.30, 2)),
        'PGTO_BASICO_GAS' => brl(round($total * 0.20, 2)),
        'PGTO_EXEC_ELETRICO_ESPECIAIS' => brl(round($total * 0.30, 2)),
        'PGTO_EXEC_HIDROSSANITARIO' => brl(round($total * 0.30, 2)),
        'PGTO_EXEC_GAS' => brl(round($total * 0.20, 2)),
        'PCT_EP' => $etapaPct(0),
        'PCT_AP' => $etapaPct(1),
        'PCT_AA' => $etapaPct(2),
        'PCT_PE' => $etapaPct(3),
        'PCT_APE' => $etapaPct(4),
        'PCT_EX' => $etapaPct(5),
        'PCT_AEX' => $etapaPct(6),
        'PCT_APROV' => $etapaPct(7),
        'PCT_FINAL' => $etapaPct(8),
        'VAL_EP' => $etapaVal(0, $total),
        'VAL_AP' => $etapaVal(1, $total),
        'VAL_AA' => $etapaVal(2, $total),
        'VAL_PE' => $etapaVal(3, $total),
        'VAL_APE' => $etapaVal(4, $total),
        'VAL_EX' => $etapaVal(5, $total),
        'VAL_AEX' => $etapaVal(6, $total),
        'VAL_APROV' => $etapaVal(7, $total),
        'VAL_FINAL' => $etapaVal(8, $total),
        'ARQ_1_NOME' => $getArquivo(0, 'nome'),
        'ARQ_1_ITEM' => $getArquivo(0, 'item'),
        'ARQ_1_REV' => $getArquivoRev(0),
        'ARQ_2_NOME' => $getArquivo(1, 'nome'),
        'ARQ_2_ITEM' => $getArquivo(1, 'item'),
        'ARQ_2_REV' => $getArquivoRev(1),
        'ARQ_3_NOME' => $getArquivo(2, 'nome'),
        'ARQ_3_ITEM' => $getArquivo(2, 'item'),
        'ARQ_3_REV' => $getArquivoRev(2),
        'ARQ_4_NOME' => $getArquivo(3, 'nome'),
        'ARQ_4_ITEM' => $getArquivo(3, 'item'),
        'ARQ_4_REV' => $getArquivoRev(3),
        'ARQ_5_NOME' => $getArquivo(4, 'nome'),
        'ARQ_5_ITEM' => $getArquivo(4, 'item'),
        'ARQ_5_REV' => $getArquivoRev(4),
        'ARQUIVO_01' => $getArquivo(0, 'nome'),
        'ARQUIVO_01_REV' => $getArquivoRev(0),
        'ARQUIVO_01_LINK' => $getArquivo(0, 'link'),
        'ARQUIVO_02' => $getArquivo(1, 'nome'),
        'ARQUIVO_02_REV' => $getArquivoRev(1),
        'ARQUIVO_02_LINK' => $getArquivo(1, 'link'),
        'ARQUIVO_03' => $getArquivo(2, 'nome'),
        'ARQUIVO_03_REV' => $getArquivoRev(2),
        'ARQUIVO_03_LINK' => $getArquivo(2, 'link'),
        'ARQUIVO_04' => $getArquivo(3, 'nome'),
        'ARQUIVO_04_REV' => $getArquivoRev(3),
        'ARQUIVO_04_LINK' => $getArquivo(3, 'link'),
        'ARQUIVO_05' => $getArquivo(4, 'nome'),
        'ARQUIVO_05_REV' => $getArquivoRev(4),
        'ARQUIVO_05_LINK' => $getArquivo(4, 'link'),
        'DATA_EMAIL_ARQUIVOS' => $proposalDate->format('d/m/Y'),
        'EXCLUSAO_A' => $excl(0),
        'EXCLUSAO_B' => $excl(1),
        'EXCLUSAO_C' => $excl(2),
        'EXCLUSAO_D' => $excl(3),
        'EXCLUSAO_E' => $excl(4),
        'EXCLUSAO_F' => $excl(5),
        'EXCLUSAO_G' => $excl(6),
        'EXCLUSAO_H' => $excl(7),
        'EXCLUSAO_I' => $excl(8),
        'EXCLUSAO_J' => $excl(9),
    ];

    if (count($consideracoes) > 0) {
        $map['INSERIR_FLUXOGRAMA_PRESTACAO_SERVICOS'] = implode(' | ', array_slice($consideracoes, 0, 4));
    }

    foreach ($map as $key => $value) {
        $map[$key] = h((string) $value);
    }

    return $map;
}

function proposal_runtime_script(
    string $token,
    string $downloadUrl,
    string $signUrl,
    array $selectedDisciplines,
    array $customDisciplines,
    array $timelineStages,
    bool $previewMode,
    array $settings = []
): string {
    $selectedJson = json_encode(array_values($selectedDisciplines), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    if (!is_string($selectedJson)) {
        $selectedJson = '[]';
    }
    $customJson = json_encode(array_values($customDisciplines), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    if (!is_string($customJson)) {
        $customJson = '[]';
    }
    $stagesJson = json_encode(array_values($timelineStages), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    if (!is_string($stagesJson)) {
        $stagesJson = '[]';
    }
    $tokenJs = json_encode($token, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?: '""';
    $downloadJs = json_encode($downloadUrl, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?: '""';
    $signJs = json_encode($signUrl, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?: '""';
    $previewJs = $previewMode ? 'true' : 'false';
    return <<<HTML
<script>
(function () {
  const previewMode = {$previewJs};
  const token = {$tokenJs};
  const downloadUrl = {$downloadJs};
  const signUrl = {$signJs};
  const selected = {$selectedJson};
  const customDisciplines = {$customJson};
  const timelineStages = {$stagesJson};

  const disciplineOrder = ['eletrica', 'especiais', 'hidraulica', 'esgoto', 'gas'];
  const contractMarkerByDiscipline = {
    eletrica: '5.1.1.',
    especiais: '5.1.2.',
    hidraulica: '5.1.3.',
    esgoto: '5.1.4.',
    gas: '5.1.5.'
  };

  function formatBrl(value) {
    const numeric = Number(value || 0);
    return new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(numeric);
  }

  function renderCustomDisciplines() {
    if (!Array.isArray(customDisciplines) || customDisciplines.length === 0) return;
    const enabledCustom = customDisciplines.filter((item) => item && item.ativa);
    if (enabledCustom.length === 0) return;

    const etapasSection = document.getElementById('etapas');
    if (etapasSection) {
      const parent = etapasSection.parentNode;
      enabledCustom.forEach((item) => {
        const scopeItem = document.createElement('div');
        scopeItem.className = 'scope-item fade-in visible';

        const scopeIcon = document.createElement('div');
        scopeIcon.className = 'scope-icon';
        const iconImg = document.createElement('img');
        iconImg.src = item.icone || '/assets/img/scope-especiais.png';
        iconImg.alt = item.nome || 'Disciplina';
        scopeIcon.appendChild(iconImg);

        const scopeContent = document.createElement('div');
        scopeContent.className = 'scope-content';
        const title = document.createElement('h3');
        title.textContent = item.nome || 'Disciplina';
        const list = document.createElement('ul');
        const line = document.createElement('li');
        line.textContent = item.descricao && item.descricao.trim() !== '' ? item.descricao : 'Escopo definido na proposta comercial.';
        list.appendChild(line);

        scopeContent.appendChild(title);
        scopeContent.appendChild(list);
        scopeItem.appendChild(scopeIcon);
        scopeItem.appendChild(scopeContent);
        parent.insertBefore(scopeItem, etapasSection);
      });
    }

    const quoteBreakdown = document.querySelector('.quote-breakdown');
    if (quoteBreakdown) {
      enabledCustom.forEach((item) => {
        const row = document.createElement('div');
        row.className = 'quote-row';
        row.innerHTML =
          '<div class="quote-row-left">' +
            '<div class="quote-row-icon"><img src="' + (item.icone || '/assets/img/scope-especiais.png') + '" alt="' + (item.nome || 'Disciplina') + '"></div>' +
            '<span class="quote-row-label"></span>' +
          '</div>' +
          '<span class="quote-row-value"></span>';
        const label = row.querySelector('.quote-row-label');
        const value = row.querySelector('.quote-row-value');
        if (label) label.textContent = item.nome || 'Disciplina';
        if (value) value.textContent = formatBrl(item.valor || 0);
        quoteBreakdown.appendChild(row);
      });
    }
  }

  function renderTimeline() {
    const horizontal = document.querySelector('.timeline-horizontal');
    if (!horizontal || !Array.isArray(timelineStages) || timelineStages.length === 0) return;

    horizontal.innerHTML = '';
    horizontal.style.setProperty('--timeline-count', String(timelineStages.length));

    timelineStages.forEach((stage, index) => {
      const step = document.createElement('div');
      step.className = 'timeline-step fade-in visible';

      const marker = document.createElement('div');
      marker.className = 'timeline-marker';
      marker.innerHTML =
        '<svg class="timeline-shape" viewBox="0 0 320 120" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">' +
          '<path d="M0 72 H120 V38 L188 14 V72 H320" />' +
        '</svg>' +
        '<span class="timeline-marker-inner">' + (index + 1) + '&ordf;</span>';

      const badge = document.createElement('span');
      badge.className = 'timeline-badge';
      badge.textContent = stage.badge || 'Etapa';

      const title = document.createElement('h4');
      title.textContent = stage.nome || 'Etapa';

      const desc = document.createElement('p');
      desc.textContent = stage.descricao || '';

      step.appendChild(marker);
      step.appendChild(badge);
      step.appendChild(title);
      step.appendChild(desc);
      horizontal.appendChild(step);
    });
  }

  function applyDisciplineVisibility() {
    const enabled = new Set(selected);
    const scopeItems = document.querySelectorAll('.scope-item');
    scopeItems.forEach((item, idx) => {
      const key = disciplineOrder[idx];
      if (!key) return;
      if (!enabled.has(key)) item.style.display = 'none';
    });

    const quoteRows = document.querySelectorAll('.quote-row');
    quoteRows.forEach((row, idx) => {
      const key = disciplineOrder[idx];
      if (!key) return;
      if (!enabled.has(key)) row.style.display = 'none';
    });

    const contractRows = document.querySelectorAll('.contract-table tr');
    contractRows.forEach((row) => {
      const text = row.textContent || '';
      disciplineOrder.forEach((key) => {
        const marker = contractMarkerByDiscipline[key];
        if (text.indexOf(marker) !== -1 && !enabled.has(key)) {
          row.style.display = 'none';
        }
      });
    });

    const exclusionLines = document.querySelectorAll('#exclusoes .exclusion-line');
    let hasVisibleExclusion = false;
    exclusionLines.forEach((line) => {
      const textEl = line.querySelector('.exclusion-text');
      const content = textEl ? textEl.textContent.trim() : '';
      if (!content) {
        line.style.display = 'none';
      } else {
        hasVisibleExclusion = true;
      }
    });
    const exclusionSection = document.getElementById('exclusoes');
    if (exclusionSection && !hasVisibleExclusion) {
      exclusionSection.style.display = 'none';
    }
  }

  function cleanupEmptyRevision() {
    document.querySelectorAll('.hero-meta-item-revision').forEach((item) => {
      const value = item.querySelector('strong');
      const text = value ? value.textContent.trim() : item.textContent.trim();
      if (!text) {
        item.style.display = 'none';
      }
    });
  }

  function renderFileLinks() {
    const cells = document.querySelectorAll('.arquivo-link-cell');
    cells.forEach((cell) => {
      const raw = (cell.getAttribute('data-link') || '').trim();
      if (!raw) {
        cell.textContent = '';
        return;
      }
      const link = document.createElement('a');
      link.className = 'arquivo-link';
      link.href = raw;
      link.target = '_blank';
      link.rel = 'noopener noreferrer';
      link.title = 'Abrir arquivo';
      link.setAttribute('aria-label', 'Abrir arquivo em nova guia');
      link.innerHTML = '&#128279;';
      cell.innerHTML = '';
      cell.appendChild(link);
    });
  }

  function setupPaymentMethods() {
    const wrapper = document.getElementById('payment-methods');
    if (!wrapper) return;

    const note = document.querySelector('.payment-note');
    const tabsWrap = wrapper.querySelector('.payment-method-switch');
    const tabs = Array.from(wrapper.querySelectorAll('.payment-method-tab'));
    const cards = Array.from(wrapper.querySelectorAll('.payment-method-card'));
    if (cards.length === 0) return;

    const available = [];
    const tabByMethod = new Map();
    tabs.forEach((tab) => {
      const method = (tab.getAttribute('data-method') || '').trim();
      if (method) tabByMethod.set(method, tab);
    });

    const syncMethodAction = (card, method) => {
      if (method !== 'cartao' && method !== 'boleto') return;
      const action = card.querySelector('.pix-button');
      if (!action) return;
      const link = (card.getAttribute('data-link') || '').trim();

      if (link !== '') {
        action.setAttribute('href', link);
        action.removeAttribute('aria-disabled');
        action.classList.remove('is-disabled');
        action.style.display = 'inline-flex';
        return;
      }

      action.removeAttribute('href');
      action.setAttribute('aria-disabled', 'true');
      action.classList.add('is-disabled');
      action.style.display = 'none';
    };

    cards.forEach((card) => {
      const method = (card.getAttribute('data-method') || '').trim();
      if (!method) return;

      let enabled = true;
      if (method === 'cartao') {
        enabled = card.getAttribute('data-enabled') === '1';
      } else if (method === 'boleto') {
        enabled = card.getAttribute('data-enabled') === '1';
      }
      syncMethodAction(card, method);

      const tab = tabByMethod.get(method);

      if (!enabled) {
        card.style.display = 'none';
        if (tab) tab.style.display = 'none';
        return;
      }

      card.style.display = '';
      if (tab) tab.style.display = '';
      available.push(method);
    });

    if (available.length === 0) {
      wrapper.style.display = 'none';
      if (note) note.style.display = 'none';
      return;
    }

    wrapper.style.display = '';
    if (note) note.style.display = '';

    const activate = (method) => {
      cards.forEach((card) => {
        const isActive = (card.getAttribute('data-method') || '').trim() === method;
        card.classList.toggle('is-active', isActive);
      });
      tabs.forEach((tab) => {
        const isActive = (tab.getAttribute('data-method') || '').trim() === method;
        tab.classList.toggle('is-active', isActive);
      });
    };

    tabs.forEach((tab) => {
      tab.addEventListener('click', () => {
        const method = (tab.getAttribute('data-method') || '').trim();
        if (!method) return;
        if (available.indexOf(method) === -1) return;
        activate(method);
      });
    });

    if (tabsWrap) {
      tabsWrap.style.display = available.length > 1 ? 'inline-flex' : 'none';
    }
    activate(available[0]);
  }

  const downloadButton = document.getElementById('download-pdf-button');
  if (downloadButton) {
    downloadButton.setAttribute('href', downloadUrl);
    if (!previewMode) downloadButton.setAttribute('target', '_blank');
  }

  function postJson(url, data, keepalive) {
    return fetch(url, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(data),
      keepalive: !!keepalive
    }).catch(() => null);
  }

  function sendEvent(type, payload) {
    if (previewMode || !window.__trackingViewId) return;
    postJson('/api/tracking/event', {
      view_id: window.__trackingViewId,
      event_type: type,
      payload: payload || {}
    }, true);
  }

  function setupAcceptance() {
    const acceptButton = document.getElementById('accept-proposal-button');
    if (acceptButton && previewMode) {
      acceptButton.textContent = '✓ Pré-visualização';
    }

    window.confirmAcceptance = function () {
      const checkbox = document.getElementById('agree-terms');
      if (!checkbox || !checkbox.checked) return;
      if (previewMode) {
        if (typeof closeModal === 'function') closeModal();
        return;
      }
      sendEvent('accept_terms', { source: 'modal_confirm' });
      sendEvent('click_sign', { source: 'modal_confirm' });
      if (typeof closeModal === 'function') closeModal();
      window.location.href = signUrl;
    };

    if (downloadButton && !previewMode) {
      downloadButton.addEventListener('click', function () {
        sendEvent('download_pdf', { source: 'accept_section' });
      });
    }
  }

  function setupTracking() {
    if (previewMode || !token) return;

    const key = 'proposal_tracking_' + token;
    const storedSession = localStorage.getItem(key + '_session') || '';
    let maxScroll = 0;
    let lastHeartbeat = Date.now();
    const sectionDelta = {};
    const visible = new Set();

    postJson('/api/tracking/init', { token: token, session_id: storedSession }).then((response) => {
      if (!response) return;
      response.json().then((json) => {
        if (!json || !json.ok) return;
        window.__trackingViewId = Number(json.view_id || 0);
        localStorage.setItem(key + '_session', json.session_id || '');
      }).catch(() => null);
    });

    function calcScroll() {
      const top = window.scrollY || document.documentElement.scrollTop || 0;
      const full = document.documentElement.scrollHeight - window.innerHeight;
      if (full <= 0) return 0;
      const value = (top / full) * 100;
      return Math.max(0, Math.min(100, value));
    }

    window.addEventListener('scroll', function () {
      maxScroll = Math.max(maxScroll, calcScroll());
    }, { passive: true });

    const sections = document.querySelectorAll('section[id], section');
    if (sections.length && window.IntersectionObserver) {
      const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
          const keyName = entry.target.id || entry.target.className || 'section';
          if (entry.isIntersecting) visible.add(keyName);
          else visible.delete(keyName);
        });
      }, { threshold: 0.35 });
      sections.forEach((section) => observer.observe(section));
    }

    setInterval(() => {
      visible.forEach((name) => {
        if (!sectionDelta[name]) sectionDelta[name] = 0;
        sectionDelta[name] += 1;
      });
    }, 1000);

    function flush(force) {
      if (!window.__trackingViewId) return;
      const now = Date.now();
      const elapsed = Math.max(1, Math.round((now - lastHeartbeat) / 1000));
      lastHeartbeat = now;
      const payload = {
        view_id: window.__trackingViewId,
        scroll_depth: maxScroll,
        elapsed_seconds: elapsed,
        section_times: sectionDelta
      };
      Object.keys(sectionDelta).forEach((k) => delete sectionDelta[k]);
      postJson('/api/tracking/heartbeat', payload, !!force);
    }

    setInterval(() => flush(false), 15000);
    window.addEventListener('beforeunload', function () { flush(true); });
    document.addEventListener('visibilitychange', function () {
      if (document.visibilityState === 'hidden') flush(true);
    });
  }

  applyDisciplineVisibility();
  renderCustomDisciplines();
  renderTimeline();
  renderFileLinks();
  setupPaymentMethods();
  cleanupEmptyRevision();
  setupAcceptance();
  setupTracking();
})();
</script>
HTML;
}

function parse_ymd(string $value): DateTimeImmutable
{
    $value = trim($value);
    if ($value === '') {
        return new DateTimeImmutable('today');
    }

    $date = DateTimeImmutable::createFromFormat('Y-m-d', $value);
    if ($date instanceof DateTimeImmutable) {
        return $date;
    }

    try {
        return new DateTimeImmutable($value);
    } catch (Throwable) {
        return new DateTimeImmutable('today');
    }
}

function normalize_custom_discipline_key(string $value): string
{
    $value = mb_strtolower(trim($value), 'UTF-8');
    if ($value === '') {
        return '';
    }

    $normalized = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $value);
    if (!is_string($normalized) || $normalized === '') {
        $normalized = $value;
    }
    $normalized = preg_replace('/[^a-z0-9]+/', '-', $normalized) ?? '';
    $normalized = trim($normalized, '-');

    if ($normalized === '') {
        return '';
    }

    return 'custom-' . substr($normalized, 0, 48);
}

function normalize_revision_for_display(string $revision): string
{
    $revision = trim($revision);
    if ($revision === '' || $revision === '00') {
        return '';
    }
    return $revision;
}






