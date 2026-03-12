<?php
$stats = $stats ?? [];
$admin = current_admin();
$isAdmin = is_admin_user($admin);
?>
<section class="panel-head">
  <div>
    <h1>Painel Administrativo</h1>
    <p>Visão geral de desempenho das propostas e conversão.</p>
  </div>
  <div class="inline-actions">
    <a class="btn btn-primary" href="/admin/proposals/new">Nova proposta</a>
    <a class="btn btn-ghost" href="/admin/proposals">Ver propostas</a>
    <?php if ($isAdmin): ?>
      <a class="btn btn-ghost" href="/admin/settings">Configurações</a>
    <?php endif; ?>
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
    <h3>Visualizações</h3>
    <strong><?= (int) ($stats['views'] ?? 0) ?></strong>
  </article>
  <article class="stat-card">
    <h3>Scroll médio</h3>
    <strong><?= number_format((float) ($stats['avg_scroll'] ?? 0), 1, ',', '.') ?>%</strong>
  </article>
</section>

<section class="grid cols-2">
  <article class="panel">
    <h2>Atalhos</h2>
    <p class="muted">Acesse rapidamente as principais ações do fluxo comercial.</p>
    <div class="inline-actions" style="margin-top: 12px;">
      <a class="btn btn-primary" href="/admin/proposals/new">Criar proposta</a>
      <a class="btn btn-ghost" href="/admin/proposals">Listar propostas</a>
    </div>
  </article>
  <article class="panel">
    <h2>Integrações</h2>
    <?php if ($isAdmin): ?>
      <p class="muted">Confira Clarity, ZapSign e URL base em Configurações.</p>
      <div class="inline-actions" style="margin-top: 12px;">
        <a class="btn btn-ghost" href="/admin/settings">Abrir configurações</a>
      </div>
    <?php else: ?>
      <p class="muted">Somente administradores podem gerenciar integrações e configurações.</p>
    <?php endif; ?>
  </article>
</section>
