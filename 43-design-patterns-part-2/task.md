# Завдання 43: патерни проєктування, частина 2

## Мета

Реалізувати по одному представнику для: Strategy, Observer, Repository,
Service layer — і побачити, як вони природно поєднуються (Service layer
працює через Repository, обидва — приклади DI з теми 41).

## Вимоги

1. **Strategy.** Інтерфейс `DiscountStrategy` (`apply(float $price):
   float`); `NoDiscount` (повертає ціну без змін); `PercentageDiscount`
   (`__construct(float $percent)`, зменшує ціну на `$percent` відсотків);
   `PriceCalculator` (`__construct(DiscountStrategy $strategy)`,
   `finalPrice(float $price): float`).
2. **Observer.** Інтерфейс `Observer` (`update(string $event): void`);
   `EventEmitter` з `subscribe(Observer $observer): void` і
   `notify(string $event): void` (сповіщає **всіх** підписників у
   порядку підписки); `LoggingObserver implements Observer` — зберігає
   всі отримані події (`log(): array`).
3. **Repository.** Інтерфейс `UserRepository` (`save(array $user):
   void`, `findByEmail(string $email): ?array`); `InMemoryUserRepository`
   — зберігає користувачів у масиві в пам'яті, ключ — email.
4. **Service layer.** `RegistrationService` (`__construct(UserRepository
   $repository)`), `register(string $email, string $name): bool` —
   `false`, якщо користувач з таким email вже існує (і **не**
   перезаписує наявний запис); інакше зберігає й повертає `true`.

## Контракт (сигнатури)

```php
interface DiscountStrategy { public function apply(float $price): float; }
final class NoDiscount implements DiscountStrategy {}
final class PercentageDiscount implements DiscountStrategy { public function __construct(float $percent) }
final class PriceCalculator
{
    public function __construct(DiscountStrategy $strategy)
    public function finalPrice(float $price): float
}

interface Observer { public function update(string $event): void; }
final class EventEmitter
{
    public function subscribe(Observer $observer): void
    public function notify(string $event): void
}
final class LoggingObserver implements Observer
{
    public function update(string $event): void
    public function log(): array
}

interface UserRepository
{
    public function save(array $user): void;
    public function findByEmail(string $email): ?array;
}
final class InMemoryUserRepository implements UserRepository {}

final class RegistrationService
{
    public function __construct(UserRepository $repository)
    public function register(string $email, string $name): bool
}
```

## Обмеження

- Файл починається з `declare(strict_types=1);`.
- `RegistrationService` не звертається до `$_POST`/PDO/чогось
  зовнішнього напряму — лише через `UserRepository`, переданий у
  конструктор.
- `EventEmitter::notify()` сповіщає підписників у тому порядку, в якому
  вони підписались.

## Як перевірити

```bash
php check.php
```

## Definition of Done

- [ ] усі перевірки зелені
- [ ] можеш пояснити, чому `RegistrationService` можна протестувати
      без жодної реальної БД, використовуючи саме
      `InMemoryUserRepository`

## Бонус (не перевіряється автоматично)

Додай `FixedAmountDiscount implements DiscountStrategy` — зменшує ціну
на фіксовану суму (не нижче нуля).

<details>
<summary>Підказка 1</summary>

```php
final class PercentageDiscount implements DiscountStrategy
{
    public function __construct(private float $percent)
    {
    }

    public function apply(float $price): float
    {
        return $price * (1 - $this->percent / 100);
    }
}
```
</details>

<details>
<summary>Підказка 2</summary>

```php
final class EventEmitter
{
    private array $observers = [];

    public function subscribe(Observer $observer): void
    {
        $this->observers[] = $observer;
    }

    public function notify(string $event): void
    {
        foreach ($this->observers as $observer) {
            $observer->update($event);
        }
    }
}
```
</details>

<details>
<summary>Підказка 3</summary>

```php
final class RegistrationService
{
    public function __construct(private UserRepository $repository)
    {
    }

    public function register(string $email, string $name): bool
    {
        if ($this->repository->findByEmail($email) !== null) {
            return false;
        }

        $this->repository->save(['email' => $email, 'name' => $name]);

        return true;
    }
}
```
</details>
