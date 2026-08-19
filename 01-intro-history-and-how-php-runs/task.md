# Завдання 01: інспектор оточення PHP

## Мета

1. Навчитись користуватись вбудованими константами й функціями, які описують
   поточне оточення PHP (версія, SAPI, ОС, розширення).
2. Побачити на практиці, що "оточення" — це не магія, а звичайні константи,
   які можна прочитати й обробити як будь-які інші дані.

## Що робимо

Пишемо невеликий "інспектор оточення" — набір функцій, які повертають інформацію
про те, де саме зараз виконується PHP. Такі речі реально потрібні: перевірка версії
перед використанням нової фічі, вивід діагностики в `--version`-команді CLI-утиліти
тощо.

## Вимоги

1. `php_major_version()` повертає major-версію PHP як ціле число (наприклад, для
   PHP 8.3.6 повертає `8`), обчислену з `PHP_VERSION_ID`, **без** парсингу рядка
   `PHP_VERSION`.
2. `is_at_least(string $version)` повертає `true`, якщо поточна версія PHP
   більша або дорівнює переданій (наприклад, `is_at_least('8.1.0')`).
3. `current_sapi()` повертає назву поточного SAPI (`cli` під час запуску через
   термінал).
4. `format_environment_report()` повертає багаторядковий рядок у точному форматі
   (див. контракт нижче) — версія PHP, SAPI, родина ОС, кількість завантажених
   розширень.

## Контракт (сигнатури)

```php
function php_major_version(): int
function is_at_least(string $version): bool
function current_sapi(): string
function format_environment_report(): string
```

Формат виводу `format_environment_report()` (рядки розділені `"\n"`, без завершального
переносу рядка в кінці):

```
PHP version: 8.3.6
SAPI: cli
OS family: Linux
Loaded extensions: 42
```

Значення бери з `PHP_VERSION`, `PHP_SAPI`, `PHP_OS_FAMILY` та `count(get_loaded_extensions())`
відповідно.

## Обмеження

- Файл має починатись з `declare(strict_types=1);`.
- `php_major_version()` не повинен парсити рядок `PHP_VERSION` регуляркою чи `explode` —
  для цього є `PHP_VERSION_ID` (ціле число виду `80306` для `8.3.6`).
- Не хардкодь версію PHP чи назву SAPI — функції мають повертати реальні поточні значення.

## Як перевірити

```bash
php check.php
```

## Definition of Done

- [ ] усі перевірки зелені
- [ ] код проходить власне перечитування
- [ ] можеш пояснити кожен рядок

## Бонус (не перевіряється автоматично)

1. Додай до звіту рядок з `memory_get_usage()` — скільки памʼяті використав скрипт.
2. Напиши версію `format_environment_report()`, яка повертає масив (`array`) замість
   рядка, і окрему функцію, що форматує масив у рядок — так простіше тестувати
   і перевикористовувати дані.

<details>
<summary>Підказка 1</summary>

`PHP_VERSION_ID` для `8.3.6` дорівнює `80306`. Формула: `major*10000 + minor*100 + patch`.
Щоб дістати major, поділи націло на `10000` — саме для цього є `intdiv()`, а не `/`
(яке повернуло б `float`).
</details>

<details>
<summary>Підказка 2</summary>

`version_compare()` вміє порівнювати рядки версій напряму і приймає третій аргумент —
оператор порівняння як рядок: `version_compare(PHP_VERSION, $version, '>=')`.
</details>

<details>
<summary>Підказка 3 (майже розв'язок)</summary>

```php
function format_environment_report(): string
{
    return implode("\n", [
        'PHP version: ' . PHP_VERSION,
        'SAPI: ' . PHP_SAPI,
        'OS family: ' . PHP_OS_FAMILY,
        'Loaded extensions: ' . count(get_loaded_extensions()),
    ]);
}
```
</details>
