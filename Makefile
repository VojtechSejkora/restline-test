cache:
	rm -rf temp/*

xdebug-on: ## Enable XDebug
	./bin/dc_exec_php.sh "bin/toggle_xdebug.sh --on"
	docker-compose stop
	docker-compose start
	@echo

xdebug-off: ## Disable XDebug
	./bin/dc_exec_php.sh "bin/toggle_xdebug.sh --off"
	docker-compose stop
	docker-compose start
	@echo

database:
	cp db/seeds/* db
