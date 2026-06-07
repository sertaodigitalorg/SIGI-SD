# Infraestrutura

O ambiente de desenvolvimento do SIGI-SD foi preparado para WSL2 no Windows com Docker Desktop.

Servicos:

- PostgreSQL com pgvector: banco relacional e suporte a extensao `vector` usada pelo Chatwoot.
- Redis: cache e fila.
- Traefik: proxy reverso.
- Portainer: gestao de containers.
- Symfony API: backend principal.
- Chatwoot: CRM multiatendimento.
- Evolution API: integracao WhatsApp.
- Botpress: chatbot e agente conversacional.
- Ollama: IA local embarcada.
- Qdrant: banco vetorial.

Todos os servicos usam a rede `sigi-network` e volumes persistentes declarados no `docker-compose.yml`.
