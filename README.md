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

- API Symfony: http://api.sigi.localhost
- Chatwoot: http://chat.sigi.localhost
- Evolution API: http://whatsapp.sigi.localhost
- Botpress: http://bot.sigi.localhost
- Ollama: http://ia.sigi.localhost
- Qdrant: http://qdrant.sigi.localhost
- Portainer: http://portainer.sigi.localhost
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
- `make shell-api`: abre shell no container Symfony.
- `make composer-install`: instala dependencias do Symfony.
- `make migrate`: executa migrations do Symfony.
- `make cache-clear`: limpa cache do Symfony.

## Estrutura

```text
apps/
  backend-symfony/   Backend principal Symfony
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

## Backend Symfony

O sistema Symfony que ja funcionava foi movido para `apps/backend-symfony`.

Comandos dentro do container:

```bash
make shell-api
php bin/console --version
php bin/console doctrine:migrations:migrate
php bin/console cache:clear
```

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
