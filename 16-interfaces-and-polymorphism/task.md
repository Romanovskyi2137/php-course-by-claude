# Завдання 16: інтерфейси та поліморфізм

## Мета

1. Оголосити інтерфейс і реалізувати його в кількох класах.
2. Написати функцію, яка працює з будь-яким об'єктом за контрактом, а не за
   конкретним класом.

## Вимоги

1. Інтерфейс `Priced` з одним методом `price(): float`.
2. Клас `Book implements Priced` — конструктор приймає вартість, `price()`
   повертає її як є.
3. Клас `Ticket implements Priced` — конструктор приймає базову ціну й
   сервісний збір окремо, `price()` повертає їхню суму.
4. `total_price(array $items)` приймає масив об'єктів `Priced` і повертає
   суму `price()` усіх елементів. Якщо серед елементів є хоч один, що **не**
   реалізує `Priced`, кидає `\InvalidArgumentException`.
5. `most_expensive(array $items)` приймає непорожній масив `Priced` і
   повертає сам об'єкт (не число) із найбільшою `price()`.

## Контракт (сигнатури)

```php
interface Priced
{
    public function price(): float;
}

class Book implements Priced
{
    public function __construct(float $cost)
    public function price(): float
}

class Ticket implements Priced
{
    public function __construct(float $basePrice, float $fee)
    public function price(): float
}

function total_price(array $items): float
function most_expensive(array $items): Priced
```

## Обмеження

- Файл починається з `declare(strict_types=1);`.
- `total_price()` і `most_expensive()` не повинні містити жодного `instanceof Book`
  чи `instanceof Ticket` — тільки `instanceof Priced` (для перевірки контракту
  у `total_price()`) і виклики `->price()`. Це і є суть поліморфізму: код не
  знає про конкретні класи.

## Як перевірити

```bash
php check.php
```

## Definition of Done

- [ ] усі перевірки зелені
- [ ] можеш пояснити, чому `total_price()` не потрібно змінювати, якщо
      завтра з'явиться третій клас `Subscription implements Priced`

## Бонус (не перевіряється автоматично)

Додай клас `Subscription implements Priced` (наприклад, місячна плата ×
кількість місяців) — і переконайся, що `total_price()`/`most_expensive()`
працюють з ним без жодної зміни свого коду.

<details>
<summary>Підказка 1</summary>

`total_price()`:
```php
function total_price(array $items): float
{
    $total = 0.0;
    foreach ($items as $item) {
        if (!$item instanceof Priced) {
            throw new \InvalidArgumentException('Очікував Priced');
        }
        $total += $item->price();
    }
    return $total;
}
```
</details>

<details>
<summary>Підказка 2</summary>

`most_expensive()` можна написати через `usort` з компаратором за `price()`
і взяти перший елемент, або через простий `foreach` з відстеженням
максимуму — обидва варіанти прийнятні.
</details>
