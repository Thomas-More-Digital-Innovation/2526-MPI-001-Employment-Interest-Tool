# MPI-001 Employment-Interest-Tool

##### First run:
1. npm install
2. npm audit fix
3. npm run build
4. composer run dev

##### From then on:
1. composer run dev

### Linux:
#### Fedora packages
dnf install composer
dnf install php-pdo
dnf install php-mysqlnd

#### General
`composer install`
`npm install`
`npm audit fix`

Docker compose start: `./vendor/bin/sail up`
Generate key: `./vendor/bin/sail artisan key:generate`
Migrate database: `./vendor/bin/sail artisan migrate`
Seed database: `./vendor/bin/sail artisan db:seed`
