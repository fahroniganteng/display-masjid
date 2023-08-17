# File variables
FILE1=.env
FILE2=.env.example

# Check if .env file exists
ifeq (,$(wildcard $(FILE1)))
$(shell cp $(FILE2) $(FILE1))
endif

# Load environment variables
include .env
export $(shell sed 's/=.*//' .env)

# Color Config
NOCOLOR=\033[0m
GREEN=\033[0;32m
BGREEN=\033[1;32m
YELLOW=\033[0;33m
CYAN=\033[0;36m

# Default action
.DEFAULT_GOAL := help

up:
	@echo ""
	@echo "${YELLOW}Start all container${NOCOLOR}"
	@echo ""
	docker-compose up -d

	@if [ ! -d vendor ]; then\
		echo "";\
		echo "${YELLOW}Install composer dependencies${NOCOLOR}";\
		echo "";\
		$(MAKE) composer;\
	fi

stop:
	@echo ""
	@echo "${YELLOW}Stop all container${NOCOLOR}"
	@echo ""
	docker-compose stop

destroy:
	@echo ""
	@echo "${YELLOW}Destroy all container${NOCOLOR}"
	@echo ""
	docker-compose down

build:
	@echo ""
	@echo "${YELLOW}Build all container${NOCOLOR}"
	@echo ""
	docker-compose up --build -d

composer:
	docker-compose exec masjid_screen_php composer install

composer_require:
	docker-compose exec masjid_screen_php composer require $(PACKAGE)

bash_php:
	docker-compose exec masjid_screen_php bash

bash_nginx:
	docker-compose exec masjid_screen_nginx bash

restart: stop up

help:
	@echo ""
	@echo "${NOCOLOR}Usage: ${CYAN}make [TARGET] [EXTRA_ARGUMENTS]"
	@echo ""
	@echo "${NOCOLOR}Targets:"
	@echo ""
	@echo "  ${BGREEN}build${YELLOW}             build the containers"
	@echo "  ${BGREEN}up${YELLOW}                Start the containers"
	@echo "  ${BGREEN}stop${YELLOW}              Stop the containers"
	@echo "  ${BGREEN}destroy${YELLOW}           Destroy the containers"
	@echo "  ${BGREEN}composer${YELLOW}          Run composer install"
	@echo "  ${BGREEN}composer_require${YELLOW}  Run composer require [PACKAGE]"
	@echo "  ${BGREEN}bash_php${YELLOW}          Open bash in php container"
	@echo "  ${BGREEN}bash_nginx${YELLOW}        Open bash in nginx container"
	@echo "  ${BGREEN}restart${YELLOW}           Restart the containers"
	@echo ""
	@echo "${NOCOLOR}Examples:"
	@echo ""
	@echo "${BGREEN}up${WHITE}                 ${CYAN}make up"
	@echo "${BGREEN}composer_require${WHITE}   ${CYAN}make composer_require -e PACKAGE=symfony/dotenv"
	@echo ""