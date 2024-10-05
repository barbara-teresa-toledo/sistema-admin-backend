web: vendor/bin/heroku-php-apache2 public/
release:composer install && php artisan migrate --force && php artisan l5-swagger:generate
