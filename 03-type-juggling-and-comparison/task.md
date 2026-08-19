# Завдання 03: жонглювання типами та порівняння

## Мета

1. Навчитись свідомо вибирати між `==` і `===`.
2. Розібратись у truthiness і різниці `isset`/`empty`/`array_key_exists`.
3. Побачити на практиці зміну поведінки порівняння рядок-число в PHP 8.

## Що робимо

Пишемо функції, які явно демонструють кожне з правил порівняння з лекції —
так, щоб різниця між строгим і нестрогим порівнянням була видна в тестах.

## Вимоги

1. `is_truthy(mixed $value)` повертає результат приведення значення до `bool`
   за правилами PHP (truthiness з розділу 3.2 лекції).
2. `strict_equals(mixed $a, mixed $b)` повертає результат строгого порівняння `===`.
3. `loose_equals(mixed $a, mixed $b)` повертає результат нестрогого порівняння `==`.
4. `key_status(array $data, string $key)` повертає одне з трьох рядкових значень:
   `'missing'` (ключа немає), `'null'` (ключ є, значення `null`), `'present'`
   (ключ є, значення не `null`) — це демонструє різницю `isset()` і
   `array_key_exists()` без виклику жодної з них напряму назовні (вони можуть
   використовуватись усередині).
5. `compare(int|float $a, int|float $b)` повертає результат spaceship-оператора `<=>`.

## Контракт (сигнатури)

```php
function is_truthy(mixed $value): bool
function strict_equals(mixed $a, mixed $b): bool
function loose_equals(mixed $a, mixed $b): bool
function key_status(array $data, string $key): string
function compare(int|float $a, int|float $b): int
```

## Обмеження

- Файл починається з `declare(strict_types=1);`.
- Кожна функція — це буквально одна операція з відповідним оператором, обгорнута
  в `return`. Мета завдання не "написати складний код", а зафіксувати правила
  порівняння PHP так, щоб їх можна було перевірити тестами.

## Як перевірити

```bash
php check.php
```

## Definition of Done

- [ ] усі перевірки зелені
- [ ] можеш пояснити різницю в поведінці кожної пари тестів (строгий/нестрогий)
- [ ] можеш пояснити кожен рядок

## Бонус (не перевіряється автоматично)

Напиши `strict_in_array(mixed $needle, array $haystack): bool` — свою версію
`in_array()`, яка завжди строго порівнює (навіть без третього аргументу `true`).

<details>
<summary>Підказка 1</summary>

Для `key_status()` придивись до різниці `isset($data[$key])` (false для null-значення)
і `array_key_exists($key, $data)` (true навіть для null-значення). Три гілки —
`match(true) { ... }` або звичайний `if/elseif`.
</details>

<details>
<summary>Підказка 2</summary>

`compare()` — це просто `return $a <=> $b;`. PHP сам визначить, що повертати:
`-1`, `0` чи `1`.
</details>

<details>
<summary>Підказка 3 (майже розв'язок)</summary>

```php
function key_status(array $data, string $key): string
{
    if (!array_key_exists($key, $data)) {
        return 'missing';
    }

    return $data[$key] === null ? 'null' : 'present';
}
```
</details>
