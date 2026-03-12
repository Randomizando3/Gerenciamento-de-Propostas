<?php
$users = is_array($users ?? null) ? $users : [];
$admin = current_admin();
$currentAdminId = (int) ($admin['id'] ?? 0);
?>

<section class="panel-head">
  <div>
    <h1>Usuários do painel</h1>
    <p>Cadastre novos acessos para o time. Todas as propostas continuam compartilhadas.</p>
  </div>
  <a class="btn btn-ghost" href="/admin">Voltar</a>
</section>

<section class="grid users-grid">
  <article class="panel">
    <h2>Novo usuário</h2>
    <p class="muted">Defina o perfil de acesso no cadastro.</p>
    <form method="post" action="/admin/users" class="stack-sm">
      <?= csrf_field() ?>
      <label class="field">
        <span>Nome</span>
        <input type="text" name="name" required>
      </label>
      <label class="field">
        <span>E-mail</span>
        <input type="email" name="email" required>
      </label>
      <label class="field">
        <span>Role</span>
        <select name="role" required>
          <option value="editor" selected>Editor</option>
          <option value="admin">Admin</option>
        </select>
      </label>
      <div class="grid cols-2">
        <label class="field">
          <span>Senha</span>
          <input type="password" name="password" minlength="6" required>
        </label>
        <label class="field">
          <span>Confirmar senha</span>
          <input type="password" name="password_confirm" minlength="6" required>
        </label>
      </div>
      <div class="inline-actions">
        <button class="btn btn-primary" type="submit">Adicionar usuário</button>
      </div>
    </form>
  </article>

  <article class="panel">
    <h2>Usuários cadastrados</h2>
    <p class="muted">Apenas administradores podem gerenciar usuários e configurações.</p>
    <div class="table-wrap users-table-wrap">
      <table class="data-table table-compact">
        <thead>
          <tr>
            <th>Nome</th>
            <th>E-mail</th>
            <th>Role</th>
            <th>Status</th>
            <th>Criado em</th>
            <th>Ações</th>
          </tr>
        </thead>
        <tbody>
          <?php if ($users === []): ?>
            <tr><td colspan="6">Nenhum usuário cadastrado.</td></tr>
          <?php endif; ?>
          <?php foreach ($users as $user): ?>
            <?php
            $isCurrent = (int) ($user['id'] ?? 0) === $currentAdminId;
            $role = mb_strtolower((string) ($user['role'] ?? 'admin'), 'UTF-8');
            $roleLabel = $role === 'editor' ? 'Editor' : 'Admin';
            ?>
            <tr>
              <td>
                <strong><?= h((string) ($user['name'] ?? '')) ?></strong>
                <?php if ($isCurrent): ?>
                  <br><small>Você</small>
                <?php endif; ?>
              </td>
              <td><?= h((string) ($user['email'] ?? '')) ?></td>
              <td><span class="badge"><?= h($roleLabel) ?></span></td>
              <td>
                <?php if ((int) ($user['is_active'] ?? 1) === 1): ?>
                  <span class="badge badge-signed">Ativo</span>
                <?php else: ?>
                  <span class="badge badge-draft">Inativo</span>
                <?php endif; ?>
              </td>
              <td><?= h((string) ($user['created_at'] ?? '')) ?></td>
              <td>
                <form method="post" action="/admin/users/<?= (int) ($user['id'] ?? 0) ?>/delete" onsubmit="return confirm('Deseja remover este usuário?');">
                  <?= csrf_field() ?>
                  <button class="btn btn-ghost btn-sm" type="submit">Remover</button>
                </form>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </article>
</section>
