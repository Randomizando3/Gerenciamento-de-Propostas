<?php

declare(strict_types=1);

function show_login(): void
{
    if (is_logged_in()) {
        redirect('/admin');
    }

    render('admin/login', [
        'title' => 'Login',
    ], 'auth');
}

function login_action(): void
{
    verify_csrf_or_die();

    $email = trim((string) request_input('email', ''));
    $password = (string) request_input('password', '');

    flash_old(['email' => $email]);

    if ($email === '' || $password === '') {
        flash('error', 'Informe e-mail e senha.');
        redirect('/admin/login');
    }

    $admin = find_admin_by_email($email);

    if (!$admin || !password_verify($password, (string) $admin['password_hash'])) {
        flash('error', 'Credenciais inválidas.');
        redirect('/admin/login');
    }

    if ((int) ($admin['is_active'] ?? 1) !== 1) {
        flash('error', 'Usuário inativo. Solicite liberação ao administrador.');
        redirect('/admin/login');
    }

    clear_old();
    set_admin_session($admin);
    flash('success', 'Bem-vindo! Painel liberado.');
    redirect('/admin');
}

function logout_action(): void
{
    logout_admin();
    flash('success', 'Sessão encerrada.');
    redirect('/admin/login');
}
