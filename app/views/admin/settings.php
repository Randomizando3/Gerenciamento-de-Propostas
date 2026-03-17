<?php
$acceptTermsVariables = is_array($acceptTermsVariables ?? null) ? $acceptTermsVariables : [];
?>

<section class="panel-head">
  <div>
    <h1>Configurações</h1>
    <p>Integra&ccedil;&otilde;es, dados da empresa e personaliza&ccedil;&atilde;o dos termos de aceite.</p>
  </div>
  <a class="btn btn-ghost" href="/admin">Voltar</a>
</section>

<form method="post" action="/admin/settings" class="stack-lg settings-form" data-settings-tabs>
  <?= csrf_field() ?>

  <div class="settings-tabs" role="tablist" aria-label="Seções de configuração">
    <button class="settings-tab is-active" type="button" data-tab-target="integrations">Integrações</button>
    <button class="settings-tab" type="button" data-tab-target="company">Empresa</button>
    <button class="settings-tab" type="button" data-tab-target="terms">Termos de aceite</button>
  </div>

  <section class="panel settings-pane is-active" data-tab-pane="integrations">
    <h2>Ambiente</h2>
    <label class="field">
      <span>URL base pública (opcional)</span>
      <input type="url" name="base_url" value="<?= h((string) ($settings['base_url'] ?? '')) ?>" placeholder="https://seu-dominio.com">
    </label>

    <hr class="settings-separator">

    <h2>Microsoft Clarity</h2>
    <label class="toggle">
      <input type="checkbox" name="clarity_enabled" <?= ((int) ($settings['clarity_enabled'] ?? 0) === 1) ? 'checked' : '' ?>>
      <span>Ativar script do Clarity nas propostas públicas</span>
    </label>
    <label class="field">
      <span>Project ID do Clarity</span>
      <input type="text" name="clarity_project_id" value="<?= h((string) ($settings['clarity_project_id'] ?? '')) ?>" placeholder="abc123xyz">
    </label>
    <label class="field">
      <span>Endpoint API Export (Live Insights)</span>
      <input type="url" name="clarity_export_endpoint" value="<?= h((string) ($settings['clarity_export_endpoint'] ?? 'https://www.clarity.ms/export-data/api/v1/project-live-insights')) ?>">
    </label>
    <label class="field field-top">
      <span>Token API Export (JWT)</span>
      <textarea name="clarity_export_token" rows="3" placeholder="Cole o token JWT da API Export do Clarity"><?= h((string) ($settings['clarity_export_token'] ?? '')) ?></textarea>
    </label>

    <hr class="settings-separator">

    <h2>ZapSign</h2>
    <label class="toggle">
      <input type="checkbox" name="zapsign_enabled" <?= ((int) ($settings['zapsign_enabled'] ?? 0) === 1) ? 'checked' : '' ?>>
      <span>Ativar integração via API</span>
    </label>
    <label class="field">
      <span>Base URL ZapSign</span>
      <input type="url" name="zapsign_base_url" value="<?= h((string) ($settings['zapsign_base_url'] ?? 'https://sandbox.api.zapsign.com.br')) ?>">
    </label>
    <label class="field">
      <span>API Key</span>
      <input type="text" name="zapsign_api_key" value="<?= h((string) ($settings['zapsign_api_key'] ?? '')) ?>">
    </label>
    <label class="field">
      <span>Webhook Secret (opcional)</span>
      <input type="text" name="zapsign_webhook_secret" value="<?= h((string) ($settings['zapsign_webhook_secret'] ?? '')) ?>">
    </label>
    <p class="muted">Webhook pronto em: <code>/webhook/zapsign</code> (compatibilidade: <code>/webhooks/zapsign</code>)</p>
  </section>

  <section class="panel settings-pane" data-tab-pane="company">
    <h2>Dados da empresa</h2>
    <div class="grid cols-2">
      <label class="field">
        <span>Nome da empresa</span>
        <input type="text" name="company_name" value="<?= h((string) ($settings['company_name'] ?? '')) ?>">
      </label>
      <label class="field">
        <span>Telefone</span>
        <input type="text" name="company_phone" value="<?= h((string) ($settings['company_phone'] ?? '')) ?>">
      </label>
    </div>
    <div class="grid cols-2">
      <label class="field">
        <span>Website</span>
        <input type="url" name="company_website" value="<?= h((string) ($settings['company_website'] ?? '')) ?>">
      </label>
      <label class="field">
        <span>Instagram (URL)</span>
        <input type="url" name="company_instagram" value="<?= h((string) ($settings['company_instagram'] ?? '')) ?>">
      </label>
    </div>
    <label class="field">
      <span>Endereço</span>
      <textarea name="company_address" rows="4"><?= h((string) ($settings['company_address'] ?? '')) ?></textarea>
    </label>

    <hr class="settings-separator">

    <h2>Dados bancários (PIX)</h2>
    <div class="grid cols-2">
      <label class="field">
        <span>Banco</span>
        <input type="text" name="company_bank_name" value="<?= h((string) ($settings['company_bank_name'] ?? '')) ?>" placeholder="Banco Inter (077)">
      </label>
      <label class="field">
        <span>Agência</span>
        <input type="text" name="company_bank_agency" value="<?= h((string) ($settings['company_bank_agency'] ?? '')) ?>" placeholder="0001">
      </label>
    </div>
    <div class="grid cols-2">
      <label class="field">
        <span>Conta corrente</span>
        <input type="text" name="company_bank_account" value="<?= h((string) ($settings['company_bank_account'] ?? '')) ?>" placeholder="3375106-4">
      </label>
      <label class="field">
        <span>Favorecido</span>
        <input type="text" name="company_bank_favored" value="<?= h((string) ($settings['company_bank_favored'] ?? '')) ?>" placeholder="Nome da empresa favorecida">
      </label>
    </div>
    <div class="grid cols-2">
      <label class="field">
        <span>CNPJ</span>
        <input type="text" name="company_bank_cnpj" value="<?= h((string) ($settings['company_bank_cnpj'] ?? '')) ?>" placeholder="00.000.000/0001-00">
      </label>
      <label class="field">
        <span>Tipo da chave PIX</span>
        <input type="text" name="company_bank_pix_key_type" value="<?= h((string) ($settings['company_bank_pix_key_type'] ?? 'CNPJ')) ?>" placeholder="CNPJ / E-mail / Telefone">
      </label>
    </div>
    <label class="field">
      <span>Chave PIX</span>
      <input type="text" name="company_bank_pix_key" value="<?= h((string) ($settings['company_bank_pix_key'] ?? '')) ?>" placeholder="23.012.176/0001-69">
    </label>

    <hr class="settings-separator">

    <h2>Textos institucionais</h2>
    <label class="field field-top">
      <span>Texto da empresa</span>
      <textarea name="company_about_text" rows="5"><?= h((string) ($settings['company_about_text'] ?? '')) ?></textarea>
    </label>
    <label class="field">
      <span>Frase do aceite</span>
      <input type="text" name="company_accept_phrase" value="<?= h((string) ($settings['company_accept_phrase'] ?? '')) ?>">
    </label>
  </section>

  <section class="panel settings-pane" data-tab-pane="terms">
    <h2>Termos de aceite</h2>
    <p class="muted">Se preencher, este conteúdo substitui o contrato padrão exibido no modal da proposta pública.</p>

    <label class="field">
      <span>Título do modal</span>
      <input type="text" name="accept_terms_title" value="<?= h((string) ($settings['accept_terms_title'] ?? '')) ?>" placeholder="CONTRATO DE PRESTAÇÃO DE SERVIÇOS...">
    </label>

    <label class="field field-top">
      <span>Conteúdo dos termos (HTML opcional)</span>
      <textarea name="accept_terms_html" rows="16" placeholder="Exemplo: &lt;p&gt;Ao aceitar a proposta {{PROPOSTA_NUM}}, ...&lt;/p&gt;"><?= h((string) ($settings['accept_terms_html'] ?? '')) ?></textarea>
    </label>

    <label class="field">
      <span>Texto do checkbox de concordância</span>
      <input type="text" name="accept_terms_checkbox_text" value="<?= h((string) ($settings['accept_terms_checkbox_text'] ?? '')) ?>">
    </label>

    <details class="settings-spoiler">
      <summary>Ver variáveis disponíveis</summary>
      <div class="settings-vars">
        <p class="muted">Use no texto com o formato <code>{{NOME_DA_VARIAVEL}}</code>.</p>
        <div class="settings-vars-grid">
          <?php foreach ($acceptTermsVariables as $var => $description): ?>
            <div class="settings-var-item">
              <code>{{<?= h((string) $var) ?>}}</code>
              <small><?= h((string) $description) ?></small>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    </details>
  </section>

  <section class="inline-actions">
    <button class="btn btn-primary" type="submit">Salvar configurações</button>
  </section>
</form>

<script>
  (function () {
    const root = document.querySelector('[data-settings-tabs]');
    if (!root) return;

    const tabs = Array.from(root.querySelectorAll('[data-tab-target]'));
    const panes = Array.from(root.querySelectorAll('[data-tab-pane]'));

    function activate(name) {
      tabs.forEach((tab) => {
        const isActive = tab.getAttribute('data-tab-target') === name;
        tab.classList.toggle('is-active', isActive);
      });
      panes.forEach((pane) => {
        const isActive = pane.getAttribute('data-tab-pane') === name;
        pane.classList.toggle('is-active', isActive);
      });
    }

    tabs.forEach((tab) => {
      tab.addEventListener('click', () => activate(tab.getAttribute('data-tab-target') || 'integrations'));
    });
  })();
</script>



