SHELL := /usr/bin/env bash

.PHONY: test
test: phpunit phpcs

.PHONY: phpcs
phpcs:
	PHP_CS_FIXER_IGNORE_ENV=true ./vendor/bin/php-cs-fixer fix --dry-run

.PHONY: phpunit
phpunit:
	./vendor/bin/phpunit
