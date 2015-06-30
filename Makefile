cs:
	vendor/bin/php-cs-fixer fix --config-file=.php_cs --verbose --diff

test:
	vendor/bin/phpspec run
cs:
	vendor/bin/php-cs-fixer fix --config-file=.php_cs --verbose --diff