COMPOSE=docker compose
API=symfony-api

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

shell-api:
	$(COMPOSE) exec $(API) bash

composer-install:
	$(COMPOSE) exec $(API) composer install

migrate:
	$(COMPOSE) exec $(API) php bin/console doctrine:migrations:migrate

cache-clear:
	$(COMPOSE) exec $(API) php bin/console cache:clear
