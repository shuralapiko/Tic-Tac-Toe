### Requirements
- php ^7.3
- composer
- node / npm (frontend part)


### Installation
After project cloned go to root, then:
1. `cp .env.example .env`
2. Create new schema in database and change database credentials in .env file to your
3. `composer install`
4. `php artisan key:generate`
5. `php artisan migrate`
6. `npm install`
7. `npm run prod`


### Start localy
php artisan serve
