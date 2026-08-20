# Завдання 47: фреймворки та екосистема

## Підготовка

```bash
cd 47-frameworks-and-ecosystem/starter && composer install && cd ..
```

## Мета

Легша, оглядова тема — жодної нової PHP-механіки. Вправа — навчитись
**впізнавати** архітектурний шар файлу за угодженнями іменування,
типовими для MVC-фреймворків (Laravel/Symfony-подібні угоди) — корисний
навик, коли відкриваєш незнайомий проєкт.

Тести вже написані в `tests/` — твоя робота лише в `src/functions.php`.

## Вимоги

1. `classify_file(string $path): string` — визначає шар за шляхом:
   - `'controller'` — шлях містить `/Controllers/` або файл закінчується
     на `Controller.php`;
   - `'model'` — шлях містить `/Models/` або файл закінчується на
     `Model.php`;
   - `'view'` — шлях містить `/Views/`, або файл закінчується на
     `.blade.php` чи `.twig`;
   - `'config'` — шлях починається з `config/`, містить `/config/`, або
     файл закінчується на `.env`;
   - `'public'` — шлях починається з `public/`;
   - `'unknown'` — жодне з вищого не підійшло.
   Перевіряй умови саме в цьому порядку (шлях може технічно збігтись
   з кількома правилами — перше, що підходить, виграє).
2. `count_files_by_layer(array $paths): array` — застосовує
   `classify_file()` до кожного шляху й повертає асоціативний масив
   `['шар' => кількість]` лише для шарів, що реально зустрілись (без
   нульових лічильників).

## Контракт (сигнатури)

```php
namespace App;

function classify_file(string $path): string
function count_files_by_layer(array $paths): array
```

## Обмеження

- Файл починається з `declare(strict_types=1);`, одразу після —
  `namespace App;`.
- Перевірки в `classify_file()` виконуються в порядку зі списку вимог
  1 — не як `match` без урахування пріоритету.

## Як перевірити

```bash
php check.php
```

## Definition of Done

- [ ] `php check.php` (PHPUnit) зелений
- [ ] можеш навести приклад проєкту, де WordPress — правильний вибір,
      і приклад, де це радше помилка

## Бонус (не перевіряється автоматично)

Додай ще один шар — `'test'` (шлях містить `/tests/` чи `/Tests/`,
або файл закінчується на `Test.php`) — постав цю перевірку **першою**
в `classify_file()` (файл тесту контролера не повинен класифікуватись
як `'controller'`).

<details>
<summary>Підказка 1</summary>

```php
function classify_file(string $path): string
{
    if (str_contains($path, '/Controllers/') || str_ends_with($path, 'Controller.php')) {
        return 'controller';
    }
    if (str_contains($path, '/Models/') || str_ends_with($path, 'Model.php')) {
        return 'model';
    }
    if (str_contains($path, '/Views/') || str_ends_with($path, '.blade.php') || str_ends_with($path, '.twig')) {
        return 'view';
    }
    if (str_starts_with($path, 'config/') || str_contains($path, '/config/') || str_ends_with($path, '.env')) {
        return 'config';
    }
    if (str_starts_with($path, 'public/')) {
        return 'public';
    }

    return 'unknown';
}
```
</details>

<details>
<summary>Підказка 2</summary>

```php
function count_files_by_layer(array $paths): array
{
    $counts = [];
    foreach ($paths as $path) {
        $layer = classify_file($path);
        $counts[$layer] = ($counts[$layer] ?? 0) + 1;
    }

    return $counts;
}
```
</details>
