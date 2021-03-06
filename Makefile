.DEFAULT_GOAL := help
APP_DIR=$(shell basename `pwd`)

help: ## Helping devs since 2016
	@echo "Cyberschall Tasks"
	@cat $(MAKEFILE_LIST) | grep -e "^[a-zA-Z_\-]*: *.*## *" | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-30s\033[0m %s\n", $$1, $$2}'
	@echo
	@$(MAKE) api-help
	@echo
	@$(MAKE) web-help

install: api-install web-install

outdated: api-outdated web-outdated

ci: api-ci web-ci

test: api-test web-test

ssh: ssh-api

sync: sync-api

# Multitarget rules

api-%:
	@$(MAKE) -C ./api $*

web-%:
	@$(MAKE) -C ./web $*

ssh-%:
	docker exec -it $(shell docker ps -qf name="^/$(APP_DIR).$*.{0,2}$$") sh

sync-%:
	@DOCKER_ID=$(shell docker ps -qf name="^/$(APP_DIR).$*.{0,2}$$") &&\
	echo syncing local changes to $(APP_DIR)-$* aka $${DOCKER_ID} &&\
	cd $* &&\
	fswatch -0 . | while read -d "" event ; do docker -D cp "$${event}" "$${DOCKER_ID}:/var/www/$${event#`pwd`}" ; done;
