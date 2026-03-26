# SIGI-SD - Diretrizes de API

## Visão geral

Este documento define a direção técnica para as APIs do SIGI-SD.

As APIs devem atender principalmente a estes cenários:
- integração com frontend
- integração com sistemas externos
- automação de processos
- acesso estruturado aos dados do sistema

## Convenção de versionamento

Base sugerida:

```text
/api/v1
```

Quando houver mudanças incompatíveis, uma nova versão deve ser criada sem quebrar contratos existentes.

## Princípios

- URLs previsíveis e estáveis
- respostas consistentes
- uso claro de códigos HTTP
- autenticação e autorização explícitas
- paginação em listas extensas
- validação de entrada com mensagens compreensíveis

## Recursos previstos

Exemplos de recursos que podem ser expostos por API:
- organizações
- pessoas
- contatos
- interações
- cobertura territorial
- áreas temáticas

## Padrão de resposta

Exemplo de resposta bem-sucedida:

```json
{
  "data": {
    "id": 1,
    "legalName": "CENTRO DE INOVACAO E TECNOLOGIA SERTAO DIGITAL"
  }
}
```

Exemplo de resposta com erro de validação:

```json
{
  "message": "Falha de validação.",
  "errors": {
    "legalName": ["Informe a razão social."],
    "cnpj": ["Informe o CNPJ."]
  }
}
```

## Segurança

Toda API deve considerar:
- autenticação adequada ao contexto
- autorização por perfil ou permissão
- proteção contra exposição indevida de dados sensíveis
- rastreabilidade para operações críticas

## Próximos passos

- definir contratos por recurso
- padronizar filtros, ordenação e paginação
- documentar autenticação
- publicar exemplos de endpoints operacionais