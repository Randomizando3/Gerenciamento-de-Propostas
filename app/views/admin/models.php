<?php
$models = $models ?? [];
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
  <div class="table-head">
    <h2>Modelos cadastrados</h2>
  </div>
  <div class="table-scroll">
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
</section>

