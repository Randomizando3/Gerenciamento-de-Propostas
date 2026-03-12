<?php

declare(strict_types=1);

function admin_dashboard(): void
{
    require_auth();

    $stats = get_dashboard_stats();

    render('admin/dashboard', [
        'title' => 'Painel Administrativo',
        'admin' => current_admin(),
        'stats' => $stats,
    ]);
}

function admin_list_proposals(): void
{
    require_auth();

    $proposals = list_proposals_with_metrics();

    render('admin/proposals', [
        'title' => 'Propostas',
        'admin' => current_admin(),
        'proposals' => $proposals,
    ]);
}

function admin_show_new_proposal_form(): void
{
    require_auth();

    $payload = default_proposal_payload();
    render('admin/proposal_form', [
        'title' => 'Nova proposta',
        'admin' => current_admin(),
        'proposal' => null,
        'payload' => $payload,
        'catalog' => discipline_catalog(),
        'settings' => load_settings(),
    ]);
}

function admin_show_edit_proposal_form(int $proposalId): void
{
    require_auth();

    $proposal = get_proposal_by_id($proposalId);
    if (!$proposal) {
        flash('error', 'Proposta não encontrada.');
        redirect('/admin');
    }

    render('admin/proposal_form', [
        'title' => 'Editar proposta',
        'admin' => current_admin(),
        'proposal' => $proposal,
        'payload' => $proposal['payload'],
        'catalog' => discipline_catalog(),
        'settings' => load_settings(),
    ]);
}

function admin_save_proposal_action(): void
{
    require_auth();
    verify_csrf_or_die();

    $id = request_input('id');
    $proposalId = is_numeric($id) ? (int) $id : null;
    $basePayload = null;

    if ($proposalId !== null) {
        $existing = get_proposal_by_id($proposalId);
        if (!$existing) {
            flash('error', 'Proposta não encontrada para atualização.');
            redirect('/admin');
        }
        $basePayload = $existing['payload'];
    }

    $payloadInput = normalize_admin_payload_input($_POST, $_FILES);
    $payload = normalize_proposal_payload($payloadInput, $basePayload);
    $savedId = save_proposal($payload, $proposalId);

    flash('success', 'Proposta salva com sucesso.');

    $saveMode = (string) request_input('save_mode', 'edit');
    if ($saveMode === 'preview') {
        redirect('/admin/proposals/' . $savedId . '/preview');
    }

    redirect('/admin/proposals/' . $savedId . '/edit');
}

function admin_publish_proposal_action(int $proposalId): void
{
    require_auth();
    verify_csrf_or_die();

    $proposal = publish_proposal($proposalId);
    if (!$proposal) {
        flash('error', 'Proposta não encontrada.');
        redirect('/admin');
    }

    $url = proposal_public_url($proposal);
    flash('success', 'Proposta publicada com sucesso. Link: ' . ($url ?? 'indisponível'));
    redirect('/admin/proposals/' . $proposalId . '/edit');
}

function admin_duplicate_proposal_action(int $proposalId): void
{
    require_auth();
    verify_csrf_or_die();

    $newId = duplicate_proposal($proposalId);
    if ($newId === null) {
        flash('error', 'Não foi possível duplicar.');
        redirect('/admin');
    }

    flash('success', 'Proposta duplicada com sucesso.');
    redirect('/admin/proposals/' . $newId . '/edit');
}

function admin_show_preview(int $proposalId): void
{
    require_auth();

    $proposal = get_proposal_by_id($proposalId);
    if (!$proposal) {
        flash('error', 'Proposta não encontrada.');
        redirect('/admin');
    }

    $settings = load_settings();
    render('public/proposal', [
        'title' => 'Pré-visualização - ' . $proposal['code'],
        'proposal' => $proposal,
        'payload' => $proposal['payload'],
        'catalog' => discipline_catalog(),
        'settings' => $settings,
        'previewMode' => true,
    ], 'none');
}

function admin_show_analytics(int $proposalId): void
{
    require_auth();
    $proposal = get_proposal_by_id($proposalId);
    if (!$proposal) {
        flash('error', 'Proposta não encontrada.');
        redirect('/admin');
    }

    $stats = get_dashboard_stats();
    $metrics = get_proposal_metrics($proposalId);

    render('admin/analytics', [
        'title' => 'Analytics',
        'admin' => current_admin(),
        'stats' => $stats,
        'focusProposal' => $proposal,
        'focusMetrics' => $metrics,
    ]);
}

function admin_show_settings(): void
{
    require_auth();
    render('admin/settings', [
        'title' => 'Configurações',
        'admin' => current_admin(),
        'settings' => load_settings(),
        'acceptTermsVariables' => acceptance_terms_variable_catalog(),
    ]);
}

function admin_save_settings_action(): void
{
    require_auth();
    verify_csrf_or_die();

    $data = [
        'clarity_enabled' => isset($_POST['clarity_enabled']) ? 1 : 0,
        'clarity_project_id' => trim((string) ($_POST['clarity_project_id'] ?? '')),
        'clarity_export_endpoint' => trim((string) ($_POST['clarity_export_endpoint'] ?? 'https://www.clarity.ms/export-data/api/v1/project-live-insights')),
        'clarity_export_token' => trim((string) ($_POST['clarity_export_token'] ?? '')),
        'zapsign_enabled' => isset($_POST['zapsign_enabled']) ? 1 : 0,
        'zapsign_api_key' => trim((string) ($_POST['zapsign_api_key'] ?? '')),
        'zapsign_webhook_secret' => trim((string) ($_POST['zapsign_webhook_secret'] ?? '')),
        'zapsign_base_url' => trim((string) ($_POST['zapsign_base_url'] ?? 'https://sandbox.api.zapsign.com.br')),
        'base_url' => trim((string) ($_POST['base_url'] ?? '')),
        'company_name' => trim((string) ($_POST['company_name'] ?? '')),
        'company_phone' => trim((string) ($_POST['company_phone'] ?? '')),
        'company_website' => trim((string) ($_POST['company_website'] ?? '')),
        'company_instagram' => trim((string) ($_POST['company_instagram'] ?? '')),
        'company_address' => trim((string) ($_POST['company_address'] ?? '')),
        'company_bank_name' => trim((string) ($_POST['company_bank_name'] ?? '')),
        'company_bank_agency' => trim((string) ($_POST['company_bank_agency'] ?? '')),
        'company_bank_account' => trim((string) ($_POST['company_bank_account'] ?? '')),
        'company_bank_favored' => trim((string) ($_POST['company_bank_favored'] ?? '')),
        'company_bank_cnpj' => trim((string) ($_POST['company_bank_cnpj'] ?? '')),
        'company_bank_pix_key' => trim((string) ($_POST['company_bank_pix_key'] ?? '')),
        'company_bank_pix_key_type' => trim((string) ($_POST['company_bank_pix_key_type'] ?? '')),
        'accept_terms_title' => trim((string) ($_POST['accept_terms_title'] ?? '')),
        'accept_terms_html' => trim((string) ($_POST['accept_terms_html'] ?? '')),
        'accept_terms_checkbox_text' => trim((string) ($_POST['accept_terms_checkbox_text'] ?? '')),
    ];

    save_settings_row($data);

    flash('success', 'Configurações salvas.');
    redirect('/admin/settings');
}

function admin_send_to_zapsign_action(int $proposalId): void
{
    require_auth();
    verify_csrf_or_die();

    $proposal = get_proposal_by_id($proposalId);
    if (!$proposal) {
        flash('error', 'Proposta não encontrada.');
        redirect('/admin');
    }

    $settings = load_settings();
    $result = create_zapsign_document($proposal, $settings);
    if (!$result['ok']) {
        flash('error', $result['message']);
        redirect('/admin/proposals/' . $proposalId . '/edit');
    }

    update_proposal_record($proposalId, [
        'zapsign_doc_id' => $result['doc_id'] ?? '',
        'zapsign_sign_url' => $result['sign_url'] ?? '',
        'status' => 'signing',
        'updated_at' => now_iso(),
    ]);

    $mode = trim((string) ($result['mode'] ?? ''));
    $modeLabel = match ($mode) {
        'url_pdf' => 'PDF da proposta',
        'markdown_text' => 'Resumo em texto',
        default => 'Integração padrão',
    };
    flash('success', 'Proposta enviada para assinatura no ZapSign. Documento usado: ' . $modeLabel . '.');
    redirect('/admin/proposals/' . $proposalId . '/edit');
}

function normalize_admin_payload_input(array $post, array $files): array
{
    $customRows = $post['disciplinas_custom'] ?? [];
    if (!is_array($customRows)) {
        $customRows = [];
    }

    foreach ($customRows as $index => $row) {
        if (!is_array($row)) {
            continue;
        }
        $uploadedPath = save_uploaded_discipline_icon($files['disciplinas_custom_icone'] ?? null, $index);
        if ($uploadedPath !== null) {
            $row['icone'] = $uploadedPath;
        }
        $customRows[$index] = $row;
    }

    $post['disciplinas_custom'] = $customRows;
    return $post;
}

function save_uploaded_discipline_icon(?array $fileBag, int|string $index): ?string
{
    if (!is_array($fileBag)) {
        return null;
    }

    $error = $fileBag['error'][$index] ?? UPLOAD_ERR_NO_FILE;
    if ((int) $error !== UPLOAD_ERR_OK) {
        return null;
    }

    $tmpName = (string) ($fileBag['tmp_name'][$index] ?? '');
    $original = (string) ($fileBag['name'][$index] ?? '');
    if ($tmpName === '' || $original === '' || !is_uploaded_file($tmpName)) {
        return null;
    }

    $ext = mb_strtolower(pathinfo($original, PATHINFO_EXTENSION), 'UTF-8');
    $allowed = ['png', 'jpg', 'jpeg', 'webp', 'svg'];
    if (!in_array($ext, $allowed, true)) {
        return null;
    }

    $dir = base_path('public/uploads/disciplinas');
    if (!is_dir($dir)) {
        mkdir($dir, 0777, true);
    }

    $filename = 'disciplina-' . date('YmdHis') . '-' . random_int(1000, 9999) . '.' . $ext;
    $target = $dir . DIRECTORY_SEPARATOR . $filename;
    if (!move_uploaded_file($tmpName, $target)) {
        return null;
    }

    return '/uploads/disciplinas/' . $filename;
}


