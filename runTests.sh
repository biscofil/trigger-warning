set -e

cp .env.example .env
composer install
php artisan key:generate
php artisan config:cache
touch db.sqlite
php artisan migrate
php artisan db:seed
vendor/bin/phpunit --configuration phpunit_cicd.xml --coverage-text --colors=never