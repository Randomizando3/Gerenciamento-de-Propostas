<?php
$isEdit = $proposal !== null;
$isModelEditor = (bool) ($isModelEditor ?? false);
$model = $model ?? null;
$total = proposal_total($payload);
$totalExtenso = currency_to_words_ptbr($total);
$publicUrl = $isEdit ? proposal_public_url($proposal) : null;
$customDisciplines = is_array($payload['disciplinas_custom'] ?? null) ? $payload['disciplinas_custom'] : [];
$fileRows = is_array($payload['arquivos'] ?? null) && $payload['arquivos'] !== [] ? $payload['arquivos'] : [['item' => '', 'nome' => '', 'rev' => '', 'data' => '']];
$stageRows = is_array($payload['etapas'] ?? null) && $payload['etapas'] !== [] ? $payload['etapas'] : [['nome' => '', 'prazo' => '', 'descricao' => '']];
$paymentRows = is_array($payload['payment_schedule_rows'] ?? null) ? $payload['payment_schedule_rows'] : [];
$manualPaymentEnabled = proposal_payment_schedule_manual_enabled($payload);
$cardPaymentEnabled = proposal_flag_enabled($payload['pagamento_cartao_ativo'] ?? false);
$boletoPaymentEnabled = proposal_flag_enabled($payload['pagamento_boleto_ativo'] ?? false);
$guidelineRows = is_array($payload['guidelines_items'] ?? null) && $payload['guidelines_items'] !== [] ? $payload['guidelines_items'] : [['title' => '', 'content' => '', 'icon' => '']];
$scopeItems = is_array($payload['scope_items'] ?? null) ? $payload['scope_items'] : [];
$models = $models ?? [];
$currentModel = $currentModel ?? null;
$acceptTermsVariables = is_array($acceptTermsVariables ?? null) ? $acceptTermsVariables : [];
$modelNameValue = (string) ($model['name'] ?? '');
$modelDescriptionValue = (string) ($model['description'] ?? '');
$formAction = $isModelEditor ? '/admin/models/save' : '/admin/proposals/save';
?>
<section class="panel-head">
  <div>
    <h1><?= $isModelEditor ? ($model ? 'Editar modelo' : 'Novo modelo') : ($isEdit ? 'Editar proposta' : 'Nova proposta') ?></h1>
    <p><?= $isModelEditor ? 'Monte modelos reutilizáveis com escopo, pagamento e contrato padrão por tipo de proposta.' : 'Fluxo completo com modelos, escopo detalhado, proposta pública, analytics e assinatura.' ?></p>
  </div>
  <div class="inline-actions">
    <a class="btn btn-ghost" href="<?= $isModelEditor ? '/admin/models' : '/admin/proposals' ?>">Voltar</a>
    <?php if ($isEdit && !$isModelEditor): ?>
      <a class="btn btn-ghost" href="/admin/proposals/<?= (int) $proposal['id'] ?>/preview" target="_blank">Abrir preview</a>
      <?php if ($publicUrl): ?>
        <a class="btn btn-primary btn-icon-only" href="<?= h($publicUrl) ?>" target="_blank" rel="noopener" aria-label="Abrir pública em nova guia" title="Abrir pública em nova guia">
          <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false">
            <path d="M14 4h6v6h-2V7.41l-7.29 7.3-1.42-1.42 7.3-7.29H14V4ZM6 6h5v2H8v8h8v-3h2v5H6V6Z" fill="currentColor"></path>
          </svg>
          <span class="sr-only">Abrir pública</span>
        </a>
      <?php endif; ?>
    <?php endif; ?>
  </div>
</section>

<?php if ($isEdit && !$isModelEditor): ?>
  <section class="panel compact">
    <div class="inline-info">
      <span><strong>Status:</strong> <?= h(status_label((string) $proposal['status'])) ?></span>
      <span><strong>Token:</strong> <?= h((string) ($proposal['token'] ?: 'não publicado')) ?></span>
      <span><strong>Atualizado:</strong> <?= h((string) $proposal['updated_at']) ?></span>
    </div>
    <div class="inline-actions">
      <?php if ((string) $proposal['status'] === 'draft'): ?>
        <form method="post" action="/admin/proposals/<?= (int) $proposal['id'] ?>/publish">
          <?= csrf_field() ?>
          <button class="btn btn-primary" type="submit">Publicar proposta</button>
        </form>
      <?php endif; ?>
      <form method="post" action="/admin/proposals/<?= (int) $proposal['id'] ?>/duplicate">
        <?= csrf_field() ?>
        <button class="btn btn-ghost" type="submit">Duplicar</button>
      </form>
      <form method="post" action="/admin/proposals/<?= (int) $proposal['id'] ?>/zapsign">
        <?= csrf_field() ?>
        <button class="btn btn-ghost" type="submit">Enviar para ZapSign</button>
      </form>
      <form method="post" action="/admin/proposals/<?= (int) $proposal['id'] ?>/delete" onsubmit="return confirm('Deseja excluir esta proposta? Esta ação não pode ser desfeita.');">
        <?= csrf_field() ?>
        <button class="btn btn-ghost" type="submit">Excluir proposta</button>
      </form>
    </div>
  </section>
<?php endif; ?>

<?php if (!$isModelEditor): ?>
<section class="panel">
  <div class="panel-subhead">
    <div>
      <h2>Modelos</h2>
      <p class="muted">Carregue um modelo em uma proposta nova ou salve a proposta atual como base.</p>
    </div>
    <a class="btn btn-ghost btn-sm" href="/admin/models">Gerenciar modelos</a>
  </div>
  <div class="grid cols-2">
    <form method="get" action="/admin/proposals/new" class="panel panel-inner stack-sm">
      <h3>Usar modelo existente</h3>
      <label class="field">
        <span>Modelo</span>
        <select name="model">
          <option value="">Selecione</option>
          <?php foreach ($models as $model): ?>
            <option value="<?= (int) $model['id'] ?>" <?= ((int) ($currentModel['id'] ?? 0) === (int) $model['id']) ? 'selected' : '' ?>><?= h((string) ($model['name'] ?? 'Modelo')) ?></option>
          <?php endforeach; ?>
        </select>
      </label>
      <button class="btn btn-primary" type="submit">Carregar modelo</button>
    </form>
    <div class="panel panel-inner stack-sm">
      <h3>Modelo carregado</h3>
      <?php if ($currentModel): ?>
        <strong><?= h((string) ($currentModel['name'] ?? 'Modelo')) ?></strong>
        <p class="muted"><?= h((string) ($currentModel['description'] ?? '')) ?></p>
      <?php else: ?>
        <p class="muted">Nenhum modelo carregado nesta proposta.</p>
      <?php endif; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<form method="post" action="<?= h($formAction) ?>" class="stack-lg" id="proposal-form" enctype="multipart/form-data">
  <?= csrf_field() ?>
  <?php if ($isModelEditor && $model): ?>
    <input type="hidden" name="model_id" value="<?= (int) $model['id'] ?>">
  <?php elseif ($isEdit): ?>
    <input type="hidden" name="id" value="<?= (int) $proposal['id'] ?>">
  <?php endif; ?>

  <?php if (!$isModelEditor): ?>
  <section class="panel">
    <h2>Cabe&ccedil;alho</h2>
    <div class="toggle-config-block">
      <label class="field">
        <span>Formato do t&iacute;tulo</span>
        <select name="header_title_layout" data-header-layout-select>
          <option value="default" <?= (($payload['header_title_layout'] ?? 'default') === 'default') ? 'selected' : '' ?>>Proposta padr&atilde;o</option>
          <option value="aditivo" <?= (($payload['header_title_layout'] ?? '') === 'aditivo') ? 'selected' : '' ?>>Aditivo da proposta</option>
        </select>
      </label>
      <div class="grid cols-2" data-header-layout-fields>
        <label class="field">
          <span>Texto superior do aditivo</span>
          <input type="text" name="header_aditivo_kicker" value="<?= h((string) ($payload['header_aditivo_kicker'] ?? 'PROPOSTA')) ?>">
        </label>
        <label class="field">
          <span>T&iacute;tulo principal do aditivo</span>
          <input type="text" name="header_aditivo_title" value="<?= h((string) ($payload['header_aditivo_title'] ?? 'ADITIVO DA PROPOSTA')) ?>">
        </label>
      </div>
    </div>
    <input type="hidden" name="header_custom_media_enabled" value="0">
    <label class="checkbox-inline payment-toggle">
      <input type="checkbox" name="header_custom_media_enabled" value="1" <?= proposal_flag_enabled($payload['header_custom_media_enabled'] ?? false) ? 'checked' : '' ?> data-header-media-toggle>
        Usar imagem ou v&iacute;deo customizado no banner principal
    </label>
    <div class="toggle-config-block" data-header-media-fields>
      <label class="field">
        <span>Link da m&iacute;dia</span>
        <input type="text" name="header_custom_media_url" value="<?= h((string) ($payload['header_custom_media_url'] ?? '')) ?>" placeholder="https://... ou /assets/...">
      </label>
      <p class="muted">Recomendado: 1920x1080 ou maior. V&iacute;deos em MP4/WebM e imagens em JPG/PNG/WebP. A m&iacute;dia sempre ser&aacute; exibida em cover, com overlay leve por cima.</p>
    </div>
  </section>
  <?php endif; ?>

  <?php if ($isModelEditor): ?>
    <section class="panel">
      <h2>Dados do modelo</h2>
      <div class="grid cols-3">
        <label class="field col-span-2"><span>Nome do modelo</span><input type="text" name="model_name" value="<?= h($modelNameValue) ?>" placeholder="Ex.: Loja de shopping - Elétrica e Especiais" required></label>
        <label class="field"><span>Descrição</span><input type="text" name="model_description" value="<?= h($modelDescriptionValue) ?>" placeholder="Ex.: Base para propostas de shopping"></label>
      </div>
    </section>
  <?php endif; ?>

  <section class="form-grid">
    <article class="panel">
      <h2><?= $isModelEditor ? 'Dados base da proposta' : 'Dados gerais' ?></h2>
      <div class="grid cols-3">
        <label class="field"><span>Código base</span><input type="text" name="codigo_base" value="<?= h((string) $payload['codigo_base']) ?>" <?= $isModelEditor ? '' : 'required' ?>></label>
        <label class="field"><span>Revisão</span><input type="text" name="revisao" value="<?= h((string) $payload['revisao']) ?>" required></label>
        <label class="field"><span>Data da proposta</span><input type="date" name="data_proposta" value="<?= h((string) $payload['data_proposta']) ?>"></label>
      </div>
      <label class="field"><span>Título</span><input type="text" name="titulo" value="<?= h((string) $payload['titulo']) ?>"></label>
    </article>

    <article class="panel">
      <h2>Cliente</h2>
      <div class="grid cols-2">
        <label class="field"><span>Nome responsável</span><input type="text" name="cliente_nome" value="<?= h((string) $payload['cliente_nome']) ?>"></label>
        <label class="field"><span>Empresa</span><input type="text" name="cliente_empresa" value="<?= h((string) $payload['cliente_empresa']) ?>"></label>
      </div>
      <div class="grid cols-4">
        <label class="field"><span>CNPJ</span><input type="text" name="cliente_cnpj" value="<?= h((string) $payload['cliente_cnpj']) ?>"></label>
        <label class="field"><span>E-mail</span><input type="email" name="cliente_email" value="<?= h((string) $payload['cliente_email']) ?>"></label>
        <label class="field"><span>Telefone</span><input type="text" name="cliente_telefone" value="<?= h((string) $payload['cliente_telefone']) ?>"></label>
        <label class="field"><span>CEP</span><input type="text" name="cliente_cep" value="<?= h((string) $payload['cliente_cep']) ?>"></label>
      </div>
      <div class="grid cols-3">
        <label class="field col-span-2"><span>Endereço</span><input type="text" name="cliente_endereco" value="<?= h((string) $payload['cliente_endereco']) ?>"></label>
        <label class="field"><span>Cidade</span><input type="text" name="cliente_cidade" value="<?= h((string) $payload['cliente_cidade']) ?>"></label>
      </div>
      <div class="grid cols-4">
        <label class="field"><span>UF</span><input type="text" name="cliente_uf" maxlength="2" value="<?= h((string) $payload['cliente_uf']) ?>"></label>
      </div>
    </article>

    <article class="panel">
      <h2>Dados da obra</h2>
      <div class="grid cols-2">
        <label class="field"><span>Nome da obra</span><input type="text" name="obra_nome" value="<?= h((string) $payload['obra_nome']) ?>"></label>
        <label class="field"><span>Endereço da obra</span><input type="text" name="obra_endereco" value="<?= h((string) $payload['obra_endereco']) ?>"></label>
      </div>
      <div class="grid cols-4">
        <label class="field"><span>Cidade</span><input type="text" name="obra_cidade" value="<?= h((string) $payload['obra_cidade']) ?>"></label>
        <label class="field"><span>UF</span><input type="text" name="obra_uf" maxlength="2" value="<?= h((string) $payload['obra_uf']) ?>"></label>
        <label class="field"><span>Prazo (dias)</span><input type="number" name="prazo_dias" min="1" max="365" value="<?= (int) $payload['prazo_dias'] ?>"></label>
        <label class="field"><span>Validade (dias)</span><input type="number" name="validade_dias" min="1" max="365" value="<?= (int) $payload['validade_dias'] ?>"></label>
      </div>
      <label class="field"><span>Título do texto introdutório</span><input type="text" name="intro_title" value="<?= h((string) ($payload['intro_title'] ?? '')) ?>"></label>
    </article>
  </section>
  <section class="panel">
    <h2>Escopo e investimento por disciplina</h2>
    <p class="muted">O título de cada disciplina será usado no escopo e também na parte de valores.</p>
    <div class="discipline-grid">
      <?php foreach ($catalog as $key => $info): ?>
        <?php $checked = in_array($key, $payload['disciplinas'], true); ?>
        <label class="discipline-card">
          <input type="checkbox" name="disciplinas[]" value="<?= h($key) ?>" <?= $checked ? 'checked' : '' ?> data-discipline-check>
          <div>
            <strong><?= h((string) ($scopeItems[$key]['title'] ?? $info['nome'])) ?></strong>
            <small><?= h((string) ($scopeItems[$key]['summary'] ?? $info['descricao'])) ?></small>
          </div>
          <input type="text" name="valores[<?= h($key) ?>]" value="<?= h((string) number_format((float) ($payload['valores'][$key] ?? 0), 2, ',', '.')) ?>" data-money-input>
        </label>
      <?php endforeach; ?>
    </div>

    <?php foreach ($catalog as $key => $info): ?>
      <?php $item = $scopeItems[$key] ?? ['title' => $info['nome'], 'subtitle' => '', 'summary' => $info['descricao'], 'topics' => []]; ?>
      <div class="panel panel-inner stack-sm" style="margin-top:12px;">
        <h3><?= h((string) $info['nome']) ?></h3>
        <div class="grid cols-2">
          <label class="field"><span>Título</span><input type="text" name="scope_items[<?= h($key) ?>][title]" value="<?= h((string) ($item['title'] ?? '')) ?>"></label>
          <label class="field"><span>Subtítulo</span><input type="text" name="scope_items[<?= h($key) ?>][subtitle]" value="<?= h((string) ($item['subtitle'] ?? '')) ?>"></label>
        </div>
        <label class="field"><span>Resumo curto</span><input type="text" name="scope_items[<?= h($key) ?>][summary]" value="<?= h((string) ($item['summary'] ?? '')) ?>"></label>
        <label class="field"><span>Tópicos do escopo (1 por linha)</span><textarea name="scope_items[<?= h($key) ?>][topics]" rows="5"><?= h(implode("\n", is_array($item['topics'] ?? null) ? $item['topics'] : [])) ?></textarea></label>
      </div>
    <?php endforeach; ?>

    <div class="panel-subhead">
      <h3>Disciplinas customizadas</h3>
      <button class="btn btn-ghost btn-sm" type="button" id="add-custom-discipline-button">+ Adicionar disciplina</button>
    </div>
    <div class="stack-sm" id="custom-disciplines-list" data-next-index="<?= count($customDisciplines) ?>">
      <?php foreach ($customDisciplines as $index => $discipline): ?>
        <div class="repeater-row custom-discipline-row">
          <div class="row-actions">
            <label class="checkbox-inline"><input type="checkbox" name="disciplinas_custom[<?= (int) $index ?>][ativa]" value="1" <?= !empty($discipline['ativa']) ? 'checked' : '' ?> data-custom-discipline-active> Ativa</label>
            <button class="btn btn-ghost btn-sm repeater-remove" type="button">Remover</button>
          </div>
          <input type="hidden" name="disciplinas_custom[<?= (int) $index ?>][key]" value="<?= h((string) ($discipline['key'] ?? '')) ?>">
          <div class="grid cols-3">
            <label class="field"><span>Nome</span><input type="text" name="disciplinas_custom[<?= (int) $index ?>][nome]" value="<?= h((string) ($discipline['nome'] ?? '')) ?>"></label>
            <label class="field"><span>Valor</span><input type="text" name="disciplinas_custom[<?= (int) $index ?>][valor]" value="<?= h((string) number_format((float) ($discipline['valor'] ?? 0), 2, ',', '.')) ?>" data-money-input></label>
          <label class="field"><span>&Iacute;cone atual (URL)</span><input type="text" name="disciplinas_custom[<?= (int) $index ?>][icone]" value="<?= h((string) ($discipline['icone'] ?? '')) ?>" placeholder="/uploads/disciplinas/exemplo.png"></label>
          </div>
          <div class="grid cols-2">
            <label class="field"><span>Subtítulo</span><input type="text" name="disciplinas_custom[<?= (int) $index ?>][subtitle]" value="<?= h((string) ($discipline['subtitle'] ?? '')) ?>"></label>
            <label class="field"><span>Resumo curto</span><input type="text" name="disciplinas_custom[<?= (int) $index ?>][descricao]" value="<?= h((string) ($discipline['descricao'] ?? '')) ?>"></label>
          </div>
          <div class="grid cols-2">
            <label class="field"><span>Tópicos do escopo (1 por linha)</span><textarea name="disciplinas_custom[<?= (int) $index ?>][topics]" rows="4"><?= h(implode("\n", is_array($discipline['topics'] ?? null) ? $discipline['topics'] : [])) ?></textarea></label>
          <label class="field"><span>Enviar &iacute;cone</span><input type="file" name="disciplinas_custom_icone[<?= (int) $index ?>]" accept=".png,.jpg,.jpeg,.webp,.svg"></label>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
    <template id="custom-discipline-template">
      <div class="repeater-row custom-discipline-row">
        <div class="row-actions">
          <label class="checkbox-inline"><input type="checkbox" name="disciplinas_custom[__INDEX__][ativa]" value="1" data-custom-discipline-active> Ativa</label>
          <button class="btn btn-ghost btn-sm repeater-remove" type="button">Remover</button>
        </div>
        <input type="hidden" name="disciplinas_custom[__INDEX__][key]" value="">
        <div class="grid cols-3">
          <label class="field"><span>Nome</span><input type="text" name="disciplinas_custom[__INDEX__][nome]" value=""></label>
          <label class="field"><span>Valor</span><input type="text" name="disciplinas_custom[__INDEX__][valor]" value="0,00" data-money-input></label>
          <label class="field"><span>&Iacute;cone atual (URL)</span><input type="text" name="disciplinas_custom[__INDEX__][icone]" value="" placeholder="/uploads/disciplinas/exemplo.png"></label>
        </div>
        <div class="grid cols-2">
          <label class="field"><span>Subtítulo</span><input type="text" name="disciplinas_custom[__INDEX__][subtitle]" value=""></label>
          <label class="field"><span>Resumo curto</span><input type="text" name="disciplinas_custom[__INDEX__][descricao]" value=""></label>
        </div>
        <div class="grid cols-2">
          <label class="field"><span>Tópicos do escopo (1 por linha)</span><textarea name="disciplinas_custom[__INDEX__][topics]" rows="4"></textarea></label>
          <label class="field"><span>Enviar &iacute;cone</span><input type="file" name="disciplinas_custom_icone[__INDEX__]" accept=".png,.jpg,.jpeg,.webp,.svg"></label>
        </div>
      </div>
    </template>

    <div class="total-box">
      <h3>Total da proposta</h3>
      <strong id="proposal-total"><?= brl($total) ?></strong>
      <small id="proposal-total-words"><?= h($totalExtenso) ?></small>
    </div>
  </section>

  <section class="panel">
    <h2>Diretrizes gerais</h2>
    <div class="grid cols-2">
      <label class="field"><span>Título da seção</span><input type="text" name="guidelines_title" value="<?= h((string) ($payload['guidelines_title'] ?? '')) ?>"></label>
      <label class="field"><span>Subtítulo da seção</span><input type="text" name="guidelines_subtitle" value="<?= h((string) ($payload['guidelines_subtitle'] ?? '')) ?>"></label>
    </div>
    <div class="panel-subhead">
      <button class="btn btn-ghost btn-sm" type="button" id="add-guideline-row-button">+ Adicionar diretriz</button>
    </div>
    <div class="stack-sm" id="guidelines-list" data-next-index="<?= count($guidelineRows) ?>">
      <?php foreach ($guidelineRows as $index => $guideline): ?>
        <div class="repeater-row">
          <div class="row-actions"><button class="btn btn-ghost btn-sm repeater-remove" type="button">Remover</button></div>
          <div class="grid cols-2">
            <label class="field"><span>Título</span><input type="text" name="guidelines_items[<?= (int) $index ?>][title]" value="<?= h((string) ($guideline['title'] ?? '')) ?>"></label>
            <label class="field"><span>&Iacute;cone (URL opcional)</span><input type="text" name="guidelines_items[<?= (int) $index ?>][icon]" value="<?= h((string) ($guideline['icon'] ?? '')) ?>"></label>
          </div>
          <label class="field"><span>Conteúdo</span><textarea name="guidelines_items[<?= (int) $index ?>][content]" rows="3"><?= h((string) ($guideline['content'] ?? '')) ?></textarea></label>
        </div>
      <?php endforeach; ?>
    </div>
    <template id="guideline-row-template">
      <div class="repeater-row">
        <div class="row-actions"><button class="btn btn-ghost btn-sm repeater-remove" type="button">Remover</button></div>
        <div class="grid cols-2">
          <label class="field"><span>Título</span><input type="text" name="guidelines_items[__INDEX__][title]" value=""></label>
          <label class="field"><span>&Iacute;cone (URL opcional)</span><input type="text" name="guidelines_items[__INDEX__][icon]" value=""></label>
        </div>
        <label class="field"><span>Conteúdo</span><textarea name="guidelines_items[__INDEX__][content]" rows="3"></textarea></label>
      </div>
    </template>
  </section>
  <section class="panel">
    <h2>Arquivos recebidos</h2>
    <div class="grid cols-3">
      <label class="field"><span>Label da seção</span><input type="text" name="files_section_label" value="<?= h((string) ($payload['files_section_label'] ?? '')) ?>"></label>
      <label class="field"><span>Título da seção</span><input type="text" name="files_section_title" value="<?= h((string) ($payload['files_section_title'] ?? '')) ?>"></label>
      <label class="field"><span>Texto introdutório</span><input type="text" name="files_section_subtitle" value="<?= h((string) ($payload['files_section_subtitle'] ?? '')) ?>"></label>
    </div>
    <div class="panel-subhead">
      <button class="btn btn-ghost btn-sm" type="button" id="add-file-row-button">+ Adicionar arquivo</button>
    </div>
    <div class="stack-sm" id="files-list" data-next-index="<?= count($fileRows) ?>">
      <?php foreach ($fileRows as $index => $arquivo): ?>
        <div class="repeater-row">
          <div class="row-actions"><button class="btn btn-ghost btn-sm repeater-remove" type="button">Remover</button></div>
          <div class="grid cols-4">
            <label class="field"><span>Item</span><input type="text" name="arquivos[<?= (int) $index ?>][item]" value="<?= h((string) ($arquivo['item'] ?? '')) ?>"></label>
            <label class="field"><span>Nome</span><input type="text" name="arquivos[<?= (int) $index ?>][nome]" value="<?= h((string) ($arquivo['nome'] ?? '')) ?>"></label>
            <label class="field"><span>Rev.</span><input type="text" name="arquivos[<?= (int) $index ?>][rev]" value="<?= h((string) ($arquivo['rev'] ?? '')) ?>"></label>
            <label class="field"><span>Data</span><input type="text" name="arquivos[<?= (int) $index ?>][data]" value="<?= h((string) ($arquivo['data'] ?? '')) ?>" placeholder="12/03/2026"></label>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
    <template id="file-row-template">
      <div class="repeater-row">
        <div class="row-actions"><button class="btn btn-ghost btn-sm repeater-remove" type="button">Remover</button></div>
        <div class="grid cols-4">
          <label class="field"><span>Item</span><input type="text" name="arquivos[__INDEX__][item]" value=""></label>
          <label class="field"><span>Nome</span><input type="text" name="arquivos[__INDEX__][nome]" value=""></label>
          <label class="field"><span>Rev.</span><input type="text" name="arquivos[__INDEX__][rev]" value=""></label>
          <label class="field"><span>Data</span><input type="text" name="arquivos[__INDEX__][data]" value="" placeholder="12/03/2026"></label>
        </div>
      </div>
    </template>
  </section>

  <section class="panel">
    <h2>Etapas e prazos</h2>
    <div class="panel-subhead">
      <button class="btn btn-ghost btn-sm" type="button" id="add-stage-row-button">+ Adicionar etapa</button>
    </div>
    <div class="stack-sm" id="stages-list" data-next-index="<?= count($stageRows) ?>">
      <?php foreach ($stageRows as $index => $etapa): ?>
        <div class="repeater-row">
          <div class="row-actions"><button class="btn btn-ghost btn-sm repeater-remove" type="button">Remover</button></div>
          <div class="grid cols-3">
            <label class="field"><span>Etapa</span><input type="text" name="etapas[<?= (int) $index ?>][nome]" value="<?= h((string) ($etapa['nome'] ?? '')) ?>"></label>
            <label class="field"><span>Prazo</span><input type="text" name="etapas[<?= (int) $index ?>][prazo]" value="<?= h((string) ($etapa['prazo'] ?? '')) ?>"></label>
            <label class="field"><span>Descrição</span><input type="text" name="etapas[<?= (int) $index ?>][descricao]" value="<?= h((string) ($etapa['descricao'] ?? '')) ?>"></label>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
    <template id="stage-row-template">
      <div class="repeater-row">
        <div class="row-actions"><button class="btn btn-ghost btn-sm repeater-remove" type="button">Remover</button></div>
        <div class="grid cols-3">
          <label class="field"><span>Etapa</span><input type="text" name="etapas[__INDEX__][nome]" value=""></label>
          <label class="field"><span>Prazo</span><input type="text" name="etapas[__INDEX__][prazo]" value=""></label>
          <label class="field"><span>Descrição</span><input type="text" name="etapas[__INDEX__][descricao]" value=""></label>
        </div>
      </div>
    </template>
  </section>

  <section class="panel">
    <h2>Forma de pagamento</h2>
    <p class="muted">Defina as formas dispon&iacute;veis para esta proposta e monte as parcelas manuais somente quando precisar desse detalhamento.</p>

    <input type="hidden" name="payment_schedule_manual_enabled" value="0">
    <label class="checkbox-inline payment-toggle">
      <input type="checkbox" name="payment_schedule_manual_enabled" value="1" <?= $manualPaymentEnabled ? 'checked' : '' ?> data-payment-manual-toggle>
      Usar forma de pagamento manual
    </label>

    <div class="proposal-subpanel <?= $manualPaymentEnabled ? '' : 'is-disabled' ?>" data-payment-manual-fields aria-disabled="<?= $manualPaymentEnabled ? 'false' : 'true' ?>">
      <div class="proposal-subpanel-head">
        <div>
          <h3>Forma de pagamento manual</h3>
          <p class="muted">Monte as parcelas manualmente. Linhas com valor entram na valida&ccedil;&atilde;o da soma; subt&iacute;tulos servem s&oacute; para organizar o bloco.</p>
        </div>
        <button class="btn btn-ghost btn-sm" type="button" id="add-payment-row-button" <?= $manualPaymentEnabled ? '' : 'disabled' ?>>+ Adicionar linha</button>
      </div>

      <div class="stack-sm" id="payment-schedule-list" data-next-index="<?= count($paymentRows) ?>">
        <?php foreach ($paymentRows as $index => $paymentRow): ?>
          <?php $paymentType = (($paymentRow['type'] ?? 'line') === 'subtitle') ? 'subtitle' : 'line'; ?>
          <div class="repeater-row payment-row">
            <div class="row-actions"><button class="btn btn-ghost btn-sm repeater-remove" type="button">Remover</button></div>
            <div class="grid cols-3">
              <label class="field">
                <span>Tipo</span>
                <select name="payment_schedule_rows[<?= (int) $index ?>][type]" data-payment-row-type <?= $manualPaymentEnabled ? '' : 'disabled' ?>>
                  <option value="line" <?= $paymentType === 'line' ? 'selected' : '' ?>>Linha com valor</option>
                  <option value="subtitle" <?= $paymentType === 'subtitle' ? 'selected' : '' ?>>Subt&iacute;tulo</option>
                </select>
              </label>
              <label class="field col-span-2"><span>Texto</span><input type="text" name="payment_schedule_rows[<?= (int) $index ?>][label]" value="<?= h((string) ($paymentRow['label'] ?? '')) ?>" <?= $manualPaymentEnabled ? '' : 'disabled' ?>></label>
            </div>
            <div class="grid cols-3">
              <label class="field payment-row-amount-field">
                <span>Valor</span>
                <input type="text" name="payment_schedule_rows[<?= (int) $index ?>][amount]" value="<?= h(number_format((float) ($paymentRow['amount'] ?? 0), 2, ',', '.')) ?>" data-money-input data-payment-row-amount <?= $manualPaymentEnabled ? '' : 'disabled' ?>>
              </label>
            </div>
          </div>
        <?php endforeach; ?>
      </div>

      <template id="payment-row-template">
        <div class="repeater-row payment-row">
          <div class="row-actions"><button class="btn btn-ghost btn-sm repeater-remove" type="button">Remover</button></div>
          <div class="grid cols-3">
            <label class="field">
              <span>Tipo</span>
              <select name="payment_schedule_rows[__INDEX__][type]" data-payment-row-type>
                <option value="line">Linha com valor</option>
                <option value="subtitle">Subt&iacute;tulo</option>
              </select>
            </label>
            <label class="field col-span-2"><span>Texto</span><input type="text" name="payment_schedule_rows[__INDEX__][label]" value=""></label>
          </div>
          <div class="grid cols-3">
            <label class="field payment-row-amount-field">
              <span>Valor</span>
              <input type="text" name="payment_schedule_rows[__INDEX__][amount]" value="0,00" data-money-input data-payment-row-amount>
            </label>
          </div>
        </div>
      </template>

      <div class="payment-summary-strip" id="payment-schedule-summary">
        <span>Soma atual da forma de pagamento: <strong data-payment-schedule-total>R$ 0,00</strong></span>
        <span>Valor total da proposta: <strong data-payment-proposal-total><?= brl($total) ?></strong></span>
      </div>
    </div>

    <div class="payment-toggle-grid">
      <input type="hidden" name="pagamento_cartao_ativo" value="0">
      <label class="checkbox-inline payment-toggle">
        <input type="checkbox" name="pagamento_cartao_ativo" value="1" <?= proposal_flag_enabled($payload['pagamento_cartao_ativo'] ?? false) ? 'checked' : '' ?> data-payment-toggle="cartao">
        Habilitar cart&atilde;o
      </label>
      <input type="hidden" name="pagamento_boleto_ativo" value="0">
      <label class="checkbox-inline payment-toggle">
        <input type="checkbox" name="pagamento_boleto_ativo" value="1" <?= proposal_flag_enabled($payload['pagamento_boleto_ativo'] ?? false) ? 'checked' : '' ?> data-payment-toggle="boleto">
        Habilitar boleto
      </label>
    </div>

    <div class="payment-method-panels">
      <div class="payment-config-block <?= $cardPaymentEnabled ? '' : 'is-disabled' ?>" data-payment-fields="cartao" aria-disabled="<?= $cardPaymentEnabled ? 'false' : 'true' ?>">
        <div class="proposal-subpanel-head proposal-subpanel-head-sm">
          <div>
            <h3>Cart&atilde;o</h3>
            <p class="muted">Personalize os textos e o link do pagamento por cart&atilde;o.</p>
          </div>
        </div>
        <div class="grid cols-2">
          <label class="field"><span>T&iacute;tulo (cart&atilde;o)</span><input type="text" name="pagamento_cartao_titulo" value="<?= h((string) ($payload['pagamento_cartao_titulo'] ?? '')) ?>" <?= $cardPaymentEnabled ? '' : 'disabled' ?>></label>
          <label class="field"><span>Bot&atilde;o (cart&atilde;o)</span><input type="text" name="pagamento_cartao_botao" value="<?= h((string) ($payload['pagamento_cartao_botao'] ?? '')) ?>" <?= $cardPaymentEnabled ? '' : 'disabled' ?>></label>
        </div>
        <label class="field"><span>Descri&ccedil;&atilde;o (cart&atilde;o)</span><input type="text" name="pagamento_cartao_descricao" value="<?= h((string) ($payload['pagamento_cartao_descricao'] ?? '')) ?>" <?= $cardPaymentEnabled ? '' : 'disabled' ?>></label>
        <label class="field"><span>Link do cart&atilde;o</span><input type="url" name="pagamento_cartao_link" value="<?= h((string) ($payload['pagamento_cartao_link'] ?? '')) ?>" placeholder="https://..." <?= $cardPaymentEnabled ? '' : 'disabled' ?>></label>
      </div>

      <div class="payment-config-block <?= $boletoPaymentEnabled ? '' : 'is-disabled' ?>" data-payment-fields="boleto" aria-disabled="<?= $boletoPaymentEnabled ? 'false' : 'true' ?>">
        <div class="proposal-subpanel-head proposal-subpanel-head-sm">
          <div>
            <h3>Boleto</h3>
            <p class="muted">Personalize os textos e o link do boleto quando essa op&ccedil;&atilde;o estiver ativa.</p>
          </div>
        </div>
        <div class="grid cols-2">
          <label class="field"><span>T&iacute;tulo (boleto)</span><input type="text" name="pagamento_boleto_titulo" value="<?= h((string) ($payload['pagamento_boleto_titulo'] ?? '')) ?>" <?= $boletoPaymentEnabled ? '' : 'disabled' ?>></label>
          <label class="field"><span>Bot&atilde;o (boleto)</span><input type="text" name="pagamento_boleto_botao" value="<?= h((string) ($payload['pagamento_boleto_botao'] ?? '')) ?>" <?= $boletoPaymentEnabled ? '' : 'disabled' ?>></label>
        </div>
        <label class="field"><span>Descri&ccedil;&atilde;o (boleto)</span><input type="text" name="pagamento_boleto_descricao" value="<?= h((string) ($payload['pagamento_boleto_descricao'] ?? '')) ?>" <?= $boletoPaymentEnabled ? '' : 'disabled' ?>></label>
        <label class="field"><span>Link do boleto</span><input type="url" name="pagamento_boleto_link" value="<?= h((string) ($payload['pagamento_boleto_link'] ?? '')) ?>" placeholder="https://..." <?= $boletoPaymentEnabled ? '' : 'disabled' ?>></label>
      </div>
    </div>
  </section>

  <section class="panel">
    <h2>Aceite e assinatura</h2>
    <p class="muted">Defina se o aceite vai usar o contrato ou o resumo da proposta e personalize o conte&uacute;do exibido ao cliente.</p>

    <input type="hidden" name="acceptance_mode" value="contract">
    <label class="checkbox-inline payment-toggle">
      <input type="checkbox" name="acceptance_mode" value="summary" <?= (($payload['acceptance_mode'] ?? 'contract') === 'summary') ? 'checked' : '' ?> data-acceptance-mode-toggle>
      Usar resumo da proposta no aceite e na assinatura [RESUMO]
    </label>

    <div class="toggle-config-block" data-acceptance-fields="contract">
      <label class="field"><span>T&iacute;tulo do contrato</span><input type="text" name="accept_terms_title" value="<?= h((string) ($payload['accept_terms_title'] ?? '')) ?>" placeholder="Se vazio, usa o t&iacute;tulo geral das configura&ccedil;&otilde;es"></label>
      <label class="field field-top"><span>Termos do contrato por proposta (HTML opcional)</span><textarea name="accept_terms_html" rows="12" placeholder="Se este campo ficar vazio, o sistema usa automaticamente o contrato geral definido em Configura&ccedil;&otilde;es."><?= h((string) ($payload['accept_terms_html'] ?? '')) ?></textarea></label>
      <label class="field"><span>Texto do checkbox</span><input type="text" name="accept_terms_checkbox_text" value="<?= h((string) ($payload['accept_terms_checkbox_text'] ?? '')) ?>" placeholder="Li e concordo com os termos deste contrato."></label>
    </div>

    <div class="toggle-config-block" data-acceptance-fields="summary">
      <label class="field field-top">
        <span>Resumo complementar do aceite (HTML opcional) [RESUMO]</span>
        <textarea name="accept_summary_html" rows="10" placeholder="Se este campo ficar vazio, o sistema usa automaticamente o resumo gerado pela proposta."><?= h((string) ($payload['accept_summary_html'] ?? '')) ?></textarea>
      </label>
      <p class="muted">Quando essa op&ccedil;&atilde;o estiver ativa, o modal de aceite e o documento enviado ao ZapSign usar&atilde;o o resumo da proposta em vez do contrato.</p>
    </div>

    <details class="settings-spoiler">
      <summary>Ver vari&aacute;veis dispon&iacute;veis</summary>
      <div class="settings-vars">
        <p class="muted">Use no texto com o formato <code>{{VARIAVEL}}</code>.</p>
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

  <section class="panel">
    <h2>Considera&ccedil;&otilde;es e exclus&otilde;es</h2>
    <div class="grid cols-2">
      <label class="field"><span>Considera&ccedil;&otilde;es importantes (1 por linha)</span><textarea name="consideracoes" rows="8"><?= h(implode("\n", $payload['consideracoes'] ?? [])) ?></textarea></label>
      <label class="field"><span>Itens fora do escopo (1 por linha)</span><textarea name="exclusoes" rows="8"><?= h(implode("\n", $payload['exclusoes'] ?? [])) ?></textarea></label>
    </div>
    <label class="field"><span>Link manual de assinatura ZapSign (opcional)</span><input type="url" name="zapsign_sign_url" value="<?= h((string) ($payload['zapsign_sign_url'] ?? '')) ?>" placeholder="https://..."></label>
  </section>

  <?php if (!$isModelEditor): ?>
  <section class="panel">
    <h2>Salvar como modelo</h2>
    <div class="grid cols-3">
      <label class="field">
        <span>Atualizar modelo existente</span>
        <select name="model_record_id">
          <option value="">Nenhum</option>
          <?php foreach ($models as $model): ?>
            <option value="<?= (int) $model['id'] ?>" <?= ((int) ($currentModel['id'] ?? 0) === (int) $model['id']) ? 'selected' : '' ?>><?= h((string) ($model['name'] ?? 'Modelo')) ?></option>
          <?php endforeach; ?>
        </select>
      </label>
      <label class="field"><span>Nome do modelo</span><input type="text" name="model_name" value="<?= h((string) ($currentModel['name'] ?? '')) ?>" placeholder="Ex.: Residência padrão"></label>
      <label class="field"><span>Descrição do modelo</span><input type="text" name="model_description" value="<?= h((string) ($currentModel['description'] ?? '')) ?>" placeholder="Ex.: Elétrica, hidráulica e gás"></label>
    </div>
  </section>
  <?php endif; ?>

  <section class="inline-actions sticky-actions">
    <?php if ($isModelEditor): ?>
      <button type="submit" class="btn btn-primary">Salvar modelo</button>
    <?php else: ?>
      <button type="submit" name="save_mode" value="edit" class="btn btn-primary">Salvar proposta</button>
      <button type="submit" name="save_mode" value="preview" class="btn btn-ghost">Salvar e ver preview</button>
      <button type="submit" name="save_mode" value="model_create" class="btn btn-ghost">Salvar como modelo</button>
      <button type="submit" name="save_mode" value="model_update" class="btn btn-ghost">Atualizar modelo</button>
    <?php endif; ?>
  </section>
</form>

