# Завдання 08: замикання, callable й аргументи

## Мета

1. Впевнено користуватись `use` (за значенням і за посиланням) та arrow functions.
2. Написати "фабрику функцій" — функцію, що повертає налаштоване замикання.
3. Спробувати variadic-параметри та first-class callable syntax.

## Вимоги

1. `make_multiplier(int $factor)` повертає `Closure`, яка примножує число на
   `$factor` — реалізуй через arrow function (`fn`), не `function() use`.
2. `make_counter()` повертає `Closure` **без параметрів**, яка при кожному
   виклику повертає наступне число, починаючи з 1 (`1, 2, 3, ...`) — власний
   лічильник, незалежний для кожного виклику `make_counter()`. Реалізуй через
   `function () use (&$count) { ... }`, не через `static`.
3. `sum_all(int ...$numbers)` повертає суму довільної кількості цілих чисел
   через variadic-параметр.
4. `string_length_callable()` повертає `Closure`, еквівалентну виклику `strlen()`,
   через first-class callable syntax (`strlen(...)`).

## Контракт (сигнатури)

```php
function make_multiplier(int $factor): Closure
function make_counter(): Closure
function sum_all(int ...$numbers): int
function string_length_callable(): Closure
```

## Обмеження

- Файл починається з `declare(strict_types=1);`.
- `make_counter()` — кожен виклик `make_counter()` створює **незалежний**
  лічильник; два лічильники, створені окремо, не повинні впливати одне на одного.
- `string_length_callable()` — саме `strlen(...)`, не `fn($s) => strlen($s)`
  (мета вправи — синтаксис first-class callable, а не результат).

## Як перевірити

```bash
php check.php
```

## Definition of Done

- [ ] усі перевірки зелені
- [ ] можеш пояснити, чому `make_counter()` обов'язково потребує `&$count`,
      а не просто `$count`

## Бонус (не перевіряється автоматично)

Напиши `pipe(callable ...$fns): Closure`, яка повертає нову функцію, що
застосовує всі передані функції по черзі зліва направо: `pipe($f, $g)($x)`
еквівалентно `$g($f($x))`.

<details>
<summary>Підказка 1</summary>

`make_counter()`:
```php
function make_counter(): Closure
{
    $count = 0;
    return function () use (&$count): int {
        return ++$count;
    };
}
```
</details>

<details>
<summary>Підказка 2</summary>

`sum_all()` — це буквально `return array_sum($numbers);` — variadic-параметр
усередині функції веде себе як звичайний масив.
</details>
