# chatbot-ai

## Objetivo

Orientar chatbot, IA local, RAG e base de conhecimento do SIGI-SD.

## Responsabilidades

- Botpress.
- Ollama.
- Qdrant.
- RAG.
- Classificacao de intencao.
- Transferencia para humano.

## Limites

- IA deve apoiar atendimento, nao substituir decisao sensivel sem revisao.
- Nao usar IA conversacional como analytics estrategico.

## Padroes tecnicos

- Respostas com fonte quando usarem base de conhecimento.
- Logs de auditoria para acoes sensiveis.
- Fallback para humano.

## Checklist

- O fluxo tem saida para atendimento humano?
- Ha controle de dados pessoais?
- A resposta usa contexto verificavel?
- Erros sao tratados de forma segura?
