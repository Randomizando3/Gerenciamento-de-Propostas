<?php
$proposals = $proposals ?? [];
$filters = $filters ?? [];
$statusOptions = $statusOptions ?? [];
$pagination = $pagination ?? [
    'page' => 1,
    'per_page' => 20,
    'total' => count($proposals),
    'total_pages' => 1,
    'from' => $proposals ? 1 : 0,
    'to' => count($proposals),
];

$currentPage = max(1, (int) ($pagination['page'] ?? 1));
$totalPages = max(1, (int) ($pagination['total_pages'] ?? 1));
$perPage = max(1, (int) ($pagination['per_page'] ?? 10));
$queryBase = $filters;

$buildProposalPageUrl = static function (int $page) use ($queryBase): string {
    $params = $queryBase;
    $params['page'] = max(1, $page);
    return '/admin/proposals?' . http_build_query($params);
};

$pageNumbers = [];
$startPage = max(1, $currentPage - 2);
$endPage = min($totalPages, $currentPage + 2);
for ($pageIndex = $startPage; $pageIndex <= $endPage; $pageIndex++) {
    $pageNumbers[] = $pageIndex;
}
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
  <form method="get" action="/admin/proposals" class="grid cols-7">
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
    <label class="field">
      <span>Ordenar por</span>
      <select name="order_by">
        <option value="updated_at" <?= ((string) ($filters['order_by'] ?? 'updated_at') === 'updated_at') ? 'selected' : '' ?>>Atualização</option>
        <option value="code" <?= ((string) ($filters['order_by'] ?? '') === 'code') ? 'selected' : '' ?>>Código</option>
        <option value="client" <?= ((string) ($filters['order_by'] ?? '') === 'client') ? 'selected' : '' ?>>Cliente</option>
        <option value="status" <?= ((string) ($filters['order_by'] ?? '') === 'status') ? 'selected' : '' ?>>Status</option>
        <option value="total_value" <?= ((string) ($filters['order_by'] ?? '') === 'total_value') ? 'selected' : '' ?>>Valor</option>
        <option value="total_views" <?= ((string) ($filters['order_by'] ?? '') === 'total_views') ? 'selected' : '' ?>>Visualizações</option>
        <option value="max_scroll" <?= ((string) ($filters['order_by'] ?? '') === 'max_scroll') ? 'selected' : '' ?>>Scroll</option>
      </select>
    </label>
    <label class="field">
      <span>Direção</span>
      <select name="order_dir">
        <option value="desc" <?= ((string) ($filters['order_dir'] ?? 'desc') === 'desc') ? 'selected' : '' ?>>Maior primeiro</option>
        <option value="asc" <?= ((string) ($filters['order_dir'] ?? '') === 'asc') ? 'selected' : '' ?>>Menor primeiro</option>
      </select>
    </label>
    <div class="inline-actions">
      <button class="btn btn-primary" type="submit">Filtrar</button>
      <a class="btn btn-ghost" href="/admin/proposals">Limpar</a>
    </div>
  </form>
</section>

<section class="table-wrap">
  <div class="table-head table-head-split">
    <div>
      <h2>Todas as propostas</h2>
      <p class="table-meta">
        <?php if ((int) ($pagination['total'] ?? 0) > 0): ?>
          Exibindo <?= (int) ($pagination['from'] ?? 0) ?>-<?= (int) ($pagination['to'] ?? 0) ?> de <?= (int) ($pagination['total'] ?? 0) ?> proposta(s).
        <?php else: ?>
          Nenhuma proposta encontrada com os filtros atuais.
        <?php endif; ?>
      </p>
    </div>
    <div class="table-meta-badge">
      <?= $perPage ?> por página
    </div>
  </div>

  <div class="table-scroll table-scroll-top" data-table-scroll-top>
    <div class="table-scroll-top-inner" data-table-scroll-top-inner></div>
  </div>

  <div class="table-scroll" data-table-scroll-bottom>
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
                <a href="<?= h($publicUrl) ?>" target="_blank" rel="noopener noreferrer">Link público</a>
              <?php endif; ?>
            </td>
            <td>
              <div class="table-actions">
                <a class="btn btn-ghost btn-sm" href="/admin/proposals/<?= (int) $row['id'] ?>/edit">Editar</a>
                <a class="btn btn-ghost btn-sm" href="/admin/proposals/<?= (int) $row['id'] ?>/preview" target="_blank" rel="noopener noreferrer">Preview</a>
                <a class="btn btn-ghost btn-sm" href="/admin/proposals/<?= (int) $row['id'] ?>/analytics">Analytics</a>
                <form method="post" action="/admin/proposals/<?= (int) $row['id'] ?>/duplicate">
                  <?= csrf_field() ?>
                  <button class="btn btn-ghost btn-sm" type="submit">Duplicar</button>
                </form>
                <form method="post" action="/admin/proposals/<?= (int) $row['id'] ?>/delete" onsubmit="return confirm('Deseja excluir esta proposta? Esta ação não pode ser desfeita.');">
                  <?= csrf_field() ?>
                  <button class="btn btn-ghost btn-sm" type="submit">Excluir</button>
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

  <?php if ($totalPages > 1): ?>
    <div class="table-pagination">
      <div class="table-pagination-info">
        Página <?= $currentPage ?> de <?= $totalPages ?>
      </div>
      <nav class="pagination-nav" aria-label="Paginação das propostas">
        <a class="pagination-link <?= $currentPage <= 1 ? 'is-disabled' : '' ?>" href="<?= $currentPage <= 1 ? '#' : h($buildProposalPageUrl(1)) ?>" <?= $currentPage <= 1 ? 'aria-disabled="true" tabindex="-1"' : '' ?>>Primeira</a>
        <a class="pagination-link <?= $currentPage <= 1 ? 'is-disabled' : '' ?>" href="<?= $currentPage <= 1 ? '#' : h($buildProposalPageUrl($currentPage - 1)) ?>" <?= $currentPage <= 1 ? 'aria-disabled="true" tabindex="-1"' : '' ?>>Anterior</a>

        <?php foreach ($pageNumbers as $pageNumber): ?>
          <a class="pagination-link <?= $pageNumber === $currentPage ? 'is-current' : '' ?>" href="<?= h($buildProposalPageUrl($pageNumber)) ?>" <?= $pageNumber === $currentPage ? 'aria-current="page"' : '' ?>>
            <?= $pageNumber ?>
          </a>
        <?php endforeach; ?>

        <a class="pagination-link <?= $currentPage >= $totalPages ? 'is-disabled' : '' ?>" href="<?= $currentPage >= $totalPages ? '#' : h($buildProposalPageUrl($currentPage + 1)) ?>" <?= $currentPage >= $totalPages ? 'aria-disabled="true" tabindex="-1"' : '' ?>>Próxima</a>
        <a class="pagination-link <?= $currentPage >= $totalPages ? 'is-disabled' : '' ?>" href="<?= $currentPage >= $totalPages ? '#' : h($buildProposalPageUrl($totalPages)) ?>" <?= $currentPage >= $totalPages ? 'aria-disabled="true" tabindex="-1"' : '' ?>>Última</a>
      </nav>
    </div>
  <?php endif; ?>
</section>
