# Завдання 42: патерни проєктування, частина 1

## Мета

Реалізувати по одному представнику для чотирьох класичних патернів:
Factory, Builder, Singleton, Decorator.

## Вимоги

1. **Factory.** Інтерфейс `Shape` (`area(): float`), класи `Circle`
   (`__construct(float $radius)`) і `Rectangle` (`__construct(float
   $width, float $height)`); `ShapeFactory::create(string $type, float
   ...$dimensions): Shape` — `'circle'` (1 вимір: радіус), `'rectangle'`
   (2 виміри: ширина, висота); кидає `\InvalidArgumentException` для
   невідомого `$type`.
2. **Builder.** `QueryBuilder` з методами `select(string ...$columns):
   self`, `from(string $table): self`, `where(string $condition): self`
   (кожен `where()` додає ще одну умову, з'єднану через `AND`),
   `build(): string` — див. точний формат у контракті.
3. **Singleton.** `AppConfig` з `private`-конструктором,
   `getInstance(): self` (завжди повертає той самий екземпляр),
   `set(string $key, mixed $value): void`, `get(string $key): mixed`
   (`null`, якщо ключа немає).
4. **Decorator.** Інтерфейс `Coffee` (`cost(): float`, `description():
   string`), `SimpleCoffee` (cost `30.0`, опис `'Кава'`),
   `MilkDecorator` (+10.0, дописує `' + молоко'`), `SugarDecorator`
   (+5.0, дописує `' + цукор'`) — обидва приймають `Coffee` в
   конструкторі й можуть обгортати одне одного.

## Контракт (сигнатури)

```php
interface Shape { public function area(): float; }
final class Circle implements Shape { public function __construct(float $radius) }
final class Rectangle implements Shape { public function __construct(float $width, float $height) }
final class ShapeFactory
{
    public static function create(string $type, float ...$dimensions): Shape
}

final class QueryBuilder
{
    public function select(string ...$columns): self
    public function from(string $table): self
    public function where(string $condition): self
    public function build(): string
}

final class AppConfig
{
    public static function getInstance(): self
    public function set(string $key, mixed $value): void
    public function get(string $key): mixed
}

interface Coffee { public function cost(): float; public function description(): string; }
final class SimpleCoffee implements Coffee {}
abstract class CoffeeDecorator implements Coffee { public function __construct(Coffee $coffee) }
final class MilkDecorator extends CoffeeDecorator {}
final class SugarDecorator extends CoffeeDecorator {}
```

`QueryBuilder::build()` без умов: `'SELECT col1, col2 FROM table'`;
з умовами: `'SELECT col1, col2 FROM table WHERE cond1 AND cond2'`.

## Обмеження

- Файл починається з `declare(strict_types=1);`.
- `AppConfig` не можна створити через `new AppConfig()` ззовні —
  конструктор `private`.
- `MilkDecorator`/`SugarDecorator` викликають метод обгорнутого
  `Coffee`, а не дублюють його логіку.

## Як перевірити

```bash
php check.php
```

## Definition of Done

- [ ] усі перевірки зелені
- [ ] можеш пояснити, чому `AppConfig` (Singleton) важче тестувати
      ізольовано, ніж `OrderService` з теми 41 (DI)

## Бонус (не перевіряється автоматично)

Скомбінуй `MilkDecorator` і `SugarDecorator` в одному ланцюжку
(`new SugarDecorator(new MilkDecorator(new SimpleCoffee()))`) і
перевір, що `cost()`/`description()` враховують обидва шари.

<details>
<summary>Підказка 1</summary>

```php
final class ShapeFactory
{
    public static function create(string $type, float ...$dimensions): Shape
    {
        return match ($type) {
            'circle' => new Circle($dimensions[0]),
            'rectangle' => new Rectangle($dimensions[0], $dimensions[1]),
            default => throw new \InvalidArgumentException("Невідома форма: {$type}"),
        };
    }
}
```
</details>

<details>
<summary>Підказка 2</summary>

```php
final class AppConfig
{
    private static ?self $instance = null;
    private array $values = [];

    private function __construct()
    {
    }

    public static function getInstance(): self
    {
        return self::$instance ??= new self();
    }

    public function set(string $key, mixed $value): void
    {
        $this->values[$key] = $value;
    }

    public function get(string $key): mixed
    {
        return $this->values[$key] ?? null;
    }
}
```
</details>

<details>
<summary>Підказка 3</summary>

```php
final class MilkDecorator extends CoffeeDecorator
{
    public function cost(): float
    {
        return $this->coffee->cost() + 10.0;
    }

    public function description(): string
    {
        return $this->coffee->description() . ' + молоко';
    }
}
```
</details>
