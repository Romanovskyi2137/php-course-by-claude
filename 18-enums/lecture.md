# 18. Enums (енуми)

> Що ти вже вмієш: enum з іншої мови (TS/Java/Kotlin) чи набір констант ·
> Що додає ця тема: PHP-специфіку "backed" vs "pure" enum і методи на енумах
> · ~20 хв

## 1. Якщо ти прийшов з JS / Python / Java

- PHP enums (8.1+) — обʼєкти першого класу, ближче до Java enum (можуть мати
  методи, реалізовувати інтерфейси) чи Kotlin `enum class`, ніж до TypeScript
  `enum` (який компілюється в об'єкт-словник) чи Python `Enum` (схожий, але
  PHP-варіант глибше інтегрований у типову систему).
  Java 8+ `enum` — найближчий концептуальний родич.
- До PHP 8.1 енумів не існувало взагалі — розробники імітували їх класами
  з константами (`class Status { const DRAFT = 'draft'; }`). Ти побачиш
  цей патерн у легасі-коді — енуми його повністю замінюють.
- На відміну від JS, де "enum" зазвичай просто обʼєкт з константами, PHP
  enum-значення — **типізовані**: параметр `function f(Status $s)` не
  прийме довільний рядок, тільки справжній кейс `Status`.

## 2. Ключові терміни

- **Pure enum (чистий enum)** — кейси без прив'язаного скалярного значення,
  лише самі себе: `enum Direction { case North; case South; }`.
- **Backed enum (енум із прив'язаним значенням)** — кожен кейс прив'язаний
  до `int` або `string`: `enum Status: string { case Draft = 'draft'; }`.
- **Case (кейс)** — один із можливих варіантів enum (`Status::Draft`).

## 3. Теорія

### 3.1 Pure enum

```php
enum Direction
{
    case North;
    case South;
    case East;
    case West;
}

$d = Direction::North;
$d === Direction::North;   // true — кейси порівнюються як обʼєкти-однакові-екземпляри
```

### 3.2 Backed enum: `from()`/`tryFrom()`

```php
enum Status: string
{
    case Draft = 'draft';
    case Published = 'published';
    case Archived = 'archived';
}

Status::Draft->value;         // 'draft' — доступ до прив'язаного значення

Status::from('draft');         // Status::Draft
Status::from('unknown');        // ValueError! from() кидає виняток на невідоме значення
Status::tryFrom('unknown');      // null — tryFrom() безпечний, не кидає
```

### 3.3 Методи на enum

```php
enum Status: string
{
    case Draft = 'draft';
    case Published = 'published';
    case Archived = 'archived';

    public function label(): string
    {
        return match ($this) {
            self::Draft => 'Чернетка',
            self::Published => 'Опубліковано',
            self::Archived => 'Архів',
        };
    }
}

Status::Draft->label();   // 'Чернетка'
```

Enum може мати методи (і навіть реалізовувати інтерфейси), точно як звичайний
клас — принципова відмінність від "енумів як констант" у PHP до 8.1.

### 3.4 `cases()`: усі варіанти

```php
Status::cases();   // [Status::Draft, Status::Published, Status::Archived]

foreach (Status::cases() as $status) {
    echo $status->value;
}
```

### 3.5 Константи в enum

```php
enum Status: string
{
    case Draft = 'draft';
    case Published = 'published';

    public const string DEFAULT = self::Draft->value;   // константа всередині enum
}
```

### 3.6 Enum у `match` — вичерпність

```php
function badge(Status $status): string
{
    return match ($status) {
        Status::Draft => 'gray',
        Status::Published => 'green',
        Status::Archived => 'black',
        // якщо забути один кейс і немає default — UnhandledMatchError
        // під час виконання (не помилка компіляції, PHP не робить exhaustiveness
        // checking на етапі статичного аналізу без інструментів на кшталт PHPStan)
    };
}
```

## 4. Підводні камені

- `from()` кидає `\ValueError`, а не повертає `null` — для "безпечного"
  парсингу завжди `tryFrom()`, якщо значення може бути некоректним
  (наприклад, прийшло з зовнішнього API чи форми).
- Pure enum **не має** `->value` — спроба звернутись кине помилку. Якщо
  потрібне серіалізоване значення (для БД, JSON, тема 25) — потрібен саме
  backed enum.
- Enum-кейси — це не рядки й не числа, навіть у backed enum: `Status::Draft === 'draft'`
  — `false`! Порівнюй або самі кейси (`Status::Draft === $x`), або `->value`
  з рядком (`$x->value === 'draft'`).

## 5. Сучасний PHP 8.3+

Enums — одна з найпомітніших фіч PHP 8.1, яка замінила десятиліття патерну
"клас із константами" в реальному коді. У PHP 8.3 enum-кейси також можуть
мати типізовані константи (та сама фіча, що для звичайних класів, тема 14).

## 6. Контекст екосистеми

Enum — стандартний вибір для "закритого набору варіантів" у сучасних
Laravel/Symfony-проєктах: статуси замовлень, ролі користувачів, типи подій —
усе, що раніше було рядковими константами й вимагало "довіри", тепер
типізовано мовою напряму.

## 7. Документація

- https://www.php.net/manual/en/language.enumerations.php
- https://www.php.net/manual/en/language.enumerations.backed.php

## 8. Питання для самоперевірки

1. Чим backed enum відрізняється від pure enum?
2. Чому `Status::from('unknown')` небезпечно викликати без `try/catch`
   на зовнішніх даних, і що варто використати замість цього?
3. Чи можна порівнювати `Status::Draft === 'draft'` напряму? Чому?
4. Що поверне `Status::cases()`?
