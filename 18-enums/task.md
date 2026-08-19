# Завдання 18: enums

## Мета

1. Оголосити backed enum з методом.
2. Безпечно перетворювати рядки в кейси enum.
3. Реалізувати простий workflow через `match` над enum-кейсами.

## Вимоги

1. Backed enum `Status` (тип `string`) із трьома кейсами: `Draft = 'draft'`,
   `Published = 'published'`, `Archived = 'archived'`.
2. Метод `Status::label()` повертає українську назву кейса: `'Чернетка'`,
   `'Опубліковано'`, `'Архів'` відповідно — через `match ($this)`.
3. Функція `next_status(Status $status)` повертає наступний статус у
   послідовності `Draft -> Published -> Archived`; якщо статус уже
   `Archived`, повертає `Archived` знову (кінцевий стан).
4. Функція `parse_status(string $value)` безпечно перетворює рядок на
   `Status` — повертає `null`, якщо значення не відповідає жодному кейсу
   (без винятків).

## Контракт (сигнатури)

```php
enum Status: string
{
    case Draft = 'draft';
    case Published = 'published';
    case Archived = 'archived';

    public function label(): string
}

function next_status(Status $status): Status
function parse_status(string $value): ?Status
```

## Обмеження

- Файл починається з `declare(strict_types=1);`.
- `label()` — тільки через `match ($this) { ... }`, без `if`/`switch`.
- `parse_status()` — через `Status::tryFrom()`, не `Status::from()` в
  `try/catch` (винятки — тема 21, тут потрібен саме безвинятковий шлях).

## Як перевірити

```bash
php check.php
```

## Definition of Done

- [ ] усі перевірки зелені
- [ ] можеш пояснити, чому `Status::from()` тут був би гіршим вибором за
      `Status::tryFrom()`

## Бонус (не перевіряється автоматично)

Додай метод `Status::color(): string`, що повертає умовний колір для
бейджа в UI (`'gray'`/`'green'`/`'black'`).

<details>
<summary>Підказка 1</summary>

`next_status()`:
```php
function next_status(Status $status): Status
{
    return match ($status) {
        Status::Draft => Status::Published,
        Status::Published => Status::Archived,
        Status::Archived => Status::Archived,
    };
}
```
</details>

<details>
<summary>Підказка 2</summary>

`parse_status()` — буквально `return Status::tryFrom($value);` — `tryFrom()`
вже повертає `?Status` сам по собі.
</details>
