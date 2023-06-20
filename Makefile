PROJECT = subsquad
COMPOSE_FILE = docker-compose.yml

.PHONY: start
start:
	docker-compose -f $(COMPOSE_FILE) -p $(PROJECT) up -d --build

.PHONY: logs
logs:
	docker-compose -f $(COMPOSE_FILE) -p $(PROJECT) logs

.PHONY: exec
exec:
	docker exec -it ${name} /bin/bash

.PHONY: restart
restart:
	docker-compose -f $(COMPOSE_FILE) -p $(PROJECT) kill && \
	docker-compose -f $(COMPOSE_FILE) -p $(PROJECT) rm -f && \
	docker-compose -f $(COMPOSE_FILE) -p $(PROJECT) up -d --build

.PHONY: reset
reset:
	rm -rf infrastructure/postgresql/data/* && \
	docker-compose -f $(COMPOSE_FILE) -p $(PROJECT) kill && \
	docker-compose -f $(COMPOSE_FILE) -p $(PROJECT) rm -f && \
	docker-compose -f $(COMPOSE_FILE) -p $(PROJECT) up -d --build

.PHONY: kill
kill:
	docker-compose -f $(COMPOSE_FILE) -p $(PROJECT) kill

.PHONY: ps
ps:
	docker-compose -f $(COMPOSE_FILE) -p $(PROJECT) ps

.PHONY: laravel
laravel:
	docker exec -it ${PROJECT}_workspace /bin/bash

.PHONY: setup
setup:
	chmod -R 777 storage/ bootstrap/ vendor/ && \
	cp .env.example .env && \
	php artisan key:generate && \
	npm run dev
	sed -i -e 's/DB_CONNECTION=.*/DB_CONNECTION=mysql/' .env
	sed -i -e 's/DB_HOST=.*/DB_HOST=withLive_mysql/' .env
	sed -i -e 's/DB_DATABASE=.*/DB_DATABASE=subsquad/' .env
	sed -i -e 's/DB_USERNAME=.*/DB_USERNAME=laravel_app/' .env
	sed -i -e 's/DB_PASSWORD=.*/DB_PASSWORD=secret/' .env
	php artisan migrate
	php artisan db:seed

.PHONY: install
install:
	composer install && \
	npm install
