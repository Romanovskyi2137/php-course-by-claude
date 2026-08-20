# 42. Патерни проєктування, частина 1

> Що ти вже вмієш: Factory/Builder/Singleton/Decorator — GoF-патерни,
> однакові в будь-якій ООП-мові · Що додає ця тема: PHP-специфічний
> синтаксис для кожного (`match`, fluent-інтерфейси, `static`) і чесна
> засторога щодо Singleton · ~30 хв

## 1. Якщо ти прийшов з JS / Python / Java

- Усі чотири патерни цієї теми — з класичної книги "Design Patterns"
  (Gang of Four, 1994) — однакові концептуально в будь-якій ООП-мові.
  PHP не додає нової **теорії**, лише свій синтаксис: `match`
  (тема 06) для Factory замість `switch`/`if`-ланцюжка, `self`/`static`
  (тема 14) для fluent-інтерфейсів Builder.
- Singleton в PHP реалізується через `private` конструктор і
  `static`-властивість — той самий підхід, що в Java
  (`private static instance`) чи C# — на відміну від Python, де
  Singleton частіше роблять через модуль (модулі в Python вже
  "одинаки" за своєю природою).

## 2. Ключові терміни

- **Factory (фабрика)** — метод/клас, що інкапсулює логіку створення
  об'єктів, приховуючи конкретний клас за загальним інтерфейсом.
- **Builder (будівельник)** — покроково конструює складний об'єкт через
  ланцюжок викликів (fluent-інтерфейс), відкладаючи фінальну збірку до
  явного виклику (`build()`).
- **Singleton (одинак)** — гарантує, що клас має **рівно один**
  екземпляр за весь час виконання, з глобальною точкою доступу.
- **Decorator (декоратор)** — обгортає об'єкт іншим об'єктом того самого
  інтерфейсу, додаючи поведінку без зміни оригінального класу.

## 3. Теорія

### 3.1 Factory: приховати вибір класу

```php
interface Shape
{
    public function area(): float;
}

final class Circle implements Shape
{
    public function __construct(private float $radius) {}
    public function area(): float { return M_PI * $this->radius ** 2; }
}

final class Rectangle implements Shape
{
    public function __construct(private float $width, private float $height) {}
    public function area(): float { return $this->width * $this->height; }
}

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

$shape = ShapeFactory::create('circle', 5.0); // викликач не знає й не дбає про Circle напряму
```

### 3.2 Builder: покрокова збірка

```php
final class QueryBuilder
{
    private array $columns = ['*'];
    private string $table = '';
    private array $conditions = [];

    public function select(string ...$columns): self
    {
        $this->columns = $columns;
        return $this;
    }

    public function from(string $table): self
    {
        $this->table = $table;
        return $this;
    }

    public function where(string $condition): self
    {
        $this->conditions[] = $condition;
        return $this;
    }

    public function build(): string
    {
        $sql = 'SELECT ' . implode(', ', $this->columns) . ' FROM ' . $this->table;
        if ($this->conditions !== []) {
            $sql .= ' WHERE ' . implode(' AND ', $this->conditions);
        }
        return $sql;
    }
}

$sql = (new QueryBuilder())
    ->select('id', 'name')
    ->from('products')
    ->where('price > 10')
    ->build();
// 'SELECT id, name FROM products WHERE price > 10'
```

Кожен метод повертає `$this` (чи `self`) — це і є "fluent"-інтерфейс,
що дозволяє ланцюжок викликів без проміжних змінних.

### 3.3 Singleton: рівно один екземпляр

```php
final class AppConfig
{
    private static ?self $instance = null;
    private array $values = [];

    private function __construct() {} // приватний — ззовні new AppConfig() неможливий

    public static function getInstance(): self
    {
        return self::$instance ??= new self();
    }

    public function set(string $key, mixed $value): void { $this->values[$key] = $value; }
    public function get(string $key): mixed { return $this->values[$key] ?? null; }
}

AppConfig::getInstance()->set('env', 'production');
AppConfig::getInstance()->get('env'); // 'production' — той самий екземпляр
```

### 3.4 Decorator: додати поведінку без зміни класу

```php
interface Coffee
{
    public function cost(): float;
    public function description(): string;
}

final class SimpleCoffee implements Coffee
{
    public function cost(): float { return 30.0; }
    public function description(): string { return 'Кава'; }
}

abstract class CoffeeDecorator implements Coffee
{
    public function __construct(protected Coffee $coffee) {}
}

final class MilkDecorator extends CoffeeDecorator
{
    public function cost(): float { return $this->coffee->cost() + 10.0; }
    public function description(): string { return $this->coffee->description() . ' + молоко'; }
}

$coffee = new MilkDecorator(new SimpleCoffee());
$coffee->cost();        // 40.0
$coffee->description(); // 'Кава + молоко'
```

Декоратори можна **комбінувати**: `new SugarDecorator(new MilkDecorator(new SimpleCoffee()))` —
кожен шар додає свою поведінку, не змінюючи внутрішні.

## 4. Підводні камені

- **Singleton — патерн, якого варто уникати за замовчуванням.**
  Головна проблема — не сам "рівно один екземпляр", а **глобальний
  доступ**: клас, що всередині методу викликає
  `AppConfig::getInstance()`, приховує цю залежність від сигнатури
  конструктора (той самий недолік, що й Service Locator, тема 41) —
  тестувати такий клас важче: підмінити глобальний Singleton у тесті
  незручно. Там, де потрібен "рівно один екземпляр" — частіше краще
  створити один екземпляр вручну і передати його через DI (тема 41),
  ніж робити клас Singleton-ом.
- Factory, що просто робить `new` без жодної логіки вибору
  (`return new Circle($radius);` для однієї-єдиної форми) — зайва
  абстракція; Factory виправдана, коли є **вибір** між кількома
  класами чи складна логіка ініціалізації.
- Забутий `build()` в Builder — незавершений ланцюжок повертає сам
  Builder (`self`), не результат; типова помилка новачків —
  використати проміжний `$this` як фінальний об'єкт.

## 5. Сучасний PHP 8.3+

`match` (PHP 8.0+, тема 06) зробив Factory-методи компактнішими за
старий `switch` — жодних `break`, суворе порівняння `===`. `self`/
`static` як тип повернення (тема 14) — стандартний вибір для
fluent-методів Builder з PHP 7.4+.

## 6. Контекст екосистеми

Symfony `HttpClient`/Guzzle `RequestOptions` — приклади реального
Builder-подібного API в популярних бібліотеках. Laravel `Eloquent`
Query Builder (`User::where(...)->orderBy(...)->get()`) — той самий
патерн у розділі 3.2, застосований до реальних SQL-запитів у продакшн-
масштабі.

## 7. Документація

- https://refactoring.guru/design-patterns/factory-method
- https://refactoring.guru/design-patterns/builder
- https://refactoring.guru/design-patterns/singleton
- https://refactoring.guru/design-patterns/decorator

## 8. Питання для самоперевірки

1. Коли Factory виправдана, а коли — зайва абстракція над простим `new`?
2. Що саме дозволяє Builder-методам ланцюжок викликів (`->select()->from()->where()`)?
3. Чому Singleton вважають антипатерном у більшості випадків, попри
   те, що іноді "рівно один екземпляр" — цілком реальна вимога?
4. Чим Decorator відрізняється від простого успадкування для додавання
   нової поведінки?
