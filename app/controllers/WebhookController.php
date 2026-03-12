<?php

declare(strict_types=1);

function webhook_zapsign(): void
{
    $rawBody = file_get_contents('php://input');
    if (!is_string($rawBody)) {
        $rawBody = '';
    }
    $settings = load_settings();
    if (!verify_zapsign_webhook_signature($rawBody, $settings)) {
        json_response(['ok' => false, 'message' => 'Assinatura do webhook inválida.'], 401);
    }

    $payload = json_decode($rawBody, true);
    if (!is_array($payload)) {
        $payload = [];
    }

    db_insert('webhooks', [
        'source' => 'zapsign',
        'payload_json' => json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
        'created_at' => now_iso(),
        'processed_at' => null,
    ]);

    $proposal = resolve_webhook_proposal($payload);
    if ($proposal !== null) {
        $status = zapsign_webhook_status_hint($payload);
        if ($status !== '' && zapsign_is_signed_status($status)) {
            update_proposal_record((int) $proposal['id'], [
                'status' => 'signed',
                'updated_at' => now_iso(),
            ]);
        }
    }

    json_response(['ok' => true]);
}

function resolve_webhook_proposal(array $payload): ?array
{
    $externalCandidates = [
        (string) ($payload['external_id'] ?? ''),
        (string) ($payload['document_external_id'] ?? ''),
        (string) ($payload['document']['external_id'] ?? ''),
        (string) ($payload['doc']['external_id'] ?? ''),
    ];
    foreach ($externalCandidates as $externalId) {
        $externalId = trim($externalId);
        if ($externalId === '') {
            continue;
        }
        if (preg_match('/proposal-(\d+)/', $externalId, $matches)) {
            $resolved = get_proposal_by_id((int) $matches[1]);
            if ($resolved !== null) {
                return $resolved;
            }
        }
    }

    $docCandidates = [
        (string) ($payload['id'] ?? ''),
        (string) ($payload['doc_id'] ?? ''),
        (string) ($payload['token'] ?? ''),
        (string) ($payload['document_token'] ?? ''),
        (string) ($payload['open_id'] ?? ''),
        (string) ($payload['document']['id'] ?? ''),
        (string) ($payload['document']['token'] ?? ''),
        (string) ($payload['document']['open_id'] ?? ''),
    ];
    foreach ($docCandidates as $docId) {
        $docId = trim($docId);
        if ($docId === '') {
            continue;
        }
        $proposal = db_first('proposals', static fn (array $row): bool => trim((string) ($row['zapsign_doc_id'] ?? '')) === $docId);
        if ($proposal) {
            $proposal['payload'] = decode_payload($proposal['payload_json']);
            return $proposal;
        }
    }

    return null;
}

function zapsign_webhook_status_hint(array $payload): string
{
    $candidates = [
        $payload['status'] ?? null,
        $payload['event_type'] ?? null,
        $payload['document_status'] ?? null,
        $payload['type'] ?? null,
        $payload['event'] ?? null,
        $payload['document']['status'] ?? null,
        $payload['document']['event_type'] ?? null,
    ];

    foreach ($candidates as $candidate) {
        if (is_string($candidate) && trim($candidate) !== '') {
            return mb_strtolower(trim($candidate), 'UTF-8');
        }
        if (is_array($candidate)) {
            foreach (['type', 'name', 'event_type', 'status'] as $key) {
                $value = $candidate[$key] ?? null;
                if (is_string($value) && trim($value) !== '') {
                    return mb_strtolower(trim($value), 'UTF-8');
                }
            }
        }
    }

    return '';
}

function zapsign_is_signed_status(string $status): bool
{
    $status = mb_strtolower(trim($status), 'UTF-8');
    if ($status === '') {
        return false;
    }

    return
        str_contains($status, 'signed') ||
        str_contains($status, 'complete') ||
        str_contains($status, 'conclu') ||
        str_contains($status, 'finaliz');
}
