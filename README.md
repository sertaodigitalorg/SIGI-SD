# SIGI-SD

Sistema de Inteligencia Geral de Interacoes.

O SIGI-SD e a plataforma tecnologica da Central Publica Digital do Sertao Digital. Ele centraliza interacoes multicanal, atendimento, CRM operacional, chatbot, IA conversacional, protocolos, ouvidoria, agendamentos e integracoes com sistemas governamentais.

O SIGI-SD nao e uma plataforma de analytics. BI, dashboards e indicadores pertencem a Plataforma 360. O SIGI-SD tambem nao e um GRP: e-Cidade, i-Educar, Amadeus LMS e outros sistemas transacionais devem ser integrados por APIs, conectores e adaptadores.

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

## Subir o ambiente

Copie o arquivo de exemplo e ajuste os segredos locais:

```bash
cp .env.example .env
docker compose up -d
```

Ou use o Makefile:

```bash
make up
make ps
make logs
```

## Servicos locais

- API Symfony: http://api.sigi.localhost
- Chatwoot: http://chat.sigi.localhost
- Evolution API: http://whatsapp.sigi.localhost
- Botpress: http://bot.sigi.localhost
- Ollama: http://ia.sigi.localhost
- Qdrant: http://qdrant.sigi.localhost
- Portainer: http://portainer.sigi.localhost

## Backend Symfony

O sistema Symfony que ja funcionava foi movido para `apps/backend-symfony`. Para executar comandos diretamente nele:

```bash
cd apps/backend-symfony
composer install
php bin/console doctrine:migrations:migrate
```

## Documentacao

A documentacao principal esta em `docs/`, com visao arquitetural, infraestrutura, Docker, WSL2, IA local, chatbot, integracoes GovTech, LGPD, agents e skills.
