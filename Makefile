SHELL := /usr/bin/env bash

.PHONY: test
test: phpunit phpcs

.PHONY: phpcs
phpcs:
	./vendor/bin/php-cs-fixer fix --dry-run

.PHONY: phpunit
phpunit:
	./vendor/bin/phpunit
