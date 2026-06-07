COMPOSE=docker compose
API=symfony-api
IA_SERVICES=ollama qdrant
BASE_SERVICES=postgres redis traefik

help:
	@echo "Comandos disponiveis:"
	@echo "  make setup              Copia .env.example para .env se necessario"
	@echo "  make up                 Sobe toda a aplicacao em segundo plano"
	@echo "  make down               Para e remove os containers"
	@echo "  make restart            Reinicia toda a aplicacao"
	@echo "  make logs               Mostra logs de todos os servicos"
	@echo "  make ps                 Lista os containers"
	@echo "  make status             Alias para make ps"
	@echo "  make build              Constroi as imagens"
	@echo "  make rebuild            Reconstroi e sobe toda a aplicacao"
	@echo "  make health             Testa endpoints principais"
	@echo "  make up-api             Sobe apenas Symfony e dependencias basicas"
	@echo "  make up-symfony         Alias para make up-api"
	@echo "  make up-ia              Sobe apenas IA local (Ollama + Qdrant)"
	@echo "  make up-ai              Alias para make up-ia"
	@echo "  make up-chat            Sobe apenas Chatwoot e dependencias basicas"
	@echo "  make up-chatwoot        Alias para make up-chat"
	@echo "  make up-whatsapp        Sobe apenas Evolution API e dependencias basicas"
	@echo "  make up-evolution       Alias para make up-whatsapp"
	@echo "  make up-bot             Sobe apenas Botpress e Traefik"
	@echo "  make up-botpress        Alias para make up-bot"
	@echo "  make up-db              Sobe apenas Postgres e Redis"
	@echo "  make up-proxy           Sobe apenas Traefik"
	@echo "  make up-portainer       Sobe apenas Portainer e Traefik"
	@echo "  make stop-api           Para Symfony"
	@echo "  make stop-ia            Para Ollama e Qdrant"
	@echo "  make stop-chat          Para Chatwoot"
	@echo "  make logs-api           Logs do Symfony"
	@echo "  make logs-ia            Logs de Ollama e Qdrant"
	@echo "  make logs-chat          Logs do Chatwoot"
	@echo "  make logs-whatsapp      Logs da Evolution API"
	@echo "  make logs-bot           Logs do Botpress"
	@echo "  make logs-proxy         Logs do Traefik"
	@echo "  make shell-api          Abre shell no Symfony"
	@echo "  make composer-install   Instala dependencias do Symfony"
	@echo "  make migrate            Executa migrations do Symfony"
	@echo "  make cache-clear        Limpa cache do Symfony"

setup:
	@if [ ! -f .env ]; then cp .env.example .env; fi

up:
	$(COMPOSE) up -d

down:
	$(COMPOSE) down

restart:
	$(COMPOSE) down
	$(COMPOSE) up -d

logs:
	$(COMPOSE) logs -f

ps:
	$(COMPOSE) ps

build:
	$(COMPOSE) build

rebuild:
	$(COMPOSE) up -d --build

status:
	$(COMPOSE) ps

health:
	@curl -I -sS http://api.sigi.localhost | head -n 1
	@curl -I -sS http://chat.sigi.localhost | head -n 1
	@curl -I -sS http://bot.sigi.localhost | head -n 1
	@curl -I -sS http://whatsapp.sigi.localhost | head -n 1
	@curl -sS http://ia.sigi.localhost/api/version
	@echo
	@curl -sS http://qdrant.sigi.localhost/collections
	@echo

up-api:
	$(COMPOSE) up -d postgres redis traefik $(API)

up-symfony: up-api

up-ia:
	$(COMPOSE) up -d traefik $(IA_SERVICES)

up-ai: up-ia

up-chat:
	$(COMPOSE) up -d postgres redis traefik chatwoot

up-chatwoot: up-chat

up-whatsapp:
	$(COMPOSE) up -d postgres redis traefik evolution-api

up-evolution:
	$(COMPOSE) up -d postgres redis traefik evolution-api

up-bot:
	$(COMPOSE) up -d traefik botpress

up-botpress: up-bot

up-db:
	$(COMPOSE) up -d postgres redis

up-proxy:
	$(COMPOSE) up -d traefik

up-portainer:
	$(COMPOSE) up -d traefik portainer

stop-api:
	$(COMPOSE) stop $(API)

stop-symfony: stop-api

stop-ia:
	$(COMPOSE) stop $(IA_SERVICES)

stop-ai: stop-ia

stop-chat:
	$(COMPOSE) stop chatwoot

stop-chatwoot: stop-chat

stop-whatsapp:
	$(COMPOSE) stop evolution-api

stop-bot:
	$(COMPOSE) stop botpress

logs-api:
	$(COMPOSE) logs -f $(API)

logs-symfony: logs-api

logs-ia:
	$(COMPOSE) logs -f $(IA_SERVICES)

logs-ai: logs-ia

logs-chat:
	$(COMPOSE) logs -f chatwoot

logs-whatsapp:
	$(COMPOSE) logs -f evolution-api

logs-bot:
	$(COMPOSE) logs -f botpress

logs-proxy:
	$(COMPOSE) logs -f traefik

shell-api:
	$(COMPOSE) exec $(API) bash

composer-install:
	$(COMPOSE) exec $(API) composer install

migrate:
	$(COMPOSE) exec $(API) php bin/console doctrine:migrations:migrate

cache-clear:
	$(COMPOSE) exec $(API) php bin/console cache:clear
