<?php

declare(strict_types=1);

function api_tracking_init(): void
{
    $data = parse_json_body();
    $token = trim((string) ($data['token'] ?? ''));
    $sessionId = trim((string) ($data['session_id'] ?? ''));

    if ($token === '') {
        json_response(['ok' => false, 'message' => 'Token ausente.'], 422);
    }

    $proposal = get_proposal_by_token($token);
    if (!$proposal) {
        json_response(['ok' => false, 'message' => 'Proposta não encontrada.'], 404);
    }

    if (!in_array((string) $proposal['status'], ['published', 'viewed', 'signing', 'signed'], true)) {
        json_response(['ok' => false, 'message' => 'Proposta indisponível para tracking.'], 409);
    }

    if ($sessionId === '') {
        $sessionId = random_token(18);
    }

    $ua = (string) ($_SERVER['HTTP_USER_AGENT'] ?? 'unknown');
    $ip = client_ip();
    $device = detect_device($ua);
    $now = now_iso();

    $existing = db_first(
        'proposal_views',
        static fn (array $row): bool =>
            (int) ($row['proposal_id'] ?? 0) === (int) $proposal['id']
            && (string) ($row['session_id'] ?? '') === $sessionId
    );

    if ($existing) {
        $updated = db_update('proposal_views', (int) $existing['id'], [
            'last_seen_at' => $now,
            'user_agent' => $ua,
            'ip' => $ip,
            'device' => $device,
        ]);
        $viewId = (int) ($updated['id'] ?? $existing['id']);
    } else {
        $created = db_insert('proposal_views', [
            'proposal_id' => (int) $proposal['id'],
            'session_id' => $sessionId,
            'ip' => $ip,
            'user_agent' => $ua,
            'device' => $device,
            'max_scroll' => 0,
            'total_time_seconds' => 0,
            'section_times_json' => '{}',
            'downloaded_pdf_at' => null,
            'clicked_sign_at' => null,
            'accepted_at' => null,
            'first_seen_at' => $now,
            'last_seen_at' => $now,
        ]);
        $viewId = (int) $created['id'];
    }

    if ((string) $proposal['status'] === 'published') {
        update_proposal_record((int) $proposal['id'], [
            'status' => 'viewed',
            'updated_at' => $now,
        ]);
    }

    json_response([
        'ok' => true,
        'view_id' => $viewId,
        'session_id' => $sessionId,
    ]);
}

function api_tracking_heartbeat(): void
{
    $data = parse_json_body();
    $viewId = (int) ($data['view_id'] ?? 0);
    $scroll = (float) ($data['scroll_depth'] ?? 0);
    $elapsed = (int) ($data['elapsed_seconds'] ?? 0);
    $sectionTimes = $data['section_times'] ?? [];

    if ($viewId <= 0) {
        json_response(['ok' => false, 'message' => 'view_id inválido'], 422);
    }

    $view = db_find('proposal_views', $viewId);
    if (!$view) {
        json_response(['ok' => false, 'message' => 'Visualização não encontrada.'], 404);
    }

    $existingSections = json_decode((string) ($view['section_times_json'] ?? '{}'), true);
    if (!is_array($existingSections)) {
        $existingSections = [];
    }

    if (is_array($sectionTimes)) {
        foreach ($sectionTimes as $section => $seconds) {
            $section = trim((string) $section);
            if ($section === '') {
                continue;
            }
            if (!isset($existingSections[$section])) {
                $existingSections[$section] = 0;
            }
            $existingSections[$section] += (int) $seconds;
        }
    }

    $newScroll = max((float) ($view['max_scroll'] ?? 0), (float) clamp($scroll, 0, 100));
    $newTotal = (int) ($view['total_time_seconds'] ?? 0) + max(0, min($elapsed, 120));

    db_update('proposal_views', $viewId, [
        'max_scroll' => $newScroll,
        'total_time_seconds' => $newTotal,
        'section_times_json' => json_encode($existingSections, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
        'last_seen_at' => now_iso(),
    ]);

    json_response(['ok' => true]);
}

function api_tracking_event(): void
{
    $data = parse_json_body();
    $viewId = (int) ($data['view_id'] ?? 0);
    $eventType = trim((string) ($data['event_type'] ?? ''));
    $payload = $data['payload'] ?? [];

    if ($viewId <= 0 || $eventType === '') {
        json_response(['ok' => false, 'message' => 'Dados incompletos para evento.'], 422);
    }

    $view = db_find('proposal_views', $viewId);
    if (!$view) {
        json_response(['ok' => false, 'message' => 'Visualização não encontrada.'], 404);
    }

    $proposalId = (int) ($view['proposal_id'] ?? 0);
    $now = now_iso();

    db_insert('proposal_events', [
        'proposal_id' => $proposalId,
        'view_id' => $viewId,
        'event_type' => $eventType,
        'payload_json' => json_encode(is_array($payload) ? $payload : ['value' => $payload], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
        'created_at' => $now,
    ]);

    if ($eventType === 'download_pdf') {
        db_update('proposal_views', $viewId, ['downloaded_pdf_at' => $now]);
    } elseif ($eventType === 'click_sign') {
        db_update('proposal_views', $viewId, ['clicked_sign_at' => $now]);
        $proposal = get_proposal_by_id($proposalId);
        if ($proposal && in_array((string) $proposal['status'], ['published', 'viewed'], true)) {
            update_proposal_record($proposalId, [
                'status' => 'signing',
                'updated_at' => $now,
            ]);
        }
    } elseif ($eventType === 'accept_terms') {
        db_update('proposal_views', $viewId, ['accepted_at' => $now]);
    }

    json_response(['ok' => true]);
}

