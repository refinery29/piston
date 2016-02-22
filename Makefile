it: cs test

composer:
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
