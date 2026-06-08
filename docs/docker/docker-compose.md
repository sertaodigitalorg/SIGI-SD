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
- `pgadmin_data`

## Dominios locais

- `admin.sigi.localhost`
- `chat.sigi.localhost`
- `whatsapp.sigi.localhost`
- `bot.sigi.localhost`
- `ia.sigi.localhost`
- `qdrant.sigi.localhost`
- `portainer.sigi.localhost`
- `pgadmin.sigi.localhost`

O dashboard local do Traefik fica em `http://localhost:18080`.

## Admin Hub Symfony

O Admin Hub Symfony roda no servico `symfony-admin`, com container `sigi-symfony-admin`, e responde em `http://admin.sigi.localhost`.

Ele usa o banco legado `sigi_sd` e o Redis interno para cache/sessoes quando configurado.

## pgAdmin

O pgAdmin roda no servico `pgadmin`, com container `sigi-pgadmin`, e responde em `http://pgadmin.sigi.localhost`.

Credenciais padrao de desenvolvimento:

- E-mail: `admin@sigi.dev.br`
- Senha: `sigi_pgadmin_dev`

Ao criar uma conexao no pgAdmin, use o host Docker `postgres`, porta `5432`, usuario `sigi` e banco `sigi_sd`, salvo se estes valores tiverem sido alterados no `.env`.

## Chatwoot

O Chatwoot roda em dois servicos no `docker-compose.yml`:

- `chatwoot`: processo web Rails/Puma, responsavel pela interface em `http://chat.sigi.localhost`.
- `chatwoot-worker`: processo Sidekiq, responsavel por filas, jobs agendados, IMAP, auto-respostas e eventos em background.

O recebimento de e-mails por IMAP depende do `chatwoot-worker`. Se apenas o container `chatwoot` estiver ativo, a interface abre normalmente, mas os e-mails podem ficar no provedor e nao virar conversas.

Para subir o Chatwoot corretamente:

```bash
make up-chat
```

ou:

```bash
docker compose up -d postgres redis traefik chatwoot chatwoot-worker
```

Para verificar:

```bash
docker compose ps chatwoot chatwoot-worker
docker compose logs -f chatwoot chatwoot-worker
```

O log esperado do worker deve incluir o cron:

```text
trigger_imap_email_inboxes_job
```

Console Rails no container:

```bash
docker exec -it sigi-chatwoot bundle exec rails c
```
