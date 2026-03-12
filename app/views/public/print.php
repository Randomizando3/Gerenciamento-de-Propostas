<?php
$catalog = $catalog ?? discipline_catalog();
$selected = [];
foreach ($payload['disciplinas'] as $key) {
    if (isset($catalog[$key])) {
        $selected[] = [
            'nome' => $catalog[$key]['nome'],
            'valor' => (float) ($payload['valores'][$key] ?? 0),
        ];
    }
}
$total = proposal_total($payload);
?>
<!doctype html>
<html lang="pt-BR">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= h($title ?? 'PDF da proposta') ?></title>
  <style>
    :root {
      --ink: #042d38;
      --teal: #0f8b8d;
      --line: #d9e9eb;
      --muted: #52686d;
      --bg: #f4f9f9;
    }
    * { box-sizing: border-box; }
    body {
      margin: 0;
      font-family: Arial, sans-serif;
      color: var(--ink);
      background: #fff;
      line-height: 1.45;
    }
    .sheet {
      max-width: 900px;
      margin: 0 auto;
      padding: 28px;
    }
    h1, h2, h3 { margin: 0 0 8px; }
    h1 { font-size: 28px; }
    h2 { font-size: 18px; margin-top: 22px; }
    p { margin: 0 0 8px; }
    .header {
      display: grid;
      grid-template-columns: 1fr auto;
      gap: 16px;
      align-items: center;
      border-bottom: 3px solid var(--teal);
      padding-bottom: 16px;
      margin-bottom: 18px;
    }
    .meta {
      background: var(--bg);
      border: 1px solid var(--line);
      border-radius: 10px;
      padding: 12px;
      min-width: 260px;
    }
    .meta div { display: flex; justify-content: space-between; margin: 4px 0; }
    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 8px;
      font-size: 14px;
    }
    th, td {
      border: 1px solid var(--line);
      padding: 8px 10px;
      text-align: left;
    }
    th { background: var(--bg); }
    .total {
      margin-top: 16px;
      background: var(--bg);
      border: 1px solid var(--line);
      border-radius: 10px;
      padding: 14px;
    }
    ul {
      margin: 6px 0 0;
      padding-left: 20px;
    }
    li { margin: 4px 0; }
    .muted { color: var(--muted); }
    @media print {
      .no-print { display: none; }
      body { background: #fff; }
      .sheet { padding: 0; }
    }
  </style>
</head>
<body>
  <div class="sheet">
    <div class="no-print" style="text-align:right; margin-bottom:10px;">
      <button onclick="window.print()">Imprimir / Salvar em PDF</button>
    </div>
    <header class="header">
      <div>
        <h1>Proposta Comercial</h1>
        <p><strong><?= h((string) ($payload['titulo'] ?: 'Projeto técnico de instalações')) ?></strong></p>
        <p class="muted">Cliente: <?= h((string) ($payload['cliente_nome'] ?: $payload['cliente_empresa'])) ?></p>
      </div>
      <div class="meta">
        <div><span>Código</span><strong><?= h((string) $payload['codigo_base']) ?></strong></div>
        <?php if (trim((string) ($payload['revisao'] ?? '')) !== '' && trim((string) ($payload['revisao'] ?? '')) !== '00'): ?>
          <div><span>Revisao</span><strong><?= h((string) $payload['revisao']) ?></strong></div>
        <?php endif; ?>
        <div><span>Prazo</span><strong><?= (int) $payload['prazo_dias'] ?> dias</strong></div>
        <div><span>Validade</span><strong><?= (int) $payload['validade_dias'] ?> dias</strong></div>
      </div>
    </header>

    <section>
      <h2>Objeto da proposta</h2>
      <p><?= nl2br(h((string) ($payload['descricao_objeto'] ?: 'Desenvolvimento do escopo técnico conforme disciplinas selecionadas.'))) ?></p>
      <p><strong>Obra:</strong> <?= h((string) ($payload['obra_nome'] ?: 'Não informado')) ?> - <?= h((string) ($payload['obra_endereco'] ?: 'Sem endereço')) ?></p>
    </section>

    <section>
      <h2>Disciplinas e valores</h2>
      <table>
        <thead>
          <tr>
            <th>Disciplina</th>
            <th>Valor</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($selected as $item): ?>
            <tr>
              <td><?= h((string) $item['nome']) ?></td>
              <td><?= brl((float) $item['valor']) ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
      <div class="total">
        <p><strong>Total:</strong> <?= brl($total) ?></p>
        <p class="muted"><?= h(currency_to_words_ptbr($total)) ?></p>
      </div>
    </section>

    <section>
      <h2>Cronograma</h2>
      <table>
        <thead>
          <tr>
            <th>Etapa</th>
            <th>%</th>
            <th>Descrição</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($payload['etapas'] as $etapa): ?>
            <tr>
              <td><?= h((string) ($etapa['nome'] ?? 'Etapa')) ?></td>
              <td><?= (int) ($etapa['percentual'] ?? 0) ?>%</td>
              <td><?= h((string) ($etapa['descricao'] ?? '')) ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </section>

    <section>
      <h2>Considerações</h2>
      <ul>
        <?php foreach ($payload['consideracoes'] as $item): ?>
          <li><?= h((string) $item) ?></li>
        <?php endforeach; ?>
      </ul>
    </section>

    <section>
      <h2>Itens fora do escopo</h2>
      <ul>
        <?php foreach ($payload['exclusoes'] as $item): ?>
          <li><?= h((string) $item) ?></li>
        <?php endforeach; ?>
      </ul>
    </section>
  </div>
  <script>
    if (window.location.search.includes("autoprint=1")) {
      window.print();
    }
  </script>
</body>
</html>

