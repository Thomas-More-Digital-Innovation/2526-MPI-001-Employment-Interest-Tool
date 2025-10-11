- docker compose -f compose.test.prod.yml up --build -d
- docker compose -f compose.test.prod.yml exec app sh
    - php artisan db:seed

openssl req -x509 -newkey rsa:2048 -nodes -days 365 \
  -keyout /etc/laravel/ssl/server.key -out /etc/laravel/ssl/server.crt \
  -subj "/CN=mpi.stijnrombouts.be"
