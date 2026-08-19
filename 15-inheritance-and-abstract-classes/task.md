# Завдання 15: успадкування та абстрактні класи

## Мета

1. Реалізувати Template Method через абстрактний клас.
2. Явно викликати `parent::__construct()` і `parent::method()`.

## Що робимо

Дві невеликі ієрархії: `Shape` з фігурами (Template Method для `area()`)
і `Animal`/`Dog` (явне розширення поведінки батька).

## Вимоги

1. Абстрактний клас `Shape` з абстрактним методом `area(): float` і звичайним
   методом `describe(): string`, що повертає рядок `"Площа: X.XX"`
   (`sprintf('Площа: %.2f', $this->area())`), використовуючи `$this->area()` —
   не знаючи конкретної формули.
2. `Circle extends Shape` — конструктор приймає радіус, `area()` повертає
   `M_PI * radius²`.
3. `Square extends Shape` — конструктор приймає довжину сторони, `area()`
   повертає `side²`.
4. `Animal` з конструктором (`protected string $name`) і методом
   `describe()`, що повертає `"Тварина на ім'я {name}"`. `Dog extends Animal`
   з додатковим полем `breed`: конструктор викликає `parent::__construct($name)`,
   а `describe()` викликає `parent::describe()` і дописує до результату
   `", порода {breed}"`.

## Контракт (сигнатури)

```php
abstract class Shape
{
    abstract public function area(): float;
    public function describe(): string
}

class Circle extends Shape
{
    public function __construct(float $radius)
    public function area(): float
}

class Square extends Shape
{
    public function __construct(float $side)
    public function area(): float
}

class Animal
{
    public function __construct(protected string $name)
    public function describe(): string
}

class Dog extends Animal
{
    public function __construct(string $name, private string $breed)
    public function describe(): string
}
```

## Обмеження

- Файл починається з `declare(strict_types=1);`.
- `Shape::describe()` не повинен знати про `Circle`/`Square` — лише викликати
  `$this->area()`.
- `Dog::describe()` обов'язково викликає `parent::describe()`, а не дублює
  логіку батька.

## Як перевірити

```bash
php check.php
```

## Definition of Done

- [ ] усі перевірки зелені, включно з тестом, що `new Shape()` кидає помилку
- [ ] можеш пояснити, що станеться, якщо забути `parent::__construct($name)`
      у `Dog`

## Бонус (не перевіряється автоматично)

Додай `Rectangle extends Shape` з шириною і висотою.

<details>
<summary>Підказка 1</summary>

`Shape::describe()`:
```php
public function describe(): string
{
    return sprintf('Площа: %.2f', $this->area());
}
```
`$this->area()` викличе перевизначений метод конкретного нащадка — це і є
поліморфізм.
</details>

<details>
<summary>Підказка 2</summary>

`Dog`:
```php
public function __construct(string $name, private string $breed)
{
    parent::__construct($name);
}

public function describe(): string
{
    return parent::describe() . ", порода {$this->breed}";
}
```
</details>
