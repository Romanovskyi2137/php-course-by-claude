# 43. Патерни проєктування, частина 2

> Що ти вже вмієш: Strategy/Observer з іншої мови, Repository — з
> будь-якого досвіду роботи з БД · Що додає ця тема: як ці патерни
> природно поєднуються з тим, що вже пройдено (інтерфейси — тема 16,
> DI — тема 41, PDO — теми 36-37) · ~30 хв

## 1. Якщо ти прийшов з JS / Python / Java

- Strategy — той самий принцип, що передача функції як стратегії в
  JS (`array.sort(comparator)`) чи Python (`sorted(key=...)`), тільки
  виражений через інтерфейс + клас замість функції першого класу —
  корисно, коли "стратегія" — не одна операція, а набір пов'язаної
  поведінки.
- Observer — той самий патерн, що `EventEmitter` у Node.js чи
  `addEventListener` у браузері: підписники реагують на подію, не
  знаючи одне про одного.
- Repository — той самий підхід, що Repository в Spring Data (Java) чи
  `Manager`-класи в Django ORM: приховує деталі зберігання (SQL,
  файл, пам'ять) за колекціє-подібним інтерфейсом.

## 2. Ключові терміни

- **Strategy (стратегія)** — інкапсулює взаємозамінний алгоритм за
  спільним інтерфейсом, обраний і переданий ззовні (типово через DI).
- **Observer (спостерігач)** — об'єкт підписується на сповіщення від
  іншого об'єкта (суб'єкта), не знаючи наперед, скільки й яких саме
  підписників буде.
- **Repository (репозиторій)** — абстракція над сховищем даних, що дає
  доступ до сутностей через колекціє-подібний інтерфейс
  (`save()`/`find()`), приховуючи, чи це БД, файл, чи масив у пам'яті.
- **Service layer (сервісний шар)** — клас, що оркеструє один
  бізнес-сценарій (use case), використовуючи один чи кілька
  репозиторіїв і доменних об'єктів.

## 3. Теорія

### 3.1 Strategy: взаємозамінний алгоритм

```php
interface DiscountStrategy
{
    public function apply(float $price): float;
}

final class NoDiscount implements DiscountStrategy
{
    public function apply(float $price): float { return $price; }
}

final class PercentageDiscount implements DiscountStrategy
{
    public function __construct(private float $percent) {}
    public function apply(float $price): float { return $price * (1 - $this->percent / 100); }
}

final class PriceCalculator
{
    public function __construct(private DiscountStrategy $strategy) {}
    public function finalPrice(float $price): float { return $this->strategy->apply($price); }
}

new PriceCalculator(new PercentageDiscount(10))->finalPrice(100.0); // 90.0
```

Той самий `PriceCalculator`, різна поведінка залежно від того, яку
`DiscountStrategy` передали — типовий приклад DI (тема 41) у дії.

### 3.2 Observer: підписка на сповіщення

```php
interface Observer
{
    public function update(string $event): void;
}

final class EventEmitter
{
    private array $observers = [];

    public function subscribe(Observer $observer): void { $this->observers[] = $observer; }

    public function notify(string $event): void
    {
        foreach ($this->observers as $observer) {
            $observer->update($event);
        }
    }
}

final class LoggingObserver implements Observer
{
    private array $log = [];
    public function update(string $event): void { $this->log[] = $event; }
    public function log(): array { return $this->log; }
}

$emitter = new EventEmitter();
$logger = new LoggingObserver();
$emitter->subscribe($logger);
$emitter->notify('order.placed'); // $logger->log() тепер містить 'order.placed'
```

`EventEmitter` нічого не знає про `LoggingObserver` конкретно — лише
про інтерфейс `Observer`; можна підписати скільки завгодно спостерігачів
різних типів без зміни `EventEmitter`.

### 3.3 Repository: абстракція над сховищем

```php
interface UserRepository
{
    public function save(array $user): void;
    public function findByEmail(string $email): ?array;
}

final class InMemoryUserRepository implements UserRepository
{
    private array $users = [];

    public function save(array $user): void { $this->users[$user['email']] = $user; }
    public function findByEmail(string $email): ?array { return $this->users[$email] ?? null; }
}
```

`InMemoryUserRepository` — реалізація для тестів (без БД, тема 44); у
продакшн-коді була б `PdoUserRepository`, що робить те саме через
`PDO` (теми 36-37), реалізуючи **той самий** інтерфейс `UserRepository`
— решта коду не бачить різниці.

### 3.4 Service layer: оркестрація use case

```php
final class RegistrationService
{
    public function __construct(private UserRepository $repository) {}

    public function register(string $email, string $name): bool
    {
        if ($this->repository->findByEmail($email) !== null) {
            return false; // вже зареєстрований
        }

        $this->repository->save(['email' => $email, 'name' => $name]);

        return true;
    }
}
```

`RegistrationService` не знає, **як саме** зберігаються користувачі
(масив у пам'яті чи БД) — лише **що** треба зробити (перевірити
дублікат, зберегти). Це і є сенс сервісного шару: бізнес-логіка
use case окремо від деталей зберігання.

## 4. Підводні камені

- Repository, що просто **дублює** методи `PDO` один-в-один
  (`query(string $sql)`) — не додає жодної абстракції, лише зайвий шар
  непрямоти. Корисний Repository говорить мовою **домену**
  (`findByEmail()`, `findActiveUsers()`), не мовою SQL.
- Service layer, що напряму містить SQL-запити чи звертається до
  `$_POST` — змішування шарів: сервіс має отримувати вже підготовлені
  дані (скалярні аргументи) і працювати через репозиторії, а не знати
  про HTTP чи SQL напряму.
- Observer без чіткого контракту, **що саме** передається в `update()`
  (просто рядок з назвою події, як у прикладі, чи об'єкт з деталями) —
  для реальних застосунків типово краще типізований об'єкт-подія
  (`OrderPlaced` DTO), а не голий рядок — простий приклад тут навмисно
  спрощений для ілюстрації самого патерну.

## 5. Сучасний PHP 8.3+

Enum-и (тема 18) добре пасують для типізованого набору стратегій,
відомого наперед (`DiscountType::Percentage`/`DiscountType::Fixed`) —
альтернатива класам-стратегіям, коли самих стратегій небагато й вони
прості. Клас-Strategy (розділ 3.1) виграє, коли в кожної стратегії своя,
потенційно складна, реалізація.

## 6. Контекст екосистеми

Repository + Service layer — стандартна архітектура для нетривіальних
Laravel/Symfony-застосунків: контролер приймає HTTP-запит, викликає
сервіс, сервіс працює через репозиторій. Checkpoint 5 (тема 49)
використає саме цю структуру для REST API.

## 7. Документація

- https://refactoring.guru/design-patterns/strategy
- https://refactoring.guru/design-patterns/observer
- https://martinfowler.com/eaaCatalog/repository.html

## 8. Питання для самоперевірки

1. Чим Strategy відрізняється від простої передачі функції-колбека?
2. Що дозволяє `EventEmitter` підписувати спостерігачів різних типів,
   не змінюючи власний код?
3. Чому `InMemoryUserRepository` і майбутня `PdoUserRepository` можуть
   бути взаємозамінні для `RegistrationService`?
4. Чому service layer не повинен містити SQL напряму?
