# Checkpoint 5: вимоги

## Підготовка

```bash
cd 49-checkpoint-5-rest-api/starter && composer install && cd ..
```

## Мета

Зібрати в одному проєкті весь блок F: DI-контейнер (тема 41),
токен-автентифікація й уніфіковані помилки (тема 46), реальний PHPUnit
(тема 44), REST-роутер (тема 45), Docker (тема 48).

## Що дано (готове, не редагуй)

- `src/Router.php` — роутер з темою 45 (реєстрація маршрутів, параметри
  шляху, `404`/`405`).
- `src/functions.php` — `extract_bearer_token()`, `authenticate()`,
  `json_response()`, `error_response()` з теми 46.
- `src/BookmarkRepository.php` — інтерфейс репозиторію закладок.
- `public/index.php` — front controller: піднімає `Container`,
  реєструє маршрути, маршрутизує реальний HTTP-запит.
- `Dockerfile` — контейнеризація застосунку (`php -S` усередині
  контейнера — навчальний приклад, не продакшн-конфігурація).

## Що робимо

Реалізуємо чотири класи в `starter/src/`: `Bookmark`,
`InMemoryBookmarkRepository`, `Container`, `Api`.

## Вимоги

1. `readonly`-клас `Bookmark` через constructor promotion: `int $id`,
   `string $title`, `string $url`; `toArray(): array` — `['id' => ...,
   'title' => ..., 'url' => ...]`.

2. `InMemoryBookmarkRepository implements BookmarkRepository`:
   - `all(): array` — усі закладки, відсортовані за `id`;
   - `find(int $id): ?Bookmark`;
   - `add(string $title, string $url): Bookmark` — створює закладку з
     новим `id` (`max(id) + 1`, чи `1` для порожнього репозиторію),
     зберігає й повертає її;
   - `delete(int $id): bool` — `true`, якщо закладку знайдено й
     видалено, інакше `false`.

3. `Container` (DI-контейнер, як у темі 41):
   - `set(string $id, \Closure $factory): void`;
   - `get(string $id): mixed` — лінива побудова, той самий екземпляр
     при повторному виклику; кидає `\OutOfBoundsException` для
     незареєстрованого `$id`;
   - `has(string $id): bool`.

4. `Api`:
   - `__construct(BookmarkRepository $repository, array $validTokens)`
     (`$validTokens` — `[токен => userId]`);
   - `list(array $params, array $server, array $body): array` — `200`,
     усі закладки;
   - `show(array $params, array $server, array $body): array` —
     `200` із закладкою за `$params['id']`, або `404`, якщо не знайдено;
   - `create(array $params, array $server, array $body): array` —
     вимагає дійсний Bearer-токен (`401`, якщо ні); вимагає непорожні
     `$body['title']` і `$body['url']` (`400`, якщо чогось немає);
     інакше створює закладку через репозиторій і повертає `201`;
   - `delete(array $params, array $server, array $body): array` —
     вимагає дійсний Bearer-токен (`401`, якщо ні); `200` з `{"deleted":
     true}` при успіху, `404`, якщо закладки з таким `id` не існує.

## Контракт (сигнатури)

```php
namespace App;

final class Bookmark
{
    public function __construct(
        public readonly int $id,
        public readonly string $title,
        public readonly string $url,
    )
    public function toArray(): array
}

final class InMemoryBookmarkRepository implements BookmarkRepository
{
    public function all(): array
    public function find(int $id): ?Bookmark
    public function add(string $title, string $url): Bookmark
    public function delete(int $id): bool
}

final class Container
{
    public function set(string $id, \Closure $factory): void
    public function get(string $id): mixed
    public function has(string $id): bool
}

final class Api
{
    public function __construct(BookmarkRepository $repository, array $validTokens)
    public function list(array $params, array $server, array $body): array
    public function show(array $params, array $server, array $body): array
    public function create(array $params, array $server, array $body): array
    public function delete(array $params, array $server, array $body): array
}
```

## Обмеження

- Кожен файл починається з `declare(strict_types=1);`, одразу після —
  `namespace App;`.
- `Api::create()`/`Api::delete()` перевіряють токен через
  `authenticate()` з `src/functions.php` — не пишуть власну перевірку
  заголовка.
- Усі відповіді `Api`-методів побудовані через `json_response()`/
  `error_response()`, не вручну.

## Як перевірити

```bash
php check.php
```

Після зеленого `check.php` — реальний сервер:

```bash
php -S localhost:8000 -t starter/public
curl http://localhost:8000/bookmarks
curl -X POST http://localhost:8000/bookmarks -H "Authorization: Bearer demo-token" \
  -d '{"title":"PHP.net","url":"https://php.net"}' -H "Content-Type: application/json"
curl -X POST http://localhost:8000/bookmarks \
  -d '{"title":"PHP.net","url":"https://php.net"}' -H "Content-Type: application/json"  # без токена -> 401
curl http://localhost:8000/bookmarks/999  # -> 404
```

**Важливо:** `InMemoryBookmarkRepository` живе лише в пам'яті одного
процесу, а `php -S` (як і `php-fpm`) створює **новий** процес на кожен
запит (тема 28, shared-nothing) — тому щойно доданий через `POST`
запис **не** з'явиться в наступному окремому `curl`-виклику `GET
/bookmarks`. Це не баг, а прямий наслідок теми 28: перевіряй кожен
маршрут окремим запитом (як у прикладах вище), не розраховуючи, що
дані переживуть між ними. Реальна персистенція між запитами — це PDO
(теми 36-37) чи файл (тема 26), як у checkpoint 4.

Чи через Docker:

```bash
cd starter && docker build -t checkpoint5 . && docker run -p 8000:8000 checkpoint5
```

## Definition of Done

- [ ] `php check.php` (PHPUnit) зелений
- [ ] реальний сервер (напряму чи через Docker) коректно відповідає на
      кожен маршрут окремим запитом: `200` зі списком, `201` при
      створенні з токеном, `401` без токена, `404` для неіснуючої закладки
- [ ] можеш пояснити, де саме в `public/index.php` використовується
      `Container`, і чому `Api` не створює `InMemoryBookmarkRepository`
      сам усередині себе
- [ ] можеш пояснити, чому закладка, додана одним `curl`-запитом, не
      з'являється в списку при наступному окремому запиті під `php -S`

## Підказки

<details>
<summary>Підказка 1 — Bookmark</summary>

```php
final class Bookmark
{
    public function __construct(
        public readonly int $id,
        public readonly string $title,
        public readonly string $url,
    ) {
    }

    public function toArray(): array
    {
        return ['id' => $this->id, 'title' => $this->title, 'url' => $this->url];
    }
}
```
</details>

<details>
<summary>Підказка 2 — InMemoryBookmarkRepository::add()</summary>

```php
public function add(string $title, string $url): Bookmark
{
    $id = $this->bookmarks === [] ? 1 : max(array_map(fn (Bookmark $b) => $b->id, $this->bookmarks)) + 1;
    $bookmark = new Bookmark($id, $title, $url);
    $this->bookmarks[] = $bookmark;

    return $bookmark;
}
```
</details>

<details>
<summary>Підказка 3 — Api::create()</summary>

```php
public function create(array $params, array $server, array $body): array
{
    if (authenticate($server, $this->validTokens) === null) {
        return error_response('Не авторизовано', 401);
    }
    if (empty($body['title']) || empty($body['url'])) {
        return error_response('Потрібні поля title і url', 400);
    }

    $bookmark = $this->repository->add($body['title'], $body['url']);

    return json_response($bookmark->toArray(), 201);
}
```
</details>
