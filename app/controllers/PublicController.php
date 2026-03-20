<?php

declare(strict_types=1);

function show_public_proposal(string $token): void
{
    $proposal = get_proposal_by_token($token);
    if (!$proposal) {
        http_response_code(404);
        render('public/not_found', [
            'title' => 'Proposta não encontrada',
        ], 'none');
        return;
    }

    if ($proposal['status'] === 'draft') {
        http_response_code(403);
        render('public/not_found', [
            'title' => 'Proposta ainda não publicada',
            'message' => 'Esta proposta ainda está em rascunho e não pode ser acessada externamente.',
        ], 'none');
        return;
    }

    $settings = load_settings();
    render('public/proposal', [
        'title' => 'Proposta ' . $proposal['code'],
        'proposal' => $proposal,
        'payload' => $proposal['payload'],
        'catalog' => discipline_catalog(),
        'settings' => $settings,
        'previewMode' => false,
    ], 'none');
}

function show_public_print(string $token): void
{
    $proposal = get_proposal_by_token($token);
    if (!$proposal) {
        http_response_code(404);
        render('public/not_found', [
            'title' => 'Proposta não encontrada',
        ], 'none');
        return;
    }

    $settings = load_settings();
    $payload = admin_proposal_payload_with_settings($proposal['payload'], $settings);

    if (!dompdf_available()) {
        http_response_code(503);
        render('public/not_found', [
            'title' => 'PDF indisponível',
            'message' => 'A biblioteca de PDF não está disponível neste ambiente.',
        ], 'none');
        return;
    }

    output_proposal_summary_pdf($proposal, $payload, $settings);
}

function show_public_contract(string $token): void
{
    $proposal = get_proposal_by_token($token);
    if (!$proposal) {
        http_response_code(404);
        render('public/not_found', [
            'title' => 'Proposta não encontrada',
        ], 'none');
        return;
    }

    $settings = load_settings();
    render('public/contract', [
        'title' => 'Contrato - Proposta ' . $proposal['code'],
        'proposal' => $proposal,
        'payload' => admin_proposal_payload_with_settings($proposal['payload'], $settings),
        'settings' => $settings,
    ], 'none');
}

function show_public_acceptance_document(string $token): void
{
    $proposal = get_proposal_by_token($token);
    if (!$proposal) {
        http_response_code(404);
        render('public/not_found', [
            'title' => 'Proposta não encontrada',
        ], 'none');
        return;
    }

    if (!dompdf_available()) {
        http_response_code(503);
        render('public/not_found', [
            'title' => 'PDF indisponível',
            'message' => 'A biblioteca de PDF não está disponível neste ambiente.',
        ], 'none');
        return;
    }

    $settings = load_settings();
    $payload = admin_proposal_payload_with_settings($proposal['payload'], $settings);

    if (proposal_acceptance_mode($payload) === 'summary') {
        output_proposal_summary_pdf($proposal, $payload, $settings);
    }

    output_proposal_contract_pdf($proposal, $payload, $settings);
}

function redirect_to_signature(string $token): void
{
    $proposal = get_proposal_by_token($token);
    if (!$proposal) {
        flash('error', 'Proposta não encontrada.');
        redirect('/');
    }

    $signUrl = trim((string) ($proposal['zapsign_sign_url'] ?? ''));
    if ($signUrl === '') {
        $payload = $proposal['payload'];
        $signUrl = trim((string) ($payload['zapsign_sign_url'] ?? ''));
    }

    if ($signUrl === '') {
        $settings = load_settings();
        $auto = create_zapsign_document($proposal, $settings);
        if (($auto['ok'] ?? false) === true) {
            $docId = trim((string) ($auto['doc_id'] ?? ''));
            $signUrl = trim((string) ($auto['sign_url'] ?? ''));
            update_proposal_record((int) $proposal['id'], [
                'zapsign_doc_id' => $docId,
                'zapsign_sign_url' => $signUrl,
                'status' => $signUrl !== '' ? 'signing' : (string) $proposal['status'],
                'updated_at' => now_iso(),
            ]);
            $proposal = get_proposal_by_id((int) $proposal['id']) ?? $proposal;
        }
        $autoError = trim((string) ($auto['message'] ?? ''));
    } else {
        $autoError = '';
    }

    if ($signUrl === '') {
        http_response_code(409);
        render('public/not_found', [
            'title' => 'Assinatura indisponível',
            'message' => $autoError !== '' ? $autoError : 'Não foi possível gerar o link de assinatura agora. Tente novamente em instantes ou peça para o administrador reenviar ao ZapSign.',
        ], 'none');
        return;
    }

    if (in_array((string) $proposal['status'], ['published', 'viewed'], true)) {
        update_proposal_record((int) $proposal['id'], [
            'status' => 'signing',
            'updated_at' => now_iso(),
        ]);
    }

    header('Location: ' . $signUrl);
    exit;
}

