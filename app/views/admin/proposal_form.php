<?php
$isEdit = $proposal !== null;
$total = proposal_total($payload);
$totalExtenso = currency_to_words_ptbr($total);
$publicUrl = $isEdit ? proposal_public_url($proposal) : null;
$customDisciplines = $payload['disciplinas_custom'] ?? [];
if (!is_array($customDisciplines)) {
    $customDisciplines = [];
}
$fileRows = $payload['arquivos'] ?? [];
if (!is_array($fileRows) || $fileRows === []) {
    $fileRows = [['item' => '', 'nome' => '', 'rev' => '']];
}
$stageRows = $payload['etapas'] ?? [];
if (!is_array($stageRows) || $stageRows === []) {
    $stageRows = [['nome' => '', 'percentual' => 0, 'descricao' => '']];
}
?>
<section class="panel-head">
  <div>
    <h1><?= $isEdit ? 'Editar proposta' : 'Nova proposta' ?></h1>
    <p>Fluxo completo: rascunho, preview, publicacao, tracking e assinatura.</p>
  </div>
  <div class="inline-actions">
    <a class="btn btn-ghost" href="/admin/proposals">Voltar</a>
    <?php if ($isEdit): ?>
      <a class="btn btn-ghost" href="/admin/proposals/<?= (int) $proposal['id'] ?>/preview" target="_blank">Abrir preview</a>
      <?php if ($publicUrl): ?>
        <a class="btn btn-primary" href="<?= h($publicUrl) ?>" target="_blank">Abrir publica</a>
      <?php endif; ?>
    <?php endif; ?>
  </div>
</section>

<?php if ($isEdit): ?>
  <section class="panel compact">
    <div class="inline-info">
      <span><strong>Status:</strong> <?= h(status_label((string) $proposal['status'])) ?></span>
      <span><strong>Token:</strong> <?= h((string) ($proposal['token'] ?: 'nao publicado')) ?></span>
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
    </div>
  </section>
<?php endif; ?>

<form method="post" action="/admin/proposals/save" class="stack-lg" id="proposal-form" enctype="multipart/form-data">
  <?= csrf_field() ?>
  <?php if ($isEdit): ?>
    <input type="hidden" name="id" value="<?= (int) $proposal['id'] ?>">
  <?php endif; ?>

  <section class="form-grid">
    <article class="panel">
      <h2>Dados gerais</h2>
      <div class="grid cols-3">
        <label class="field">
          <span>Codigo base</span>
          <input type="text" name="codigo_base" value="<?= h((string) $payload['codigo_base']) ?>" required>
        </label>
        <label class="field">
          <span>Revisao</span>
          <input type="text" name="revisao" value="<?= h((string) $payload['revisao']) ?>" required>
        </label>
        <label class="field">
          <span>Data da proposta</span>
          <input type="date" name="data_proposta" value="<?= h((string) $payload['data_proposta']) ?>">
        </label>
      </div>
      <label class="field">
        <span>Titulo</span>
        <input type="text" name="titulo" value="<?= h((string) $payload['titulo']) ?>">
      </label>
    </article>

    <article class="panel">
      <h2>Cliente</h2>
      <div class="grid cols-2">
        <label class="field">
          <span>Nome responsavel</span>
          <input type="text" name="cliente_nome" value="<?= h((string) $payload['cliente_nome']) ?>">
        </label>
        <label class="field">
          <span>Empresa</span>
          <input type="text" name="cliente_empresa" value="<?= h((string) $payload['cliente_empresa']) ?>">
        </label>
      </div>
      <div class="grid cols-4">
        <label class="field">
          <span>CNPJ</span>
          <input type="text" name="cliente_cnpj" value="<?= h((string) $payload['cliente_cnpj']) ?>">
        </label>
        <label class="field">
          <span>E-mail</span>
          <input type="email" name="cliente_email" value="<?= h((string) $payload['cliente_email']) ?>">
        </label>
        <label class="field">
          <span>Telefone</span>
          <input type="text" name="cliente_telefone" value="<?= h((string) $payload['cliente_telefone']) ?>">
        </label>
        <label class="field">
          <span>CEP</span>
          <input type="text" name="cliente_cep" value="<?= h((string) $payload['cliente_cep']) ?>">
        </label>
      </div>
      <div class="grid cols-3">
        <label class="field col-span-2">
          <span>Endereco</span>
          <input type="text" name="cliente_endereco" value="<?= h((string) $payload['cliente_endereco']) ?>">
        </label>
        <label class="field">
          <span>Cidade</span>
          <input type="text" name="cliente_cidade" value="<?= h((string) $payload['cliente_cidade']) ?>">
        </label>
      </div>
      <div class="grid cols-4">
        <label class="field">
          <span>UF</span>
          <input type="text" name="cliente_uf" maxlength="2" value="<?= h((string) $payload['cliente_uf']) ?>">
        </label>
      </div>
    </article>

    <article class="panel">
      <h2>Dados da obra</h2>
      <div class="grid cols-2">
        <label class="field">
          <span>Nome da obra</span>
          <input type="text" name="obra_nome" value="<?= h((string) $payload['obra_nome']) ?>">
        </label>
        <label class="field">
          <span>Endereco da obra</span>
          <input type="text" name="obra_endereco" value="<?= h((string) $payload['obra_endereco']) ?>">
        </label>
      </div>
      <div class="grid cols-4">
        <label class="field">
          <span>Cidade</span>
          <input type="text" name="obra_cidade" value="<?= h((string) $payload['obra_cidade']) ?>">
        </label>
        <label class="field">
          <span>UF</span>
          <input type="text" name="obra_uf" maxlength="2" value="<?= h((string) $payload['obra_uf']) ?>">
        </label>
        <label class="field">
          <span>Prazo (dias)</span>
          <input type="number" name="prazo_dias" min="1" max="365" value="<?= (int) $payload['prazo_dias'] ?>">
        </label>
        <label class="field">
          <span>Validade (dias)</span>
          <input type="number" name="validade_dias" min="1" max="365" value="<?= (int) $payload['validade_dias'] ?>">
        </label>
      </div>
      <label class="field">
        <span>Finalidade da obra</span>
        <textarea name="finalidade_obra" rows="2"><?= h((string) $payload['finalidade_obra']) ?></textarea>
      </label>
      <label class="field">
        <span>Descricao do objeto</span>
        <textarea name="descricao_objeto" rows="3"><?= h((string) $payload['descricao_objeto']) ?></textarea>
      </label>
    </article>
  </section>

  <section class="panel">
    <h2>Escopo e investimento por disciplina</h2>
    <p class="muted">Selecione as disciplinas base. O sistema remove automaticamente do template o que nao foi marcado.</p>
    <div class="discipline-grid">
      <?php foreach ($catalog as $key => $info): ?>
        <?php $checked = in_array($key, $payload['disciplinas'], true); ?>
        <label class="discipline-card">
          <input type="checkbox" name="disciplinas[]" value="<?= h($key) ?>" <?= $checked ? 'checked' : '' ?> data-discipline-check>
          <div>
            <strong><?= h((string) $info['nome']) ?></strong>
            <small><?= h((string) $info['descricao']) ?></small>
          </div>
          <input type="text" name="valores[<?= h($key) ?>]" value="<?= h((string) number_format((float) ($payload['valores'][$key] ?? 0), 2, ',', '.')) ?>" data-money-input>
        </label>
      <?php endforeach; ?>
    </div>

    <div class="panel-subhead">
      <h3>Disciplinas customizadas</h3>
      <button class="btn btn-ghost btn-sm" type="button" id="add-custom-discipline-button">+ Adicionar disciplina</button>
    </div>

    <div class="stack-sm" id="custom-disciplines-list" data-next-index="<?= count($customDisciplines) ?>">
      <?php foreach ($customDisciplines as $index => $discipline): ?>
        <div class="repeater-row custom-discipline-row">
          <div class="row-actions">
            <label class="checkbox-inline">
              <input type="checkbox" name="disciplinas_custom[<?= (int) $index ?>][ativa]" value="1" <?= !empty($discipline['ativa']) ? 'checked' : '' ?> data-custom-discipline-active>
              Ativa
            </label>
            <button class="btn btn-ghost btn-sm repeater-remove" type="button">Remover</button>
          </div>
          <input type="hidden" name="disciplinas_custom[<?= (int) $index ?>][key]" value="<?= h((string) ($discipline['key'] ?? '')) ?>">
          <div class="grid cols-3">
            <label class="field">
              <span>Nome</span>
              <input type="text" name="disciplinas_custom[<?= (int) $index ?>][nome]" value="<?= h((string) ($discipline['nome'] ?? '')) ?>">
            </label>
            <label class="field">
              <span>Valor</span>
              <input type="text" name="disciplinas_custom[<?= (int) $index ?>][valor]" value="<?= h((string) number_format((float) ($discipline['valor'] ?? 0), 2, ',', '.')) ?>" data-money-input>
            </label>
            <label class="field">
              <span>Icone atual (URL)</span>
              <input type="text" name="disciplinas_custom[<?= (int) $index ?>][icone]" value="<?= h((string) ($discipline['icone'] ?? '')) ?>" placeholder="/uploads/disciplinas/exemplo.png">
            </label>
          </div>
          <div class="grid cols-2">
            <label class="field">
              <span>Descricao do escopo</span>
              <input type="text" name="disciplinas_custom[<?= (int) $index ?>][descricao]" value="<?= h((string) ($discipline['descricao'] ?? '')) ?>">
            </label>
            <label class="field">
              <span>Enviar icone</span>
              <input type="file" name="disciplinas_custom_icone[<?= (int) $index ?>]" accept=".png,.jpg,.jpeg,.webp,.svg">
            </label>
          </div>
        </div>
      <?php endforeach; ?>
    </div>

    <template id="custom-discipline-template">
      <div class="repeater-row custom-discipline-row">
        <div class="row-actions">
          <label class="checkbox-inline">
            <input type="checkbox" name="disciplinas_custom[__INDEX__][ativa]" value="1" checked data-custom-discipline-active>
            Ativa
          </label>
          <button class="btn btn-ghost btn-sm repeater-remove" type="button">Remover</button>
        </div>
        <input type="hidden" name="disciplinas_custom[__INDEX__][key]" value="">
        <div class="grid cols-3">
          <label class="field">
            <span>Nome</span>
            <input type="text" name="disciplinas_custom[__INDEX__][nome]" value="">
          </label>
          <label class="field">
            <span>Valor</span>
            <input type="text" name="disciplinas_custom[__INDEX__][valor]" value="0,00" data-money-input>
          </label>
          <label class="field">
            <span>Icone atual (URL)</span>
            <input type="text" name="disciplinas_custom[__INDEX__][icone]" value="" placeholder="/uploads/disciplinas/exemplo.png">
          </label>
        </div>
        <div class="grid cols-2">
          <label class="field">
            <span>Descricao do escopo</span>
            <input type="text" name="disciplinas_custom[__INDEX__][descricao]" value="">
          </label>
          <label class="field">
            <span>Enviar icone</span>
            <input type="file" name="disciplinas_custom_icone[__INDEX__]" accept=".png,.jpg,.jpeg,.webp,.svg">
          </label>
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
    <h2>Arquivos recebidos</h2>
    <div class="panel-subhead">
      <p class="muted">Adicione quantos arquivos precisar.</p>
      <button class="btn btn-ghost btn-sm" type="button" id="add-file-row-button">+ Adicionar arquivo</button>
    </div>
    <div class="stack-sm" id="files-list" data-next-index="<?= count($fileRows) ?>">
      <?php foreach ($fileRows as $index => $arquivo): ?>
        <div class="repeater-row">
          <div class="row-actions">
            <button class="btn btn-ghost btn-sm repeater-remove" type="button">Remover</button>
          </div>
          <div class="grid cols-4">
            <label class="field">
              <span>Item</span>
              <input type="text" name="arquivos[<?= (int) $index ?>][item]" value="<?= h((string) ($arquivo['item'] ?? '')) ?>">
            </label>
            <label class="field">
              <span>Nome</span>
              <input type="text" name="arquivos[<?= (int) $index ?>][nome]" value="<?= h((string) ($arquivo['nome'] ?? '')) ?>">
            </label>
            <label class="field">
              <span>Rev.</span>
              <input type="text" name="arquivos[<?= (int) $index ?>][rev]" value="<?= h((string) ($arquivo['rev'] ?? '')) ?>">
            </label>
            <label class="field">
              <span>Link</span>
              <input type="url" name="arquivos[<?= (int) $index ?>][link]" value="<?= h((string) ($arquivo['link'] ?? '')) ?>" placeholder="https://...">
            </label>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
    <template id="file-row-template">
      <div class="repeater-row">
        <div class="row-actions">
          <button class="btn btn-ghost btn-sm repeater-remove" type="button">Remover</button>
        </div>
        <div class="grid cols-4">
          <label class="field">
            <span>Item</span>
            <input type="text" name="arquivos[__INDEX__][item]" value="">
          </label>
          <label class="field">
            <span>Nome</span>
            <input type="text" name="arquivos[__INDEX__][nome]" value="">
          </label>
          <label class="field">
            <span>Rev.</span>
            <input type="text" name="arquivos[__INDEX__][rev]" value="">
          </label>
          <label class="field">
            <span>Link</span>
            <input type="url" name="arquivos[__INDEX__][link]" value="" placeholder="https://...">
          </label>
        </div>
      </div>
    </template>
  </section>

  <section class="panel">
    <h2>Etapas e prazos</h2>
    <div class="panel-subhead">
      <p class="muted">Adicione etapas extras para controlar cronograma e timeline.</p>
      <button class="btn btn-ghost btn-sm" type="button" id="add-stage-row-button">+ Adicionar etapa</button>
    </div>
    <div class="stack-sm" id="stages-list" data-next-index="<?= count($stageRows) ?>">
      <?php foreach ($stageRows as $index => $etapa): ?>
        <div class="repeater-row">
          <div class="row-actions">
            <button class="btn btn-ghost btn-sm repeater-remove" type="button">Remover</button>
          </div>
          <div class="grid cols-3">
            <label class="field">
              <span>Etapa</span>
              <input type="text" name="etapas[<?= (int) $index ?>][nome]" value="<?= h((string) ($etapa['nome'] ?? '')) ?>">
            </label>
            <label class="field">
              <span>%</span>
              <input type="number" min="0" max="100" name="etapas[<?= (int) $index ?>][percentual]" value="<?= (int) ($etapa['percentual'] ?? 0) ?>">
            </label>
            <label class="field">
              <span>Descricao</span>
              <input type="text" name="etapas[<?= (int) $index ?>][descricao]" value="<?= h((string) ($etapa['descricao'] ?? '')) ?>">
            </label>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
    <template id="stage-row-template">
      <div class="repeater-row">
        <div class="row-actions">
          <button class="btn btn-ghost btn-sm repeater-remove" type="button">Remover</button>
        </div>
        <div class="grid cols-3">
          <label class="field">
            <span>Etapa</span>
            <input type="text" name="etapas[__INDEX__][nome]" value="">
          </label>
          <label class="field">
            <span>%</span>
            <input type="number" min="0" max="100" name="etapas[__INDEX__][percentual]" value="0">
          </label>
          <label class="field">
            <span>Descricao</span>
            <input type="text" name="etapas[__INDEX__][descricao]" value="">
          </label>
        </div>
      </div>
    </template>
  </section>

  <section class="panel">
    <h2>Observacoes e exclusoes</h2>
    <div class="grid cols-2">
      <label class="field">
        <span>Consideracoes importantes (1 por linha)</span>
        <textarea name="consideracoes" rows="8"><?= h(implode("\n", $payload['consideracoes'])) ?></textarea>
      </label>
      <label class="field">
        <span>Itens fora do escopo (1 por linha)</span>
        <textarea name="exclusoes" rows="8"><?= h(implode("\n", $payload['exclusoes'])) ?></textarea>
      </label>
    </div>
    <label class="field">
      <span>Observacoes finais</span>
      <textarea name="observacoes" rows="3"><?= h((string) $payload['observacoes']) ?></textarea>
    </label>
    <label class="field">
      <span>Link manual de assinatura ZapSign (opcional)</span>
      <input type="url" name="zapsign_sign_url" value="<?= h((string) $payload['zapsign_sign_url']) ?>" placeholder="https://app.zapsign.com.br/verificar/...">
    </label>
  </section>

  <section class="inline-actions sticky-actions">
    <button type="submit" name="save_mode" value="edit" class="btn btn-primary">Salvar proposta</button>
    <button type="submit" name="save_mode" value="preview" class="btn btn-ghost">Salvar e visualizar preview</button>
  </section>
</form>
