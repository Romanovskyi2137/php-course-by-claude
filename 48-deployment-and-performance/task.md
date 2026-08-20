# Завдання 48: деплой та продуктивність

## Підготовка

```bash
cd 48-deployment-and-performance/starter && composer install && cd ..
```

## Мета

1. Реалізувати health-check ендпоінт як чисту функцію.
2. Порахувати hit rate OPcache з сирих лічильників.
3. Відформатувати uptime процесу в читабельний вигляд.

Тести вже написані в `tests/` — твоя робота лише в `src/functions.php`.

## Вимоги

1. `health_check(array $checks): array` — `$checks` — асоціативний
   масив `['назва' => callable]`, де кожен `callable(): bool` перевіряє
   одну залежність. Повертає `['status' => 'ok'|'degraded', 'checks' =>
   ['назва' => 'ok'|'fail', ...]]`; `status` — `'ok'`, лише якщо **всі**
   перевірки повернули `true`.
2. `opcache_hit_rate(int $hits, int $misses): float` — відсоток
   влучень (`hits / (hits + misses) * 100`), округлений до 2 знаків
   після крапки; `0.0`, якщо `$hits + $misses === 0` (без ділення на
   нуль).
3. `format_uptime(int $seconds): string` — читабельний час: показує
   **два** найбільші ненульові розряди з `d`(дні)/`h`(години)/
   `m`(хвилини)/`s`(секунди), молодший розряд — `s`, якщо все інше нуль.
   Приклади: `45` → `'45s'`; `125` → `'2m 5s'`; `3725` → `'1h 2m'`;
   `90061` → `'1d 1h'`.

## Контракт (сигнатури)

```php
namespace App;

function health_check(array $checks): array
function opcache_hit_rate(int $hits, int $misses): float
function format_uptime(int $seconds): string
```

## Обмеження

- Файл починається з `declare(strict_types=1);`, одразу після —
  `namespace App;`.
- `health_check()` викликає кожен `callable` з `$checks` **точно один
  раз**.

## Як перевірити

```bash
php check.php
```

## Definition of Done

- [ ] `php check.php` (PHPUnit) зелений
- [ ] можеш пояснити, чому health check не повинен сам робити повний
      round-trip до кожного зовнішнього сервісу на кожен виклик

## Бонус (не перевіряється автоматично)

Додай `health_check_summary(array $result): string` — однорядковий
підсумок на кшталт `'ok (3/3)'` чи `'degraded (2/3)'` з результату
`health_check()`.

<details>
<summary>Підказка 1</summary>

```php
function health_check(array $checks): array
{
    $results = [];
    foreach ($checks as $name => $check) {
        $results[$name] = $check() ? 'ok' : 'fail';
    }

    $allOk = !in_array('fail', $results, true);

    return ['status' => $allOk ? 'ok' : 'degraded', 'checks' => $results];
}
```
</details>

<details>
<summary>Підказка 2</summary>

```php
function opcache_hit_rate(int $hits, int $misses): float
{
    $total = $hits + $misses;
    if ($total === 0) {
        return 0.0;
    }

    return round($hits / $total * 100, 2);
}
```
</details>

<details>
<summary>Підказка 3</summary>

```php
function format_uptime(int $seconds): string
{
    $days = intdiv($seconds, 86400);
    $hours = intdiv($seconds % 86400, 3600);
    $minutes = intdiv($seconds % 3600, 60);
    $secs = $seconds % 60;

    if ($days > 0) {
        return "{$days}d {$hours}h";
    }
    if ($hours > 0) {
        return "{$hours}h {$minutes}m";
    }
    if ($minutes > 0) {
        return "{$minutes}m {$secs}s";
    }

    return "{$secs}s";
}
```
</details>
