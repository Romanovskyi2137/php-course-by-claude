# Завдання 21: помилки та винятки

## Мета

1. Написати власний клас винятку.
2. Кидати й перехоплювати винятки в реалістичному сценарії (рахунок/зняття коштів).
3. Відрізняти `Exception` (варто ловити) від `Error` (сигнал бага, але тут —
   навчальний приклад перехоплення вбудованого `Error`).

## Вимоги

1. Клас `InsufficientFundsException extends \RuntimeException` — без
   додаткової логіки, лише власний тип для розрізнення причини помилки.
2. Клас `Account` з конструктором (`float $balance = 0.0`), методом
   `withdraw(float $amount): void`, що кидає `InsufficientFundsException`,
   якщо `$amount` більше за поточний баланс (з повідомленням, що містить
   і запитану суму, і наявний баланс), інакше зменшує баланс на `$amount`,
   і методом `balance(): float`.
3. `safe_withdraw(Account $account, float $amount)` викликає
   `$account->withdraw($amount)` у `try/catch`; повертає `true`, якщо
   зняття вдалось, `false` — якщо було спіймано `InsufficientFundsException`
   (виняток **не** повинен "просочитись" далі назовні `safe_withdraw`).
4. `safe_divide(int $a, int $b)` повертає `$a / $b` як `float`, а якщо
   `$b === 0` (ділення кидає вбудований `\DivisionByZeroError`) — повертає
   `null` замість того, щоб дати помилці впасти назовні.

## Контракт (сигнатури)

```php
class InsufficientFundsException extends \RuntimeException
{
}

class Account
{
    public function __construct(float $balance = 0.0)
    public function withdraw(float $amount): void
    public function balance(): float
}

function safe_withdraw(Account $account, float $amount): bool
function safe_divide(int $a, int $b): ?float
```

## Обмеження

- Файл починається з `declare(strict_types=1);`.
- `safe_withdraw()` ловить саме `InsufficientFundsException`, не
  `\Throwable`/`\Exception` загалом — щоб не приховати інші, неочікувані помилки.
- `safe_divide()` ловить саме `\DivisionByZeroError`.

## Як перевірити

```bash
php check.php
```

## Definition of Done

- [ ] усі перевірки зелені
- [ ] можеш пояснити, чому `safe_withdraw()` ловить конкретний клас
      винятку, а не `\Exception` взагалі

## Бонус (не перевіряється автоматично)

Додай `Account::history(): array` — список рядків-описів усіх успішних
і невдалих спроб зняття коштів (успішні — `"-{amount}"`, невдалі —
`"REJECTED: -{amount}"`).

<details>
<summary>Підказка 1</summary>

```php
public function withdraw(float $amount): void
{
    if ($amount > $this->balance) {
        throw new InsufficientFundsException(
            "Недостатньо коштів: запит {$amount}, наявно {$this->balance}"
        );
    }
    $this->balance -= $amount;
}
```
</details>

<details>
<summary>Підказка 2</summary>

```php
function safe_divide(int $a, int $b): ?float
{
    try {
        return $a / $b;
    } catch (\DivisionByZeroError) {
        return null;
    }
}
```
Зверни увагу на non-capturing catch — тут `$e` не потрібен, бо просто
повертаємо `null`.
</details>
