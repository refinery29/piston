it: cs test

composer:
	composer install

coverage: composer
	bin/phpspec run --config phpspec.with-coverage.yml

cs: composer
	bin/php-cs-fixer fix --config=.php_cs --verbose --diff

spec: composer
	bin/phpspec run --config phpspec.yml

test: spec unit

unit: composer
	bin/phpunit --configuration=test/Unit/phpunit.xml
