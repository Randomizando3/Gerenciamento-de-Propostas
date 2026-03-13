# Gerenciamento de Propostas

Sistema web em PHP + JS + HTML/CSS puro para criacao, publicacao e acompanhamento de propostas comerciais.

## Principais recursos

- Painel administrativo com login
- Criacao, edicao, preview e publicacao de propostas
- Pagina publica responsiva por token unico
- Analytics de leitura (views, scroll, tempo e eventos)
- Integracao opcional com Microsoft Clarity
- Integracao com ZapSign para assinatura digital
- Gestao de usuarios com roles (admin e editor)

## Perfis de acesso

- admin: acesso completo (propostas, usuarios e configuracoes)
- editor: acesso ao fluxo de propostas

## Estrutura do projeto

- app/: controllers, services, views e helpers
- public/: front controller, assets e webhook publico
- storage/: banco JSON, backups e logs
- propostabase.html: template base da proposta publica
- documentation.html: manual funcional em HTML

## Requisitos

- PHP 8.1+
- Extensao cURL habilitada
- Permissao de escrita em:
  - storage/
  - public/uploads/

## Rodando localmente

Na raiz do projeto:

```bash
php -S localhost:9898 -t public router.php
```

Painel:

- URL: http://localhost:9898/admin/login

Observacao:

- em ambiente limpo, o usuario inicial e criado com base em app/config.php (default_admin)

## Deploy (Apache/Nginx)

### Apache

- apontar DocumentRoot para public/
- garantir .htaccess habilitado (AllowOverride All)

### Nginx

- apontar raiz para public/
- redirecionar requisicoes para index.php quando o arquivo nao existir

### Em ambos

- habilitar HTTPS
- ajustar permissoes de escrita nas pastas de runtime
- definir Base URL em Admin > Configuracoes quando necessario

## Rotas importantes

- GET /admin/login
- GET /admin
- GET /admin/proposals
- GET /admin/proposals/new
- GET /admin/settings (admin)
- GET /admin/users (admin)
- POST /webhook/zapsign
- GET /p/{token}
- GET /p/{token}/print
- GET /p/{token}/sign

## Seguranca e dados sensiveis

- chaves e tokens ficam em storage/database.json
- esse arquivo esta no .gitignore
- nao versionar storage/database.json em repositorio remoto

## Dicas operacionais

- faca backup periodico de storage/database.json
- use storage/backups/ para rollback rapido
- se ocorrer erro 403 no ZapSign, valide token e ambiente (sandbox x producao)
- em mudancas de front, faca hard refresh (Ctrl+F5)

## Documentacao complementar

- documentation.html