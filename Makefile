.PHONY: build
build:
	docker-compose build

.PHONY: spec
spec:
	docker-compose run gaufrette phpspec run

.PHONY: behat
behat:
	docker-compose run gaufrette behat

.PHONY: test
test:
	make build spec behat
