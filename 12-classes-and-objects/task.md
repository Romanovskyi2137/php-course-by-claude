# Завдання 12: класи та об'єкти

## Мета

1. Написати клас з приватними властивостями, конструктором і методами.
2. Побачити на практиці, що зміна об'єкта через один "хендл" видно через інший.
3. Використати nullsafe-оператор для безпечного ланцюжка звернень.

## Що робимо

Клас `Point3D` — точка в тривимірному просторі, плюс невелика пара класів
`Address`/`Profile` для демонстрації `?->`.

## Вимоги

1. Клас `Point3D` з приватними властивостями `x`, `y`, `z` (усі `float`),
   конструктором, що приймає всі три, і методами-геттерами `x()`, `y()`, `z()`,
   які повертають відповідні значення.
2. Метод `Point3D::length()` повертає довжину радіус-вектора точки:
   `sqrt(x² + y² + z²)`.
3. Метод `Point3D::withX(float $x)` **змінює** властивість `x` цього ж об'єкта
   (не створює новий) і нічого не повертає — демонстрація мутації через `$this`.
4. Класи `Address` (публічна властивість `city`, встановлюється в конструкторі)
   і `Profile` (публічна властивість `address` типу `?Address`, за замовчуванням
   `null`). Функція `profile_city(Profile $profile)` повертає місто через
   nullsafe-оператор `?->` або `null`, якщо адреси немає.

## Контракт (сигнатури)

```php
class Point3D
{
    public function __construct(float $x, float $y, float $z)
    public function x(): float
    public function y(): float
    public function z(): float
    public function length(): float
    public function withX(float $x): void
}

class Address
{
    public function __construct(string $city)
    // публічна властивість city доступна як $address->city
}

class Profile
{
    public ?Address $address = null;
}

function profile_city(Profile $profile): ?string
```

## Обмеження

- Файл починається з `declare(strict_types=1);`.
- Властивості `Point3D` — `private`, доступ лише через геттери.
- `profile_city()` — рівно одне звернення через `?->`, без `if`/`isset()`.

## Як перевірити

```bash
php check.php
```

## Definition of Done

- [ ] усі перевірки зелені
- [ ] можеш пояснити, чому дві змінні, що вказують на один `Point3D`, обидві
      "бачать" виклик `withX()`

## Бонус (не перевіряється автоматично)

Додай метод `Point3D::distanceTo(Point3D $other): float` — відстань між двома
точками (довжина вектора різниці координат).

<details>
<summary>Підказка 1</summary>

`length()`: `return sqrt($this->x ** 2 + $this->y ** 2 + $this->z ** 2);`
</details>

<details>
<summary>Підказка 2</summary>

`profile_city()`: `return $profile->address?->city;` — якщо `$profile->address`
є `null`, весь вираз одразу повертає `null`, без звернення до `->city`.
</details>
