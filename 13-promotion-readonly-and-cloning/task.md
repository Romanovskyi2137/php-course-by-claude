# Завдання 13: promotion, readonly, клонування

## Мета

1. Писати класи в сучасному стилі — constructor property promotion.
2. Побудувати незмінний (`readonly`) клас з "wither"-методом.
3. Виправити типову пастку поверхневого клонування через `__clone()`.

## Вимоги

1. Клас `Vector2D` з `readonly` властивостями `x`, `y` (обидві `float`),
   оголошеними через **constructor property promotion** (без ручного
   `$this->x = $x;`).
2. Метод `Vector2D::add(Vector2D $other)` повертає **новий** `Vector2D` із
   сумою відповідних координат, не змінюючи ні `$this`, ні `$other`.
3. Спроба записати `$vector->x = ...` ззовні класу має кидати `\Error`
   (це вбудована поведінка `readonly` — просто переконайся, що властивості
   дійсно `readonly`).
4. Класи `Tag` (властивість `name`, через promotion) і `Box` (властивість
   `tag` типу `Tag`, через promotion). У `Box` реалізуй `__clone()` так,
   щоб клонування `Box` створювало **незалежну** копію вкладеного `Tag`
   (зміна `name` в копії не повинна впливати на оригінал, і навпаки).

## Контракт (сигнатури)

```php
class Vector2D
{
    public function __construct(public readonly float $x, public readonly float $y)
    public function add(Vector2D $other): Vector2D
}

class Tag
{
    public function __construct(public string $name)
}

class Box
{
    public function __construct(public Tag $tag)
    public function __clone(): void
}
```

## Обмеження

- Файл починається з `declare(strict_types=1);`.
- Усі чотири властивості (`Vector2D::$x`, `Vector2D::$y`, `Tag::$name`,
  `Box::$tag`) оголошені через promotion у конструкторі, без окремих
  `private float $x;` рядків над конструктором.
- `Vector2D::$x`/`$y` — саме `readonly`.

## Як перевірити

```bash
php check.php
```

## Definition of Done

- [ ] усі перевірки зелені
- [ ] можеш пояснити, чому без `__clone()` у `Box` зміна тегу копії вплинула б
      на оригінал

## Бонус (не перевіряється автоматично)

Додай `Vector2D::scale(float $factor): Vector2D` — теж повертає новий вектор,
з координатами, помноженими на `$factor`.

<details>
<summary>Підказка 1</summary>

`add()`: `return new self($this->x + $other->x, $this->y + $other->y);`
</details>

<details>
<summary>Підказка 2</summary>

`Box::__clone()`:
```php
public function __clone(): void
{
    $this->tag = clone $this->tag;
}
```
Викликається автоматично одразу після `clone $box` — на момент виклику
`$this` уже поверхнева копія, тут ти "заглиблюєш" копіювання ще на один рівень.
</details>
