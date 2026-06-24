# Admin Hub Symfony do SIGI-SD

Este diretorio contem o sistema Symfony existente, movido da raiz do repositorio para `apps/backend-symfony`.

O backend Symfony e o Admin Hub do SIGI-SD. Ele deve concentrar a administracao geral da plataforma, regras de interacao, atendimento, protocolos, ouvidoria, agendamentos, integracoes GovTech, auditoria, LGPD e suporte a IA conversacional.

## Execucao local

```bash
composer install
php bin/console doctrine:migrations:migrate
php -S localhost:8000 -t public
```

## Execucao via Docker

Na raiz do repositorio:

```bash
docker compose up -d symfony-admin
```

## Modularizacao

A base modular futura esta em `src/Modules`. A aplicacao existente continua disponivel em `src/Controller`, `src/Entity`, `src/Form`, `src/Repository`, `templates` e demais diretorios atuais para permitir evolucao incremental.

## Central SIGI e Chatwoot

- `/admin`: Hub/Central SIGI, pagina principal do admin.
- `/admin/atendimentos`: protocolos sincronizados do Chatwoot.
- `/admin/atendimentos/dashboard`: indicadores de atendimento.
- `/admin/atendimentos/configuracao`: regra do sequencial diario ou global.

Sincronizacao:

```bash
php bin/console sigi:chatwoot:sync --limit=50
```

Variaveis principais:

```env
CHATWOOT_BASE_URL=
CHATWOOT_ACCOUNT_ID=
CHATWOOT_API_TOKEN=
CHATWOOT_INBOX_ID=
SIGI_CHATWOOT_URL=
SIGI_BOTPRESS_URL=
SIGI_TYPEBOT_URL=
SIGI_PORTAINER_URL=
SIGI_BI_URL=
SIGI_DOCS_URL=
```
