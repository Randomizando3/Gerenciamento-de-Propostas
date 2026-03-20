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
        'intro_title' => 'Obrigado pelo convite para desenvolver os projetos de instalações da {{OBRA}}.',
        'files_section_label' => '01 - INTRODUÇÃO',
        'files_section_title' => 'Arquivos Recebidos',
        'files_section_subtitle' => 'A presente proposta foi elaborada utilizando arquivos enviados via WhatsApp no dia {{DATA_ARQUIVOS}}.',
        'guidelines_title' => 'Diretrizes Gerais',
        'guidelines_subtitle' => '',
        'guidelines_items' => default_guidelines_items(),
        'scope_items' => default_scope_blueprints(),
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
            ['nome' => 'Sinal e kick-off', 'prazo' => '20 dias', 'descricao' => 'Assinatura e início'],
            ['nome' => 'Projeto básico', 'prazo' => '15 dias', 'descricao' => 'Entrega preliminar'],
            ['nome' => 'Projeto executivo', 'prazo' => '10 dias', 'descricao' => 'Entrega executiva'],
            ['nome' => 'Aprovação final', 'prazo' => 'Final', 'descricao' => 'Encerramento'],
        ],
        'arquivos' => [
            ['item' => 'ARQ-01', 'nome' => 'Memorial descritivo', 'rev' => '00', 'data' => $today->format('d/m/Y')],
            ['item' => 'ARQ-02', 'nome' => 'Projeto básico', 'rev' => '00', 'data' => $today->format('d/m/Y')],
            ['item' => 'ARQ-03', 'nome' => 'Projeto executivo', 'rev' => '00', 'data' => $today->format('d/m/Y')],
        ],
        'payment_schedule_manual_enabled' => false,
        'payment_schedule_rows' => [],
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
        'pagamento_cartao_titulo' => 'Pagamento por cartão',
        'pagamento_cartao_descricao' => 'Use o link seguro para pagar no cartão de crédito.',
        'pagamento_cartao_link' => '',
        'pagamento_cartao_botao' => 'Pagar no cartão',
        'pagamento_boleto_ativo' => false,
        'pagamento_boleto_titulo' => 'Pagamento por boleto',
        'pagamento_boleto_descricao' => 'Use o link para emitir ou visualizar o boleto.',
        'pagamento_boleto_link' => '',
        'pagamento_boleto_botao' => 'Abrir boleto',
        'header_title_layout' => 'default',
        'header_aditivo_kicker' => 'PROPOSTA',
        'header_aditivo_title' => 'ADITIVO DA PROPOSTA',
        'header_custom_media_enabled' => false,
        'header_custom_media_url' => '',
        'acceptance_mode' => 'contract',
        'accept_terms_title' => '',
        'accept_terms_html' => '',
        'accept_summary_html' => '',
        'accept_terms_checkbox_text' => '',
        'observacoes' => '',
        'zapsign_sign_url' => '',
    ];
}

function default_guidelines_items(): array
{
    return [
        ['title' => 'Normas e Conformidade', 'content' => 'Projetos conforme ABNT NBR, NRs e regulamentos das concessionárias do Rio de Janeiro.', 'icon' => '/assets/img/guideline-1.png'],
        ['title' => 'ART Emitida', 'content' => 'Anotação de Responsabilidade Técnica dos projetos será emitida.', 'icon' => '/assets/img/guideline-2.png'],
        ['title' => 'Entrega DWG e PDF', 'content' => 'Projetos executivos cadastrados no gerenciador eletrônico do empreendimento.', 'icon' => '/assets/img/guideline-3.png'],
        ['title' => 'Checklists de Qualidade', 'content' => 'Garantia de atendimento aos padrões de qualidade definidos pela contratante.', 'icon' => '/assets/img/guideline-4.png'],
        ['title' => 'Reuniões de Coordenação', 'content' => 'Participação ativa para alinhamento técnico e solução de interferências.', 'icon' => '/assets/img/guideline-5.png'],
        ['title' => 'Metodologia BIM', 'content' => 'Os projetos serão desenvolvidos em metodologia BIM.', 'icon' => '/assets/img/guideline-6.png'],
    ];
}

function default_scope_blueprints(): array
{
    return [
        'eletrica' => [
            'title' => 'Projeto Elétrico de Baixa Tensão',
            'subtitle' => '',
            'summary' => 'Projeto de instalações elétricas, cargas, quadros e detalhamentos técnicos.',
            'icon' => '/assets/img/scope-eletrica.png',
            'topics' => [
                'Projeto do sistema de distribuição de força e luz.',
                'Projetos dos quadros de força, iluminação e tomadas, com apresentação dos diagramas trifilares, conforme padrões e normas brasileiras, em especial a NBR 5410.',
                'Memorial descrevendo as instalações projetadas, bem como as características dos materiais que serão utilizados na execução da obra.',
            ],
        ],
        'especiais' => [
            'title' => 'Projeto de Especiais',
            'subtitle' => 'Rede de dados, telefonia, CFTV, TV e som',
            'summary' => 'Soluções especiais como dados, CFTV, telefonia e sistemas complementares.',
            'icon' => '/assets/img/scope-especiais.png',
            'topics' => [
                'Projeto de distribuição e dimensionamento de tubulação seca para rede de telefonia, dados, antena, CFTV e sonorização.',
                'Memorial descrevendo as instalações projetadas, bem como as características dos materiais que serão utilizados na execução da obra.',
            ],
        ],
        'hidraulica' => [
            'title' => 'Projeto Hidráulico',
            'subtitle' => '',
            'summary' => 'Projeto de água fria/quente, distribuição, barrilete e pontos de consumo.',
            'icon' => '/assets/img/scope-hidraulica.png',
            'topics' => [
                'Projeto executivo e detalhes; desenvolvimento do projeto conforme NBR 5626 e 8160.',
                'Detalhamento dos pontos de coleta de esgoto e dos aparelhos em plantas, cortes e detalhes gerais.',
                'Distribuição da rede interna de água fria e água quente para abastecimento dos pontos de consumo, com plantas, vistas e detalhes.',
                'Memorial descrevendo as instalações projetadas, bem como as características dos materiais que serão utilizados na execução da obra.',
            ],
        ],
        'esgoto' => [
            'title' => 'Projeto de Esgoto Sanitário',
            'subtitle' => '',
            'summary' => 'Projeto sanitário, ventilação e encaminhamento para rede pública.',
            'icon' => '/assets/img/scope-esgoto.png',
            'topics' => [
                'Projeto executivo e detalhes; desenvolvimento do projeto conforme NBR 5626 e 8160.',
                'Detalhamento dos pontos de coleta de esgoto e dos aparelhos em plantas, cortes e detalhes gerais.',
                'Distribuição da rede interna de água fria e água quente para abastecimento dos pontos de consumo, com plantas, vistas e detalhes.',
                'Memorial descrevendo as instalações projetadas, bem como as características dos materiais que serão utilizados na execução da obra.',
            ],
        ],
        'gas' => [
            'title' => 'Projeto de Gás Canalizado',
            'subtitle' => '',
            'summary' => 'Projeto de gás conforme normas aplicáveis e segurança operacional.',
            'icon' => '/assets/img/scope-gas.png',
            'topics' => [
                'Distribuição interna das instalações de gás e detalhes de execução conforme padrões e normas brasileiras.',
                'Memorial descrevendo as instalações projetadas, bem como as características dos materiais que serão utilizados na execução da obra.',
            ],
        ],
    ];
}

function discipline_catalog(): array
{
    return [
        'eletrica' => ['nome' => 'Elétrica', 'icone' => '/assets/img/scope-eletrica.png', 'descricao' => 'Projeto de instalações elétricas, cargas, quadros e detalhamentos técnicos.'],
        'hidraulica' => ['nome' => 'Hidráulica', 'icone' => '/assets/img/scope-hidraulica.png', 'descricao' => 'Projeto de água fria/quente, distribuição, barrilete e pontos de consumo.'],
        'esgoto' => ['nome' => 'Esgoto', 'icone' => '/assets/img/scope-esgoto.png', 'descricao' => 'Projeto sanitário, ventilação e encaminhamento para rede pública.'],
        'gas' => ['nome' => 'Gás', 'icone' => '/assets/img/scope-gas.png', 'descricao' => 'Projeto de gás conforme normas aplicáveis e segurança operacional.'],
        'especiais' => ['nome' => 'Especiais', 'icone' => '/assets/img/scope-especiais.png', 'descricao' => 'Soluções especiais como SPDA, incêndio e sistemas complementares.'],
    ];
}

function normalize_topic_lines(mixed $value): array
{
    if (is_string($value)) {
        $value = parse_multilines($value);
    }
    if (!is_array($value)) {
        return [];
    }

    $lines = [];
    foreach ($value as $line) {
        $line = trim((string) $line);
        if ($line !== '') {
            $lines[] = $line;
        }
    }

    return $lines;
}

function proposal_flag_enabled(mixed $value): bool
{
    if (is_bool($value)) {
        return $value;
    }
    if (is_int($value) || is_float($value)) {
        return (int) $value === 1;
    }
    if (is_string($value)) {
        $normalized = mb_strtolower(trim($value), 'UTF-8');
        return in_array($normalized, ['1', 'true', 'on', 'yes', 'sim'], true);
    }
    return false;
}

function proposal_payload_for_model(array $payload): array
{
    $payload = normalize_proposal_payload($payload, $payload);
    $payload['codigo_base'] = '';
    $payload['revisao'] = '00';
    $payload['data_proposta'] = '';
    $payload['cliente_nome'] = '';
    $payload['cliente_empresa'] = '';
    $payload['cliente_cnpj'] = '';
    $payload['cliente_email'] = '';
    $payload['cliente_telefone'] = '';
    $payload['cliente_endereco'] = '';
    $payload['cliente_cidade'] = '';
    $payload['cliente_uf'] = '';
    $payload['cliente_cep'] = '';
    $payload['obra_nome'] = '';
    $payload['obra_endereco'] = '';
    $payload['obra_cidade'] = '';
    $payload['obra_uf'] = '';
    $payload['zapsign_sign_url'] = '';

    return $payload;
}

function proposal_payload_from_model(array $payload): array
{
    $defaults = default_proposal_payload();
    $modelPayload = normalize_proposal_payload($payload, $payload);
    $newPayload = array_replace_recursive($defaults, $modelPayload);

    $newPayload['codigo_base'] = $defaults['codigo_base'];
    $newPayload['revisao'] = '00';
    $newPayload['data_proposta'] = $defaults['data_proposta'];
    $newPayload['cliente_nome'] = '';
    $newPayload['cliente_empresa'] = '';
    $newPayload['cliente_cnpj'] = '';
    $newPayload['cliente_email'] = '';
    $newPayload['cliente_telefone'] = '';
    $newPayload['cliente_endereco'] = '';
    $newPayload['cliente_cidade'] = '';
    $newPayload['cliente_uf'] = '';
    $newPayload['cliente_cep'] = '';
    $newPayload['zapsign_sign_url'] = '';

    return $newPayload;
}

function normalize_proposal_payload(array $input, ?array $base = null): array
{
    $defaults = default_proposal_payload();
    $payload = $base ? array_replace_recursive($defaults, $base) : $defaults;

    $stringFields = [
        'codigo_base', 'revisao', 'titulo', 'data_proposta', 'cliente_nome', 'cliente_empresa', 'cliente_cnpj', 'cliente_email', 'cliente_telefone',
        'cliente_endereco', 'cliente_cidade', 'cliente_uf', 'cliente_cep', 'obra_nome', 'obra_endereco', 'obra_cidade', 'obra_uf', 'finalidade_obra',
        'descricao_objeto', 'intro_title', 'files_section_label', 'files_section_title', 'files_section_subtitle', 'guidelines_title', 'guidelines_subtitle',
        'pagamento_cartao_titulo', 'pagamento_cartao_descricao', 'pagamento_cartao_link', 'pagamento_cartao_botao', 'pagamento_boleto_titulo',
        'pagamento_boleto_descricao', 'pagamento_boleto_link', 'pagamento_boleto_botao', 'header_title_layout',
        'header_aditivo_kicker', 'header_aditivo_title', 'header_custom_media_url', 'acceptance_mode', 'accept_terms_title',
        'accept_terms_html', 'accept_summary_html', 'accept_terms_checkbox_text', 'observacoes', 'zapsign_sign_url',
    ];
    foreach ($stringFields as $field) {
        if (array_key_exists($field, $input)) {
            $payload[$field] = trim((string) $input[$field]);
        }
    }

    $payload['pagamento_cartao_ativo'] = array_key_exists('pagamento_cartao_ativo', $input)
        ? proposal_flag_enabled($input['pagamento_cartao_ativo'])
        : false;
    $payload['pagamento_boleto_ativo'] = array_key_exists('pagamento_boleto_ativo', $input)
        ? proposal_flag_enabled($input['pagamento_boleto_ativo'])
        : false;
    $manualPaymentProvided = array_key_exists('payment_schedule_manual_enabled', $input);
    $manualPaymentExisting = $base !== null && array_key_exists('payment_schedule_manual_enabled', $base)
        ? proposal_flag_enabled($base['payment_schedule_manual_enabled'])
        : null;
    $payload['header_custom_media_enabled'] = array_key_exists('header_custom_media_enabled', $input)
        ? proposal_flag_enabled($input['header_custom_media_enabled'])
        : false;
    $layout = trim((string) ($input['header_title_layout'] ?? ($payload['header_title_layout'] ?? 'default')));
    $payload['header_title_layout'] = $layout === 'aditivo' ? 'aditivo' : 'default';
    $acceptanceMode = trim((string) ($input['acceptance_mode'] ?? ($payload['acceptance_mode'] ?? 'contract')));
    $payload['acceptance_mode'] = $acceptanceMode === 'summary' ? 'summary' : 'contract';
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

    $scopeDefaults = default_scope_blueprints();
    $scopeBase = is_array($payload['scope_items'] ?? null) ? $payload['scope_items'] : [];
    $scopeInput = $input['scope_items'] ?? $scopeBase;
    if (!is_array($scopeInput)) {
        $scopeInput = [];
    }
    $scopeItems = [];
    foreach ($scopeDefaults as $key => $defaultItem) {
        $baseItem = is_array($scopeBase[$key] ?? null) ? $scopeBase[$key] : [];
        $source = is_array($scopeInput[$key] ?? null) ? $scopeInput[$key] : [];
        $merged = array_replace_recursive($defaultItem, $baseItem, $source);
        $scopeItems[$key] = [
            'title' => trim((string) ($merged['title'] ?? $defaultItem['title'])),
            'subtitle' => trim((string) ($merged['subtitle'] ?? '')),
            'summary' => trim((string) ($merged['summary'] ?? $defaultItem['summary'])),
            'icon' => trim((string) ($merged['icon'] ?? $defaultItem['icon'])),
            'topics' => array_slice(normalize_topic_lines($merged['topics'] ?? $defaultItem['topics']), 0, 24),
        ];
    }
    $payload['scope_items'] = $scopeItems;

    $guidelinesInput = $input['guidelines_items'] ?? ($payload['guidelines_items'] ?? []);
    if (!is_array($guidelinesInput)) {
        $guidelinesInput = [];
    }
    $guidelineDefaults = default_guidelines_items();
    $guidelines = [];
    foreach ($guidelinesInput as $index => $item) {
        if (!is_array($item)) {
            continue;
        }
        $defaultItem = $guidelineDefaults[$index] ?? ['title' => '', 'content' => '', 'icon' => ''];
        $title = trim((string) ($item['title'] ?? $defaultItem['title'] ?? ''));
        $content = trim((string) ($item['content'] ?? $defaultItem['content'] ?? ''));
        $icon = trim((string) ($item['icon'] ?? $defaultItem['icon'] ?? ''));
        if ($title === '' && $content === '') {
            continue;
        }
        $guidelines[] = ['title' => $title !== '' ? $title : 'Diretriz', 'content' => $content, 'icon' => $icon];
    }
    if ($guidelines === []) {
        $guidelines = $guidelineDefaults;
    }
    $payload['guidelines_items'] = array_slice($guidelines, 0, 24);

    $customDisciplinesInput = $input['disciplinas_custom'] ?? ($payload['disciplinas_custom'] ?? []);
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
        $customDisciplines[] = [
            'key' => $key,
            'nome' => $nome,
            'subtitle' => trim((string) ($item['subtitle'] ?? '')),
            'descricao' => trim((string) ($item['descricao'] ?? '')),
            'icone' => trim((string) ($item['icone'] ?? '')),
            'valor' => round(to_float($item['valor'] ?? 0), 2),
            'ativa' => isset($item['ativa']) && (string) ($item['ativa'] ?? '0') !== '0',
            'topics' => array_slice(normalize_topic_lines($item['topics'] ?? []), 0, 24),
        ];
        $customValues[$key] = round(to_float($item['valor'] ?? 0), 2);
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
        $prazo = trim((string) ($stage['prazo'] ?? ''));
        if ($prazo === '' && isset($stage['percentual'])) {
            $prazo = (string) ((int) $stage['percentual']) . '%';
        }
        $descricao = trim((string) ($stage['descricao'] ?? ''));
        if ($name === '') {
            continue;
        }
        $normalizedStages[] = ['nome' => $name, 'prazo' => $prazo !== '' ? $prazo : 'Etapa', 'descricao' => $descricao];
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
        $data = trim((string) ($file['data'] ?? ($file['link'] ?? '')));
        if ($item === '' && $nome === '') {
            continue;
        }
        $normalizedFiles[] = ['item' => $item !== '' ? $item : 'ARQ', 'nome' => $nome !== '' ? $nome : 'Arquivo', 'rev' => $rev, 'data' => $data];
    }
    $payload['arquivos'] = array_slice($normalizedFiles, 0, 40);

    $paymentRows = $input['payment_schedule_rows'] ?? ($payload['payment_schedule_rows'] ?? []);
    if (!is_array($paymentRows)) {
        $paymentRows = [];
    }
    $normalizedPaymentRows = [];
    foreach ($paymentRows as $row) {
        if (!is_array($row)) {
            continue;
        }
        $type = trim((string) ($row['type'] ?? 'line'));
        $type = $type === 'subtitle' ? 'subtitle' : 'line';
        $label = trim((string) ($row['label'] ?? ''));
        $amountRaw = trim((string) ($row['amount'] ?? ''));

        if ($label === '' && $amountRaw === '') {
            continue;
        }

        $normalizedPaymentRows[] = [
            'type' => $type,
            'label' => $label,
            'amount' => $type === 'line' ? round(to_float($amountRaw), 2) : 0.0,
        ];
    }
    $payload['payment_schedule_rows'] = array_slice($normalizedPaymentRows, 0, 40);
    if ($manualPaymentProvided) {
        $payload['payment_schedule_manual_enabled'] = proposal_flag_enabled($input['payment_schedule_manual_enabled']);
    } elseif ($manualPaymentExisting !== null) {
        $payload['payment_schedule_manual_enabled'] = $manualPaymentExisting;
    } else {
        $payload['payment_schedule_manual_enabled'] = proposal_payment_schedule_manual_enabled([
            'payment_schedule_rows' => $payload['payment_schedule_rows'],
        ]);
    }

    $payload['consideracoes'] = array_slice(normalize_topic_lines($input['consideracoes'] ?? $payload['consideracoes']), 0, 30);
    $payload['exclusoes'] = array_slice(normalize_topic_lines($input['exclusoes'] ?? $payload['exclusoes']), 0, 40);

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

function proposal_payment_schedule_entries(array $payload): array
{
    if (!proposal_payment_schedule_manual_enabled($payload)) {
        return [];
    }

    $rows = is_array($payload['payment_schedule_rows'] ?? null) ? $payload['payment_schedule_rows'] : [];
    $entries = [];

    foreach ($rows as $row) {
        if (!is_array($row)) {
            continue;
        }

        $type = trim((string) ($row['type'] ?? 'line'));
        $type = $type === 'subtitle' ? 'subtitle' : 'line';
        $label = trim((string) ($row['label'] ?? ''));
        $amount = round((float) ($row['amount'] ?? 0), 2);

        if ($label === '' && ($type !== 'line' || $amount <= 0)) {
            continue;
        }

        $entries[] = [
            'type' => $type,
            'label' => $label !== '' ? $label : ($type === 'subtitle' ? 'Grupo' : 'Parcela'),
            'amount' => $type === 'line' ? $amount : 0.0,
            'amount_label' => $type === 'line' ? brl($amount) : '',
        ];
    }

    return $entries;
}

function proposal_payment_schedule_manual_enabled(array $payload): bool
{
    if (array_key_exists('payment_schedule_manual_enabled', $payload)) {
        return proposal_flag_enabled($payload['payment_schedule_manual_enabled']);
    }

    $rows = is_array($payload['payment_schedule_rows'] ?? null) ? $payload['payment_schedule_rows'] : [];
    foreach ($rows as $row) {
        if (!is_array($row)) {
            continue;
        }

        $label = trim((string) ($row['label'] ?? ''));
        $amount = round((float) ($row['amount'] ?? 0), 2);
        if ($label !== '' || $amount > 0) {
            return true;
        }
    }

    return false;
}

function proposal_payment_schedule_total(array $payload): float
{
    $sum = 0.0;
    foreach (proposal_payment_schedule_entries($payload) as $entry) {
        if (($entry['type'] ?? 'line') !== 'line') {
            continue;
        }
        $sum += (float) ($entry['amount'] ?? 0);
    }

    return round($sum, 2);
}

function proposal_payment_schedule_has_lines(array $payload): bool
{
    foreach (proposal_payment_schedule_entries($payload) as $entry) {
        if (($entry['type'] ?? 'line') === 'line') {
            return true;
        }
    }

    return false;
}

function proposal_payment_schedule_validation(array $payload): array
{
    if (!proposal_payment_schedule_has_lines($payload)) {
        return ['ok' => true, 'message' => ''];
    }

    $total = proposal_total($payload);
    $scheduleTotal = proposal_payment_schedule_total($payload);
    if (abs($scheduleTotal - $total) < 0.01) {
        return ['ok' => true, 'message' => ''];
    }

    return [
        'ok' => false,
        'message' => 'A soma da forma de pagamento (' . brl($scheduleTotal) . ') precisa ser igual ao valor total da proposta (' . brl($total) . ').',
    ];
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

function list_proposals_with_metrics(array $filters = []): array
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

    $query = mb_strtolower(trim((string) ($filters['query'] ?? '')), 'UTF-8');
    $status = trim((string) ($filters['status'] ?? ''));
    $client = mb_strtolower(trim((string) ($filters['client'] ?? '')), 'UTF-8');
    $minValue = round(to_float($filters['min_total'] ?? 0), 2);
    $maxValueRaw = trim((string) ($filters['max_total'] ?? ''));
    $maxValue = $maxValueRaw !== '' ? round(to_float($maxValueRaw), 2) : null;
    $orderBy = trim((string) ($filters['order_by'] ?? 'updated_at'));
    $orderDir = mb_strtolower(trim((string) ($filters['order_dir'] ?? 'desc')), 'UTF-8') === 'asc' ? 'asc' : 'desc';

    if ($query !== '' || $status !== '' || $client !== '' || $minValue > 0 || $maxValue !== null) {
        $rows = array_values(array_filter($rows, static function (array $row) use ($query, $status, $client, $minValue, $maxValue): bool {
            $haystack = mb_strtolower(
                implode(' ', [
                    (string) ($row['code'] ?? ''),
                    (string) ($row['title'] ?? ''),
                    (string) ($row['client_name'] ?? ''),
                    (string) ($row['client_company'] ?? ''),
                    (string) ($row['obra_nome'] ?? ''),
                ]),
                'UTF-8'
            );

            if ($query !== '' && !str_contains($haystack, $query)) {
                return false;
            }
            if ($status !== '' && (string) ($row['status'] ?? '') !== $status) {
                return false;
            }
            if ($client !== '' && !str_contains($haystack, $client)) {
                return false;
            }

            $total = (float) ($row['total_value'] ?? 0);
            if ($minValue > 0 && $total < $minValue) {
                return false;
            }
            if ($maxValue !== null && $total > $maxValue) {
                return false;
            }

            return true;
        }));
    }

    $sortMap = [
        'code' => static fn (array $row): string => mb_strtolower((string) ($row['code'] ?? ''), 'UTF-8'),
        'client' => static fn (array $row): string => mb_strtolower((string) ($row['client_name'] ?? ''), 'UTF-8'),
        'status' => static fn (array $row): string => mb_strtolower((string) ($row['status'] ?? ''), 'UTF-8'),
        'total_value' => static fn (array $row): float => (float) ($row['total_value'] ?? 0),
        'total_views' => static fn (array $row): int => (int) ($row['total_views'] ?? 0),
        'max_scroll' => static fn (array $row): float => (float) ($row['max_scroll'] ?? 0),
        'updated_at' => static fn (array $row): string => (string) ($row['updated_at'] ?? ''),
    ];
    $sortResolver = $sortMap[$orderBy] ?? $sortMap['updated_at'];

    usort($rows, static function (array $a, array $b) use ($sortResolver, $orderDir): int {
        $valueA = $sortResolver($a);
        $valueB = $sortResolver($b);
        $result = $valueA <=> $valueB;
        if (is_string($valueA) || is_string($valueB)) {
            $result = strcmp((string) $valueA, (string) $valueB);
        }

        return $orderDir === 'asc' ? $result : -$result;
    });

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

function delete_proposal_record(int $id): bool
{
    $rows = db_all('proposals');
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

    db_replace_rows('proposals', $filtered);
    db_replace_rows(
        'proposal_views',
        array_values(array_filter(
            db_all('proposal_views'),
            static fn (array $row): bool => (int) ($row['proposal_id'] ?? 0) !== $id
        ))
    );
    db_replace_rows(
        'proposal_events',
        array_values(array_filter(
            db_all('proposal_events'),
            static fn (array $row): bool => (int) ($row['proposal_id'] ?? 0) !== $id
        ))
    );

    return true;
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

function proposal_media_type_from_url(string $url): string
{
    $path = (string) parse_url($url, PHP_URL_PATH);
    $extension = mb_strtolower(pathinfo($path, PATHINFO_EXTENSION), 'UTF-8');
    if (in_array($extension, ['mp4', 'webm', 'ogg', 'mov', 'm4v'], true)) {
        return 'video';
    }

    return 'image';
}

function proposal_contract_url(array $proposal): ?string
{
    if (empty($proposal['token'])) {
        return null;
    }

    return app_url('/p/' . $proposal['token'] . '/contract');
}

function proposal_acceptance_pdf_url(array $proposal): ?string
{
    if (empty($proposal['token'])) {
        return null;
    }

    return app_url('/p/' . $proposal['token'] . '/acceptance-document');
}

function proposal_acceptance_mode(array $payload): string
{
    $mode = trim((string) ($payload['acceptance_mode'] ?? 'contract'));
    return $mode === 'summary' ? 'summary' : 'contract';
}

function proposal_acceptance_document_url(array $proposal, ?array $payload = null): ?string
{
    return proposal_acceptance_pdf_url($proposal);
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
    $template = apply_accept_terms_customization($template, $payload, $settings, $replaceMap);

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
    $downloadTarget = '';

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

    $runtimeScript = proposal_runtime_script(
        (string) $token,
        $downloadUrl,
        $signUrl,
        proposal_public_render_data($proposal, $payload, $settings),
        $previewMode
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
    $keys = array_values(array_filter(
        array_keys(build_proposal_placeholder_map($sampleProposal, default_proposal_payload(), default_settings_values())),
        static fn (string $key): bool => !preg_match('/^ARQUIVO_\d+_LINK$/', $key)
    ));

    $knownDescriptions = [
        'PROPOSTA_NUM' => 'Código da proposta (ex: P260311-293)',
        'CODIGO_BASE' => 'Código base da proposta',
        'REVISAO' => 'Revisão atual (vazio quando 00)',
        'NOME_CLIENTE' => 'Nome do cliente',
        'CLIENTE_NOME' => 'Nome do cliente',
        'CLIENTE_EMPRESA' => 'Empresa do cliente',
        'CLIENTE_EMPRESA_RAW' => 'Empresa do cliente sem fallback',
        'CLIENTE_CNPJ' => 'CNPJ do cliente',
        'CNPJ_CLIENTE' => 'CNPJ do cliente',
        'CNPJ' => 'CNPJ do cliente',
        'EMPRESA_CLIENTE' => 'Empresa do cliente',
        'EMPRESA' => 'Empresa do cliente',
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
        'BANK_AGENCY' => 'Agência bancária',
        'BANK_ACCOUNT' => 'Conta corrente bancária',
        'BANK_FAVORED' => 'Favorecido da conta',
        'BANK_CNPJ' => 'CNPJ vinculado ao pagamento',
        'BANK_PIX_KEY' => 'Chave PIX',
        'BANK_PIX_KEY_TYPE' => 'Tipo da chave PIX',
        'PAYMENT_CARD_LINK' => 'Link de pagamento no cartão',
        'PAYMENT_BOLETO_LINK' => 'Link para emissão de boleto',
        'LINK_PROPOSTA_ORIGINAL' => 'Link público da proposta original',
        'PROPOSTA_LINK_ORIGINAL' => 'Link público da proposta original',
        'ARQUIVO_01_DATA' => 'Data do arquivo recebido 01',
        'ARQUIVO_02_DATA' => 'Data do arquivo recebido 02',
        'ARQUIVO_03_DATA' => 'Data do arquivo recebido 03',
        'ARQUIVO_04_DATA' => 'Data do arquivo recebido 04',
        'ARQUIVO_05_DATA' => 'Data do arquivo recebido 05',
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

function apply_accept_terms_customization(string $template, array $payload, array $settings, array $replaceMap): string
{
    $tokens = [];
    foreach ($replaceMap as $key => $value) {
        $tokens['{{' . $key . '}}'] = (string) $value;
    }

    $renderTokens = static function (string $content) use ($tokens): string {
        return strtr($content, $tokens);
    };

    $titleTpl = trim((string) ($payload['accept_terms_title'] ?? ''));
    if ($titleTpl === '') {
        $titleTpl = trim((string) ($settings['accept_terms_title'] ?? ''));
    }
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
    $checkboxTpl = trim((string) ($payload['accept_terms_checkbox_text'] ?? ''));
    if ($checkboxTpl === '') {
        $checkboxTpl = trim((string) ($settings['accept_terms_checkbox_text'] ?? $checkboxDefault));
    }
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

    $termsTpl = trim((string) ($payload['accept_terms_html'] ?? ''));
    if ($termsTpl === '') {
        $termsTpl = trim((string) ($settings['accept_terms_html'] ?? ''));
    }
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
    $revBadge = $rev !== '' ? ' - Revisão ' . $rev : '';
    $revBadgeUpper = $rev !== '' ? ' - REVISAO ' . $rev : '';
    $revTitle = $rev !== '' ? ' - Revisão ' . $rev : '';
    $revContract = $rev !== '' ? '(Revisão ' . $rev . ')' : '';
    $clientName = trim((string) ($payload['cliente_nome'] ?? ''));
    $clientCompany = trim((string) ($payload['cliente_empresa'] ?? ''));
    $clientCnpj = trim((string) ($payload['cliente_cnpj'] ?? ''));
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
        $prazo = trim((string) ($etapas[$index]['prazo'] ?? ''));
        if ($prazo !== '') {
            return $prazo;
        }
        $pct = (int) ($etapas[$index]['percentual'] ?? 0);
        return $pct > 0 ? ($pct . '%') : 'Etapa';
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
        $badge = trim((string) ($item['prazo'] ?? ''));
        if ($badge === '') {
            $pct = (int) ($item['percentual'] ?? 0);
            $badge = $pct > 0 ? ($pct . '%') : 'Etapa';
        }
        $cronogramaParts[] = (string) ($item['nome'] ?? 'Etapa') . ' (' . $badge . ')';
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
        $bankFavored = 'Complementare Projetos de Instalações LTDA-EPP';
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
    $cardEnabled = proposal_flag_enabled($payload['pagamento_cartao_ativo'] ?? false);
    $boletoEnabled = proposal_flag_enabled($payload['pagamento_boleto_ativo'] ?? false);
    $paymentMethodsCount = 1 + ($cardEnabled ? 1 : 0) + ($boletoEnabled ? 1 : 0);
    $paymentScheduleEntries = proposal_payment_schedule_entries($payload);

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
        $paymentExtraParts[] = 'Também é possível pagar por cartão no link: ' . $cardLink . '.';
    }
    if ($boletoEnabled && $boletoLink !== '') {
        $paymentExtraParts[] = 'Também é possível pagar por boleto no link: ' . $boletoLink . '.';
    }
    $paymentExtraText = implode(' ', $paymentExtraParts);

    if ($paymentScheduleEntries !== []) {
        $scheduleLines = [];
        foreach ($paymentScheduleEntries as $entry) {
            if (($entry['type'] ?? 'line') === 'subtitle') {
                $scheduleLines[] = trim((string) ($entry['label'] ?? ''));
                continue;
            }

            $scheduleLines[] = trim((string) ($entry['label'] ?? 'Parcela')) . ': ' . brl((float) ($entry['amount'] ?? 0));
        }
        $paymentBaseText = implode(' | ', array_filter($scheduleLines));
    }

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
        'CLIENTE_NOME' => $clientName !== '' ? $clientName : $clientDisplay,
        'CLIENTE_EMPRESA' => $clientCompany !== '' ? $clientCompany : $clientDisplay,
        'CLIENTE_EMPRESA_RAW' => $clientCompany,
        'CLIENTE_CNPJ' => $clientCnpj,
        'CNPJ_CLIENTE' => $clientCnpj,
        'CNPJ' => $clientCnpj,
        'EMPRESA_CLIENTE' => $clientCompany !== '' ? $clientCompany : $clientDisplay,
        'EMPRESA' => $clientCompany !== '' ? $clientCompany : $clientDisplay,
        'CONTRATANTE_RAZAO' => $clientCompany !== '' ? $clientCompany : $clientDisplay,
        'CONTRATANTE_CNPJ' => $clientCnpj,
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
        'VALOR_TOTAL_EXTENSO_CARD' => currency_to_words_ptbr($total),
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
        'DATA_ARQUIVOS' => (string) ($arquivos[0]['data'] ?? $proposalDate->format('d/m/Y')),
        'DATA_EMAIL_ARQUIVOS' => $proposalDate->format('d/m/Y'),
        'DATA_ASSINATURA' => date('d/m/Y'),
        'ANO' => (string) date('Y'),
        'INTRO_TITLE' => (string) ($payload['intro_title'] ?? ''),
        'FILES_SECTION_LABEL' => (string) ($payload['files_section_label'] ?? ''),
        'FILES_SECTION_TITLE' => (string) ($payload['files_section_title'] ?? ''),
        'FILES_SECTION_SUBTITLE' => (string) ($payload['files_section_subtitle'] ?? ''),
        'GUIDELINES_TITLE' => (string) ($payload['guidelines_title'] ?? ''),
        'GUIDELINES_SUBTITLE' => (string) ($payload['guidelines_subtitle'] ?? ''),
        'COMPANY_ABOUT_TEXT' => (string) ($settings['company_about_text'] ?? ''),
        'COMPANY_ACCEPT_PHRASE' => (string) ($settings['company_accept_phrase'] ?? ''),
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
        'PAYMENT_NOTE_STYLE' => $paymentMethodsCount > 1 ? '' : 'display:none;',
        'PAYMENT_SWITCH_STYLE' => $paymentMethodsCount > 1 ? 'display:inline-flex;' : 'display:none;',
        'PAYMENT_CARD_ENABLED' => $cardEnabled ? '1' : '0',
        'PAYMENT_CARD_STYLE' => $cardEnabled ? '' : 'display:none;',
        'PAYMENT_CARD_TITLE' => (string) ($payload['pagamento_cartao_titulo'] ?? 'Pagamento por cartão'),
        'PAYMENT_CARD_DESC' => (string) ($payload['pagamento_cartao_descricao'] ?? ''),
        'PAYMENT_CARD_LINK' => $cardLink,
        'PAYMENT_CARD_BUTTON' => (string) ($payload['pagamento_cartao_botao'] ?? 'Pagar no cartão'),
        'PAYMENT_BOLETO_ENABLED' => $boletoEnabled ? '1' : '0',
        'PAYMENT_BOLETO_STYLE' => $boletoEnabled ? '' : 'display:none;',
        'PAYMENT_BOLETO_TITLE' => (string) ($payload['pagamento_boleto_titulo'] ?? 'Pagamento por boleto'),
        'PAYMENT_BOLETO_DESC' => (string) ($payload['pagamento_boleto_descricao'] ?? ''),
        'PAYMENT_BOLETO_LINK' => $boletoLink,
        'PAYMENT_BOLETO_BUTTON' => (string) ($payload['pagamento_boleto_botao'] ?? 'Abrir boleto'),
        'FORMA_PAGAMENTO_BASE' => $paymentBaseText,
        'FORMA_PAGAMENTO_EXTRA' => $paymentExtraText,
        'LINK_PROPOSTA_ORIGINAL' => (string) (proposal_public_url($proposal) ?? ''),
        'PROPOSTA_LINK_ORIGINAL' => (string) (proposal_public_url($proposal) ?? ''),
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
        'ARQUIVO_01_DATA' => $getArquivo(0, 'data'),
        'ARQUIVO_01_LINK' => $getArquivo(0, 'data'),
        'ARQUIVO_02' => $getArquivo(1, 'nome'),
        'ARQUIVO_02_REV' => $getArquivoRev(1),
        'ARQUIVO_02_DATA' => $getArquivo(1, 'data'),
        'ARQUIVO_02_LINK' => $getArquivo(1, 'data'),
        'ARQUIVO_03' => $getArquivo(2, 'nome'),
        'ARQUIVO_03_REV' => $getArquivoRev(2),
        'ARQUIVO_03_DATA' => $getArquivo(2, 'data'),
        'ARQUIVO_03_LINK' => $getArquivo(2, 'data'),
        'ARQUIVO_04' => $getArquivo(3, 'nome'),
        'ARQUIVO_04_REV' => $getArquivoRev(3),
        'ARQUIVO_04_DATA' => $getArquivo(3, 'data'),
        'ARQUIVO_04_LINK' => $getArquivo(3, 'data'),
        'ARQUIVO_05' => $getArquivo(4, 'nome'),
        'ARQUIVO_05_REV' => $getArquivoRev(4),
        'ARQUIVO_05_DATA' => $getArquivo(4, 'data'),
        'ARQUIVO_05_LINK' => $getArquivo(4, 'data'),
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

function proposal_scope_entries(array $payload): array
{
    $catalog = discipline_catalog();
    $scopeItems = is_array($payload['scope_items'] ?? null) ? $payload['scope_items'] : [];
    $selected = is_array($payload['disciplinas'] ?? null) ? $payload['disciplinas'] : [];
    $values = is_array($payload['valores'] ?? null) ? $payload['valores'] : [];
    $entries = [];

    foreach ($selected as $key) {
        if (!isset($catalog[$key])) {
            continue;
        }

        $defaultItem = default_scope_blueprints()[$key] ?? [];
        $item = is_array($scopeItems[$key] ?? null) ? $scopeItems[$key] : [];
        $merged = array_replace_recursive($defaultItem, $item);
        $entries[] = [
            'key' => $key,
            'title' => trim((string) ($merged['title'] ?? $catalog[$key]['nome'] ?? 'Disciplina')),
            'subtitle' => trim((string) ($merged['subtitle'] ?? '')),
            'summary' => trim((string) ($merged['summary'] ?? ($catalog[$key]['descricao'] ?? ''))),
            'icon' => trim((string) ($merged['icon'] ?? ($catalog[$key]['icone'] ?? ''))),
            'topics' => array_values(normalize_topic_lines($merged['topics'] ?? [])),
            'value' => round((float) ($values[$key] ?? 0), 2),
        ];
    }

    $customDisciplines = is_array($payload['disciplinas_custom'] ?? null) ? $payload['disciplinas_custom'] : [];
    foreach ($customDisciplines as $item) {
        if (!is_array($item) || !(bool) ($item['ativa'] ?? false)) {
            continue;
        }

        $name = trim((string) ($item['nome'] ?? ''));
        if ($name === '') {
            continue;
        }

        $entries[] = [
            'key' => trim((string) ($item['key'] ?? '')),
            'title' => $name,
            'subtitle' => trim((string) ($item['subtitle'] ?? '')),
            'summary' => trim((string) ($item['descricao'] ?? '')),
            'icon' => trim((string) ($item['icone'] ?? '')),
            'topics' => array_values(normalize_topic_lines($item['topics'] ?? [])),
            'value' => round((float) ($item['valor'] ?? 0), 2),
        ];
    }

    return $entries;
}

function proposal_guideline_entries(array $payload): array
{
    $items = is_array($payload['guidelines_items'] ?? null) ? $payload['guidelines_items'] : [];
    $entries = [];
    foreach ($items as $item) {
        if (!is_array($item)) {
            continue;
        }
        $title = trim((string) ($item['title'] ?? ''));
        $content = trim((string) ($item['content'] ?? ''));
        if ($title === '' && $content === '') {
            continue;
        }
        $entries[] = [
            'title' => $title !== '' ? $title : 'Diretriz',
            'content' => $content,
            'icon' => trim((string) ($item['icon'] ?? '')),
        ];
    }

    return $entries;
}

function proposal_file_entries(array $payload): array
{
    $files = is_array($payload['arquivos'] ?? null) ? $payload['arquivos'] : [];
    $entries = [];
    foreach ($files as $file) {
        if (!is_array($file)) {
            continue;
        }
        $item = trim((string) ($file['item'] ?? ''));
        $name = trim((string) ($file['nome'] ?? ''));
        $revision = trim((string) ($file['rev'] ?? ''));
        $date = trim((string) ($file['data'] ?? ($file['link'] ?? '')));
        if ($item === '' && $name === '') {
            continue;
        }
        $entries[] = [
            'item' => $item,
            'name' => $name,
            'revision' => $revision === '00' ? '' : $revision,
            'date' => $date,
        ];
    }

    return $entries;
}

function proposal_timeline_entries(array $payload): array
{
    $stages = is_array($payload['etapas'] ?? null) ? $payload['etapas'] : [];
    $entries = [];
    foreach ($stages as $index => $stage) {
        if (!is_array($stage)) {
            continue;
        }
        $name = trim((string) ($stage['nome'] ?? ''));
        if ($name === '') {
            continue;
        }
        $badge = trim((string) ($stage['prazo'] ?? ''));
        if ($badge === '' && isset($stage['percentual'])) {
            $pct = (int) ($stage['percentual'] ?? 0);
            $badge = $pct > 0 ? ($pct . '%') : 'Etapa';
        }
        $entries[] = [
            'index' => $index + 1,
            'label' => ($index + 1) . 'ª',
            'name' => $name,
            'badge' => $badge !== '' ? $badge : 'Etapa',
            'description' => trim((string) ($stage['descricao'] ?? '')),
        ];
    }

    return $entries;
}

function render_proposal_tokens(string $content, array $replaceMap, bool $allowHtml = false): string
{
    $tokens = [];
    foreach ($replaceMap as $key => $value) {
        $tokens['{{' . $key . '}}'] = (string) $value;
    }

    $rendered = strtr($content, $tokens);
    if ($allowHtml) {
        return $rendered;
    }

    return nl2br(h($rendered));
}

function render_terms_html(string $content, array $replaceMap): string
{
    $trimmed = trim($content);
    if ($trimmed === '') {
        return '';
    }

    if (preg_match('/<[^>]+>/', $trimmed) === 1) {
        return render_proposal_tokens($trimmed, $replaceMap, true);
    }

    return '<p>' . render_proposal_tokens($trimmed, $replaceMap, false) . '</p>';
}

function strip_html_to_plaintext(string $html): string
{
    $text = preg_replace('/<br\s*\/?>/i', "\n", $html) ?? $html;
    $text = preg_replace('/<\/p>/i', "\n\n", $text) ?? $text;
    $text = preg_replace('/<\/li>/i', "\n", $text) ?? $text;
    $text = preg_replace('/<\/h[1-6]>/i', "\n\n", $text) ?? $text;
    $text = html_entity_decode(strip_tags($text), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    $text = preg_replace("/\n{3,}/", "\n\n", $text) ?? $text;
    return trim((string) $text);
}

function proposal_contract_render_data(array $proposal, array $payload, array $settings): array
{
    $replaceMap = build_proposal_placeholder_map($proposal, $payload, $settings);
    $plainTokens = [];
    foreach ($replaceMap as $key => $value) {
        $plainTokens['{{' . $key . '}}'] = html_entity_decode((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
    $renderPlainText = static function (string $text) use ($plainTokens): string {
        return trim(strtr($text, $plainTokens));
    };

    $title = trim((string) ($payload['accept_terms_title'] ?? ''));
    if ($title === '') {
        $title = trim((string) ($settings['accept_terms_title'] ?? ''));
    }
    if ($title === '') {
        $title = 'Contrato da proposta';
    }
    $title = $renderPlainText($title);

    $checkbox = trim((string) ($payload['accept_terms_checkbox_text'] ?? ''));
    if ($checkbox === '') {
        $checkbox = trim((string) ($settings['accept_terms_checkbox_text'] ?? ''));
    }
    if ($checkbox === '') {
        $checkbox = 'Li e concordo com os termos deste contrato.';
    }
    $checkbox = $renderPlainText($checkbox);

    $terms = trim((string) ($payload['accept_terms_html'] ?? ''));
    if ($terms === '') {
        $terms = trim((string) ($settings['accept_terms_html'] ?? ''));
    }

    $bodyHtml = render_terms_html($terms, $replaceMap);
    if (trim(strip_tags($bodyHtml)) === '') {
        $bodyHtml = '<p>Ao aceitar esta proposta, as partes concordam com o escopo, os valores e as condições descritas neste documento.</p>';
    }

    return [
        'mode' => 'contract',
        'title' => $title,
        'checkbox_text' => $checkbox,
        'body_html' => $bodyHtml,
    ];
}

function render_proposal_summary_fragment(array $proposal, array $payload, array $settings): string
{
    $scopeEntries = proposal_scope_entries($payload);
    $files = proposal_file_entries($payload);
    $stages = proposal_timeline_entries($payload);
    $considerations = array_values(normalize_topic_lines($payload['consideracoes'] ?? []));
    $exclusions = array_values(normalize_topic_lines($payload['exclusoes'] ?? []));
    $replaceMap = build_proposal_placeholder_map($proposal, $payload, $settings);
    $tokens = [];
    foreach ($replaceMap as $key => $value) {
        $tokens['{{' . $key . '}}'] = html_entity_decode((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
    $renderText = static function (string $value) use ($tokens): string {
        return strtr($value, $tokens);
    };
    $revision = normalize_revision_for_display((string) ($payload['revisao'] ?? ($proposal['revision'] ?? '00')));
    $code = (string) ($payload['codigo_base'] ?? ($proposal['code'] ?? ''));
    $proposalDate = parse_ymd((string) ($payload['data_proposta'] ?? date('Y-m-d')))->format('d/m/Y');
    $total = proposal_total($payload);
    $publicUrl = proposal_public_url($proposal);
    $paymentSchedule = proposal_payment_schedule_entries($payload);
    $allowImages = pdf_embedded_images_available();

    ob_start();
    ?>
    <div class="proposal-summary-doc" style="font-family:Arial,sans-serif;color:#173942;line-height:1.6;">
      <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:24px;margin-bottom:24px;padding-bottom:18px;border-bottom:2px solid #d8ecef;">
        <div style="display:flex;align-items:center;gap:16px;">
          <?php if ($allowImages): ?>
            <img src="<?= h(app_url('/assets/img/logohorizontalbranco.png')) ?>" alt="Complementare" style="height:42px;width:auto;background:#0d4854;border-radius:10px;padding:10px 14px;">
          <?php endif; ?>
          <div>
            <div style="font-size:12px;letter-spacing:1.5px;text-transform:uppercase;color:#178f9c;font-weight:700;">Proposta Comercial</div>
            <h2 style="margin:4px 0 0;font-size:28px;line-height:1.1;"><?= h((string) ($payload['titulo'] ?? 'Proposta Comercial')) ?></h2>
          </div>
        </div>
        <div style="display:grid;gap:8px;min-width:220px;">
          <div style="background:#f3fbfc;border:1px solid #d5edf0;border-radius:14px;padding:12px 14px;"><strong>Proposta:</strong> <?= h($code) ?></div>
          <?php if ($revision !== ''): ?>
            <div style="background:#f3fbfc;border:1px solid #d5edf0;border-radius:14px;padding:12px 14px;"><strong>Revisão:</strong> <?= h($revision) ?></div>
          <?php endif; ?>
          <div style="background:#f3fbfc;border:1px solid #d5edf0;border-radius:14px;padding:12px 14px;"><strong>Data:</strong> <?= h($proposalDate) ?></div>
        </div>
      </div>

      <div style="display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:16px;margin-bottom:24px;">
        <div style="background:#ffffff;border:1px solid #d5edf0;border-radius:18px;padding:18px;">
          <div style="font-size:12px;text-transform:uppercase;letter-spacing:1.2px;color:#178f9c;font-weight:700;">Cliente</div>
          <div style="margin-top:8px;font-size:18px;font-weight:700;"><?= h((string) ($payload['cliente_nome'] ?: ($payload['cliente_empresa'] ?: 'Cliente'))) ?></div>
          <?php if (trim((string) ($payload['cliente_empresa'] ?? '')) !== ''): ?><div><?= h((string) $payload['cliente_empresa']) ?></div><?php endif; ?>
          <?php if (trim((string) ($payload['cliente_cnpj'] ?? '')) !== ''): ?><div>CNPJ: <?= h((string) $payload['cliente_cnpj']) ?></div><?php endif; ?>
          <?php if (trim((string) ($payload['cliente_email'] ?? '')) !== ''): ?><div><?= h((string) $payload['cliente_email']) ?></div><?php endif; ?>
          <?php if (trim((string) ($payload['cliente_telefone'] ?? '')) !== ''): ?><div><?= h((string) $payload['cliente_telefone']) ?></div><?php endif; ?>
        </div>
        <div style="background:#ffffff;border:1px solid #d5edf0;border-radius:18px;padding:18px;">
          <div style="font-size:12px;text-transform:uppercase;letter-spacing:1.2px;color:#178f9c;font-weight:700;">Obra</div>
          <div style="margin-top:8px;font-size:18px;font-weight:700;"><?= h((string) ($payload['obra_nome'] ?: 'Não informado')) ?></div>
          <?php if (trim((string) ($payload['obra_endereco'] ?? '')) !== ''): ?><div><?= h((string) $payload['obra_endereco']) ?></div><?php endif; ?>
          <div><?= h(trim((string) ($payload['obra_cidade'] ?? '') . ' / ' . (string) ($payload['obra_uf'] ?? ''))) ?></div>
        </div>
      </div>

      <section style="margin-bottom:24px;">
        <h3 style="margin:0 0 10px;font-size:20px;">Arquivos Recebidos</h3>
        <p style="margin:0 0 12px;color:#54767d;"><?= h($renderText((string) ($payload['files_section_subtitle'] ?? ''))) ?></p>
        <?php if ($files !== []): ?>
          <table style="width:100%;border-collapse:collapse;border:1px solid #d5edf0;border-radius:16px;overflow:hidden;">
            <thead>
              <tr style="background:#0d8c98;color:#fff;">
                <th style="padding:10px 12px;text-align:left;">Arquivo</th>
                <th style="padding:10px 12px;text-align:center;">Revisão</th>
                <th style="padding:10px 12px;text-align:center;">Data</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($files as $file): ?>
                <tr>
                  <td style="padding:10px 12px;border-top:1px solid #e5f2f4;"><?= h(trim($file['item'] . ' ' . $file['name'])) ?></td>
                  <td style="padding:10px 12px;border-top:1px solid #e5f2f4;text-align:center;"><?= h($file['revision']) ?></td>
                  <td style="padding:10px 12px;border-top:1px solid #e5f2f4;text-align:center;"><?= h($file['date']) ?></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        <?php endif; ?>
      </section>

      <section style="margin-bottom:24px;">
        <h3 style="margin:0 0 10px;font-size:20px;">Escopo da Proposta</h3>
        <div style="display:grid;gap:14px;">
          <?php foreach ($scopeEntries as $entry): ?>
            <div style="border:1px solid #d5edf0;border-radius:18px;padding:18px;background:#fff;">
              <div style="display:flex;align-items:center;gap:12px;margin-bottom:10px;">
                <?php if ($allowImages && $entry['icon'] !== ''): ?><img src="<?= h(app_url($entry['icon'])) ?>" alt="<?= h($entry['title']) ?>" style="width:40px;height:40px;object-fit:contain;"><?php endif; ?>
                <div>
                  <div style="font-size:18px;font-weight:700;"><?= h($entry['title']) ?></div>
                  <?php if ($entry['subtitle'] !== ''): ?><div style="color:#54767d;"><?= h($entry['subtitle']) ?></div><?php endif; ?>
                </div>
              </div>
              <?php if ($entry['topics'] !== []): ?>
                <ul style="margin:0;padding-left:18px;">
                  <?php foreach ($entry['topics'] as $topic): ?><li><?= h((string) $topic) ?></li><?php endforeach; ?>
                </ul>
              <?php elseif ($entry['summary'] !== ''): ?>
                <p style="margin:0;"><?= h($entry['summary']) ?></p>
              <?php endif; ?>
            </div>
          <?php endforeach; ?>
        </div>
      </section>

      <section style="margin-bottom:24px;">
        <h3 style="margin:0 0 10px;font-size:20px;">Etapas e Prazos</h3>
        <div style="display:grid;gap:10px;">
          <?php foreach ($stages as $stage): ?>
            <div style="display:grid;grid-template-columns:110px 1fr;gap:12px;align-items:start;border:1px solid #d5edf0;border-radius:16px;padding:14px;background:#fff;">
              <div style="display:flex;align-items:center;justify-content:center;border-radius:999px;background:#0fb2af;color:#fff;font-weight:700;padding:8px 12px;"><?= h($stage['badge']) ?></div>
              <div>
                <div style="font-weight:700;"><?= h($stage['name']) ?></div>
                <?php if ($stage['description'] !== ''): ?><div style="color:#54767d;"><?= h($stage['description']) ?></div><?php endif; ?>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      </section>

      <section style="margin-bottom:24px;">
        <h3 style="margin:0 0 10px;font-size:20px;">Valor da Proposta</h3>
        <div style="background:linear-gradient(135deg,#0d8c98,#18b5af);color:#fff;border-radius:18px;padding:18px;margin-bottom:12px;">
          <div style="font-size:12px;text-transform:uppercase;letter-spacing:1.2px;">Total</div>
          <div style="font-size:34px;font-weight:800;margin-top:4px;"><?= h(brl($total)) ?></div>
          <div style="margin-top:6px;opacity:.9;"><?= h(currency_to_words_ptbr($total)) ?></div>
        </div>
        <table style="width:100%;border-collapse:collapse;border:1px solid #d5edf0;border-radius:16px;overflow:hidden;">
          <thead>
            <tr style="background:#f3fbfc;">
              <th style="padding:10px 12px;text-align:left;">Disciplina</th>
              <th style="padding:10px 12px;text-align:right;">Valor</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($scopeEntries as $entry): ?>
              <tr>
                <td style="padding:10px 12px;border-top:1px solid #e5f2f4;"><?= h($entry['title']) ?></td>
                <td style="padding:10px 12px;border-top:1px solid #e5f2f4;text-align:right;"><?= h(brl((float) $entry['value'])) ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </section>

      <?php if ($paymentSchedule !== []): ?>
        <section style="margin-bottom:24px;">
          <h3 style="margin:0 0 10px;font-size:20px;">Forma de Pagamento</h3>
          <div style="display:grid;gap:10px;">
            <?php foreach ($paymentSchedule as $entry): ?>
              <?php if (($entry['type'] ?? 'line') === 'subtitle'): ?>
                <div style="font-weight:700;color:#0d4854;margin-top:4px;"><?= h((string) $entry['label']) ?></div>
              <?php else: ?>
                <div style="display:flex;justify-content:space-between;gap:16px;border:1px solid #d5edf0;border-radius:16px;padding:14px;background:#fff;">
                  <span><?= h((string) $entry['label']) ?></span>
                  <strong><?= h((string) $entry['amount_label']) ?></strong>
                </div>
              <?php endif; ?>
            <?php endforeach; ?>
          </div>
        </section>
      <?php endif; ?>

      <?php if ($considerations !== []): ?>
        <section style="margin-bottom:24px;">
          <h3 style="margin:0 0 10px;font-size:20px;">Considerações Importantes</h3>
          <ul style="margin:0;padding-left:18px;"><?php foreach ($considerations as $item): ?><li><?= h((string) $item) ?></li><?php endforeach; ?></ul>
        </section>
      <?php endif; ?>

      <?php if ($exclusions !== []): ?>
        <section>
          <h3 style="margin:0 0 10px;font-size:20px;">Itens Fora do Escopo</h3>
          <ol type="A" style="margin:0;padding-left:22px;"><?php foreach ($exclusions as $item): ?><li><?= h((string) $item) ?></li><?php endforeach; ?></ol>
        </section>
      <?php endif; ?>

      <?php if ($publicUrl): ?>
        <section style="margin-top:24px;">
          <h3 style="margin:0 0 10px;font-size:20px;">Link da Proposta Original</h3>
          <p style="margin:0;">
            <a href="<?= h($publicUrl) ?>" style="color:#0d8c98;text-decoration:none;word-break:break-all;"><?= h($publicUrl) ?></a>
          </p>
        </section>
      <?php endif; ?>
    </div>
    <?php

    return trim((string) ob_get_clean());
}

function render_proposal_summary_page(array $proposal, array $payload, array $settings): string
{
    $title = 'Resumo - Proposta ' . (string) ($proposal['code'] ?? ($payload['codigo_base'] ?? ''));
    $body = render_proposal_summary_fragment($proposal, $payload, $settings);

    return '<!doctype html><html lang="pt-BR"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">' .
        '<title>' . h($title) . '</title>' .
        '<style>body{margin:0;padding:32px;background:#eef7f8;} .summary-page{max-width:980px;margin:0 auto;background:#fff;border-radius:28px;padding:32px;box-shadow:0 20px 60px rgba(11,85,99,.12);} @media print{body{background:#fff;padding:0}.summary-page{max-width:none;box-shadow:none;border-radius:0;padding:0}}</style>' .
        '</head><body><main class="summary-page">' . $body . '</main></body></html>';
}

function render_proposal_contract_page(array $proposal, array $payload, array $settings): string
{
    $contract = proposal_contract_render_data($proposal, $payload, $settings);
    $revision = normalize_revision_for_display((string) ($payload['revisao'] ?? ($proposal['revision'] ?? '00')));
    $code = (string) ($payload['codigo_base'] ?? ($proposal['code'] ?? ''));
    $proposalDate = parse_ymd((string) ($payload['data_proposta'] ?? date('Y-m-d')))->format('d/m/Y');
    $client = trim((string) ($payload['cliente_nome'] ?: ($payload['cliente_empresa'] ?: 'Cliente')));
    $obra = trim((string) ($payload['obra_nome'] ?: 'Não informado'));
    $files = proposal_file_entries($payload);
    $scopeEntries = proposal_scope_entries($payload);
    $paymentSchedule = proposal_payment_schedule_entries($payload);
    $considerations = array_values(normalize_topic_lines($payload['consideracoes'] ?? []));
    $exclusions = array_values(normalize_topic_lines($payload['exclusoes'] ?? []));
    $total = proposal_total($payload);
    $publicUrl = proposal_public_url($proposal);
    $allowImages = pdf_embedded_images_available();

    ob_start();
    ?>
    <!doctype html>
    <html lang="pt-BR">
    <head>
      <meta charset="utf-8">
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <title><?= h((string) $contract['title']) ?> - <?= h($code) ?></title>
      <style>
        body{margin:0;padding:32px;background:#eef7f8;font-family:Arial,sans-serif;color:#173942;}
        .contract-page{max-width:980px;margin:0 auto;background:#fff;border-radius:28px;padding:32px;box-shadow:0 20px 60px rgba(11,85,99,.12);}
        .contract-head{display:flex;justify-content:space-between;gap:24px;align-items:flex-start;padding-bottom:18px;margin-bottom:24px;border-bottom:2px solid #d8ecef;}
        .contract-brand{display:flex;align-items:center;gap:16px;}
        .contract-meta{display:grid;gap:8px;min-width:220px;}
        .contract-chip{background:#f3fbfc;border:1px solid #d5edf0;border-radius:14px;padding:12px 14px;}
        .contract-summary{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:16px;margin-bottom:24px;}
        .contract-box{background:#fff;border:1px solid #d5edf0;border-radius:18px;padding:18px;}
        .contract-box-label{font-size:12px;text-transform:uppercase;letter-spacing:1.2px;color:#178f9c;font-weight:700;}
        .contract-body,.contract-annex{border:1px solid #d5edf0;border-radius:22px;padding:24px;background:#fff;line-height:1.7;}
        .contract-body h1,.contract-body h2,.contract-body h3,.contract-annex h2,.contract-annex h3{color:#0d4854;}
        .contract-annex{margin-top:24px;}
        .contract-annex section + section{margin-top:24px;}
        .contract-table{width:100%;border-collapse:collapse;border:1px solid #d5edf0;border-radius:16px;overflow:hidden;}
        .contract-table th,.contract-table td{padding:10px 12px;border-top:1px solid #e5f2f4;text-align:left;}
        .contract-table thead th{background:#0d8c98;color:#fff;border-top:none;}
        .contract-scope{display:grid;gap:14px;}
        .contract-scope-item{border:1px solid #d5edf0;border-radius:18px;padding:18px;background:#fdfefe;}
        .contract-scope-head{display:flex;align-items:center;gap:12px;margin-bottom:10px;}
        .contract-total{display:inline-flex;align-items:center;justify-content:center;padding:12px 18px;border-radius:999px;background:#0d8c98;color:#fff;font-weight:700;}
        @media (max-width:720px){body{padding:16px}.contract-page{padding:22px;border-radius:20px}.contract-head,.contract-summary{display:grid}.contract-meta{min-width:0;}}
        @media print{body{background:#fff;padding:0}.contract-page{max-width:none;border-radius:0;box-shadow:none;padding:0}}
      </style>
    </head>
    <body>
      <main class="contract-page">
        <div class="contract-head">
          <div class="contract-brand">
            <?php if ($allowImages): ?>
              <img src="<?= h(app_url('/assets/img/logohorizontalbranco.png')) ?>" alt="Complementare" style="height:42px;width:auto;background:#0d4854;border-radius:10px;padding:10px 14px;">
            <?php endif; ?>
            <div>
              <div style="font-size:12px;letter-spacing:1.5px;text-transform:uppercase;color:#178f9c;font-weight:700;">Documento de aceite</div>
              <h1 style="margin:4px 0 0;font-size:28px;line-height:1.1;"><?= h((string) $contract['title']) ?></h1>
            </div>
          </div>
          <div class="contract-meta">
            <div class="contract-chip"><strong>Proposta:</strong> <?= h($code) ?></div>
            <?php if ($revision !== ''): ?><div class="contract-chip"><strong>Revisão:</strong> <?= h($revision) ?></div><?php endif; ?>
            <div class="contract-chip"><strong>Data:</strong> <?= h($proposalDate) ?></div>
          </div>
        </div>

        <div class="contract-summary">
          <div class="contract-box">
            <div class="contract-box-label">Cliente</div>
            <div style="margin-top:8px;font-size:18px;font-weight:700;"><?= h($client) ?></div>
            <?php if (trim((string) ($payload['cliente_empresa'] ?? '')) !== ''): ?><div><?= h((string) $payload['cliente_empresa']) ?></div><?php endif; ?>
            <?php if (trim((string) ($payload['cliente_cnpj'] ?? '')) !== ''): ?><div>CNPJ: <?= h((string) $payload['cliente_cnpj']) ?></div><?php endif; ?>
          </div>
          <div class="contract-box">
            <div class="contract-box-label">Obra</div>
            <div style="margin-top:8px;font-size:18px;font-weight:700;"><?= h($obra) ?></div>
          </div>
        </div>

        <section class="contract-body">
          <?= (string) ($contract['body_html'] ?? '') ?>
        </section>

        <section class="contract-annex">
          <?php if ($files !== []): ?>
            <section>
              <h2 style="margin:0 0 12px;">Arquivos recebidos</h2>
              <table class="contract-table">
                <thead>
                  <tr><th>Arquivo</th><th style="text-align:center;">Revisão</th><th style="text-align:center;">Data</th></tr>
                </thead>
                <tbody>
                  <?php foreach ($files as $file): ?>
                    <tr>
                      <td><?= h(trim((string) ($file['item'] ?? '') . ' ' . (string) ($file['name'] ?? ''))) ?></td>
                      <td style="text-align:center;"><?= h((string) ($file['revision'] ?? '')) ?></td>
                      <td style="text-align:center;"><?= h((string) ($file['date'] ?? '')) ?></td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </section>
          <?php endif; ?>

          <?php if ($scopeEntries !== []): ?>
            <section>
              <h2 style="margin:0 0 12px;">Escopo da proposta</h2>
              <div class="contract-scope">
                <?php foreach ($scopeEntries as $entry): ?>
                  <article class="contract-scope-item">
                    <div class="contract-scope-head">
                      <?php if ($allowImages && trim((string) ($entry['icon'] ?? '')) !== ''): ?><img src="<?= h(app_url((string) $entry['icon'])) ?>" alt="<?= h((string) ($entry['title'] ?? 'Disciplina')) ?>" style="width:40px;height:40px;object-fit:contain;"><?php endif; ?>
                      <div>
                        <div style="font-size:18px;font-weight:700;"><?= h((string) ($entry['title'] ?? 'Disciplina')) ?></div>
                        <?php if (trim((string) ($entry['subtitle'] ?? '')) !== ''): ?><div style="color:#54767d;"><?= h((string) $entry['subtitle']) ?></div><?php endif; ?>
                      </div>
                    </div>
                    <?php if (!empty($entry['topics'])): ?>
                      <ul style="margin:0;padding-left:18px;"><?php foreach ((array) $entry['topics'] as $topic): ?><li><?= h((string) $topic) ?></li><?php endforeach; ?></ul>
                    <?php elseif (trim((string) ($entry['summary'] ?? '')) !== ''): ?>
                      <p style="margin:0;"><?= h((string) $entry['summary']) ?></p>
                    <?php endif; ?>
                  </article>
                <?php endforeach; ?>
              </div>
            </section>
          <?php endif; ?>

          <section>
            <h2 style="margin:0 0 12px;">Valor total</h2>
            <div class="contract-total"><?= h(brl($total)) ?> | <?= h(currency_to_words_ptbr($total)) ?></div>
          </section>

          <?php if ($paymentSchedule !== []): ?>
            <section>
              <h2 style="margin:0 0 12px;">Forma de pagamento</h2>
              <div style="display:grid;gap:10px;">
                <?php foreach ($paymentSchedule as $entry): ?>
                  <?php if (($entry['type'] ?? 'line') === 'subtitle'): ?>
                    <strong><?= h((string) $entry['label']) ?></strong>
                  <?php else: ?>
                    <div style="display:flex;justify-content:space-between;gap:16px;border:1px solid #d5edf0;border-radius:14px;padding:12px 14px;">
                      <span><?= h((string) $entry['label']) ?></span>
                      <strong><?= h((string) $entry['amount_label']) ?></strong>
                    </div>
                  <?php endif; ?>
                <?php endforeach; ?>
              </div>
            </section>
          <?php endif; ?>

          <?php if ($considerations !== []): ?>
            <section>
              <h3 style="margin:0 0 10px;">Considerações importantes</h3>
              <ul style="margin:0;padding-left:18px;"><?php foreach ($considerations as $item): ?><li><?= h((string) $item) ?></li><?php endforeach; ?></ul>
            </section>
          <?php endif; ?>

          <?php if ($exclusions !== []): ?>
            <section>
              <h3 style="margin:0 0 10px;">Itens fora do escopo</h3>
              <ul style="margin:0;padding-left:18px;"><?php foreach ($exclusions as $item): ?><li><?= h((string) $item) ?></li><?php endforeach; ?></ul>
            </section>
          <?php endif; ?>

          <?php if ($publicUrl): ?>
            <section>
              <h3 style="margin:0 0 10px;">Link da proposta original</h3>
              <p style="margin:0;"><a href="<?= h($publicUrl) ?>" style="color:#0d8c98;text-decoration:none;word-break:break-all;"><?= h($publicUrl) ?></a></p>
            </section>
          <?php endif; ?>
        </section>
      </main>
    </body>
    </html>
    <?php

    return trim((string) ob_get_clean());
}

function proposal_acceptance_render_data(array $proposal, array $payload, array $settings): array
{
    if (proposal_acceptance_mode($payload) !== 'summary') {
        return proposal_contract_render_data($proposal, $payload, $settings);
    }

    $replaceMap = build_proposal_placeholder_map($proposal, $payload, $settings);
    $plainTokens = [];
    foreach ($replaceMap as $key => $value) {
        $plainTokens['{{' . $key . '}}'] = html_entity_decode((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
    $renderPlainText = static function (string $text) use ($plainTokens): string {
        return trim(strtr($text, $plainTokens));
    };

    $summaryIntro = trim((string) ($payload['accept_summary_html'] ?? ''));
    $summaryHtml = render_proposal_summary_fragment($proposal, $payload, $settings);
    if ($summaryIntro !== '') {
        $summaryHtml = '<section style="margin-bottom:20px;">' . render_terms_html($summaryIntro, $replaceMap) . '</section>' . $summaryHtml;
    }

    $checkbox = trim((string) ($payload['accept_terms_checkbox_text'] ?? ''));
    if ($checkbox === '') {
        $checkbox = 'Li e concordo com o resumo desta proposta.';
    }

    return [
        'mode' => 'summary',
        'title' => 'Resumo da proposta',
        'checkbox_text' => $renderPlainText($checkbox),
        'body_html' => $summaryHtml,
    ];
}

function proposal_public_render_data(array $proposal, array $payload, array $settings): array
{
    $replaceMap = build_proposal_placeholder_map($proposal, $payload, $settings);
    $tokens = [];
    foreach ($replaceMap as $key => $value) {
        $tokens['{{' . $key . '}}'] = html_entity_decode((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
    $renderText = static function (string $value) use ($tokens): string {
        return strtr($value, $tokens);
    };

    $heroMediaUrl = $renderText((string) ($payload['header_custom_media_url'] ?? ''));

    return [
        'intro_title' => $renderText((string) ($payload['intro_title'] ?? '')),
        'company_about_text' => (string) ($settings['company_about_text'] ?? ''),
        'company_accept_phrase' => (string) ($settings['company_accept_phrase'] ?? ''),
        'client_name' => $renderText((string) ($payload['cliente_nome'] ?? '')),
        'client_company' => $renderText((string) ($payload['cliente_empresa'] ?? '')),
        'client_cnpj' => $renderText((string) ($payload['cliente_cnpj'] ?? '')),
        'public_url' => proposal_public_url($proposal) ?? '',
        'hero_header' => [
            'layout' => (string) ($payload['header_title_layout'] ?? 'default'),
            'kicker' => $renderText((string) ($payload['header_aditivo_kicker'] ?? 'PROPOSTA')),
            'title' => $renderText((string) ($payload['header_aditivo_title'] ?? 'ADITIVO DA PROPOSTA')),
            'code' => $renderText((string) ($payload['codigo_base'] ?? ($proposal['code'] ?? ''))),
        ],
        'hero_media' => [
            'enabled' => proposal_flag_enabled($payload['header_custom_media_enabled'] ?? false),
            'url' => $heroMediaUrl,
            'type' => proposal_media_type_from_url($heroMediaUrl),
        ],
        'files_section' => [
            'label' => $renderText((string) ($payload['files_section_label'] ?? '')),
            'title' => $renderText((string) ($payload['files_section_title'] ?? '')),
            'subtitle' => $renderText((string) ($payload['files_section_subtitle'] ?? '')),
        ],
        'guidelines' => [
            'title' => (string) ($payload['guidelines_title'] ?? ''),
            'subtitle' => (string) ($payload['guidelines_subtitle'] ?? ''),
            'items' => proposal_guideline_entries($payload),
        ],
        'scope_entries' => proposal_scope_entries($payload),
        'timeline' => proposal_timeline_entries($payload),
        'files' => proposal_file_entries($payload),
        'payment_schedule' => proposal_payment_schedule_entries($payload),
        'considerations' => array_values(normalize_topic_lines($payload['consideracoes'] ?? [])),
        'exclusions' => array_values(normalize_topic_lines($payload['exclusoes'] ?? [])),
        'acceptance' => proposal_acceptance_render_data($proposal, $payload, $settings),
    ];
}

function proposal_runtime_script(
    string $token,
    string $downloadUrl,
    string $signUrl,
    array $publicData,
    bool $previewMode
): string {
    $publicJson = json_encode($publicData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    if (!is_string($publicJson)) {
        $publicJson = '{}';
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
  const publicData = {$publicJson};

  function formatBrl(value) {
    const numeric = Number(value || 0);
    return new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(numeric);
  }

  function escapeHtml(value) {
    return String(value || '')
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;')
      .replace(/'/g, '&#039;');
  }

  function toParagraphs(text) {
    return String(text || '')
      .split(/\\n{2,}/)
      .map((item) => item.trim())
      .filter(Boolean);
  }

  function renderIntro() {
    const introRight = document.querySelector('.intro-right');
    if (!introRight) return;

    const about = toParagraphs(publicData.company_about_text || '');
    const introTitle = String(publicData.intro_title || '').trim();
    const acceptPhrase = String(publicData.company_accept_phrase || '').trim();

    let html = '';
    if (introTitle) {
      html += '<h2>' + escapeHtml(introTitle) + '</h2>';
    }
    about.forEach((paragraph) => {
      html += '<p>' + escapeHtml(paragraph) + '</p>';
    });
    if (acceptPhrase) {
      html += '<div class="intro-highlight"><p>' + escapeHtml(acceptPhrase) + '</p></div>';
    }
    if (html !== '') {
      introRight.innerHTML = html;
    }
  }

  function setupHeroMedia() {
    const hero = document.querySelector('.hero');
    if (!hero) return;

    hero.classList.remove('has-custom-media', 'has-custom-video');
    hero.querySelectorAll('.hero-custom-media').forEach((node) => node.remove());

    const media = publicData.hero_media || {};
    const enabled = !!media.enabled;
    const url = String(media.url || '').trim();
    if (!enabled || url === '') {
      return;
    }

    const detectMediaType = (value) => {
      return /\.(mp4|webm|ogg|mov|m4v)(?:$|[?#])/i.test(String(value || '')) ? 'video' : 'image';
    };
    const mediaType = detectMediaType(url);

    const wrapper = document.createElement('div');
    wrapper.className = 'hero-custom-media';

    if (mediaType === 'video') {
      const video = document.createElement('video');
      const source = document.createElement('source');
      const extensionMatch = url.match(/\.([a-z0-9]+)(?:$|[?#])/i);
      const extension = extensionMatch ? extensionMatch[1].toLowerCase() : 'mp4';
      const mimeTypeMap = {
        mp4: 'video/mp4',
        m4v: 'video/mp4',
        webm: 'video/webm',
        ogg: 'video/ogg',
        mov: 'video/quicktime'
      };
      video.className = 'hero-custom-video';
      video.src = url;
      video.setAttribute('src', url);
      video.autoplay = true;
      video.muted = true;
      video.defaultMuted = true;
      video.volume = 0;
      video.loop = true;
      video.playsInline = true;
      video.preload = 'metadata';
      video.setAttribute('autoplay', '');
      video.setAttribute('muted', '');
      video.setAttribute('loop', '');
      video.setAttribute('playsinline', '');
      video.setAttribute('webkit-playsinline', '');
      video.setAttribute('preload', 'metadata');
      video.setAttribute('disablepictureinpicture', '');
      video.controls = false;
      video.poster = '/assets/img/hero-image8.png';
      source.src = url;
      source.type = mimeTypeMap[extension] || 'video/mp4';
      video.appendChild(source);

      const tryPlay = () => {
        video.muted = true;
        video.defaultMuted = true;
        const playAttempt = video.play();
        if (playAttempt && typeof playAttempt.catch === 'function') {
          playAttempt.catch(() => {});
        }
      };

      video.addEventListener('loadedmetadata', tryPlay);
      video.addEventListener('loadeddata', tryPlay);
      video.addEventListener('canplay', tryPlay);
      video.addEventListener('canplaythrough', tryPlay);
      video.addEventListener('playing', tryPlay);
      window.addEventListener('pageshow', tryPlay, { once: true });
      document.addEventListener('visibilitychange', () => {
        if (document.visibilityState === 'visible') {
          tryPlay();
        }
      });
      ['click', 'touchstart', 'keydown', 'scroll'].forEach((eventName) => {
        window.addEventListener(eventName, tryPlay, { once: true, passive: true });
      });
      wrapper.appendChild(video);
      hero.classList.add('has-custom-media', 'has-custom-video');
      requestAnimationFrame(() => {
        video.load();
        tryPlay();
      });
      [300, 900, 1800, 3200].forEach((delay) => {
        window.setTimeout(tryPlay, delay);
      });
    } else {
      const image = document.createElement('div');
      image.className = 'hero-custom-image';
      image.style.backgroundImage = 'url("' + url.replace(/"/g, '\\"') + '")';
      wrapper.appendChild(image);
      hero.classList.add('has-custom-media');
    }

    const firstLayer = hero.querySelector('.hero-grid, .hero-orb, .hero-content');
    if (firstLayer) {
      hero.insertBefore(wrapper, firstLayer);
      return;
    }

    hero.appendChild(wrapper);
  }

  function renderHeroHeading() {
    const group = document.querySelector('.hero-title-group');
    if (!group) return;

    const header = publicData.hero_header || {};
    const layout = String(header.layout || 'default').trim();

    if (layout === 'aditivo') {
      const kicker = String(header.kicker || 'PROPOSTA').trim() || 'PROPOSTA';
      const title = String(header.title || 'ADITIVO DA PROPOSTA').trim() || 'ADITIVO DA PROPOSTA';
      const code = String(header.code || '').trim();

      group.classList.add('hero-title-group-card');
      group.innerHTML =
        '<div class="hero-aditivo-card">' +
          '<div class="hero-aditivo-kicker">' + escapeHtml(kicker) + '</div>' +
          '<div class="hero-aditivo-title">' + escapeHtml(title) + '</div>' +
          (code ? '<div class="hero-aditivo-code">' + escapeHtml(code) + '</div>' : '') +
        '</div>';
      return;
    }

    group.classList.remove('hero-title-group-card');
    group.innerHTML =
      '<h1 class="hero-title">PROPOSTA</h1>' +
      '<p class="hero-subtitle">COMERCIAL</p>';
  }

  function renderHeroClientMeta() {
    const clientBox = document.querySelector('.hero-info-value.hero-info-client');
    if (!clientBox) return;

    const name = String(publicData.client_name || '').trim();
    const company = String(publicData.client_company || '').trim();
    const cnpj = String(publicData.client_cnpj || '').trim();
    const nameNode = clientBox.querySelector('strong');
    const companyNode = clientBox.querySelector('.hero-client-company');
    const cnpjNode = clientBox.querySelector('.hero-client-cnpj');

    if (nameNode && name) {
      nameNode.textContent = name;
    }
    if (companyNode) {
      if (company && company !== name) {
        companyNode.textContent = company;
        companyNode.style.display = '';
      } else {
        companyNode.textContent = '';
        companyNode.style.display = 'none';
      }
    }
    if (cnpjNode) {
      if (cnpj) {
        cnpjNode.textContent = 'CNPJ: ' + cnpj;
        cnpjNode.style.display = '';
      } else {
        cnpjNode.textContent = '';
        cnpjNode.style.display = 'none';
      }
    }
  }

  function renderFilesSection() {
    const section = document.getElementById('arquivos');
    if (!section) return;

    const header = section.querySelector('.section-header');
    if (header) {
      const number = header.querySelector('.section-number');
      const title = header.querySelector('.section-title');
      const subtitle = header.querySelector('.section-subtitle');
      if (number) number.textContent = (publicData.files_section && publicData.files_section.label) || number.textContent;
      if (title) title.textContent = (publicData.files_section && publicData.files_section.title) || title.textContent;
      if (subtitle) subtitle.textContent = (publicData.files_section && publicData.files_section.subtitle) || subtitle.textContent;
    }

    const table = section.querySelector('table');
    if (!table) return;

    const thirdHead = table.querySelector('thead th:nth-child(3)');
    if (thirdHead) thirdHead.textContent = 'Data';

    const tbody = table.querySelector('tbody');
    if (!tbody) return;

    const files = Array.isArray(publicData.files) ? publicData.files : [];
    if (files.length === 0) {
      section.style.display = 'none';
      return;
    }

    tbody.innerHTML = files.map((file) => (
      '<tr>' +
        '<td>' + escapeHtml((file.item ? file.item + ' ' : '') + (file.name || 'Arquivo')) + '</td>' +
        '<td style="text-align:center;">' + escapeHtml(file.revision || '') + '</td>' +
        '<td style="text-align:center;">' + escapeHtml(file.date || '') + '</td>' +
      '</tr>'
    )).join('');
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

  function renderGuidelines() {
    const grid = document.querySelector('.guidelines-grid');
    if (!grid) return;

    const section = grid.closest('section');
    if (!section) return;

    const titleEl = section.querySelector('h3');
    if (titleEl && publicData.guidelines && publicData.guidelines.title) {
      titleEl.textContent = publicData.guidelines.title;
    }

    let subtitleEl = section.querySelector('.guidelines-subtitle');
    const subtitle = String((publicData.guidelines && publicData.guidelines.subtitle) || '').trim();
    if (subtitle !== '') {
      if (!subtitleEl) {
        subtitleEl = document.createElement('p');
        subtitleEl.className = 'guidelines-subtitle fade-in visible';
        subtitleEl.style.margin = '0 0 1.5rem';
        subtitleEl.style.textAlign = 'center';
        subtitleEl.style.color = 'var(--gray-500)';
        if (titleEl) {
          titleEl.insertAdjacentElement('afterend', subtitleEl);
        }
      }
      subtitleEl.textContent = subtitle;
    } else if (subtitleEl) {
      subtitleEl.remove();
    }

    const items = publicData.guidelines && Array.isArray(publicData.guidelines.items) ? publicData.guidelines.items : [];
    if (items.length === 0) {
      section.style.display = 'none';
      return;
    }

    grid.innerHTML = items.map((item) => (
      '<div class="guideline-card fade-in visible">' +
        '<div class="guideline-icon">' +
          (item.icon ? '<img src="' + escapeHtml(item.icon) + '" alt="' + escapeHtml(item.title || 'Diretriz') + '">' : '') +
        '</div>' +
        '<h4>' + escapeHtml(item.title || 'Diretriz') + '</h4>' +
        '<p>' + escapeHtml(item.content || '') + '</p>' +
      '</div>'
    )).join('');
  }

  function renderScope() {
    const etapasSection = document.getElementById('etapas');
    if (!etapasSection) return;

    document.querySelectorAll('.scope-item').forEach((item) => item.remove());
    const entries = Array.isArray(publicData.scope_entries) ? publicData.scope_entries : [];
    const parent = etapasSection.parentNode;

    entries.forEach((entry) => {
      const block = document.createElement('div');
      block.className = 'scope-item fade-in visible';
      block.innerHTML =
        '<div class="scope-icon">' + (entry.icon ? '<img src="' + escapeHtml(entry.icon) + '" alt="' + escapeHtml(entry.title || 'Disciplina') + '">' : '') + '</div>' +
        '<div class="scope-content">' +
          '<h3>' + escapeHtml(entry.title || 'Disciplina') + '</h3>' +
          (entry.subtitle ? '<h4>' + escapeHtml(entry.subtitle) + '</h4>' : '') +
          (
            Array.isArray(entry.topics) && entry.topics.length
              ? '<ul>' + entry.topics.map((topic) => '<li>' + escapeHtml(topic) + '</li>').join('') + '</ul>'
              : '<p>' + escapeHtml(entry.summary || '') + '</p>'
          ) +
        '</div>';
      parent.insertBefore(block, etapasSection);
    });
  }

  function renderTimeline() {
    const horizontal = document.querySelector('.timeline-horizontal');
    const stages = Array.isArray(publicData.timeline) ? publicData.timeline : [];
    if (!horizontal || stages.length === 0) return;

    horizontal.innerHTML = '';
    horizontal.style.setProperty('--timeline-count', String(stages.length));

    stages.forEach((stage, index) => {
      const step = document.createElement('div');
      step.className = 'timeline-step fade-in visible';
      step.innerHTML =
        '<div class="timeline-marker">' +
          '<svg class="timeline-shape" viewBox="0 0 320 120" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">' +
            '<path d="M0 72 H120 V38 L188 14 V72 H320" />' +
          '</svg>' +
          '<span class="timeline-marker-inner"><span class="timeline-marker-number">' + escapeHtml(String(stage.index || (index + 1))) + '</span><span class="timeline-marker-ordinal">&ordf;</span></span>' +
        '</div>' +
        '<span class="timeline-badge">' + escapeHtml(stage.badge || 'Etapa') + '</span>' +
        '<h4>' + escapeHtml(stage.name || 'Etapa') + '</h4>' +
        '<p>' + escapeHtml(stage.description || '') + '</p>';
      horizontal.appendChild(step);
    });
  }

  function renderQuoteBreakdown() {
    const breakdown = document.querySelector('.quote-breakdown');
    if (!breakdown) return;

    const entries = Array.isArray(publicData.scope_entries) ? publicData.scope_entries : [];
    if (entries.length === 0) {
      breakdown.style.display = 'none';
      return;
    }

    breakdown.innerHTML =
      '<div class="quote-breakdown-header"><span>Disciplina</span><span>Valor</span></div>' +
      entries.map((entry) => (
        '<div class="quote-row">' +
          '<div class="quote-row-left">' +
            '<div class="quote-row-icon">' + (entry.icon ? '<img src="' + escapeHtml(entry.icon) + '" alt="' + escapeHtml(entry.title || 'Disciplina') + '">' : '') + '</div>' +
            '<span class="quote-row-label">' + escapeHtml(entry.title || 'Disciplina') + '</span>' +
          '</div>' +
          '<span class="quote-row-value">' + escapeHtml(formatBrl(entry.value || 0)) + '</span>' +
        '</div>'
      )).join('');
  }

  function renderPaymentSchedule() {
    const section = document.querySelector('.payment-schedule');
    const lines = section ? section.querySelector('.payment-lines') : null;
    if (!section || !lines) return;

    const entries = Array.isArray(publicData.payment_schedule) ? publicData.payment_schedule : [];
    if (entries.length === 0) {
      section.style.display = 'none';
      return;
    }

    section.style.display = '';
    lines.innerHTML = entries.map((entry) => {
      if ((entry.type || 'line') === 'subtitle') {
        return '<div class="payment-subtitle">' + escapeHtml(entry.label || 'Grupo') + '</div>';
      }

      return '<div class="payment-line">' +
        '<span class="payment-line-label">' + escapeHtml(entry.label || 'Parcela') + '</span>' +
        '<span class="payment-line-value">' + escapeHtml(entry.amount_label || formatBrl(entry.amount || 0)) + '</span>' +
      '</div>';
    }).join('');
  }

  function renderConsiderations() {
    const list = document.querySelector('.considerations-list');
    const section = document.getElementById('consideracoes');
    if (!list || !section) return;

    const items = Array.isArray(publicData.considerations) ? publicData.considerations : [];
    if (items.length === 0) {
      section.style.display = 'none';
      return;
    }

    list.innerHTML = items.map((item) => '<li class="fade-in visible">' + escapeHtml(item) + '</li>').join('');
  }

  function renderExclusions() {
    const list = document.querySelector('#exclusoes .exclusions-list');
    const section = document.getElementById('exclusoes');
    if (!list || !section) return;

    const items = Array.isArray(publicData.exclusions) ? publicData.exclusions : [];
    if (items.length === 0) {
      section.style.display = 'none';
      return;
    }

    list.innerHTML = items.map((item) => (
      '<div class="exclusion-line fade-in visible">' +
        '<div class="exclusion-letter">&times;</div>' +
        '<div class="exclusion-text">' + escapeHtml(item) + '</div>' +
      '</div>'
    )).join('');
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
    if (note) note.style.display = available.length > 1 ? '' : 'none';

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
    if (!previewMode) {
      downloadButton.removeAttribute('target');
      downloadButton.setAttribute('download', '');
    }
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
    const modalTitle = document.querySelector('.modal-header h2');
    const checkboxLabel = document.querySelector('label[for="agree-terms"]');
    const contractBody = document.querySelector('.contract-content .contract-body');

    if (modalTitle && publicData.acceptance && publicData.acceptance.title) {
      modalTitle.textContent = publicData.acceptance.title;
    }
    if (checkboxLabel && publicData.acceptance && publicData.acceptance.checkbox_text) {
      checkboxLabel.textContent = publicData.acceptance.checkbox_text;
    }
    if (contractBody && publicData.acceptance && publicData.acceptance.body_html) {
      contractBody.innerHTML = publicData.acceptance.body_html;
    }

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

  setupHeroMedia();
  renderHeroHeading();
  renderHeroClientMeta();
  renderIntro();
  renderFilesSection();
  renderGuidelines();
  renderScope();
  renderTimeline();
  renderQuoteBreakdown();
  renderPaymentSchedule();
  renderConsiderations();
  renderExclusions();
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









