# Завдання 14: static, константи класу, LSB

## Мета

1. Написати клас зі статичним станом (лічильник).
2. Побачити різницю `self::` і `static::` наживо, на одній і тій самій ієрархії класів.
3. Реалізувати фабричний метод, що коректно працює для підкласів через `new static()`.

## Вимоги

1. Клас `IdGenerator` зі статичним внутрішнім лічильником:
   `next()` повертає наступне число, починаючи з 1; `current()` повертає
   останнє видане число без інкременту (`0`, якщо `next()` ще не викликали);
   `reset()` скидає лічильник до `0`.
2. Клас `Model` з константою `TABLE = 'models'` і двома статичними методами:
   `tableViaSelf()` повертає `self::TABLE`, `tableViaStatic()` повертає
   `static::TABLE`. Клас `Product extends Model` з власною константою
   `TABLE = 'products'` — уже написаний у `starter.php`, редагувати не треба.
3. Статичний метод `Model::create()` повертає **новий екземпляр того класу,
   з якого його реально викликали** (`new static()`, не `new self()`).
   Клас `Widget extends Model` — уже написаний, редагувати не треба.

## Контракт (сигнатури)

```php
class IdGenerator
{
    public static function next(): int
    public static function current(): int
    public static function reset(): void
}

class Model
{
    public const string TABLE = 'models';
    public static function tableViaSelf(): string
    public static function tableViaStatic(): string
    public static function create(): static
}
```

## Обмеження

- Файл починається з `declare(strict_types=1);`.
- `tableViaSelf()` — рівно `self::TABLE`. `tableViaStatic()` — рівно
  `static::TABLE`. Не міняй місцями (у цьому й суть вправи).
- `create()` — рівно `new static();`, не `new self()` і не `new Model()`.

## Як перевірити

```bash
php check.php
```

## Definition of Done

- [ ] усі перевірки зелені
- [ ] можеш словами пояснити, чому `Product::tableViaSelf()` повертає
      `'models'`, а `Product::tableViaStatic()` — `'products'`

## Бонус (не перевіряється автоматично)

Додай `Model::describe(): string`, яка повертає рядок на кшталт
`"Model backed by table: products"`, використовуючи `static::TABLE`
всередині нестатичного контексту теж можна — правило LSB працює однаково.

<details>
<summary>Підказка 1</summary>

`IdGenerator` — той самий патерн, що `static`-змінна всередині функції
(тема 07), тільки тепер це `private static int $value` на рівні класу,
а не `static` всередині тіла функції.
</details>

<details>
<summary>Підказка 2</summary>

`create()`:
```php
public static function create(): static
{
    return new static();
}
```
Виклич `Widget::create()` — отримаєш `Widget`, хоча метод оголошений у `Model`.
</details>
