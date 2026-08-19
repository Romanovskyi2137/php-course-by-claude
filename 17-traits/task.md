# Завдання 17: traits

## Мета

1. Написати трейт зі своїм станом і поведінкою.
2. Використати кілька трейтів в одному класі.
3. Вирішити конфлікт однойменних методів через `insteadof`/`as`.

## Вимоги

1. Трейт `Loggable` з приватною властивістю-масивом і методами `log(string $message): void`
   (додає повідомлення) і `logs(): array` (повертає всі додані повідомлення
   в порядку додавання).
2. Трейт `Timestamped` з методами `touch(): void` (записує поточну дату у
   форматі `Y-m-d` у приватну властивість) і `createdAt(): ?string` (повертає
   записану дату або `null`, якщо `touch()` ще не викликали).
3. Клас `Article`, що використовує **обидва** трейти (`use Loggable, Timestamped;`).
4. Два трейти `Greets` і `Speaks`, обидва з методом `hello(): string`, що
   повертають різні рядки (`'Hello from Greets'` і `'Hello from Speaks'`
   відповідно). Клас `Robot` використовує обидва, вирішуючи конфлікт так,
   щоб `hello()` викликав саме `Greets::hello`, а версія з `Speaks`
   лишалась доступна під назвою `speaksHello()`.

## Контракт (сигнатури)

```php
trait Loggable
{
    public function log(string $message): void
    public function logs(): array
}

trait Timestamped
{
    public function touch(): void
    public function createdAt(): ?string
}

class Article
{
    use Loggable, Timestamped;
}

trait Greets
{
    public function hello(): string
}

trait Speaks
{
    public function hello(): string
}

class Robot
{
    use Greets, Speaks {
        // тут потрібне явне вирішення конфлікту
    }
}
```

## Обмеження

- Файл починається з `declare(strict_types=1);`.
- `Article` не оголошує власних методів — уся поведінка приходить із трейтів.
- Вирішення конфлікту в `Robot` — рівно через `insteadof` і `as` у блоці
  `use Greets, Speaks { ... }`, без перейменування самих методів у трейтах.

## Як перевірити

```bash
php check.php
```

Поки конфлікт `hello()` у `Robot` не вирішено, `php check.php` виведе не
звичайний звіт check.php, а **сирий PHP Fatal error** на кшталт
`Trait method Speaks::hello has not been applied...` — це сам PHP відмовляється
оголосити клас із невирішеним конфліктом ще до того, як встигли запуститись
тести. Це очікувано і саме про це розділ 3.3 лекції.

## Definition of Done

- [ ] усі перевірки зелені
- [ ] можеш пояснити, чому `$article instanceof Loggable` не є способом
      перевірити, чи клас використовує трейт (і що використати замість цього)

## Бонус (не перевіряється автоматично)

Додай трейту `Comparable` (з абстрактним методом `value(): int` і методом
`isGreaterThan(self $other): bool`) і клас, що його використовує.

<details>
<summary>Підказка 1</summary>

`Loggable::log()`/`logs()` — звичайний масив і `[] =`/`return`, так само,
як і будь-який інший масив у класі — трейт тут просто "переносить" ці два
методи й властивість у клас-хазяїн.
</details>

<details>
<summary>Підказка 2</summary>

Вирішення конфлікту в `Robot`:
```php
class Robot
{
    use Greets, Speaks {
        Greets::hello insteadof Speaks;
        Speaks::hello as speaksHello;
    }
}
```
</details>
