# Завдання 19: магічні методи та об'єктні протоколи

## Мета

1. Реалізувати динамічний контейнер властивостей через `__get`/`__set`/`__isset`.
2. Зробити об'єкт "рядковим" через `__toString`.
3. Реалізувати `ArrayAccess` + `Countable`, щоб об'єкт поводився як масив.

## Вимоги

1. Клас `DynamicBag` — зберігає довільні властивості у внутрішньому масиві,
   доступ через звичайний синтаксис `$bag->anything`: `__get()` повертає
   значення (або `null`, якщо не встановлено), `__set()` записує значення,
   `__isset()` каже, чи властивість встановлена.
2. Клас `Money implements \Stringable` — зберігає суму в **центах** (int),
   `__toString()` повертає рядок виду `'12.50'` (`number_format(cents/100, 2)`).
3. Клас `Config implements \ArrayAccess, \Countable` — обгортка над масивом:
   `$config['key']` читає й пише значення, `isset($config['key'])`/
   `unset($config['key'])` працюють, `count($config)` повертає кількість
   ключів.

## Контракт (сигнатури)

```php
class DynamicBag
{
    public function __get(string $name): mixed
    public function __set(string $name, mixed $value): void
    public function __isset(string $name): bool
}

class Money implements \Stringable
{
    public function __construct(int $cents)
    public function __toString(): string
}

class Config implements \ArrayAccess, \Countable
{
    public function __construct(array $data)
    public function offsetExists(mixed $offset): bool
    public function offsetGet(mixed $offset): mixed
    public function offsetSet(mixed $offset, mixed $value): void
    public function offsetUnset(mixed $offset): void
    public function count(): int
}
```

## Обмеження

- Файл починається з `declare(strict_types=1);`.
- `DynamicBag` не оголошує жодної публічної властивості — уся робота через
  `__get`/`__set`/`__isset` і приватний масив усередині.
- `Money::__toString()` — рівно одне значення, без символу валюти (просто
  число з двома знаками після коми).

## Як перевірити

```bash
php check.php
```

## Definition of Done

- [ ] усі перевірки зелені
- [ ] можеш пояснити, чому `__get`/`__set` не спрацювали б, якби властивості
      `DynamicBag` були публічними

## Бонус (не перевіряється автоматично)

Додай `Money::__invoke(int $multiplier): Money`, що повертає нову `Money`
із сумою, помноженою на `$multiplier`.

<details>
<summary>Підказка 1</summary>

`DynamicBag` — усередині рівно один приватний масив `private array $data = [];`,
усі три магічні методи читають/пишуть у нього за ключем `$name`.
</details>

<details>
<summary>Підказка 2</summary>

`Config` — теж один приватний масив усередині, чотири `offset*`-методи
транслюють `$config['key']`-синтаксис у звичайні операції з цим масивом
(`isset`, `??`, присвоєння за ключем, `unset`).
</details>
