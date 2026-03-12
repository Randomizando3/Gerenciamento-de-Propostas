<?php
/** @var string $title */
/** @var array $flashes */
$adminUser = current_admin();
$path = request_path();
$isDashboard = $path === '/admin';
$isProposals = $path === '/admin/proposals' || str_starts_with($path, '/admin/proposals/');
$isNewProposal = $path === '/admin/proposals/new';
$isUsers = str_starts_with($path, '/admin/users');
$isSettings = str_starts_with($path, '/admin/settings');
$isAdmin = is_admin_user($adminUser);
?>
<!doctype html>
<html lang="pt-BR">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= h($title ?? 'Admin') ?> | <?= h((string) app_config('app_name')) ?></title>
  <link rel="stylesheet" href="/assets/css/admin.css">
</head>
<body>
  <div class="admin-shell">
    <aside class="admin-sidebar">
      <div class="brand">
        <img class="brand-logo" src="/assets/img/logohorizontalbranco.png" alt="Complementare">
      </div>
      <nav class="admin-nav">
        <a href="/admin" class="<?= $isDashboard ? 'active' : '' ?>">Dashboard</a>
        <a href="/admin/proposals" class="<?= $isProposals && !$isNewProposal ? 'active' : '' ?>">Propostas</a>
        <a href="/admin/proposals/new" class="<?= $isNewProposal ? 'active' : '' ?>">Nova proposta</a>
        <?php if ($isAdmin): ?>
          <a href="/admin/users" class="<?= $isUsers ? 'active' : '' ?>">Usuários</a>
          <a href="/admin/settings" class="<?= $isSettings ? 'active' : '' ?>">Configurações</a>
        <?php endif; ?>
      </nav>
      <div class="sidebar-foot">
        <?php if ($adminUser): ?>
          <div class="whoami"><?= h((string) $adminUser['name']) ?><br><small><?= h((string) $adminUser['email']) ?></small></div>
        <?php endif; ?>
        <form method="post" action="/admin/logout">
          <?= csrf_field() ?>
          <button class="btn btn-ghost" type="submit">Sair</button>
        </form>
      </div>
    </aside>
    <main class="admin-main">
      <?php if (!empty($flashes)): ?>
        <div class="flash-wrap">
          <?php foreach ($flashes as $flash): ?>
            <div class="flash flash-<?= h((string) ($flash['type'] ?? 'info')) ?>"><?= h((string) ($flash['message'] ?? '')) ?></div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
      <?php require $viewFile; ?>
    </main>
  </div>
  <script src="/assets/js/admin.js" defer></script>
</body>
</html>
