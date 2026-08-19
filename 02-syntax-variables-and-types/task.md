# Завдання 02: типи, константи, strict_types

## Мета

1. Впевнено читати й повертати скалярні типи PHP.
2. Відчути на практиці, що саме змінює `declare(strict_types=1)`.
3. Навчитись оголошувати константи через `const`.

## Що робимо

Пишемо невеликий набір функцій-хелперів для роботи з типами плюс одну константу
верхнього рівня.

## Вимоги

1. У `starter.php` на верхньому рівні файлу оголошена константа `APP_NAME` через
   `const` зі значенням `'PHP Course'`.
2. `type_of(mixed $value)` повертає **нормалізовану** назву типу: `'int'`, `'float'`,
   `'string'`, `'bool'`, `'array'`, `'null'` (не історичні назви `gettype()`).
3. `to_debug_string(mixed $value)` повертає рядкове представлення значення, придатне
   для дебагу (через `var_export`).
4. `sum_strict(int $a, int $b)` повертає суму — і саме тому, що файл під
   `strict_types=1`, виклик з нецілим аргументом (навіть `2.0` чи `'2'`) має кинути
   `TypeError`, а не тихо привести тип.

## Контракт (сигнатури)

```php
const APP_NAME = 'PHP Course';

function type_of(mixed $value): string
function to_debug_string(mixed $value): string
function sum_strict(int $a, int $b): int
```

## Обмеження

- Файл починається з `declare(strict_types=1);` — це стандарт для всього курсу,
  перевіряється окремо (вимога 0).
- `type_of()` не повинен використовувати `is_int`/`is_string`/... ланцюжком if —
  використай `gettype()` і `match` для мапування на нормалізовані назви (див. лекцію,
  розділ 3.5, чому це потрібно).

## Важлива деталь про вимогу 4

`check.php` викликає `sum_strict()` **зі свого власного файлу**, а `check.php` теж
має `declare(strict_types=1)` вгорі. Саме тому виклик кидає `TypeError` — навіть
якби в `starter.php` цієї директиви не було. Це не помилка в завданні, а ілюстрація
підводного каменя з лекції (розділ 4): `strict_types` — властивість файлу-викликача,
а не файлу, де функція оголошена.

## Як перевірити

```bash
php check.php
```

## Definition of Done

- [ ] усі перевірки зелені
- [ ] код проходить власне перечитування
- [ ] можеш пояснити кожен рядок

## Бонус (не перевіряється автоматично)

Додай у `type_of()` підтримку `'object'` і `'resource'` — які значення в PHP
дають ці типи?

<details>
<summary>Підказка 1</summary>

`gettype()` повертає: `'integer'`, `'double'` (не 'float'!), `'string'`, `'boolean'`,
`'array'`, `'NULL'` (великими літерами), `'object'`. `match` без предмету
(`match(true)`) тут не потрібен — досить `match(gettype($value)) { 'integer' => 'int', ... }`.
</details>

<details>
<summary>Підказка 2</summary>

`var_export($value, true)` — другий аргумент `true` каже повернути рядок замість
одразу друкувати його.
</details>

<details>
<summary>Підказка 3 (майже розв'язок)</summary>

```php
function type_of(mixed $value): string
{
    return match (gettype($value)) {
        'integer' => 'int',
        'double' => 'float',
        'boolean' => 'bool',
        'NULL' => 'null',
        default => gettype($value),
    };
}
```
</details>
