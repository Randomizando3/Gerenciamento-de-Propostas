<?php

declare(strict_types=1);

require dirname(__DIR__) . '/app/bootstrap.php';

// When Apache forwards requests through FallbackResource, it may preserve a 404
// status code from the original path. Reset known application routes to 200.
http_response_code(200);

$method = request_method();
$path = request_path();

if ($path === '/') {
    redirect('/admin');
}

if ($path === '/admin/login' && $method === 'GET') {
    show_login();
    return;
}

if ($path === '/admin/login' && $method === 'POST') {
    login_action();
    return;
}

if ($path === '/admin/logout' && $method === 'POST') {
    logout_action();
    return;
}

if ($path === '/admin' && $method === 'GET') {
    admin_dashboard();
    return;
}

if ($path === '/admin/proposals' && $method === 'GET') {
    admin_list_proposals();
    return;
}

if ($path === '/admin/users' && $method === 'GET') {
    admin_list_users();
    return;
}

if ($path === '/admin/users' && $method === 'POST') {
    admin_create_user_action();
    return;
}

if ($path === '/admin/models' && $method === 'GET') {
    admin_list_models();
    return;
}

if ($path === '/admin/models/new' && $method === 'GET') {
    admin_show_new_model_form();
    return;
}

if ($path === '/admin/models/save' && $method === 'POST') {
    admin_save_model_action();
    return;
}

if (preg_match('#^/admin/models/(\d+)/edit$#', $path, $matches) && $method === 'GET') {
    admin_show_edit_model_form((int) $matches[1]);
    return;
}

if (preg_match('#^/admin/models/(\d+)/delete$#', $path, $matches) && $method === 'POST') {
    admin_delete_model_action((int) $matches[1]);
    return;
}

if (preg_match('#^/admin/users/(\d+)/delete$#', $path, $matches) && $method === 'POST') {
    admin_delete_user_action((int) $matches[1]);
    return;
}

if ($path === '/admin/proposals/new' && $method === 'GET') {
    admin_show_new_proposal_form();
    return;
}

if ($path === '/admin/proposals/save' && $method === 'POST') {
    admin_save_proposal_action();
    return;
}

if ($path === '/admin/settings' && $method === 'GET') {
    admin_show_settings();
    return;
}

if ($path === '/admin/settings' && $method === 'POST') {
    admin_save_settings_action();
    return;
}

if (preg_match('#^/admin/proposals/(\d+)/edit$#', $path, $matches) && $method === 'GET') {
    admin_show_edit_proposal_form((int) $matches[1]);
    return;
}

if (preg_match('#^/admin/proposals/(\d+)/preview$#', $path, $matches) && $method === 'GET') {
    admin_show_preview((int) $matches[1]);
    return;
}

if (preg_match('#^/admin/proposals/(\d+)/publish$#', $path, $matches) && $method === 'POST') {
    admin_publish_proposal_action((int) $matches[1]);
    return;
}

if (preg_match('#^/admin/proposals/(\d+)/duplicate$#', $path, $matches) && $method === 'POST') {
    admin_duplicate_proposal_action((int) $matches[1]);
    return;
}

if (preg_match('#^/admin/proposals/(\d+)/analytics$#', $path, $matches) && $method === 'GET') {
    admin_show_analytics((int) $matches[1]);
    return;
}

if (preg_match('#^/admin/proposals/(\d+)/zapsign$#', $path, $matches) && $method === 'POST') {
    admin_send_to_zapsign_action((int) $matches[1]);
    return;
}

if (preg_match('#^/p/([a-zA-Z0-9]+)/print$#', $path, $matches) && $method === 'GET') {
    show_public_print((string) $matches[1]);
    return;
}

if (preg_match('#^/p/([a-zA-Z0-9]+)/contract$#', $path, $matches) && $method === 'GET') {
    show_public_contract((string) $matches[1]);
    return;
}

if (preg_match('#^/p/([a-zA-Z0-9]+)/sign$#', $path, $matches) && $method === 'GET') {
    redirect_to_signature((string) $matches[1]);
    return;
}

if (preg_match('#^/p/([a-zA-Z0-9]+)$#', $path, $matches) && $method === 'GET') {
    show_public_proposal((string) $matches[1]);
    return;
}

if ($path === '/api/tracking/init' && $method === 'POST') {
    api_tracking_init();
    return;
}

if ($path === '/api/tracking/heartbeat' && $method === 'POST') {
    api_tracking_heartbeat();
    return;
}

if ($path === '/api/tracking/event' && $method === 'POST') {
    api_tracking_event();
    return;
}

if (($path === '/webhooks/zapsign' || $path === '/webhook/zapsign') && $method === 'POST') {
    webhook_zapsign();
    return;
}

http_response_code(404);
render('public/not_found', [
    'title' => 'Rota n?o encontrada',
    'message' => 'A rota solicitada n?o existe.',
], 'none');
