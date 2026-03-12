<form method="post" action="/admin/login" class="stack-md">
  <?= csrf_field() ?>
  <label class="field">
    <span>E-mail</span>
    <input type="email" name="email" required value="<?= h((string) old_input('email')) ?>" placeholder="admin@local">
  </label>
  <label class="field">
    <span>Senha</span>
    <input type="password" name="password" required placeholder="********">
  </label>
  <button class="btn btn-primary" type="submit">Entrar no painel</button>
</form>


