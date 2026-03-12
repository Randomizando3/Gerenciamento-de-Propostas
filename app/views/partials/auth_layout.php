<?php
/** @var string $title */
/** @var array $flashes */
?>
<!doctype html>
<html lang="pt-BR">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= h($title ?? 'Login') ?> | <?= h((string) app_config('app_name')) ?></title>
  <link rel="stylesheet" href="/assets/css/admin.css">
</head>
<body class="auth-body">
  <div class="auth-logo-wrap">
    <img src="/assets/img/logohorizontalbranco.png" alt="Complementare">
  </div>
  <div class="auth-card">
    <?php if (!empty($flashes)): ?>
      <div class="flash-wrap">
        <?php foreach ($flashes as $flash): ?>
          <div class="flash flash-<?= h((string) ($flash['type'] ?? 'info')) ?>"><?= h((string) ($flash['message'] ?? '')) ?></div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
    <?php require $viewFile; ?>
  </div>
</body>
</html>

