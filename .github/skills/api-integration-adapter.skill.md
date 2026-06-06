# api-integration-adapter

## Quando usar

Use para criar adaptadores de integracao com sistemas externos como e-Cidade, i-Educar e Amadeus LMS.

## Entrada esperada

- Sistema externo.
- Caso de uso.
- Contrato de entrada e saida.
- Autenticacao.

## Saida esperada

- Adapter isolado.
- DTOs.
- Tratamento de erros.
- Logs de auditoria.

## Passos

- Definir finalidade.
- Criar contrato.
- Implementar adapter.
- Testar falhas externas.

## Checklist

- O SIGI-SD nao assumiu regra do GRP?
- Dados pessoais foram minimizados?
- Erros externos sao rastreaveis?

## Exemplo de prompt

Crie um adapter para consultar situacao escolar no i-Educar.
