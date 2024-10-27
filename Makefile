.PHONY: help

env ?= dev 

## Colors
COLOR_RESET			= \033[0m
COLOR_ERROR			= \033[31m
COLOR_INFO			= \033[32m
COLOR_COMMENT		= \033[33m
COLOR_TITLE_BLOCK	= \033[0;44m\033[37m

CONTAINER_ID_PHP = php83

#---SYMFONY--#
DOCKER_PHP = docker exec -it $(CONTAINER_ID_PHP) bash
#------------#

PHP = docker exec -it $(CONTAINER_ID_PHP)
NODE = docker exec -it icad_node

DOCKER_COMPOSE = docker compose -p php-icad
#---PHPUNIT-#
PHPUNIT = vendor/bin/phpunit
#------------#

## Help
help:
	@printf "${COLOR_TITLE_BLOCK}MAKEFILE_LIST${COLOR_RESET}\n"
	@printf "\n"
	@printf "${COLOR_COMMENT}Usage:${COLOR_RESET}\n"
	@printf " make [target]\n\n"
	@printf "${COLOR_COMMENT}Available targets:${COLOR_RESET}\n"
	@awk '/^[a-zA-Z\-\_0-9\@]+:/ { \
		helpLine = match(lastLine, /^## (.*)/); \
		helpCommand = substr($$1, 0, index($$1, ":")); \
		helpMessage = substr(lastLine, RSTART + 3, RLENGTH); \
		printf " ${COLOR_INFO}%-16s${COLOR_RESET} %s\n", helpCommand, helpMessage; \
	} \
	{ lastLine = $$0 }' $(MAKEFILE_LIST)

## launch docker containers, no rebuild
build:
	@$(DOCKER_COMPOSE) up -d --build --force-recreate --remove-orphans

start:
	@$(DOCKER_COMPOSE) up -d
## stop docker containers
stop:
	@$(DOCKER_COMPOSE) stop

## down docker containers
down:
	@$(DOCKER_COMPOSE) down

## show docker containers logs
logs:
	@$(DOCKER_COMPOSE) logs -f

## stop docker containers
restart: stop start

## shell app and php -v
shell-app:
	@$(DOCKER_PHP)

## shell node
shell-node:
	@$(NODE) bash

## composer install
composer-install:
	$(PHP) composer install

## yarn install and run
yarn-install-and-run:
	$(NODE) yarn
	$(NODE) yarn run dev

## install
install: start

## run tests
test:
	docker exec -it php83 sh -c "vendor/bin/phpunit tests --colors"

## launch project at http://localhost:8082
launch:
	@echo "Démarrage du serveur sur http://localhost:8082"
	# Commande pour ouvrir le navigateur selon le système d'exploitation
	@if [ "$(OS)" = "Windows_NT" ]; then \
		start http://localhost:8082; \
	elif [ "$(shell uname)" = "Darwin" ]; then \
		open http://localhost:8082; \
	else \
		xdg-open http://localhost:8082; \
	fi

## init project
init-project: start composer-install yarn-install-and-run launch