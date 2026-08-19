# Завдання 04: оператори та вирази

## Мета

1. Свідомо розрізняти `??` (null coalescing) і `?:` (Elvis).
2. Впевнено користуватись `intdiv()` замість `/` там, де потрібне ціле ділення.

## Що робимо

Невеликий набір функцій-хелперів, які часто пишуться вручну в реальному коді:
безпечне ділення, значення за замовчуванням, обмеження числа діапазоном.

## Вимоги

1. `integer_divide(int $a, int $b)` повертає результат цілочисельного ділення
   `$a` на `$b` (округлення до нуля, як `intdiv()`).
2. `coalesce(mixed $value, mixed $default)` повертає `$value`, якщо воно **не
   `null`**, інакше `$default` (поведінка `??`).
3. `elvis(mixed $value, mixed $default)` повертає `$value`, якщо воно **truthy**,
   інакше `$default` (поведінка `?:`) — навмисно інша семантика за `coalesce()`.
4. `clamp(int $value, int $min, int $max)` обмежує `$value` діапазоном
   `[$min, $max]`.

## Контракт (сигнатури)

```php
function integer_divide(int $a, int $b): int
function coalesce(mixed $value, mixed $default): mixed
function elvis(mixed $value, mixed $default): mixed
function clamp(int $value, int $min, int $max): int
```

## Обмеження

- Файл починається з `declare(strict_types=1);`.
- `integer_divide()` використовує `intdiv()`, не `(int) ($a / $b)` (для від'ємних
  чисел і крайніх випадків результат може відрізнятись; `intdiv()` — правильний
  інструмент саме для цього).
- `coalesce()` і `elvis()` мають реально відрізнятись у поведінці для `0` — це
  і перевіряється тестами.

## Як перевірити

```bash
php check.php
```

## Definition of Done

- [ ] усі перевірки зелені
- [ ] можеш на словах пояснити, чому `coalesce(0, 10)` і `elvis(0, 10)` повертають
      різні значення

## Бонус (не перевіряється автоматично)

`intdiv()` кидає `DivisionByZeroError` при діленні на 0. Обгорни виклик
`integer_divide()` у власну функцію `safe_integer_divide()`, яка ловить цей
виняток і повертає `null` замість того, щоб падати.

<details>
<summary>Підказка 1</summary>

`clamp()` можна написати одним виразом через вкладені `min`/`max`:
`max($min, min($max, $value))`.
</details>

<details>
<summary>Підказка 2</summary>

`coalesce()` — це буквально `return $value ?? $default;`. `elvis()` — буквально
`return $value ?: $default;`. Різниця не в тому, як ти їх пишеш, а в тому, який
оператор PHP використовує під капотом.
</details>
