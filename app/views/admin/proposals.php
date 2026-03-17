<?php
$proposals = $proposals ?? [];
$filters = $filters ?? [];
$statusOptions = $statusOptions ?? [];
?>
<section class="panel-head">
  <div>
    <h1>Propostas</h1>
    <p>Lista completa com filtros por status, cliente, código e valor.</p>
  </div>
  <div class="inline-actions">
    <a class="btn btn-primary" href="/admin/proposals/new">Nova proposta</a>
    <a class="btn btn-ghost" href="/admin">Dashboard</a>
  </div>
</section>

<section class="panel">
  <h2>Filtros</h2>
  <form method="get" action="/admin/proposals" class="grid cols-5">
    <label class="field">
      <span>Busca geral</span>
      <input type="text" name="query" value="<?= h((string) ($filters['query'] ?? '')) ?>" placeholder="Código, título ou obra">
    </label>
    <label class="field">
      <span>Cliente</span>
      <input type="text" name="client" value="<?= h((string) ($filters['client'] ?? '')) ?>" placeholder="Nome do cliente">
    </label>
    <label class="field">
      <span>Status</span>
      <select name="status">
        <option value="">Todos</option>
        <?php foreach ($statusOptions as $value => $label): ?>
          <option value="<?= h((string) $value) ?>" <?= ((string) ($filters['status'] ?? '') === (string) $value) ? 'selected' : '' ?>>
            <?= h((string) $label) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </label>
    <label class="field">
      <span>Valor mínimo</span>
      <input type="text" name="min_total" value="<?= h((string) ($filters['min_total'] ?? '')) ?>" placeholder="0,00">
    </label>
    <label class="field">
      <span>Valor máximo</span>
      <input type="text" name="max_total" value="<?= h((string) ($filters['max_total'] ?? '')) ?>" placeholder="0,00">
    </label>
    <div class="inline-actions">
      <button class="btn btn-primary" type="submit">Filtrar</button>
      <a class="btn btn-ghost" href="/admin/proposals">Limpar</a>
    </div>
  </form>
</section>

<section class="table-wrap">
  <div class="table-head">
    <h2>Todas as propostas</h2>
  </div>
  <div class="table-scroll">
    <table class="data-table proposal-table">
      <thead>
        <tr>
          <th>Código</th>
          <th>Cliente</th>
          <th>Status</th>
          <th>Total</th>
          <th>Visualizações</th>
          <th>Scroll</th>
          <th>Criada por</th>
          <th>Editada depois</th>
          <th>Atualização</th>
          <th>Ações</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!$proposals): ?>
          <tr><td colspan="10">Nenhuma proposta encontrada.</td></tr>
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
              <?= h((string) ($row['client_name'] ?: 'Não informado')) ?><br>
              <small><?= h((string) ($row['obra_nome'] ?: 'Sem obra')) ?></small>
            </td>
            <td>
              <span class="badge badge-<?= h((string) $row['status']) ?>"><?= h(status_label((string) $row['status'])) ?></span>
            </td>
            <td><?= brl((float) $row['total_value']) ?></td>
            <td><?= (int) $row['total_views'] ?></td>
            <td><?= number_format((float) $row['max_scroll'], 1, ',', '.') ?>%</td>
            <td><?= h((string) ($row['created_by_admin_name'] ?? 'Não informado')) ?></td>
            <td>
              <?php if ((int) ($row['edited_after_create'] ?? 0) === 1): ?>
                <span class="badge badge-viewed">Sim</span><br>
                <small><?= h((string) ($row['last_edited_by_admin_name'] ?? '')) ?></small>
              <?php else: ?>
                <span class="badge">Não</span>
              <?php endif; ?>
            </td>
            <td>
              <?= h((string) $row['updated_at']) ?><br>
              <?php if ($publicUrl): ?>
                <a href="<?= h($publicUrl) ?>" target="_blank">Link público</a>
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
  </div>
</section>

