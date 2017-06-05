.PHONY: composer coverage cs humbug test

it: cs test

composer:
	rm -rf composer.lock
	composer self-update
	composer validate
	composer install

coverage: composer
	bin/phpunit --configuration test/Unit/phpunit.xml --coverage-text

cs: composer
	bin/php-cs-fixer fix --config=.php_cs --verbose --diff

humbug:
	bin/humbug

test: composer
	bin/phpspec run --config phpspec.yml
	bin/phpunit --configuration=test/Unit/phpunit.xml
	composer update --prefer-lowest
	bin/phpspec run --config phpspec.yml
	bin/phpunit --configuration=test/Unit/phpunit.xml

