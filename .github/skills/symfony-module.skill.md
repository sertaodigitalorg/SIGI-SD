# symfony-module

## Quando usar

Use para criar ou evoluir modulos em `apps/backend-symfony/src/Modules`.

## Entrada esperada

- Nome do modulo.
- Responsabilidade.
- Entidades ou endpoints previstos.

## Saida esperada

- Estrutura do modulo.
- Services, DTOs, Controllers e testes quando aplicavel.
- Documentacao atualizada.

## Passos

- Validar se o modulo pertence ao SIGI-SD.
- Criar estrutura dentro de `src/Modules`.
- Isolar regra de negocio em Services.
- Adicionar testes proporcionais ao risco.

## Checklist

- Modulo respeita limites do SIGI-SD?
- Nomes estao em ingles tecnico no codigo?
- Documentacao esta em portugues do Brasil?

## Exemplo de prompt

Crie o modulo Protocol com endpoint para consultar protocolo por numero.
