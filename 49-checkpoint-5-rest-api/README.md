# Checkpoint 5: REST API закладок

П'ятий і останній checkpoint курсу. Мета — зібрати докупи весь блок F
(професійні практики: теми 41-48) у невеликий, але реалістичний REST
API: DI-контейнер, токен-автентифікація, PHPUnit-тести, Docker.

## Що будуємо

REST API для керування закладками (bookmarks): `GET /bookmarks`,
`GET /bookmarks/{id}`, `POST /bookmarks` (потребує Bearer-токен),
`DELETE /bookmarks/{id}` (потребує Bearer-токен). Дані — у пам'яті
(без БД — фокус теми на DI/auth/тестах/Docker, не на PDO).

## Структура

```
49-checkpoint-5-rest-api/
  README.md          # цей файл
  task.md             # вимоги, контракт, як перевірити
  starter/
    composer.json         # готовий, phpunit + psr-4 "App\\": "src/"
    phpunit.xml
    Dockerfile              # готовий — контейнеризація застосунку
    src/
      Bookmark.php             # ТУТ ти пишеш код — readonly DTO
      InMemoryBookmarkRepository.php  # ТУТ ти пишеш код — репозиторій
      Container.php               # ТУТ ти пишеш код — DI-контейнер
      Api.php                        # ТУТ ти пишеш код — REST-обробники
      BookmarkRepository.php           # готовий — інтерфейс
      Router.php                        # готовий — роутер з теми 45
      functions.php                      # готовий — auth/response з теми 46
    tests/                                # готові PHPUnit-тести
    public/
      index.php                            # готовий front controller
  solution/
    ...                                     # той самий набір, повністю реалізований
  check.php                                  # автоматична перевірка src/*.php
```

## Як працювати

1. Прочитай `task.md`.
2. `cd starter && composer install && cd ..`
3. Редагуй `starter/src/Bookmark.php`, `InMemoryBookmarkRepository.php`,
   `Container.php`, `Api.php`.
4. Перевіряй: `php check.php`.
5. Коли `check.php` зелений:
   ```bash
   php -S localhost:8000 -t starter/public
   ```
   або через Docker:
   ```bash
   cd starter && docker build -t checkpoint5 . && docker run -p 8000:8000 checkpoint5
   ```
6. Попрацюй з API через `curl` (приклади в `task.md`).
7. Порівняй з `solution/`, заціни чекбокс у `CURRICULUM.md`, зроби коміт.

Цим завершується весь курс — вітаємо! Подивись на `CURRICULUM.md`:
якщо всі 49 чекбоксів позначені — пройдено все, від `<?php` до Docker.
