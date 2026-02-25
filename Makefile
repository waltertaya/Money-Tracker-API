IMAGE_NAME ?= money-tracker-api
CONTAINER_NAME ?= money-tracker-api
PORT ?= 8000

.PHONY: build run up stop down logs shell migrate test restart

build:
	docker build -t $(IMAGE_NAME) .

run:
	docker run -d --name $(CONTAINER_NAME) -p $(PORT):80 --env-file .env $(IMAGE_NAME)

up: build
	-docker rm -f $(CONTAINER_NAME)
	docker run -d --name $(CONTAINER_NAME) -p $(PORT):80 --env-file .env $(IMAGE_NAME)

stop:
	-docker stop $(CONTAINER_NAME)

down:
	-docker rm -f $(CONTAINER_NAME)

restart:
	$(MAKE) down
	$(MAKE) up

logs:
	docker logs -f $(CONTAINER_NAME)

shell:
	docker exec -it $(CONTAINER_NAME) bash

migrate:
	docker exec -it $(CONTAINER_NAME) php artisan migrate --force

test:
	docker exec -it $(CONTAINER_NAME) php artisan test
