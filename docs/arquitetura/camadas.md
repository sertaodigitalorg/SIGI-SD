# Camadas

## Interface e canais

- Chatwoot para atendimento humano e CRM operacional.
- Evolution API para WhatsApp.
- Botpress para chatbot e transferencia para humano.
- Canais futuros: e-mail, Telegram, webchat, telefone local e presencial.

## Backend

- Symfony API como backend principal.
- Controllers, DTOs, Services, Entities e Repositories.
- Modulos em `apps/backend-symfony/src/Modules`.

## Inteligencia conversacional

- Ollama para IA local.
- Qdrant para embeddings e busca vetorial.
- Base de conhecimento para FAQ, documentos e RAG.

## Persistencia e filas

- PostgreSQL para dados relacionais.
- Redis para cache, filas e suporte operacional.

## Integracoes

- Adaptadores para e-Cidade, i-Educar, Amadeus LMS e outros sistemas.
- Contratos claros para entrada, saida, autenticacao, rastreabilidade e erros.
