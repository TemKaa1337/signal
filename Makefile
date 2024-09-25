.PHONY: setup snapshot tests

up:
	@docker compose up -d --build

down:
	@docker compose down

cli:
	@docker compose exec php zsh

tests:
	@docker compose exec php php vendor/bin/phpunit --testsuite integrations
