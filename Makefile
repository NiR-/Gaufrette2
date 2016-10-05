.PHONY: build spec behat test

build:
	docker-compose build

spec:
	docker-compose run gaufrette phpspec run

behat:
	docker-compose run gaufrette behat

test: build spec behat
