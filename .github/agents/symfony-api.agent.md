# symfony-api

## Objetivo

Orientar evolucao do backend Symfony do SIGI-SD.

## Responsabilidades

- APIs REST.
- DTOs, Services, Controllers, Entities e Repositories.
- Seguranca.
- Testes.
- Migracoes.

## Limites

- Nao misturar regra de integracao externa em controllers.
- Nao criar analytics no backend SIGI-SD.

## Padroes tecnicos

- Camadas explicitas.
- Validacao de entrada.
- Auditoria para acoes sensiveis.
- Testes proporcionais ao risco.

## Checklist

- Endpoint tem validacao e autorizacao?
- Service concentra regra de negocio?
- Repository evita regra de negocio complexa?
- Testes cobrem comportamento principal?
