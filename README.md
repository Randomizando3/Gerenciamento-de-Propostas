# Gerenciamento de Propostas

Sistema em **PHP + JS + HTML/CSS** para criacao, publicacao e acompanhamento de propostas comerciais com pagina publica, tracking e integracao com assinatura digital.

## Recursos principais

- Painel admin com login
- Criacao e edicao de propostas
- Preview interno da proposta
- Publicacao com token unico e link publico
- Tracking proprio (views, scroll, tempo por secao, eventos)
- Analytics no admin com graficos
- Integracao com Microsoft Clarity (script e campos de export)
- Integracao com ZapSign (envio para assinatura)
- Termos de aceite editaveis pelo admin (com variaveis)

## Estrutura

- `public/` - arquivos publicos e front-end
- `app/` - controllers, services, views e helpers
- `storage/database.json` - base local em JSON
- `storage/backups/` - snapshots automaticos do banco JSON
- `storage/logs/` - logs locais
- `propostabase.html` - template principal da proposta publica
- `documentation.html` - manual de uso do sistema

## Requisitos

- PHP 8.1+
- Extensao cURL habilitada
- Permissao de escrita em `storage/` e `public/uploads/`

## Executar localmente

Na raiz do projeto:

```bash
php -S localhost:9898 -t public router.php
```

Acesso admin:

- URL: `http://localhost:9898/admin/login`
- Usuario padrao: `admin@local`
- Senha padrao: `admin123`

## Publicacao (servidor)

- Apontar o Document Root para `public/`
- Garantir HTTPS
- Ajustar permissoes de escrita
- Configurar em `Admin > Configuracoes`:
  - Base URL publica
  - Clarity
  - ZapSign

## Observacoes sobre ZapSign

- Se aparecer erro de token (`403 token not found`), a API key/token informado esta invalido, expirado ou no ambiente errado.
- Revise a chave no painel da ZapSign e teste novamente.

## Documentacao completa

Abra `documentation.html` no navegador para o manual funcional de uso.
