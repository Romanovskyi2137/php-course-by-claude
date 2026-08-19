# Завдання 20: простори імен та автозавантаження

## Мета

1. Написати власний автозавантажувач через `spl_autoload_register()`.
2. Скористатись класами з простору імен `App\...`, жодного разу не
   написавши `require` для них вручну.

## Що робимо

У папці `classes/` уже лежать два готові класи — `App\Domain\Product` і
`App\Support\StringHelper` (не редагуй їх). Твоє завдання — написати
автозавантажувач у `starter.php`, який підвантажує їх лише тоді, коли
вони реально потрібні.

Відповідність простору імен файлу: `App\Domain\Product` →
`classes/Domain/Product.php` (префікс `App\` відкидається, решта
частин простору імен — це вкладені папки, `\` → `/`, і додається `.php`).

## Вимоги

1. `register_autoloader(string $baseDir)` реєструє (через
   `spl_autoload_register`) функцію, яка для будь-якого класу з префіксом
   `App\` обчислює шлях за правилом вище (відносно `$baseDir`) і робить
   `require`, якщо такий файл існує. Для класів без префіксу `App\`
   (або якщо файл не знайдено) — нічого не робить (не кидає помилку,
   просто дає іншим зареєстрованим автозавантажувачам шанс спрацювати).
2. `make_product(string $name, float $price)` створює й повертає
   `\App\Domain\Product` — **без** явного `require` цього класу деінде
   у твоєму коді (автозавантажувач має підвантажити його сам, коли
   `new \App\Domain\Product(...)` буде виконано).
3. `shout_via_autoload(string $text)` викликає
   `\App\Support\StringHelper::shout($text)` — так само, без явного `require`.

## Контракт (сигнатури)

```php
function register_autoloader(string $baseDir): void
function make_product(string $name, float $price): \App\Domain\Product
function shout_via_autoload(string $text): string
```

## Обмеження

- Файл починається з `declare(strict_types=1);`.
- Жодного `require`/`require_once`/`include` для `Product.php` чи
  `StringHelper.php` у `starter.php` — увесь доступ лише через
  зареєстрований автозавантажувач.
- `check.php` викликає `register_autoloader(__DIR__ . '/classes')` перед
  тестами вимог 2-3 — саме так автозавантажувач дізнається, де шукати файли.

## Як перевірити

```bash
php check.php
```

## Definition of Done

- [ ] усі перевірки зелені
- [ ] можеш пояснити, у який момент реально викликається твоя функція
      з `spl_autoload_register` — до чи після рядка `new \App\Domain\Product(...)`

## Бонус (не перевіряється автоматично)

Додай третій клас `App\Support\NumberHelper` із власним файлом
`classes/Support/NumberHelper.php` і переконайся, що він теж підвантажується
без змін у `register_autoloader()`.

<details>
<summary>Підказка 1</summary>

```php
function register_autoloader(string $baseDir): void
{
    spl_autoload_register(function (string $class) use ($baseDir): void {
        if (!str_starts_with($class, 'App\\')) {
            return;
        }
        $relative = substr($class, strlen('App\\'));
        $path = $baseDir . '/' . str_replace('\\', '/', $relative) . '.php';
        if (is_file($path)) {
            require $path;
        }
    });
}
```
</details>

<details>
<summary>Підказка 2</summary>

`make_product()` і `shout_via_autoload()` — просто звичайний код, що
використовує класи за повним іменем (`\App\Domain\Product`,
`\App\Support\StringHelper`). Уся "магія" — саме в
`register_autoloader()`, який має бути викликаний ДО першого використання
цих класів.
</details>
