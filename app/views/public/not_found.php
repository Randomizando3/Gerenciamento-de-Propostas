<?php $message = $message ?? 'Não encontramos a proposta solicitada.'; ?>
<!doctype html>
<html lang="pt-BR">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= h($title ?? 'Não encontrado') ?></title>
  <style>
    body {
      margin: 0;
      min-height: 100vh;
      display: grid;
      place-items: center;
      font-family: Arial, sans-serif;
      background: linear-gradient(120deg, #062d3b, #0b6b7b);
      color: #fff;
      padding: 24px;
    }
    .box {
      max-width: 720px;
      text-align: center;
      background: rgba(255,255,255,0.08);
      border: 1px solid rgba(255,255,255,0.2);
      border-radius: 16px;
      padding: 32px;
      backdrop-filter: blur(6px);
    }
    a {
      color: #9bf8ec;
      text-decoration: none;
    }
  </style>
</head>
<body>
  <main class="box">
    <h1><?= h($title ?? 'Não encontrado') ?></h1>
    <p><?= h((string) $message) ?></p>
    <p><a href="<?= h(app_url('/')) ?>">Voltar ao início</a></p>
  </main>
</body>
</html>
