.DEFAULT_GOAL := help

help:
	@echo "Web Make Tasks"
	@cat $(MAKEFILE_LIST) | grep -e "^[a-zA-Z_\-]*: *.*## *" | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-30s\033[0m %s\n", $$1, $$2}'
	@echo
	@echo "Web ./bin/console Commands"
	@npm run

install: ## Setup this project
	yarn

clean: ## clean up previous build
	rm -rf build

ci: ## Run checks for continuous integration
	@if test -z "$${TRAVIS}"; then $(MAKE) prettier lint-fix; else $(MAKE) lint; fi

build: clean install ## Do a production optimized build
	yarn build

.DEFAULT: ## proxy every unknown target to npm/yarn
	yarn $@
