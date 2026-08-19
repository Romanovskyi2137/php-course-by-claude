# Завдання 09: масиви та деструктуризація

## Мета

1. Впевнено користуватись деструктуризацією списків.
2. Побачити на практиці value semantics масивів (копія, а не посилання).
3. Навчитись зливати масиви через spread зі строковими ключами.

## Вимоги

1. `swap(array $pair)` приймає масив з рівно двох елементів `[$a, $b]` і повертає
   `[$b, $a]` — реалізуй через деструктуризацію `[$a, $b] = $pair;`, не через
   `$pair[0]`/`$pair[1]`.
2. `merge_configs(array $defaults, array $overrides)` повертає новий масив, де
   значення з `$overrides` перезаписують однакові ключі з `$defaults`, а решта
   ключів `$defaults` зберігається — через spread-оператор `[...$a, ...$b]`,
   не `array_merge()`.
3. `append(array $items, mixed $value)` повертає **новий** масив з доданим
   `$value` в кінець, **не змінюючи** переданий `$items`.
4. `pluck(array $records, string $key)` приймає список асоціативних масивів
   і повертає простий (індексований) масив значень за ключем `$key` з кожного
   запису — через `foreach`, без `array_column()` (це тема 10).

## Контракт (сигнатури)

```php
function swap(array $pair): array
function merge_configs(array $defaults, array $overrides): array
function append(array $items, mixed $value): array
function pluck(array $records, string $key): array
```

## Обмеження

- Файл починається з `declare(strict_types=1);`.
- `merge_configs()` — тільки spread, без `array_merge()`/оператора `+` (мета —
  практика саме spread-синтаксису).
- `append()` не повинен змінювати оригінальний `$items` (це і так гарантовано
  value semantics PHP-масивів, якщо не використовувати `&` — просто пиши код
  природно, і тест це підтвердить).

## Як перевірити

```bash
php check.php
```

## Definition of Done

- [ ] усі перевірки зелені
- [ ] можеш пояснити, чому `append()` не змінює оригінал, навіть без спеціальних
      зусиль з твого боку

## Бонус (не перевіряється автоматично)

Напиши `deep_merge(array $a, array $b): array`, яка рекурсивно зливає вкладені
асоціативні масиви (а не лише верхній рівень, як `merge_configs()`).

<details>
<summary>Підказка 1</summary>

`merge_configs()` — буквально `return [...$defaults, ...$overrides];`. Пізніший
spread перезаписує однакові ключі, як в об'єктному literal spread у JS.
</details>

<details>
<summary>Підказка 2</summary>

`pluck()`:
```php
function pluck(array $records, string $key): array
{
    $result = [];
    foreach ($records as $record) {
        $result[] = $record[$key];
    }
    return $result;
}
```
</details>
