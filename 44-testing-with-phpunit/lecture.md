# 44. Тестування з PHPUnit

> Що ти вже вмієш: Jest/pytest/JUnit з іншої мови — та сама ідея, інший
> синтаксис · Що додає ця тема: з цього моменту `check.php` для кожної
> теми — тонкий запускач **справжнього** PHPUnit, а не власної бібліотеки
> ассертів курсу · ~30 хв

## 1. Якщо ти прийшов з JS / Python / Java

- PHPUnit — прямий аналог JUnit (Java, той самий автор ідеї
  xUnit-фреймворків), Jest (JS) чи pytest (Python): клас-нащадок
  `TestCase`, методи `test*()`, `assert*()`-твердження всередині.
- `#[DataProvider]` — те, що в pytest дає `@pytest.mark.parametrize`, а
  в JS — `test.each()`: один тест-метод, що виконується багато разів з
  різними наборами вхідних даних.
- Test doubles (mock/stub) — та сама ідея, що Jest `jest.mock()` чи
  Mockito (Java): підмінна реалізація залежності для ізольованого
  тестування, без реального зовнішнього ресурсу.

## 2. Ключові терміни

- **Test case (тест-кейс)** — клас, що успадковує
  `PHPUnit\Framework\TestCase`; кожен публічний метод з префіксом
  `test` (чи з атрибутом `#[Test]`) — окремий тест.
- **Assertion (твердження)** — виклик `$this->assertX(...)` усередині
  тесту; провал хоч одного твердження — тест "червоний".
- **Data provider (постачальник даних)** — метод (позначений
  `#[DataProvider('methodName')]`), що повертає масив наборів вхідних
  даних; тест-метод виконується окремо для кожного набору.
- **Test double (тестовий дублер)** — узагальнена назва для
  підмінних об'єктів у тестах: **stub** (повертає заготовлені дані),
  **mock** (додатково перевіряє, що метод був викликаний очікувану
  кількість разів з очікуваними аргументами).

## 3. Теорія

### 3.1 Базовий тест-кейс

```php
namespace App\Tests;

use App\Calculator;
use PHPUnit\Framework\TestCase;

final class CalculatorTest extends TestCase
{
    public function testAdd(): void
    {
        $calculator = new Calculator();

        $this->assertSame(5, $calculator->add(2, 3));
    }
}
```

```bash
vendor/bin/phpunit
```

### 3.2 `#[DataProvider]`: один тест, багато наборів даних

```php
use PHPUnit\Framework\Attributes\DataProvider;

#[DataProvider('divisionCases')]
public function testDivide(int $a, int $b, float $expected): void
{
    $calculator = new Calculator();

    $this->assertSame($expected, $calculator->divide($a, $b));
}

public static function divisionCases(): array
{
    return [
        'десять на два' => [10, 2, 5.0],
        "дев'ять на три" => [9, 3, 3.0],
    ];
}
```

Ключі масиву (`'десять на два'`) стають назвою конкретного запуску у
виводі PHPUnit — зручно одразу бачити, **який саме** набір даних
провалився, а не лише номер ітерації.

### 3.3 Очікування винятку

```php
public function testDivideByZeroThrows(): void
{
    $calculator = new Calculator();

    $this->expectException(\DivisionByZeroError::class);

    $calculator->divide(5, 0);
}
```

### 3.4 Test double: `createMock()`

```php
public function testRenderUsesFormatter(): void
{
    $formatter = $this->createMock(PriceFormatter::class);
    $formatter->expects($this->once())
        ->method('format')
        ->with(99.0)
        ->willReturn('99.00 UAH');

    $tag = new PriceTag($formatter);

    $this->assertSame('[99.00 UAH]', $tag->render(99.0));
}
```

`createMock()` створює об'єкт, що реалізує інтерфейс `PriceFormatter`,
не викликаючи жодної реальної логіки — `willReturn()` каже, що саме
повернути, `expects($this->once())` перевіряє, що метод дійсно
викликаний **рівно один раз**. Без DI (тема 41) — конструктор
`PriceTag`, що приймає `PriceFormatter` ззовні — така підміна була б
неможливою.

## 4. Підводні камені

- `assertEquals()` порівнює нестрого (`==`), `assertSame()` — строго
  (`===`, тема 03); для чисел з плаваючою комою (float) типово беруть
  `assertEquals()` через похибки округлення, для всього іншого —
  `assertSame()` за замовчуванням.
- Тест, що не має **жодного** `assert*()`-виклику, PHPUnit позначає як
  "risky" (підозрілий) — тест technically "зелений", але нічого
  реально не перевіряє. `$this->fail('текст')` (нема твердження, лише
  примусовий провал) — навпаки, коректний спосіб позначити "ще не
  реалізовано", саме так побудовані стартові тести в цій темі.
- Надмірне мокання (мок кожної дрібної залежності) робить тести
  крихкими: вони починають перевіряти **як** метод працює
  (implementation details), а не **що** він повертає — тест ламається
  при рефакторингу, що не міняє поведінку ззовні. Мокай межі системи
  (зовнішні API, файлова система, час) — не внутрішню логіку.

## 5. Сучасний PHP 8.3+

Атрибути (`#[DataProvider(...)]`, `#[Test]`, `#[CoversClass(...)]`) —
стандарт PHPUnit 10+ (PHP 8.0+ атрибути, тема "магічні методи" це не
зачіпало напряму, але сам синтаксис `#[...]` — PHP 8.0+). Старі
докблок-анотації (`/** @dataProvider divisionCases */`) досі
підтримуються для сумісності, але новий код пишеться саме через
атрибути.

## 6. Контекст екосистеми

З цієї теми `check.php` **перестає** бути власною бібліотекою курсу
(`_lib/check.php`) і стає тонким запускачем `vendor/bin/phpunit` —
команда лишається та сама (`php check.php`), але під капотом працює
інструмент, яким реально користуються в індустрії. CI (тема 48) для
будь-якого серйозного PHP-проєкту завжди має крок "прогнати PHPUnit" —
розуміння цієї теми напряму переноситься на реальну роботу.

## 7. Документація

- https://docs.phpunit.de/en/11.5/
- https://docs.phpunit.de/en/11.5/attributes.html
- https://docs.phpunit.de/en/11.5/test-doubles.html

## 8. Питання для самоперевірки

1. Чим `assertSame()` відрізняється від `assertEquals()` і коли
   кожен доречний?
2. Що дає `#[DataProvider]` порівняно з копіюванням тесту кілька разів
   вручну для різних вхідних даних?
3. Чим mock відрізняється від stub з точки зору того, що саме
   перевіряється?
4. Чому надмірне мокання внутрішньої логіки (не меж системи) робить
   тести крихкими?
