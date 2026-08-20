# Завдання 38: Composer та PSR-4

## Мета

1. Додати PSR-4-автозавантаження в `composer.json`.
2. Написати клас, чий шлях до файлу відповідає його простору імен.
3. Побачити, як `vendor/autoload.php` підключає класи без ручного `require`.

## Що робимо

У `starter/composer.json` **немає** секції `autoload`. У
`starter/src/Money.php` і `starter/src/Cart.php` — класи з `namespace
App;` і TODO замість реалізації.

## Вимоги

1. Додай у `starter/composer.json` секцію:
   ```json
   "autoload": {
       "psr-4": {
           "App\\": "src/"
       }
   }
   ```
2. Реалізуй `App\Money` — `readonly`-клас (промоція в конструкторі):
   `int $cents`, `string $currency`; `add(Money $other): self` — сума
   (нова `Money`), кидає `\InvalidArgumentException`, якщо валюти
   різні; `format(): string` — `"15.00 UAH"` (2 знаки після крапки,
   через `number_format`).
3. Реалізуй `App\Cart` — контейнер товарів (`Money`):
   `add(Money $item): void` додає товар; `total(): Money` — сума всіх
   товарів (через `Money::add()`), кидає `\RuntimeException` для
   порожнього кошика.
4. Виконай у папці `starter/`:
   ```bash
   composer dump-autoload
   ```

## Контракт (сигнатури)

```php
namespace App;

final class Money
{
    public function __construct(int $cents, string $currency)
    public function add(Money $other): self
    public function format(): string
}

final class Cart
{
    public function add(Money $item): void
    public function total(): Money
}
```

## Обмеження

- Обидва файли `src/Money.php`/`src/Cart.php` починаються з
  `declare(strict_types=1);`, одразу після — `namespace App;`.
- Ім'я файлу й шлях точно відповідають PSR-4 (`App\Money` →
  `src/Money.php`, регістр важливий).
- Ні `Money`, ні `Cart` не роблять `require`/`include` одне одного —
  автозавантажувач Composer робить це сам.

## Як перевірити

```bash
cd 38-composer-and-psr4/starter && composer dump-autoload && cd ..
php check.php
```

Якщо `vendor/autoload.php` не знайдено — `check.php` виведе `[skip]` з
підказкою виконати `composer dump-autoload`, а не провалиться.

## Definition of Done

- [ ] усі перевірки зелені
- [ ] можеш пояснити, чому `Money.php` мусить бути саме в `src/Money.php`
      (не `src/money.php`, не `src/Models/Money.php`) для мапінгу
      `"App\\": "src/"`

## Бонус (не перевіряється автоматично)

Спробуй `composer require --dev phpunit/phpunit` у `starter/` (для
цього потрібне розширення `zip` або встановлений `unzip`/`7z`) —
подивись, як `composer.json` і `composer.lock` змінюються після
реальної інсталяції пакета.

<details>
<summary>Підказка 1</summary>

```json
{
    "name": "yourname/topic-38",
    "type": "project",
    "require": {
        "php": ">=8.3"
    },
    "autoload": {
        "psr-4": {
            "App\\": "src/"
        }
    }
}
```
</details>

<details>
<summary>Підказка 2</summary>

```php
namespace App;

final class Money
{
    public function __construct(
        public readonly int $cents,
        public readonly string $currency,
    ) {
    }

    public function add(Money $other): self
    {
        if ($other->currency !== $this->currency) {
            throw new \InvalidArgumentException('Не можна додати гроші різних валют');
        }

        return new self($this->cents + $other->cents, $this->currency);
    }

    public function format(): string
    {
        return number_format($this->cents / 100, 2, '.', '') . ' ' . $this->currency;
    }
}
```
</details>

<details>
<summary>Підказка 3</summary>

```php
namespace App;

final class Cart
{
    private array $items = [];

    public function add(Money $item): void
    {
        $this->items[] = $item;
    }

    public function total(): Money
    {
        if ($this->items === []) {
            throw new \RuntimeException('Кошик порожній');
        }

        return array_reduce(
            array_slice($this->items, 1),
            static fn (Money $carry, Money $item): Money => $carry->add($item),
            $this->items[0]
        );
    }
}
```
</details>
