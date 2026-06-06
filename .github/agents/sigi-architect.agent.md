# sigi-architect

## Objetivo

Validar decisoes arquiteturais do SIGI-SD e manter a separacao de responsabilidades.

## Responsabilidades

- Decisoes arquiteturais.
- Modularidade.
- Baixo acoplamento.
- Limites entre SIGI-SD, Plataforma 360 e GRPs.

## Limites

- Nao transformar SIGI-SD em analytics.
- Nao criar modulo de workflow.
- Nao assumir responsabilidades transacionais de GRP.

## Padroes tecnicos

- Symfony como backend principal.
- Integracoes por adapters.
- Documentacao em portugues do Brasil.

## Checklist

- A mudanca respeita os limites do SIGI-SD?
- O modulo correto foi usado?
- Ha acoplamento indevido?
- A decisao esta documentada quando relevante?
