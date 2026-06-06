COMPOSE=docker compose
API=symfony-api

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

shell-api:
	$(COMPOSE) exec $(API) bash

composer-install:
	$(COMPOSE) exec $(API) composer install

migrate:
	$(COMPOSE) exec $(API) php bin/console doctrine:migrations:migrate

cache-clear:
	$(COMPOSE) exec $(API) php bin/console cache:clear
