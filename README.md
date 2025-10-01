# MPI-001 Employment-Interest-Tool

##### First run:
1. npm install
2. npm audit fix
3. npm run build
4. composer run dev

##### From then on:
1. composer run dev

### Linux:
##### Fedora packages
- `dnf install composer`
- `dnf install php-pdo`
- `dnf install php-mysqlnd`

##### First run:
- `cd code`
- `composer install`
- `npm install`
- `npm audit fix`
- `npm run build`
- `cp .env.example .env`
- Docker compose start: `./vendor/bin/sail up`
- Generate key: `./vendor/bin/sail artisan key:generate`
- Migrate database: `./vendor/bin/sail artisan migrate`
- Seed database: `./vendor/bin/sail artisan db:seed`

##### From then on:
1. Docker compose start: `./vendor/bin/sail up`
