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
