<?php
$proposals = $proposals ?? [];
?>
<section class="panel-head">
  <div>
    <h1>Propostas</h1>
    <p>Lista completa de propostas com status, visualizacoes e acoes.</p>
  </div>
  <div class="inline-actions">
    <a class="btn btn-primary" href="/admin/proposals/new">Nova proposta</a>
    <a class="btn btn-ghost" href="/admin">Dashboard</a>
  </div>
</section>

<section class="table-wrap">
  <div class="table-head">
    <h2>Todas as propostas</h2>
  </div>
  <table class="data-table">
    <thead>
      <tr>
        <th>Codigo</th>
        <th>Cliente</th>
        <th>Status</th>
        <th>Total</th>
        <th>Views</th>
        <th>Scroll</th>
        <th>Atualizacao</th>
        <th>Acoes</th>
      </tr>
    </thead>
    <tbody>
      <?php if (!$proposals): ?>
        <tr><td colspan="8">Nenhuma proposta cadastrada.</td></tr>
      <?php endif; ?>
      <?php foreach ($proposals as $row): ?>
        <?php $publicUrl = proposal_public_url($row); ?>
        <tr>
          <td>
            <strong><?= h((string) $row['code']) ?></strong><br>
            <?php if (trim((string) ($row['revision'] ?? '')) !== '' && trim((string) ($row['revision'] ?? '')) !== '00'): ?>
              <small>Rev. <?= h((string) $row['revision']) ?></small>
            <?php endif; ?>
          </td>
          <td>
            <?= h((string) ($row['client_name'] ?: 'Nao informado')) ?><br>
            <small><?= h((string) ($row['obra_nome'] ?: 'Sem obra')) ?></small>
          </td>
          <td>
            <span class="badge badge-<?= h((string) $row['status']) ?>"><?= h(status_label((string) $row['status'])) ?></span>
          </td>
          <td><?= brl((float) $row['total_value']) ?></td>
          <td><?= (int) $row['total_views'] ?></td>
          <td><?= number_format((float) $row['max_scroll'], 1, ',', '.') ?>%</td>
          <td>
            <?= h((string) $row['updated_at']) ?><br>
            <?php if ($publicUrl): ?>
              <a href="<?= h($publicUrl) ?>" target="_blank">Link publico</a>
            <?php endif; ?>
          </td>
          <td>
            <div class="table-actions">
              <a class="btn btn-ghost btn-sm" href="/admin/proposals/<?= (int) $row['id'] ?>/edit">Editar</a>
              <a class="btn btn-ghost btn-sm" href="/admin/proposals/<?= (int) $row['id'] ?>/preview" target="_blank">Preview</a>
              <a class="btn btn-ghost btn-sm" href="/admin/proposals/<?= (int) $row['id'] ?>/analytics">Analytics</a>
              <form method="post" action="/admin/proposals/<?= (int) $row['id'] ?>/duplicate">
                <?= csrf_field() ?>
                <button class="btn btn-ghost btn-sm" type="submit">Duplicar</button>
              </form>
              <?php if ((string) $row['status'] === 'draft'): ?>
                <form method="post" action="/admin/proposals/<?= (int) $row['id'] ?>/publish">
                  <?= csrf_field() ?>
                  <button class="btn btn-primary btn-sm" type="submit">Publicar</button>
                </form>
              <?php endif; ?>
            </div>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</section>
