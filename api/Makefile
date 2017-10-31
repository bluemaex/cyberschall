.DEFAULT_GOAL := help

help:
	@echo "API Make Tasks"
	@cat $(MAKEFILE_LIST) | grep -e "^[a-zA-Z_\-]*: *.*## *" | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-30s\033[0m %s\n", $$1, $$2}'
	@echo
	@echo "API ./bin/console Commands"
	@./bin/console list

install: composer ## Setup this project
	@if test -z "$${TRAVIS}"; then $(MAKE) create-sqlite; fi

test: phpunit ## Run all the unit tests

ci: phpcs phpunit ## Run checks for continuous integration

composer: ## Install php dependencies
	@./composer.phar validate
	./composer.phar install --prefer-dist --optimize-autoloader

phpunit: ## Run test suite
	./vendor/bin/phpunit

phpcs: ## Run style checks
	./vendor/bin/phpcs --colors --standard=PSR2 src test
	@if test -z "$$TRAVIS"; then\
		echo Running locally, attempt to fix style issues ...;\
		./vendor/bin/php-cs-fixer fix;\
		./vendor/bin/phpcbf --colors --standard=PSR2 --tab-width=4 src test;\
	else\
		./vendor/bin/php-cs-fixer fix --dry-run --diff --verbose;\
	fi
	./vendor/bin/phpmd src text unusedcode
	./vendor/bin/phpmd test text unusedcode

create-sqlite: ## create a SQL database with demo data
	mkdir -p var && touch var/cyberschall.sqlite
	./bin/console orm:schema-tool:update --force
	./bin/console setup:demo-user
