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
        $status = mb_strtolower((string) (
            $payload['status'] ??
            $payload['event_type'] ??
            $payload['document_status'] ??
            ''
        ), 'UTF-8');

        if ($status !== '' && (
            str_contains($status, 'signed') ||
            str_contains($status, 'complete') ||
            str_contains($status, 'conclu')
        )) {
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
    $externalId = (string) ($payload['external_id'] ?? $payload['document']['external_id'] ?? '');
    if ($externalId !== '' && preg_match('/proposal-(\d+)/', $externalId, $matches)) {
        return get_proposal_by_id((int) $matches[1]);
    }

    $docId = (string) ($payload['id'] ?? $payload['doc_id'] ?? $payload['document']['id'] ?? '');
    if ($docId !== '') {
        $proposal = db_first('proposals', static fn (array $row): bool => (string) ($row['zapsign_doc_id'] ?? '') === $docId);
        if ($proposal) {
            $proposal['payload'] = decode_payload($proposal['payload_json']);
            return $proposal;
        }
    }

    return null;
}
