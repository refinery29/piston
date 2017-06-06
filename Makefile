.PHONY: composer coverage cs humbug spec test unit

it: cs test

composer:
	composer self-update
	composer validate
	composer install

coverage: composer
	bin/phpunit --configuration test/Unit/phpunit.xml --coverage-text

cs: composer
	bin/php-cs-fixer fix --config=.php_cs --verbose --diff

spec: composer
	bin/phpspec run --config phpspec.yml

test: spec unit

unit: composer
	bin/phpunit --configuration=test/Unit/phpunit.xml

humbug:
	bin/humbug
