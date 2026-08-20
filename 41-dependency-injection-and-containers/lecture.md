# 41. Dependency Injection та контейнери

> Що ти вже вмієш: Spring `@Autowired`/NestJS DI/Angular DI з іншої
> мови — той самий принцип · Що додає ця тема: чому DI-контейнер —
> не магія, а звичайний масив фабрик, і чим він відрізняється від
> Service Locator · ~25 хв

## 1. Якщо ти прийшов з JS / Python / Java

- Dependency Injection (DI) — той самий принцип, що в Spring
  (`@Autowired`), Angular (конструктор-based DI) чи NestJS: клас не
  створює свої залежності сам, а отримує їх ззовні (типово — через
  конструктор). PHP не має вбудованого DI-фреймворку в мові — це
  патерн, а не мовна конструкція, як і в JS/Python.
- PSR-11 — стандартизований **інтерфейс** контейнера
  (`get()`/`has()`), не конкретна реалізація — приблизно як
  `javax.inject` в Java: різні фреймворки (Symfony, Laravel — частково,
  Slim) реалізують один і той самий контракт, тому пакети, написані
  проти PSR-11, працюють з будь-яким сумісним контейнером.
- Service Locator (розділ 3.3) — антипатерн, знайомий програмістам на
  будь-якій мові: клас **сам** іде в глобальний реєстр по залежність,
  замість отримати її ззовні — приховує реальні залежності класу від
  того, хто читає його конструктор.

## 2. Ключові терміни

- **Dependency Injection (DI, впровадження залежностей)** — передача
  залежності класу ззовні (типово через конструктор), а не створення
  її всередині самого класу.
- **Inversion of Control (IoC, інверсія управління)** — загальніший
  принцип, з якого випливає DI: не клас керує тим, звідки беруться
  залежності, а щось зовнішнє (контейнер, викликач).
- **DI-контейнер** — об'єкт, що знає, як побудувати й видати
  залежності за їхнім ідентифікатором (типово — ім'я класу/інтерфейсу).
- **PSR-11 (Container Interface)** — стандарт PHP-FIG, що визначає
  мінімальний контракт контейнера: `get(string $id)`, `has(string $id): bool`.
- **Service Locator** — антипатерн: глобальний реєстр, з якого код
  **сам** дістає залежності (`ServiceLocator::get(Logger::class)`)
  замість отримання їх через конструктор.

## 3. Теорія

### 3.1 Без DI: клас створює залежність сам

```php
final class OrderService
{
    private FileLogger $logger;

    public function __construct()
    {
        $this->logger = new FileLogger('/var/log/app.log'); // жорстко "зашито" всередині
    }
}
```

Проблема: `OrderService` неможливо протестувати без реального файлу
логів — залежність жорстко "зашита", підмінити її нема як.

### 3.2 З DI: залежність передається ззовні

```php
interface Logger
{
    public function log(string $message): void;
}

final class OrderService
{
    public function __construct(private Logger $logger) {}

    public function placeOrder(string $item): void
    {
        $this->logger->log("Order placed: {$item}");
    }
}

// у продакшн-коді:
new OrderService(new FileLogger('/var/log/app.log'));

// у тестах (тема 44):
new OrderService(new ArrayLogger()); // логи в пам'яті, легко перевірити
```

`OrderService` тепер узагалі не знає, **яка саме** реалізація `Logger`
йому передана — жорсткого зв'язку немає, підміна для тестів тривіальна.

### 3.3 Service Locator: чому це не те саме, що DI

```php
// Service Locator (антипатерн):
final class OrderService
{
    public function placeOrder(string $item): void
    {
        $logger = ServiceLocator::get(Logger::class); // клас сам "іде" по залежність
        $logger->log("Order placed: {$item}");
    }
}
```

Різниця тонка, але важлива: конструктор `OrderService` з DI **чесно
показує** список залежностей (подивись на сигнатуру — і все зрозуміло).
З Service Locator залежність похована всередині методу — щоб дізнатись,
що класу взагалі потрібен `Logger`, доводиться читати реалізацію, а не
сигнатуру.

### 3.4 DI-контейнер: фабрики за ідентифікатором

```php
final class Container
{
    private array $factories = [];
    private array $instances = [];

    public function set(string $id, \Closure $factory): void
    {
        $this->factories[$id] = $factory;
    }

    public function get(string $id): mixed
    {
        if (!isset($this->instances[$id])) {
            if (!isset($this->factories[$id])) {
                throw new \OutOfBoundsException("Сервіс не зареєстровано: {$id}");
            }
            $this->instances[$id] = ($this->factories[$id])($this);
        }

        return $this->instances[$id];
    }

    public function has(string $id): bool
    {
        return isset($this->factories[$id]);
    }
}

$container = new Container();
$container->set(Logger::class, fn () => new FileLogger('/var/log/app.log'));
$container->set(OrderService::class, fn (Container $c) => new OrderService($c->get(Logger::class)));

$service = $container->get(OrderService::class); // контейнер сам розбирає ланцюжок залежностей
```

Контейнер — не магія: масив "рецептів" (замикань), що знають, як
побудувати кожен сервіс, плюс кеш уже побудованих екземплярів
(типово — singleton-поведінка: один і той самий об'єкт при повторному
`get()`).

## 4. Підводні камені

- Плутанина DI з DI-**контейнером**: DI — принцип (передавай залежності
  через конструктор), контейнер — лише **інструмент**, що автоматизує
  побудову об'єктів із залежностями. Можна писати DI зовсім без
  контейнера (просто `new OrderService(new FileLogger(...))` вручну) —
  для маленьких проєктів це часто найпростіший, найзрозуміліший варіант.
- Service Locator (розділ 3.3) іноді **виглядає** як DI, бо теж
  використовує контейнер — різниця в тому, **хто** викликає `get()`:
  зовнішній код (правильно, це і є "впровадження") чи сам клас
  всередині власного методу (Service Locator, антипатерн).
- Контейнер, що сам створює **всі** об'єкти в застосунку без
  розбору (включно з простими value-об'єктами типу `Money`) — зайва
  складність; контейнер потрібен для сервісів з залежностями
  (репозиторії, клієнти зовнішніх API), не для кожного класу підряд.

## 5. Сучасний PHP 8.3+

Символа `psr/container` (пакет з інтерфейсами PSR-11) — окрема
Composer-залежність, не частина мови; фреймворки (Symfony
`DependencyInjection`, Laravel `Container`) мають значно потужніші
контейнери за приклад із розділу 3.4 (автоматичне визначення залежностей
через Reflection API за типами параметрів конструктора — "autowiring"),
але базовий принцип — той самий масив фабрик.

## 6. Контекст екосистеми

Symfony `DependencyInjection` і Laravel `Container` — промислові
DI-контейнери з autowiring, тегами сервісів, lazy-завантаженням. Вони
вирішують ту саму задачу, що й `Container` із розділу 3.4, просто з
набагато більшою кількістю функцій — розуміння простого випадку тут
готує до читання документації будь-якого з них без відчуття, що це
"чорна магія".

## 7. Документація

- https://www.php-fig.org/psr/psr-11/
- https://symfony.com/doc/current/components/dependency_injection.html
- https://martinfowler.com/articles/injection.html

## 8. Питання для самоперевірки

1. Чим Dependency Injection відрізняється від DI-контейнера?
2. Чому Service Locator вважається антипатерном, попри те, що теж
   "впроваджує" залежності через контейнер?
3. Що визначає PSR-11 і навіщо стандартизувати саме інтерфейс
   контейнера, а не його реалізацію?
4. Чому конструкторна ін'єкція робить `OrderService` (розділ 3.2)
   легшим для тестування, ніж версія з розділу 3.1?
