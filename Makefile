.PHONY: composer coverage cs test

it: cs test

composer:
	rm -rf composer.lock
	composer self-update
	composer validate
	composer install

coverage: composer
	vendor/bin/phpunit --configuration test/Unit/phpunit.xml --coverage-text

cs: composer
	vendor/bin/php-cs-fixer fix --config=.php_cs --verbose --diff

test: composer
	vendor/bin/phpspec run --config phpspec.yml
	vendor/bin/phpunit --configuration=test/Unit/phpunit.xml
	composer update --prefer-lowest
	vendor/bin/phpspec run --config phpspec.yml
	vendor/bin/phpunit --configuration=test/Unit/phpunit.xml

