# CURRICULUM.md — план курсу та прогрес

Це єдине джерело правди щодо порядку тем і прогресу. Онови чекбокс у тому ж коміті,
де закінчив тему (`git commit -m "topic 07: functions — done"`).

## Рішення, які вже прийняті (не переглядати без причини)

- **Аудиторія**: студент вже вміє програмувати іншою мовою (JS/Python/Java/...).
  Базові поняття (що таке змінна, цикл) не пояснюються — фокус на специфіці PHP.
- **Мова**: проза українською. Технічні терміни — англійською з перекладом
  у дужках при першій появі, напр. **Traits (трейти)**. Код — англійські ідентифікатори.
- **PHP 8.3+**, `declare(strict_types=1)` скрізь.
- **Обсяг**: full professional track — мова, веб, БД, professional practices.
- **44 теми + 5 checkpoint-проєктів** (49 папок), inline нумерація без окремої
  папки `projects/`.
- **Файли теми**: `lecture.md`, `task.md`, `starter.php`, `check.php`, `solution.php`.
  Веб/БД-теми додатково можуть мати `public/`, `templates/`, `data/`.
- **Перевірка**: власна легка бібліотека ассертів `_lib/check.php` (без залежностей)
  для тем 01–43; починаючи з теми 44 (PHPUnit) — реальний PHPUnit, `check.php`
  стає тонким запускачем `vendor/bin/phpunit`. Команда завжди одна: `php check.php`.
- **Toolchain**: нативний PHP 8.3 (apt: `php8.3-cli php8.3-mbstring php8.3-xml
  php8.3-mysql php8.3-intl php8.3-curl php8.3-sqlite3`) + Composer. Репозиторій
  `phpcourse/` — власний git-репозиторій (не залежить від `~/projects`).
- **БД для перевірок**: реальний MySQL 8 через `docker-compose` (`_env/`).
  Якщо БД не піднята — `check.php` для цих тем виводить `skip()` з інструкцією
  замість провалу.
- **Git-дисципліна**: студент редагує `starter.php` на місці й комітить після
  кожної теми. `solution.php` відкривається тільки після зеленого `check.php`
  або вичерпання всіх трьох підказок у `task.md`.
- **Темп генерації курсу**: block by block. Після кожного блоку — пауза на ревʼю
  тону/глибини/стилю перед наступним.

## Прогрес генерації курсу (для мене/AI — не для студента)

- [x] Інфраструктура: `_lib/check.php`, `_env/`, `README.md`, `GLOSSARY.md`, `.gitignore`
- [x] Block A — Фундамент мови (01–10) + `11-checkpoint-1-cli-sales-report`
- [ ] Block B — ООП (12–21)
- [ ] Block C — Стандартна бібліотека (22–26) + `27-checkpoint-2-task-manager-cli`
- [ ] Block D — Веб-фундамент (28–33) + `34-checkpoint-3-guestbook-web-app`
- [ ] Block E — Дані та інструменти (35–39) + `40-checkpoint-4-crud-web-app`
- [ ] Block F — Професійні практики (41–48) + `49-checkpoint-5-rest-api`

## Повний список тем

### Block A — Мова: фундамент (01–10)

| # | Папка | Тема |
|---|---|---|
| 01 | `01-intro-history-and-how-php-runs` | Історія PHP, філософія, де живе сьогодні, як виконується запит, SAPI |
| 02 | `02-syntax-variables-and-types` | Теги, echo/print, константи, strict_types, скалярні типи |
| 03 | `03-type-juggling-and-comparison` | Коерція типів, ==/===/<=>, truthiness, isset/empty |
| 04 | `04-operators-and-expressions` | Арифметика, intdiv, конкатенація, ??=, пріоритет операторів |
| 05 | `05-strings-and-encoding` | Лапки, heredoc/nowdoc, str_* функції, mb_* та UTF-8 |
| 06 | `06-control-flow-and-match` | if/switch/match, foreach за значенням/посиланням, break N |
| 07 | `07-functions-and-type-declarations` | Типи параметрів/повернення, strict_types, передача за посиланням |
| 08 | `08-closures-callables-and-arguments` | Variadic, named args, arrow fn, Closure/use, first-class callable |
| 09 | `09-arrays-and-destructuring` | Масив як хеш-мапа, value semantics, list()/[] destructuring |
| 10 | `10-array-functions` | array_map/filter/reduce, usort, array_column/combine/slice |
| 11 | `11-checkpoint-1-cli-sales-report` | **CP1**: CLI-звіт по продажах — функції, масиви, рядки, $argv |

### Block B — ООП (12–21)

| # | Папка | Тема |
|---|---|---|
| 12 | `12-classes-and-objects` | class/new/$this, видимість, конструктор/деструктор, nullsafe |
| 13 | `13-promotion-readonly-and-cloning` | Constructor promotion, readonly, clone/__clone |
| 14 | `14-static-constants-and-late-static-binding` | static, self vs static vs parent, LSB, ::class, final |
| 15 | `15-inheritance-and-abstract-classes` | extends, parent::, abstract, коваріантність |
| 16 | `16-interfaces-and-polymorphism` | interface, union/intersection типи, Stringable, Countable |
| 17 | `17-traits` | insteadof/as, абстрактні/статичні члени трейтів |
| 18 | `18-enums` | Pure/backed enums, cases()/from()/tryFrom(), match |
| 19 | `19-magic-methods-and-object-protocols` | __get/__set/__call/__invoke/__toString, ArrayAccess |
| 20 | `20-namespaces-and-autoloading` | namespace/use/FQN, spl_autoload_register, ручний PSR-4 |
| 21 | `21-errors-and-exceptions` | Throwable/Error/Exception, try/catch/finally, власні винятки |

### Block C — Стандартна бібліотека (22–26)

| # | Папка | Тема |
|---|---|---|
| 22 | `22-iterators-generators-and-spl` | Iterator, yield/yield from, SplStack/SplObjectStorage |
| 23 | `23-date-and-time` | DateTimeImmutable, DateInterval, DateTimeZone |
| 24 | `24-regular-expressions` | preg_match/replace/split, іменовані групи, модифікатор u |
| 25 | `25-json-and-serialization` | json_encode/decode, JSON_THROW_ON_ERROR, JsonSerializable |
| 26 | `26-files-and-streams` | file_get/put_contents, fopen/fgets, SplFileObject, CSV |
| 27 | `27-checkpoint-2-task-manager-cli` | **CP2**: Task manager — readonly DTO, enum, репозиторій, генератор |

### Block D — Веб-фундамент (28–33)

| # | Папка | Тема |
|---|---|---|
| 28 | `28-web-request-lifecycle` | HTTP-цикл, nginx+php-fpm концептуально, php -S, header() |
| 29 | `29-superglobals-and-request-data` | $_GET/$_POST/$_SERVER/$_FILES, php://input |
| 30 | `30-forms-validation-and-uploads` | filter_var, PRG-патерн, $_FILES, finfo |
| 31 | `31-cookies-and-sessions` | setcookie, session_start, session_regenerate_id |
| 32 | `32-authentication-and-passwords` | password_hash/verify, hash_equals, потік логіну |
| 33 | `33-web-security-essentials` | XSS, CSRF, SQLi (прев'ю), mass assignment, CSP |
| 34 | `34-checkpoint-3-guestbook-web-app` | **CP3**: Гостьова книга — форма, CSRF, сесії, аутентифікація |

### Block E — Дані та інструменти (35–39)

| # | Папка | Тема |
|---|---|---|
| 35 | `35-databases-and-sql-essentials` | MySQL через docker-compose, SELECT/JOIN/INSERT/UPDATE |
| 36 | `36-pdo-connections-and-queries` | DSN, ERRMODE_EXCEPTION, FETCH_CLASS, lastInsertId |
| 37 | `37-prepared-statements-crud-transactions` | Плейсхолдери, транзакції, N+1, пагінація |
| 38 | `38-composer-and-psr4` | composer init/require, autoload.psr-4, semver |
| 39 | `39-psr-standards-and-code-quality` | PSR-1/12, PSR-4, PHPStan/Psalm, php-cs-fixer |
| 40 | `40-checkpoint-4-crud-web-app` | **CP4**: CRUD-каталог — Composer, PDO-репозиторій, міграції |

### Block F — Професійні практики (41–48)

| # | Папка | Тема |
|---|---|---|
| 41 | `41-dependency-injection-and-containers` | DI vs Service Locator, PSR-11, простий контейнер |
| 42 | `42-design-patterns-part-1` | Factory, Builder, Singleton (і чому обережно), Decorator |
| 43 | `43-design-patterns-part-2` | Strategy, Observer, Repository + Service layer |
| 44 | `44-testing-with-phpunit` | PHPUnit, assertions, data providers, test doubles |
| 45 | `45-rest-api-with-plain-php` | Роутер, коди статусу, JSON, content negotiation |
| 46 | `46-api-auth-errors-and-logging` | Bearer-токени, JWT концептуально, PSR-3 логування |
| 47 | `47-frameworks-and-ecosystem` | Laravel/Symfony/Slim vs plain PHP, WordPress |
| 48 | `48-deployment-and-performance` | php-fpm, OPcache/JIT, Docker, health-check |
| 49 | `49-checkpoint-5-rest-api` | **CP5**: REST API — DI-контейнер, токен-auth, PHPUnit, Docker |

## Optional merges (не рекомендовано, але можливо)

Якщо захочеться стиснути 44→40 тем: `03`+`04`, `42`+`43`, `45`+`46`, `47`+`48`.
