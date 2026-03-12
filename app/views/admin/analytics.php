<?php
$focusProposal = $focusProposal ?? null;
$focusMetrics = $focusMetrics ?? null;
$stats = $stats ?? [];
if (!$focusProposal || !$focusMetrics) {
    return;
}
?>
<section class="panel-head">
  <div>
    <h1>Analytics da Proposta</h1>
    <p><?= h((string) $focusProposal['code']) ?> - desempenho de leitura e conversao.</p>
  </div>
  <div class="inline-actions">
    <a class="btn btn-ghost" href="/admin/proposals">Voltar para propostas</a>
    <a class="btn btn-primary" href="/admin/proposals/<?= (int) $focusProposal['id'] ?>/edit">Editar proposta</a>
  </div>
</section>

<section class="stats-grid">
  <article class="stat-card">
    <h3>Total propostas</h3>
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
    <h3>Visualizacoes</h3>
    <strong><?= (int) ($stats['views'] ?? 0) ?></strong>
  </article>
  <article class="stat-card">
    <h3>Scroll medio</h3>
    <strong><?= number_format((float) ($stats['avg_scroll'] ?? 0), 1, ',', '.') ?>%</strong>
  </article>
</section>

<section class="focus-metrics">
  <div class="table-head">
    <h2>Analytics: <?= h((string) $focusProposal['code']) ?></h2>
  </div>
  <div class="stats-grid">
    <article class="stat-card">
      <h3>Leituras</h3>
      <strong><?= (int) $focusMetrics['views'] ?></strong>
    </article>
    <article class="stat-card">
      <h3>Tempo medio</h3>
      <strong><?= (int) $focusMetrics['avg_time_seconds'] ?>s</strong>
    </article>
    <article class="stat-card">
      <h3>Scroll medio</h3>
      <strong><?= number_format((float) $focusMetrics['avg_scroll'], 1, ',', '.') ?>%</strong>
    </article>
    <article class="stat-card">
      <h3>Downloads PDF</h3>
      <strong><?= (int) $focusMetrics['downloads'] ?></strong>
    </article>
    <article class="stat-card">
      <h3>Cliques assinatura</h3>
      <strong><?= (int) $focusMetrics['sign_clicks'] ?></strong>
    </article>
    <article class="stat-card">
      <h3>Aceites termos</h3>
      <strong><?= (int) $focusMetrics['accepted'] ?></strong>
    </article>
  </div>

  <?php
  $sections = is_array($focusMetrics['sections'] ?? null) ? $focusMetrics['sections'] : [];
  $maxSectionSeconds = 0;
  foreach ($sections as $seconds) {
      $maxSectionSeconds = max($maxSectionSeconds, (int) $seconds);
  }

  $devices = is_array($focusMetrics['devices'] ?? null) ? $focusMetrics['devices'] : ['desktop' => 0, 'mobile' => 0, 'tablet' => 0];
  $desktop = (int) ($devices['desktop'] ?? 0);
  $mobile = (int) ($devices['mobile'] ?? 0);
  $tablet = (int) ($devices['tablet'] ?? 0);
  $totalDevices = max(1, $desktop + $mobile + $tablet);
  $desktopPct = round(($desktop / $totalDevices) * 100, 1);
  $mobilePct = round(($mobile / $totalDevices) * 100, 1);
  $tabletPct = max(0.0, 100 - $desktopPct - $mobilePct);

  $viewsBase = max(1, (int) ($focusMetrics['views'] ?? 0));
  $downloads = (int) ($focusMetrics['downloads'] ?? 0);
  $signClicks = (int) ($focusMetrics['sign_clicks'] ?? 0);
  $accepted = (int) ($focusMetrics['accepted'] ?? 0);
  ?>

  <div class="analytics-charts">
    <article class="panel chart-panel">
      <h3>Tempo por secao</h3>
      <?php if (empty($sections)): ?>
        <p class="muted">Sem dados por secao ainda.</p>
      <?php else: ?>
        <div class="bar-chart">
          <?php foreach ($sections as $section => $seconds): ?>
            <?php $width = $maxSectionSeconds > 0 ? round(((int) $seconds * 100) / $maxSectionSeconds, 1) : 0; ?>
            <div class="bar-row">
              <div class="bar-row-head">
                <span><?= h((string) $section) ?></span>
                <strong><?= (int) $seconds ?>s</strong>
              </div>
              <div class="bar-track">
                <span class="bar-fill" style="width: <?= $width ?>%;"></span>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </article>

    <article class="panel chart-panel">
      <h3>Dispositivos</h3>
      <div class="device-chart">
        <div class="device-donut" style="background: conic-gradient(#0d8a98 0 <?= $desktopPct ?>%, #10b7a4 <?= $desktopPct ?>% <?= ($desktopPct + $mobilePct) ?>%, #244e7f <?= ($desktopPct + $mobilePct) ?>% 100%);">
          <div class="device-donut-center">
            <strong><?= $desktop + $mobile + $tablet ?></strong>
            <span>leituras</span>
          </div>
        </div>
        <div class="device-legend">
          <div><i class="dot dot-desktop"></i>Desktop <strong><?= $desktop ?></strong></div>
          <div><i class="dot dot-mobile"></i>Mobile <strong><?= $mobile ?></strong></div>
          <div><i class="dot dot-tablet"></i>Tablet <strong><?= $tablet ?></strong></div>
        </div>
      </div>
    </article>
  </div>

  <div class="metrics-columns">
    <article class="panel chart-panel">
      <h3>Funil de conversao</h3>
      <div class="funnel-rows">
        <div class="funnel-row">
          <div class="funnel-head"><span>Visualizacoes</span><strong><?= (int) $focusMetrics['views'] ?></strong></div>
          <div class="funnel-track"><span class="funnel-fill" style="width: 100%;"></span></div>
        </div>
        <div class="funnel-row">
          <div class="funnel-head"><span>Downloads PDF</span><strong><?= $downloads ?></strong></div>
          <div class="funnel-track"><span class="funnel-fill" style="width: <?= round(($downloads / $viewsBase) * 100, 1) ?>%;"></span></div>
        </div>
        <div class="funnel-row">
          <div class="funnel-head"><span>Cliques em Assinar</span><strong><?= $signClicks ?></strong></div>
          <div class="funnel-track"><span class="funnel-fill" style="width: <?= round(($signClicks / $viewsBase) * 100, 1) ?>%;"></span></div>
        </div>
        <div class="funnel-row">
          <div class="funnel-head"><span>Aceites</span><strong><?= $accepted ?></strong></div>
          <div class="funnel-track"><span class="funnel-fill" style="width: <?= round(($accepted / $viewsBase) * 100, 1) ?>%;"></span></div>
        </div>
      </div>
    </article>

    <article class="panel">
      <h3>Resumo por dispositivo</h3>
      <ul class="simple-list">
        <li><span>Desktop</span><strong><?= $desktop ?></strong></li>
        <li><span>Mobile</span><strong><?= $mobile ?></strong></li>
        <li><span>Tablet</span><strong><?= $tablet ?></strong></li>
      </ul>
    </article>
  </div>
</section>
