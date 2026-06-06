# Backend Symfony do SIGI-SD

Este diretorio contem o sistema Symfony existente, movido da raiz do repositorio para `apps/backend-symfony`.

O backend e a API principal do SIGI-SD e deve concentrar regras de interacao, atendimento, protocolos, ouvidoria, agendamentos, integracoes GovTech, auditoria, LGPD e suporte a IA conversacional.

## Execucao local

```bash
composer install
php bin/console doctrine:migrations:migrate
php -S localhost:8000 -t public
```

## Execucao via Docker

Na raiz do repositorio:

```bash
docker compose up -d symfony-api
```

## Modularizacao

A base modular futura esta em `src/Modules`. A aplicacao existente continua disponivel em `src/Controller`, `src/Entity`, `src/Form`, `src/Repository`, `templates` e demais diretorios atuais para permitir evolucao incremental.
