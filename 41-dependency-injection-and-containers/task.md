# Завдання 41: Dependency Injection та контейнери

## Мета

1. Написати інтерфейс і реалізацію, придатну для підміни в тестах.
2. Впровадити залежність через конструктор (DI), а не створювати її
   всередині класу.
3. Реалізувати мінімальний DI-контейнер: реєстрація фабрик, лінива
   побудова, кешування вже створених екземплярів.

## Вимоги

1. Інтерфейс `Logger` з методом `log(string $message): void`; клас
   `ArrayLogger implements Logger` — зберігає повідомлення в
   пам'яті (`private array`), метод `messages(): array` повертає їх
   усі.
2. Клас `OrderService` з конструктором, що приймає `Logger` (DI, не
   створює логер сам); метод `placeOrder(string $item): void` викликає
   `$logger->log("Order placed: {$item}")`.
3. Клас `Container`:
   - `set(string $id, \Closure $factory): void` — реєструє фабрику;
     `$factory` приймає сам контейнер як аргумент (щоб фабрика могла
     дістати інші залежності через `$container->get(...)`);
   - `get(string $id): mixed` — лінива побудова через зареєстровану
     фабрику; **той самий** екземпляр при повторному виклику з тим
     самим `$id` (singleton-поведінка); кидає `\OutOfBoundsException`
     для незареєстрованого `$id`;
   - `has(string $id): bool` — чи зареєстрована фабрика для `$id`.

## Контракт (сигнатури)

```php
interface Logger
{
    public function log(string $message): void;
}

final class ArrayLogger implements Logger
{
    public function log(string $message): void
    public function messages(): array
}

final class OrderService
{
    public function __construct(Logger $logger)
    public function placeOrder(string $item): void
}

final class Container
{
    public function set(string $id, \Closure $factory): void
    public function get(string $id): mixed
    public function has(string $id): bool
}
```

## Обмеження

- Файл починається з `declare(strict_types=1);`.
- `OrderService` не створює `Logger` сам і не звертається до жодного
  глобального реєстру всередині методів (не Service Locator) — єдиний
  спосіб отримати логер — через конструктор.
- `Container::get()` викликає фабрику **не раніше**, ніж її вперше
  реально запитають (лінива побудова, не одразу при `set()`).

## Як перевірити

```bash
php check.php
```

## Definition of Done

- [ ] усі перевірки зелені
- [ ] можеш пояснити, чим `Container::get()` (вимога 3) принципово
      відрізняється від виклику `ServiceLocator::get()` **зсередини**
      `OrderService::placeOrder()`

## Бонус (не перевіряється автоматично)

Додай `Container::extend(string $id, \Closure $decorator): void` —
обгортає вже зареєстровану фабрику додатковою логікою (наприклад,
логування кожного виклику `get()` для конкретного сервісу), не
змінюючи оригінальну реєстрацію.

<details>
<summary>Підказка 1</summary>

```php
final class ArrayLogger implements Logger
{
    private array $messages = [];

    public function log(string $message): void
    {
        $this->messages[] = $message;
    }

    public function messages(): array
    {
        return $this->messages;
    }
}
```
</details>

<details>
<summary>Підказка 2</summary>

```php
final class OrderService
{
    public function __construct(private Logger $logger)
    {
    }

    public function placeOrder(string $item): void
    {
        $this->logger->log("Order placed: {$item}");
    }
}
```
</details>

<details>
<summary>Підказка 3</summary>

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
        if (!array_key_exists($id, $this->instances)) {
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
```
</details>
