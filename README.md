# SIGI-SD

Sistema de Inteligencia Geral de Interacoes.

O SIGI-SD e a plataforma tecnologica da Central Publica Digital do Sertao Digital. Ele centraliza interacoes multicanal, atendimento, CRM operacional, chatbot, IA conversacional, protocolos, ouvidoria, agendamentos e integracoes com sistemas governamentais.

O SIGI-SD nao e uma plataforma de analytics. BI, dashboards e indicadores pertencem a Plataforma 360. O SIGI-SD tambem nao e um GRP: e-Cidade, i-Educar, Amadeus LMS e outros sistemas transacionais devem ser integrados por APIs, conectores e adaptadores.

## Como subir

Recomendado: rodar pelo WSL2 com Docker ativo.

```bash
cd /mnt/c/Users/Public/sigi-sd
make setup
make up
make ps
```

O comando que sobe toda a aplicacao e:

```bash
make up
```

Para reconstruir imagens e subir tudo:

```bash
make rebuild
```

Para testar os endpoints principais:

```bash
make health
```

## Subir servicos individuais

Use estes comandos quando nao quiser subir tudo:

```bash
make up-admin      # Symfony Admin Hub + Postgres + Redis + Traefik
make up-ia         # Ollama + Qdrant + Traefik
make up-chat       # Chatwoot web + worker + Postgres + Redis + Traefik
make up-whatsapp   # Evolution API + Postgres + Redis + Traefik
make up-bot        # Botpress + Traefik
make up-db         # Postgres + Redis
make up-proxy      # Traefik
make up-portainer  # Portainer + Traefik
make up-pgadmin    # pgAdmin + Postgres + Traefik
```

## Pre-requisitos

- Windows com WSL2.
- Docker Engine ou Docker Desktop integrado ao WSL2.
- `make` instalado no WSL.
- Portas locais `80` e `18080` livres.

No Ubuntu/WSL, se `make` nao existir:

```bash
sudo apt update
sudo apt install -y make curl
```

## Servicos locais

- Admin Hub Symfony: http://admin.sigi.localhost
- Chatwoot: http://chat.sigi.localhost
- Evolution API: http://whatsapp.sigi.localhost
- Botpress: http://bot.sigi.localhost
- Ollama: http://ia.sigi.localhost
- Qdrant: http://qdrant.sigi.localhost
- Portainer: http://portainer.sigi.localhost
- pgAdmin: http://pgadmin.sigi.localhost
- Dashboard Traefik: http://localhost:18080

## Comandos Make

- `make help`: mostra comandos disponiveis.
- `make setup`: cria `.env` a partir de `.env.example`, se ainda nao existir.
- `make up`: sobe toda a aplicacao.
- `make down`: para e remove containers.
- `make restart`: reinicia tudo.
- `make logs`: acompanha logs.
- `make ps` ou `make status`: lista containers.
- `make build`: constroi imagens.
- `make rebuild`: constroi e sobe.
- `make health`: testa endpoints principais.
- `make up-admin` ou `make up-symfony`: sobe Symfony Admin Hub e dependencias basicas.
- `make up-ia` ou `make up-ai`: sobe Ollama e Qdrant.
- `make up-chat` ou `make up-chatwoot`: sobe Chatwoot web, worker Sidekiq e dependencias.
- `make up-whatsapp` ou `make up-evolution`: sobe Evolution API.
- `make up-bot` ou `make up-botpress`: sobe Botpress.
- `make up-db`: sobe Postgres e Redis.
- `make up-proxy`: sobe Traefik.
- `make up-portainer`: sobe Portainer.
- `make up-pgadmin`: sobe pgAdmin, Postgres e Traefik.
- `make stop-admin`, `make stop-ia`, `make stop-chat`, `make stop-whatsapp`, `make stop-bot`: para servicos especificos.
- `make logs-admin`, `make logs-ia`, `make logs-chat`, `make logs-whatsapp`, `make logs-bot`, `make logs-proxy`: acompanha logs especificos.
- `make shell-admin`: abre shell no container Symfony.
- `make composer-install`: instala dependencias do Symfony.
- `make migrate`: executa migrations do Symfony.
- `make cache-clear`: limpa cache do Symfony.

## Estrutura

```text
apps/
  backend-symfony/   Admin Hub Symfony e backend principal
  chatwoot/          Espaco para configuracoes do CRM multiatendimento
  evolution-api/     Espaco para configuracoes da integracao WhatsApp
  botpress/          Espaco para configuracoes do chatbot
  ollama/            Espaco para IA local
  qdrant/            Espaco para banco vetorial
docker/
  postgres/
  redis/
  traefik/
  portainer/
docs/
scripts/
```

## pgAdmin

O pgAdmin fica em `http://pgadmin.sigi.localhost`.

Credenciais padrao de desenvolvimento:

- E-mail: `admin@sigi.dev.br`
- Senha: `sigi_pgadmin_dev`

Para cadastrar o servidor PostgreSQL no pgAdmin, use:

- Host: `postgres`
- Porta: `5432`
- Banco principal: `sigi_sd`
- Usuario: valor de `POSTGRES_USER`
- Senha: valor de `POSTGRES_PASSWORD`

## Admin Hub Symfony

O sistema Symfony que ja funcionava foi movido para `apps/backend-symfony` e responde como hub administrativo em `http://admin.sigi.localhost`.

A pagina principal do admin agora e a Central SIGI, acessivel pelo menu `Central SIGI` ou por `/admin`. Ela centraliza indicadores resumidos, links para Dashboard, protocolos e ferramentas do ecossistema.

Comandos dentro do container:

```bash
make shell-admin
php bin/console --version
php bin/console doctrine:migrations:migrate
php bin/console cache:clear
```

### Sincronizacao Chatwoot e protocolos

Configure no `.env`:

```env
CHATWOOT_BASE_URL=http://chat.sigi.localhost
CHATWOOT_ACCOUNT_ID=1
CHATWOOT_API_TOKEN=token-do-chatwoot
CHATWOOT_INBOX_ID=
SIGI_CHATWOOT_URL=http://chat.sigi.localhost
SIGI_BOTPRESS_URL=http://bot.sigi.localhost
SIGI_TYPEBOT_URL=
SIGI_PORTAINER_URL=http://portainer.sigi.localhost
SIGI_BI_URL=
SIGI_DOCS_URL=
```

Rode a sincronizacao manual:

```bash
make shell-admin
php bin/console sigi:chatwoot:sync --limit=50
```

Para validar, abra `/admin/atendimentos`: cada conversa importada deve ter protocolo no formato `YYYYMMDD000001` e botao `Abrir no Chatwoot`. A regra do sequencial fica em `/admin/atendimentos/configuracao`.

## Documentacao

Inicio:

- [Documentacao geral](docs/README.md)
- [Manual do usuario](docs/operacao/manual-do-usuario.md)

Arquitetura:

- [Arquitetura](docs/arquitetura/README.md)
- [Visao geral](docs/arquitetura/visao-geral.md)
- [Camadas](docs/arquitetura/camadas.md)
- [Modulos](docs/arquitetura/modulos.md)
- [Decisoes arquiteturais](docs/arquitetura/decisoes-arquiteturais.md)

Infraestrutura, Docker e WSL:

- [Infraestrutura](docs/infraestrutura/visao-geral.md)
- [Docker Compose](docs/docker/docker-compose.md)
- [Ambiente WSL2](docs/wsl/ambiente-wsl.md)

IA e chatbot:

- [IA embarcada](docs/ia/ia-embarcada.md)
- [RAG local](docs/ia/rag-local.md)
- [Qdrant](docs/ia/qdrant.md)
- [Ollama](docs/ia/ollama.md)
- [Botpress](docs/chatbot/botpress.md)
- [Fluxos conversacionais](docs/chatbot/fluxos-conversacionais.md)

Integracoes:

- [Chatwoot x SIGI-SD](docs/integracoes/chatwoot-sigi.md)
- [Padrao de adapters](docs/integracoes/padrao-adapters.md)
- [e-Cidade](docs/integracoes/e-cidade.md)
- [i-Educar](docs/integracoes/i-educar.md)
- [Amadeus LMS](docs/integracoes/amadeus-lms.md)

Operacao, LGPD, agents e skills:

- [Operacao de atendimento](docs/operacao/operacao-atendimento.md)
- [LGPD by design](docs/lgpd/lgpd-by-design.md)
- [Agents](docs/agents/README.md)
- [Skills](docs/skills/README.md)

Documentos legados do backend atual:

- [API](docs/api.md)
- [Arquitetura antiga](docs/architecture.md)
- [Modelo de dados](docs/data_model.md)
- [Guia de desenvolvimento](docs/dev-guide.md)
- [Fixtures](docs/fixtures.md)
- [Roadmap](docs/roadmap.md)
- [Seguranca](docs/security.md)
