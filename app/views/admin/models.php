<?php
$models = $models ?? [];
$pagination = $pagination ?? [
    'page' => 1,
    'per_page' => 10,
    'total' => count($models),
    'total_pages' => 1,
    'from' => $models ? 1 : 0,
    'to' => count($models),
];

$currentPage = max(1, (int) ($pagination['page'] ?? 1));
$totalPages = max(1, (int) ($pagination['total_pages'] ?? 1));
$perPage = max(1, (int) ($pagination['per_page'] ?? 10));
$buildModelPageUrl = static function (int $page): string {
    return '/admin/models?page=' . max(1, $page);
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
    <h1>Modelos de proposta</h1>
    <p>Use modelos para acelerar novos orçamentos e padronizar o escopo.</p>
  </div>
  <div class="inline-actions">
    <a class="btn btn-primary" href="/admin/models/new">Novo modelo</a>
    <a class="btn btn-ghost" href="/admin/proposals/new">Nova proposta</a>
    <a class="btn btn-ghost" href="/admin/proposals">Ver propostas</a>
  </div>
</section>

<section class="table-wrap">
  <div class="table-head table-head-split">
    <div>
      <h2>Modelos cadastrados</h2>
      <p class="table-meta">
        <?php if ((int) ($pagination['total'] ?? 0) > 0): ?>
          Exibindo <?= (int) ($pagination['from'] ?? 0) ?>-<?= (int) ($pagination['to'] ?? 0) ?> de <?= (int) ($pagination['total'] ?? 0) ?> modelo(s).
        <?php else: ?>
          Nenhum modelo salvo ainda.
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
          <th>Nome</th>
          <th>Descrição</th>
          <th>Criado por</th>
          <th>Atualizado por</th>
          <th>Atualizado em</th>
          <th>Ações</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!$models): ?>
          <tr><td colspan="6">Nenhum modelo salvo ainda.</td></tr>
        <?php endif; ?>
        <?php foreach ($models as $model): ?>
          <tr>
            <td><strong><?= h((string) ($model['name'] ?? 'Modelo')) ?></strong></td>
            <td><?= h((string) ($model['description'] ?? '')) ?></td>
            <td><?= h((string) ($model['created_by_admin_name'] ?? '')) ?></td>
            <td><?= h((string) ($model['updated_by_admin_name'] ?? '')) ?></td>
            <td><?= h((string) ($model['updated_at'] ?? '')) ?></td>
            <td>
              <div class="table-actions">
                <a class="btn btn-ghost btn-sm" href="/admin/models/<?= (int) $model['id'] ?>/edit">Editar</a>
                <a class="btn btn-ghost btn-sm" href="/admin/proposals/new?model=<?= (int) $model['id'] ?>">Usar no novo</a>
                <form method="post" action="/admin/models/<?= (int) $model['id'] ?>/delete" onsubmit="return confirm('Remover este modelo?');">
                  <?= csrf_field() ?>
                  <button class="btn btn-ghost btn-sm" type="submit">Excluir</button>
                </form>
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
      <nav class="pagination-nav" aria-label="Paginação dos modelos">
        <a class="pagination-link <?= $currentPage <= 1 ? 'is-disabled' : '' ?>" href="<?= $currentPage <= 1 ? '#' : h($buildModelPageUrl(1)) ?>" <?= $currentPage <= 1 ? 'aria-disabled="true" tabindex="-1"' : '' ?>>Primeira</a>
        <a class="pagination-link <?= $currentPage <= 1 ? 'is-disabled' : '' ?>" href="<?= $currentPage <= 1 ? '#' : h($buildModelPageUrl($currentPage - 1)) ?>" <?= $currentPage <= 1 ? 'aria-disabled="true" tabindex="-1"' : '' ?>>Anterior</a>

        <?php foreach ($pageNumbers as $pageNumber): ?>
          <a class="pagination-link <?= $pageNumber === $currentPage ? 'is-current' : '' ?>" href="<?= h($buildModelPageUrl($pageNumber)) ?>" <?= $pageNumber === $currentPage ? 'aria-current="page"' : '' ?>>
            <?= $pageNumber ?>
          </a>
        <?php endforeach; ?>

        <a class="pagination-link <?= $currentPage >= $totalPages ? 'is-disabled' : '' ?>" href="<?= $currentPage >= $totalPages ? '#' : h($buildModelPageUrl($currentPage + 1)) ?>" <?= $currentPage >= $totalPages ? 'aria-disabled="true" tabindex="-1"' : '' ?>>Próxima</a>
        <a class="pagination-link <?= $currentPage >= $totalPages ? 'is-disabled' : '' ?>" href="<?= $currentPage >= $totalPages ? '#' : h($buildModelPageUrl($totalPages)) ?>" <?= $currentPage >= $totalPages ? 'aria-disabled="true" tabindex="-1"' : '' ?>>Última</a>
      </nav>
    </div>
  <?php endif; ?>
</section>
