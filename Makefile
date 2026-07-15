COMPOSE=docker compose
ADMIN=symfony-admin
WORKER=sigi-worker
IA_SERVICES=ollama qdrant
BASE_SERVICES=postgres redis traefik
CLOUDFLARED_IMAGE=cloudflare/cloudflared:latest
WEBHOOK_TUNNEL_CONTAINER=sigi-webhook-tunnel
WEBHOOK_TUNNEL_URL=http://sigi-traefik:80
WEBHOOK_TUNNEL_HOST=chat.sigi.localhost

help:
	@echo "Comandos disponiveis:"
	@echo "  make setup              Copia .env.example para .env se necessario"
	@echo "  make up                 Sobe toda a aplicacao em segundo plano"
	@echo "  make stop               Para os containers e o tunnel webhook"
	@echo "  make down               Para e remove os containers"
	@echo "  make restart            Reinicia toda a aplicacao"
	@echo "  make logs               Mostra logs de todos os servicos"
	@echo "  make ps                 Lista os containers"
	@echo "  make status             Alias para make ps"
	@echo "  make build              Constroi as imagens"
	@echo "  make rebuild            Reconstroi e sobe toda a aplicacao"
	@echo "  make health             Testa endpoints principais"
	@echo "  make up-admin           Sobe apenas Symfony Admin Hub e dependencias basicas"
	@echo "  make up-symfony         Alias para make up-admin"
	@echo "  make up-ia              Sobe apenas IA local (Ollama + Qdrant)"
	@echo "  make up-ai              Alias para make up-ia"
	@echo "  make up-chat            Sobe apenas Chatwoot e dependencias basicas"
	@echo "  make up-worker          Sobe o worker Messenger do SIGI"
	@echo "  make up-chatwoot        Alias para make up-chat"
	@echo "  make up-bot             Sobe apenas Botpress e Traefik"
	@echo "  make up-botpress        Alias para make up-bot"
	@echo "  make up-db              Sobe apenas Postgres e Redis"
	@echo "  make up-proxy           Sobe apenas Traefik"
	@echo "  make up-portainer       Sobe apenas Portainer e Traefik"
	@echo "  make up-pgadmin         Sobe apenas pgAdmin e Postgres"
	@echo "  make up-webhook-tunnel  Abre tunnel trycloudflare para o Chatwoot"
	@echo "  make stop-webhook-tunnel  Para o tunnel trycloudflare"
	@echo "  make logs-webhook-tunnel  Logs do tunnel trycloudflare"
	@echo "  make stop-admin         Para Symfony Admin Hub"
	@echo "  make stop-ia            Para Ollama e Qdrant"
	@echo "  make stop-chat          Para Chatwoot"
	@echo "  make stop-worker        Para o worker Messenger do SIGI"
	@echo "  make logs-admin         Logs do Symfony Admin Hub"
	@echo "  make logs-ia            Logs de Ollama e Qdrant"
	@echo "  make logs-chat          Logs do Chatwoot"
	@echo "  make logs-worker        Logs do worker Messenger do SIGI"
	@echo "  make logs-bot           Logs do Botpress"
	@echo "  make logs-proxy         Logs do Traefik"
	@echo "  make shell-admin        Abre shell no Symfony Admin Hub"
	@echo "  make consume-async      Consome a fila async manualmente"
	@echo "  make composer-install   Instala dependencias do Symfony"
	@echo "  make migrate            Executa migrations do Symfony"
	@echo "  make cache-clear        Limpa cache do Symfony"
	@echo "  make sync-chatwoot      Importa conversas recentes do Chatwoot para o SIGI"
	@echo "  make register-chatwoot-assistant  Registra o Assistente SIGI no Chatwoot"

setup:
	@if [ ! -f .env ]; then cp .env.example .env; fi

up:
	$(COMPOSE) up -d

stop: stop-webhook-tunnel
	$(COMPOSE) stop

down:
	@docker stop $(WEBHOOK_TUNNEL_CONTAINER) >/dev/null 2>&1 || true
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
	@curl -I -sS http://admin.sigi.localhost | head -n 1
	@curl -I -sS http://chat.sigi.localhost | head -n 1
	@curl -I -sS http://bot.sigi.localhost | head -n 1
	@curl -I -sS http://pgadmin.sigi.localhost | head -n 1
	@curl -sS http://ia.sigi.localhost/api/version
	@echo
	@curl -sS http://qdrant.sigi.localhost/collections
	@echo

up-admin:
	$(COMPOSE) up -d postgres redis traefik $(ADMIN)

up-symfony: up-admin

up-worker: up-admin
	$(COMPOSE) up -d $(WORKER)

up-ia:
	$(COMPOSE) up -d traefik $(IA_SERVICES)

up-ai: up-ia

up-chat:
	$(COMPOSE) up -d postgres redis traefik chatwoot chatwoot-worker

up-chatwoot: up-chat

up-bot:
	$(COMPOSE) up -d traefik botpress

up-botpress: up-bot

up-db:
	$(COMPOSE) up -d postgres redis

up-proxy:
	$(COMPOSE) up -d traefik

up-portainer:
	$(COMPOSE) up -d traefik portainer

up-pgadmin:
	$(COMPOSE) up -d postgres traefik pgadmin

up-webhook-tunnel: up-chat
	docker run -d --rm --name $(WEBHOOK_TUNNEL_CONTAINER) --network sigi-network $(CLOUDFLARED_IMAGE) tunnel --no-autoupdate --url $(WEBHOOK_TUNNEL_URL) --http-host-header $(WEBHOOK_TUNNEL_HOST)

logs-webhook-tunnel:
	docker logs -f $(WEBHOOK_TUNNEL_CONTAINER)

stop-webhook-tunnel:
	@docker stop $(WEBHOOK_TUNNEL_CONTAINER) >/dev/null 2>&1 || true

stop-admin:
	$(COMPOSE) stop $(ADMIN)

stop-symfony: stop-admin

stop-worker:
	$(COMPOSE) stop $(WORKER)

stop-ia:
	$(COMPOSE) stop $(IA_SERVICES)

stop-ai: stop-ia

stop-chat: stop-webhook-tunnel
	$(COMPOSE) stop chatwoot chatwoot-worker

stop-chatwoot: stop-chat

stop-bot:
	$(COMPOSE) stop botpress

logs-admin:
	$(COMPOSE) logs -f $(ADMIN)

logs-symfony: logs-admin

logs-worker:
	$(COMPOSE) logs -f $(WORKER)

logs-ia:
	$(COMPOSE) logs -f $(IA_SERVICES)

logs-ai: logs-ia

logs-chat:
	$(COMPOSE) logs -f chatwoot chatwoot-worker

logs-bot:
	$(COMPOSE) logs -f botpress

logs-proxy:
	$(COMPOSE) logs -f traefik

shell-admin:
	$(COMPOSE) exec $(ADMIN) bash

shell-symfony: shell-admin

consume-async:
	$(COMPOSE) exec $(ADMIN) php bin/console messenger:consume async -vv

composer-install:
	$(COMPOSE) exec $(ADMIN) composer install

migrate:
	$(COMPOSE) exec $(ADMIN) php bin/console doctrine:migrations:migrate

cache-clear:
	$(COMPOSE) exec $(ADMIN) php bin/console cache:clear

sync-chatwoot:
	$(COMPOSE) exec $(ADMIN) php bin/console sigi:chatwoot:sync --limit=50

register-chatwoot-assistant:
	$(COMPOSE) cp scripts/register-chatwoot-sigi-assistant.rb chatwoot:/tmp/register-chatwoot-sigi-assistant.rb
	$(COMPOSE) exec chatwoot bundle exec rails runner /tmp/register-chatwoot-sigi-assistant.rb
