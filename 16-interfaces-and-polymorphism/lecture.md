# 16. Інтерфейси та поліморфізм

> Що ти вже вмієш: інтерфейси з іншої мови (Java/TS) або duck typing (Python) ·
> Що додає ця тема: PHP-специфіку множинної реалізації, union/intersection типів,
> вбудованих інтерфейсів · ~20 хв

## 1. Якщо ти прийшов з JS / Python / Java

- Інтерфейси PHP схожі на Java/TypeScript `interface` — контракт без реалізації,
  клас може реалізовувати **кілька** інтерфейсів одночасно (`implements A, B`),
  на відміну від одиничного `extends` для класів (тема 15).
- Python не має формальних інтерфейсів (typing `Protocol` — найближчий
  аналог, і той опційний); PHP — типізована мова з обов'язковою перевіркою
  `implements` на етапі оголошення класу.
- `instanceof` — як `instanceof` у Java/JS чи `isinstance()` у Python, працює
  однаково і для класів, і для інтерфейсів.

## 2. Ключові терміни

- **Interface (інтерфейс)** — контракт: перелік сигнатур методів (і опційно
  констант) без реалізації, який клас зобов'язується виконати через `implements`.
- **Polymorphism (поліморфізм)** — можливість працювати з обʼєктами різних
  класів однаково, якщо вони реалізують спільний інтерфейс/успадковують
  спільний базовий клас.
- **Program to an interface (програмуй до контракту)** — принцип: функції й
  методи повинні приймати найзагальніший тип, що їм реально потрібен
  (інтерфейс), а не конкретний клас.

## 3. Теорія

### 3.1 Оголошення й реалізація

```php
interface Priced
{
    public function price(): float;
}

class Book implements Priced
{
    public function __construct(private float $cost) {}

    public function price(): float
    {
        return $this->cost;
    }
}

class Ticket implements Priced
{
    public function __construct(private float $basePrice, private float $fee) {}

    public function price(): float
    {
        return $this->basePrice + $this->fee;
    }
}
```

### 3.2 Поліморфізм на практиці

```php
function totalPrice(array $items): float   // приймає Priced[], не конкретні класи
{
    $total = 0.0;
    foreach ($items as $item) {
        $total += $item->price();   // не важливо, Book це чи Ticket — обидва мають price()
    }
    return $total;
}

totalPrice([new Book(10.0), new Ticket(20.0, 2.5)]);   // 32.5
```

Функція `totalPrice` **нічого не знає** про `Book`/`Ticket` — лише про те, що
кожен елемент вміє `price()`. Додати новий тип "з ціною" (`Subscription`,
`Course`) — не потрібно чіпати `totalPrice()` взагалі.

### 3.3 Множинна реалізація й константи в інтерфейсах

```php
interface Comparable
{
    public function compareTo(self $other): int;
}

interface Priced
{
    public const string CURRENCY = 'USD';
    public function price(): float;
}

class Product implements Comparable, Priced   // реалізує ОБИДВА інтерфейси
{
    /* ... */
}
```

### 3.4 `instanceof` і перевірка контракту

```php
function describe(mixed $item): string
{
    if (!$item instanceof Priced) {
        throw new \InvalidArgumentException('Очікував обʼєкт, що реалізує Priced');
    }
    return sprintf('Ціна: %.2f', $item->price());
}
```

### 3.5 Union/intersection типи з інтерфейсами

```php
function process(Priced&Comparable $item): void { /* item реалізує ОБИДВА */ }
function handle(Book|Ticket $item): void { /* item це Book АБО Ticket */ }
```

Intersection types (`A&B`, PHP 8.1+) вимагають, щоб обʼєкт реалізовував
одразу кілька контрактів — рідше за union, але корисно для функцій,
яким потрібно кілька незалежних можливостей одночасно.

### 3.6 Вбудовані інтерфейси

PHP постачає кілька "магічних" інтерфейсів, які змінюють поведінку класу:
`Stringable` (дає `(string) $obj`, тема 19), `Countable` (дає `count($obj)`),
`ArrayAccess` (дає `$obj[$key]`), `IteratorAggregate`/`Iterator` (дає `foreach`
по обʼєкту, тема 22), `JsonSerializable` (керує `json_encode($obj)`, тема 25).

## 4. Підводні камені

- Функція, типізована під конкретний клас (`function f(Book $b)`), не приймає
  `Ticket`, навіть якщо в нього є той самий метод `price()` — PHP не робить
  структурну типізацію (duck typing), лише номінальну (через `implements`).
  Для поліморфізму типізуй саме інтерфейсом.
- `instanceof` з інтерфейсом працює так само, як із класом — це та сама
  перевірка "чи обʼєкт відповідає цьому контракту", незалежно від того,
  клас це чи інтерфейс.
- Занадто дрібні інтерфейси "на кожен метод" (interface segregation
  доведена до абсурду) роблять код важким для читання не менше, ніж
  занадто товсті — шукай баланс за реальними потребами коду, що викликає.

## 5. Сучасний PHP 8.3+

Intersection types (8.1) дозволяють типізувати "обʼєкт, що одночасно
`Countable` і `IteratorAggregate`" одним виразом `Countable&IteratorAggregate`,
без створення окремого порожнього інтерфейсу-обʼєднання, як доводилось
робити раніше.

## 6. Контекст екосистеми

"Program to an interface" — основа Dependency Injection (тема 41): сервіси
в Laravel/Symfony типізують залежності інтерфейсами (`LoggerInterface`,
`CacheInterface`), а конкретну реалізацію підставляє контейнер — код сервісу
взагалі не знає, який саме логер чи кеш використовується.

## 7. Документація

- https://www.php.net/manual/en/language.oop5.interfaces.php
- https://www.php.net/manual/en/language.types.intersection.php
- https://www.php.net/manual/en/reserved.interfaces.php — список вбудованих інтерфейсів

## 8. Питання для самоперевірки

1. Чим `Book|Ticket` (union) відрізняється від `Priced&Comparable` (intersection)?
2. Чому PHP не дозволить передати обʼєкт з методом `price()` у функцію,
   типізовану `function f(Priced $p)`, якщо клас не написав `implements Priced`?
3. Наведи приклад, коли варто типізувати параметр інтерфейсом, а не
   конкретним класом.
4. Назви три вбудовані PHP-інтерфейси і що кожен з них дає класу.
