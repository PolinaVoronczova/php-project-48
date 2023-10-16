install:
	composer install
lint:
	composer exec --verbose phpcs -- --standard=PSR12 src bin
test:
	composer exec --verbose phpunit tests
test-coverage:
	php -d xdebug.mode=coverage vendor/phpunit/phpunit/phpunit ./tests/genDiffTest.php  --coverage-clover ./tests/coverage/coverage.xml