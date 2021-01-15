#!/usr/bin/make

install:
	docker run --rm -v $(shell pwd):/opt -w /opt -u $(shell id -u) laravelsail/php80-composer:latest composer install
	stat .env || cp .env.example .env

lint:
	./vendor/bin/sail composer phpcs
	./vendor/bin/sail composer phpstan

test:
	./vendor/bin/sail artisan test
