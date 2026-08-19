# Завдання 07: функції та декларації типів

## Мета

1. Впевнено користуватись nullable/union типами в сигнатурах.
2. Розібратись, коли передавати параметр за посиланням.
3. Побачити на практиці, як `static`-змінна зберігає стан між викликами.

## Вимоги

1. `format_name(string $first, string $last, ?string $middle = null)` повертає
   `"{first} {middle} {last}"`, якщо `$middle` передано, або `"{first} {last}"`,
   якщо ні.
2. `try_parse_int(string $value)` повертає число як `int`, якщо рядок є цілим
   числом (можливо з пробілами навколо), або `null`, якщо ні — **без винятків**.
3. `increment_by(int &$value, int $step = 1)` збільшує передану за посиланням
   змінну на `$step` (за замовчуванням на 1). Нічого не повертає.
4. `next_sequence_value()` повертає послідовні цілі числа, починаючи з 1,
   і при кожному новому виклику **в межах одного запуску скрипта** повертає
   на 1 більше, ніж попереднього разу — через `static`-змінну всередині функції.

## Контракт (сигнатури)

```php
function format_name(string $first, string $last, ?string $middle = null): string
function try_parse_int(string $value): ?int
function increment_by(int &$value, int $step = 1): void
function next_sequence_value(): int
```

## Обмеження

- Файл починається з `declare(strict_types=1);`.
- `try_parse_int()` не кидає винятків на нечислові рядки — тільки повертає `null`.
  Скористайся `ctype_digit()`/`is_numeric()` для перевірки перед приведенням,
  а не `try/catch` (винятки — тема 21).
- `next_sequence_value()` без параметрів і без глобальних змінних — лічильник
  живе тільки як `static` всередині функції.

## Як перевірити

```bash
php check.php
```

## Definition of Done

- [ ] усі перевірки зелені
- [ ] можеш пояснити, чому `next_sequence_value()` — приклад прихованого стану
      і чим це ускладнює тестування

## Бонус (не перевіряється автоматично)

Додай `try_parse_int()` підтримку від'ємних чисел (`'-42'`) — правильно working
з `is_numeric()`, чи потрібні додаткові перевірки?

<details>
<summary>Підказка 1</summary>

`try_parse_int()`: `$trimmed = trim($value); if (!ctype_digit($trimmed)) { return null; } return (int) $trimmed;`
— зверни увагу, `ctype_digit` не приймає знак мінус, тому для бонусу знадобиться
інша перевірка.
</details>

<details>
<summary>Підказка 2</summary>

`next_sequence_value()`:
```php
function next_sequence_value(): int
{
    static $value = 0;
    return ++$value;
}
```
</details>
