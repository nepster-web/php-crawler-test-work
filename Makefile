#############################################################################
#
# This is an easily customizable makefile template. The purpose is to
# provide an instant building environment for docker.
#
# Usage:
# ------
# $ make start         - run docker containers
# $ make restart       - restart docker containers
# $ make stop          - stop docker containers
#
#===========================================================================
## Variables
##==========================================================================

BASH_DOCKER_COMPOSE_RUN_WITHOUT_TTL = docker-compose exec -T php-cli /bin/bash
COMMAND_TESTS = /www/vendor/bin/phpunit


## make start
##==========================================================================
start:
	make chmod-permissions
	docker-compose up -d --build
	make chmod-permissions
	make composer-install


## make restart
##==========================================================================
restart:
	make stop
	make start


## make stop
##==========================================================================
stop:
	docker-compose down


## make cwr
##==========================================================================
cwr:
	$(BASH_DOCKER_COMPOSE_RUN_WITHOUT_TTL)  -c "php ./cwr $(ARGS)"


## make tests
##==========================================================================
tests:
	$(BASH_DOCKER_COMPOSE_RUN_WITHOUT_TTL) -c "$(COMMAND_TESTS)"


## make composer-install
##==========================================================================
composer-install:
	$(BASH_DOCKER_COMPOSE_RUN_WITHOUT_TTL) -c "composer install"


## make composer-update
##==========================================================================
composer-update:
	$(BASH_DOCKER_COMPOSE_RUN_WITHOUT_TTL) -c "composer update"


## make chmod-permissions
##==========================================================================
chmod-permissions:
	sudo chmod -R 777 www/reports
