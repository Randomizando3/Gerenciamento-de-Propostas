<?php
$stats = $stats ?? [];
$admin = current_admin();
$isAdmin = is_admin_user($admin);
$settings = is_array($settings ?? null) ? $settings : [];
$clarityUrl = 'https://clarity.microsoft.com/';
$zapsignUrl = 'https://app.zapsign.com.br/';
?>
<section class="panel-head">
  <div>
    <h1>Painel Administrativo</h1>
    <p>Vis&atilde;o geral de desempenho das propostas e convers&atilde;o.</p>
  </div>
  <div class="inline-actions">
    <a class="btn btn-primary" href="/admin/proposals/new">Nova proposta</a>
    <a class="btn btn-ghost" href="/admin/proposals">Ver propostas</a>
  </div>
</section>

<section class="stats-grid">
  <article class="stat-card">
    <h3>Total de propostas</h3>
    <strong><?= (int) ($stats['total_proposals'] ?? 0) ?></strong>
  </article>
  <article class="stat-card">
    <h3>Publicadas</h3>
    <strong><?= (int) ($stats['published'] ?? 0) ?></strong>
  </article>
  <article class="stat-card">
    <h3>Assinadas</h3>
    <strong><?= (int) ($stats['signed'] ?? 0) ?></strong>
  </article>
  <article class="stat-card">
    <h3>Visualiza&ccedil;&otilde;es</h3>
    <strong><?= (int) ($stats['views'] ?? 0) ?></strong>
  </article>
  <article class="stat-card">
    <h3>Scroll m&eacute;dio</h3>
    <strong><?= number_format((float) ($stats['avg_scroll'] ?? 0), 1, ',', '.') ?>%</strong>
  </article>
</section>

<section class="grid cols-2">
  <article class="panel">
    <h2>Atalhos</h2>
    <p class="muted">Acesse rapidamente as principais a&ccedil;&otilde;es do fluxo comercial.</p>
    <div class="inline-actions" style="margin-top: 12px;">
      <a class="btn btn-primary" href="/admin/proposals/new">Criar proposta</a>
      <a class="btn btn-ghost" href="/admin/proposals">Listar propostas</a>
    </div>
  </article>
  <article class="panel">
    <h2>Integra&ccedil;&otilde;es</h2>
    <p class="muted">Acesso externo r&aacute;pido para acompanhar analytics e documentos em assinatura.</p>
    <div class="inline-actions" style="margin-top: 12px;">
      <a class="btn btn-ghost" href="<?= h($clarityUrl) ?>" target="_blank" rel="noopener">Abrir Clarity</a>
      <a class="btn btn-ghost" href="<?= h($zapsignUrl) ?>" target="_blank" rel="noopener">Abrir ZapSign</a>
    </div>
  </article>
</section>
