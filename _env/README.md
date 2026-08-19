# _env — оточення для курсу

## PHP 8.3+

```bash
sudo apt-get update
sudo apt-get install -y php8.3-cli php8.3-mbstring php8.3-xml php8.3-mysql php8.3-intl php8.3-curl php8.3-sqlite3
php -v
```

## Composer

Потрібен з теми `38-composer-and-psr4` і далі.

```bash
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
php composer-setup.php --install-dir=/usr/local/bin --filename=composer
php -r "unlink('composer-setup.php');"
composer --version
```

## MySQL (для тем блоку E/F, з теми `35-databases-and-sql-essentials`)

```bash
docker compose -f _env/docker-compose.yml up -d
```

Це піднімає MySQL 8.4 на `localhost:3306` (база `phpcourse`, юзер/пароль `phpcourse`/`phpcourse`)
та Adminer на `localhost:8080` для перегляду БД у браузері.

Скопіюй `_env/config.example.php` у `_env/config.local.php` (уже в `.gitignore`) —
`check.php` для тем із БД читає підключення звідти. Якщо MySQL не піднятий,
`check.php` не провалиться, а виведе `[skip]` з підказкою підняти контейнер.

Зупинити: `docker compose -f _env/docker-compose.yml down` (додай `-v`, щоб стерти дані).
