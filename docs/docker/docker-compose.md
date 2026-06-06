# Docker Compose

O arquivo `docker-compose.yml` fica na raiz do repositorio e sobe o ambiente com:

```bash
docker compose up -d
```

## Rede

Todos os servicos usam `sigi-network`.

## Volumes

- `postgres_data`
- `redis_data`
- `chatwoot_data`
- `evolution_data`
- `botpress_data`
- `qdrant_data`
- `ollama_data`
- `portainer_data`

## Dominios locais

- `api.sigi.localhost`
- `chat.sigi.localhost`
- `whatsapp.sigi.localhost`
- `bot.sigi.localhost`
- `ia.sigi.localhost`
- `qdrant.sigi.localhost`
- `portainer.sigi.localhost`
