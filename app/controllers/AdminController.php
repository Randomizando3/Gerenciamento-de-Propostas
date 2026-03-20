<?php

declare(strict_types=1);

function admin_dashboard(): void
{
    require_auth();
    $settings = load_settings();

    render('admin/dashboard', [
        'title' => 'Painel Administrativo',
        'admin' => current_admin(),
        'stats' => get_dashboard_stats(),
        'settings' => $settings,
    ]);
}

function admin_list_proposals(): void
{
    require_auth();

    $page = max(1, (int) request_input('page', 1));
    $perPage = 20;
    $filters = [
        'query' => trim((string) request_input('query', '')),
        'client' => trim((string) request_input('client', '')),
        'status' => trim((string) request_input('status', '')),
        'min_total' => trim((string) request_input('min_total', '')),
        'max_total' => trim((string) request_input('max_total', '')),
        'order_by' => trim((string) request_input('order_by', 'updated_at')),
        'order_dir' => trim((string) request_input('order_dir', 'desc')),
    ];
    $pagination = paginate_proposals_with_metrics($filters, $page, $perPage);

    render('admin/proposals', [
        'title' => 'Propostas',
        'admin' => current_admin(),
        'proposals' => $pagination['items'],
        'pagination' => $pagination,
        'filters' => $filters,
        'statusOptions' => app_config('status_labels', []),
    ]);
}

function admin_list_users(): void
{
    require_admin();

    render('admin/users', [
        'title' => 'Usuários',
        'admin' => current_admin(),
        'users' => list_admin_users(),
    ]);
}

function admin_list_models(): void
{
    require_auth();

    render('admin/models', [
        'title' => 'Modelos',
        'admin' => current_admin(),
        'models' => list_proposal_models(),
    ]);
}

function admin_show_new_model_form(): void
{
    require_auth();

    $settings = load_settings();
    $payload = proposal_payload_for_model(default_proposal_payload());
    render('admin/model_form', [
        'title' => 'Novo modelo',
        'admin' => current_admin(),
        'model' => null,
        'payload' => $payload,
        'catalog' => discipline_catalog(),
        'settings' => $settings,
        'acceptTermsVariables' => acceptance_terms_variable_catalog(),
    ]);
}

function admin_show_edit_model_form(int $modelId): void
{
    require_auth();

    $model = get_proposal_model($modelId);
    if (!$model) {
        flash('error', 'Modelo não encontrado.');
        redirect('/admin/models');
    }

    $settings = load_settings();
    $payload = normalize_proposal_payload((array) ($model['payload'] ?? []), (array) ($model['payload'] ?? []));

    render('admin/model_form', [
        'title' => 'Editar modelo',
        'admin' => current_admin(),
        'model' => $model,
        'payload' => $payload,
        'catalog' => discipline_catalog(),
        'settings' => $settings,
        'acceptTermsVariables' => acceptance_terms_variable_catalog(),
    ]);
}

function admin_delete_model_action(int $modelId): void
{
    require_auth();
    verify_csrf_or_die();

    if (!delete_proposal_model_record($modelId)) {
        flash('error', 'Modelo não encontrado.');
        redirect('/admin/models');
    }

    flash('success', 'Modelo removido com sucesso.');
    redirect('/admin/models');
}

function admin_proposal_payload_with_settings(array $payload, array $settings): array
{
    return normalize_proposal_payload($payload, $payload);
}

function admin_render_proposal_form_state(?array $proposal, array $payload, array $settings, string $title): void
{
    render('admin/proposal_form', [
        'title' => $title,
        'admin' => current_admin(),
        'proposal' => $proposal,
        'payload' => $payload,
        'catalog' => discipline_catalog(),
        'settings' => $settings,
        'models' => list_proposal_models(),
        'currentModel' => null,
        'acceptTermsVariables' => acceptance_terms_variable_catalog(),
    ]);
}

function admin_show_new_proposal_form(): void
{
    require_auth();

    $settings = load_settings();
    $model = null;
    $modelId = request_input('model');
    if (is_numeric($modelId)) {
        $model = get_proposal_model((int) $modelId);
    }

    $payload = $model ? proposal_payload_from_model((array) ($model['payload'] ?? [])) : default_proposal_payload();
    $payload = admin_proposal_payload_with_settings($payload, $settings);

    render('admin/proposal_form', [
        'title' => 'Nova proposta',
        'admin' => current_admin(),
        'proposal' => null,
        'payload' => $payload,
        'catalog' => discipline_catalog(),
        'settings' => $settings,
        'models' => list_proposal_models(),
        'currentModel' => $model,
        'acceptTermsVariables' => acceptance_terms_variable_catalog(),
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

    $settings = load_settings();
    $payload = admin_proposal_payload_with_settings($proposal['payload'], $settings);

    render('admin/proposal_form', [
        'title' => 'Editar proposta',
        'admin' => current_admin(),
        'proposal' => $proposal,
        'payload' => $payload,
        'catalog' => discipline_catalog(),
        'settings' => $settings,
        'models' => list_proposal_models(),
        'currentModel' => null,
        'acceptTermsVariables' => acceptance_terms_variable_catalog(),
    ]);
}

function admin_save_proposal_action(): void
{
    require_auth();
    verify_csrf_or_die();

    $id = request_input('id');
    $proposalId = is_numeric($id) ? (int) $id : null;
    $basePayload = null;
    $existing = null;

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
    $paymentValidation = proposal_payment_schedule_validation($payload);
    if (!(bool) ($paymentValidation['ok'] ?? false)) {
        flash('error', (string) ($paymentValidation['message'] ?? 'Revise a forma de pagamento.'));
        $settings = load_settings();
        admin_render_proposal_form_state($proposalId !== null ? ($existing ?? null) : null, $payload, $settings, $proposalId !== null ? 'Editar proposta' : 'Nova proposta');
        return;
    }

    $savedId = save_proposal($payload, $proposalId, current_admin());

    $saveMode = (string) request_input('save_mode', 'edit');
    $modelName = trim((string) request_input('model_name', ''));
    $modelDescription = trim((string) request_input('model_description', ''));
    $modelRecordId = request_input('model_record_id');
    $modelId = is_numeric($modelRecordId) ? (int) $modelRecordId : null;

    if ($saveMode === 'model_create' || $saveMode === 'model_update') {
        if ($modelName === '') {
            flash('error', 'Informe um nome para o modelo.');
            redirect('/admin/proposals/' . $savedId . '/edit');
        }

        if ($saveMode === 'model_update' && ($modelId === null || $modelId <= 0)) {
            flash('error', 'Selecione um modelo existente para atualizar.');
            redirect('/admin/proposals/' . $savedId . '/edit');
        }

        save_proposal_model_record(
            $saveMode === 'model_update' ? $modelId : null,
            $modelName,
            $modelDescription,
            proposal_payload_for_model($payload),
            current_admin()
        );

        flash('success', $saveMode === 'model_update' ? 'Modelo atualizado com sucesso.' : 'Modelo salvo com sucesso.');
        redirect('/admin/proposals/' . $savedId . '/edit');
    }

    flash('success', 'Proposta salva com sucesso.');

    if ($saveMode === 'preview') {
        redirect('/admin/proposals/' . $savedId . '/preview');
    }

    redirect('/admin/proposals/' . $savedId . '/edit');
}

function admin_save_model_action(): void
{
    require_auth();
    verify_csrf_or_die();

    $id = request_input('model_id');
    $modelId = is_numeric($id) ? (int) $id : null;
    $basePayload = null;

    if ($modelId !== null) {
        $existing = get_proposal_model($modelId);
        if (!$existing) {
            flash('error', 'Modelo nao encontrado para atualizacao.');
            redirect('/admin/models');
        }
        $basePayload = (array) ($existing['payload'] ?? []);
    }

    $name = trim((string) request_input('model_name', ''));
    $description = trim((string) request_input('model_description', ''));
    if ($name === '') {
        flash('error', 'Informe um nome para o modelo.');
        redirect($modelId ? '/admin/models/' . $modelId . '/edit' : '/admin/models/new');
    }

    $payloadInput = normalize_admin_payload_input($_POST, $_FILES);
    $payload = proposal_payload_for_model(normalize_proposal_payload($payloadInput, $basePayload));
    $saved = save_proposal_model_record($modelId, $name, $description, $payload, current_admin());

    flash('success', $modelId ? 'Modelo atualizado com sucesso.' : 'Modelo criado com sucesso.');
    redirect('/admin/models/' . (int) ($saved['id'] ?? 0) . '/edit');
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

    flash('success', 'Proposta publicada com sucesso.');
    redirect('/admin/proposals/' . $proposalId . '/edit');
}

function admin_duplicate_proposal_action(int $proposalId): void
{
    require_auth();
    verify_csrf_or_die();

    $newId = duplicate_proposal($proposalId, current_admin());
    if ($newId === null) {
        flash('error', 'Nao foi possivel duplicar a proposta.');
        redirect('/admin');
    }

    flash('success', 'Proposta duplicada com sucesso.');
    redirect('/admin/proposals/' . $newId . '/edit');
}

function admin_delete_proposal_action(int $proposalId): void
{
    require_auth();
    verify_csrf_or_die();

    if (!delete_proposal_record($proposalId)) {
        flash('error', 'Proposta não encontrada.');
        redirect('/admin/proposals');
    }

    flash('success', 'Proposta excluída com sucesso.');
    redirect('/admin/proposals');
}

function admin_create_user_action(): void
{
    require_admin();
    verify_csrf_or_die();

    $name = trim((string) request_input('name', ''));
    $email = mb_strtolower(trim((string) request_input('email', '')), 'UTF-8');
    $role = mb_strtolower(trim((string) request_input('role', 'editor')), 'UTF-8');
    $password = (string) request_input('password', '');
    $passwordConfirm = (string) request_input('password_confirm', '');

    if ($name === '' || $email === '' || $password === '' || $passwordConfirm === '') {
        flash('error', 'Preencha nome, e-mail, senha e confirmacao.');
        redirect('/admin/users');
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        flash('error', 'Informe um e-mail valido.');
        redirect('/admin/users');
    }

    if (mb_strlen($password, 'UTF-8') < 6) {
        flash('error', 'A senha deve ter pelo menos 6 caracteres.');
        redirect('/admin/users');
    }

    if (!hash_equals($password, $passwordConfirm)) {
        flash('error', 'A confirmacao de senha nao confere.');
        redirect('/admin/users');
    }

    try {
        create_admin_user($name, $email, $password, $role);
    } catch (InvalidArgumentException $exception) {
        flash('error', $exception->getMessage());
        redirect('/admin/users');
    }

    flash('success', 'Usuario criado com sucesso.');
    redirect('/admin/users');
}

function admin_delete_user_action(int $userId): void
{
    require_admin();
    verify_csrf_or_die();
    $currentUserId = (int) ((current_admin()['id'] ?? 0));

    if (!delete_admin_user($userId)) {
        flash('error', 'Usuario nao encontrado.');
        redirect('/admin/users');
    }

    if ($currentUserId === $userId) {
        logout_admin();
        flash('success', 'Seu usuário foi removido.');
        redirect('/admin/login');
    }

    flash('success', 'Usuario removido com sucesso.');
    redirect('/admin/users');
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
        'payload' => admin_proposal_payload_with_settings($proposal['payload'], $settings),
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

    render('admin/analytics', [
        'title' => 'Analytics',
        'admin' => current_admin(),
        'stats' => get_dashboard_stats(),
        'focusProposal' => $proposal,
        'focusMetrics' => get_proposal_metrics($proposalId),
    ]);
}

function admin_show_settings(): void
{
    require_admin();

    render('admin/settings', [
        'title' => 'Configurações',
        'admin' => current_admin(),
        'settings' => load_settings(),
        'acceptTermsVariables' => acceptance_terms_variable_catalog(),
    ]);
}

function admin_save_settings_action(): void
{
    require_admin();
    verify_csrf_or_die();

    save_settings_row([
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
        'company_about_text' => trim((string) ($_POST['company_about_text'] ?? '')),
        'company_accept_phrase' => trim((string) ($_POST['company_accept_phrase'] ?? '')),
        'accept_terms_title' => trim((string) ($_POST['accept_terms_title'] ?? '')),
        'accept_terms_html' => trim((string) ($_POST['accept_terms_html'] ?? '')),
        'accept_terms_checkbox_text' => trim((string) ($_POST['accept_terms_checkbox_text'] ?? '')),
    ]);

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

    $result = create_zapsign_document($proposal, load_settings());
    if (!$result['ok']) {
        flash('error', (string) ($result['message'] ?? 'Falha ao enviar para o ZapSign.'));
        redirect('/admin/proposals/' . $proposalId . '/edit');
    }

    update_proposal_record($proposalId, [
        'zapsign_doc_id' => $result['doc_id'] ?? '',
        'zapsign_sign_url' => $result['sign_url'] ?? '',
        'status' => 'signing',
        'updated_at' => now_iso(),
    ]);

    flash('success', 'Proposta enviada para assinatura no ZapSign.');
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

