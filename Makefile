CONTAINER:=timetraveller:1
define CMD_DOCKER_BASE
    docker run -it --rm \
            -e COMPOSER_MEMORY_LIMIT=-1 \
            --user $$(stat -c '%u' .):$$(stat -c '%g' .) \
            -v $$PWD:$$PWD \
            -v /etc/passwd:/etc/passwd \
            -w $$PWD
endef

.PHONY: build
build:
	docker build --no-cache -t $(CONTAINER) .

.PHONY: install
install:
	$(CMD_DOCKER_BASE) $(CONTAINER) composer install

.PHONY: update
update:
	$(CMD_DOCKER_BASE) $(CONTAINER) composer update

.PHONY: shell
shell:
	$(CMD_DOCKER_BASE) $(CONTAINER) sh

.PHONY: test
test:
	$(CMD_DOCKER_BASE) $(CONTAINER) vendor/bin/phpunit

.PHONY: coverage
coverage:
	$(CMD_DOCKER_BASE) -e XDEBUG_MODE=coverage $(CONTAINER) vendor/bin/phpunit --coverage-html coverage
