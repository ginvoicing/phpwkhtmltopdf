test:
	docker-compose exec php /usr/local/bin/php /app/vendor/bin/phpunit
ssh:
	docker-compose exec php /bin/bash	