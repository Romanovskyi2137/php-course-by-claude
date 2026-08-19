# Завдання 10: функції для роботи з масивами

## Мета

1. Впевнено комбінувати `array_filter`/`array_map`/`array_reduce`.
2. Правильно прибирати "дірки" в ключах після фільтрації.
3. Написати власний компаратор для `usort`.
4. Використати `array_column` для індексації записів.

## Вимоги

1. `active_usernames(array $users)` приймає список асоціативних масивів
   `['name' => string, 'active' => bool]`, повертає **список** (послідовні
   ключі з 0) імен лише активних користувачів.
2. `total_price(array $items)` приймає список асоціативних масивів
   `['price' => float, 'qty' => int]` і повертає загальну суму (`price * qty`
   для кожного, просумовані) через `array_reduce`.
3. `sorted_by_score_desc(array $players)` приймає список асоціативних масивів
   `['name' => string, 'score' => int]` і повертає новий масив, відсортований
   за `score` **за спаданням**, через `usort` з власним компаратором.
4. `index_by_id(array $records)` приймає список асоціативних масивів з ключем
   `'id'` і повертає асоціативний масив, де ключ — значення `'id'`, а значення —
   весь оригінальний запис, через `array_column()`.

## Контракт (сигнатури)

```php
function active_usernames(array $users): array
function total_price(array $items): float
function sorted_by_score_desc(array $players): array
function index_by_id(array $records): array
```

## Обмеження

- Файл починається з `declare(strict_types=1);`.
- `active_usernames()` — результат `array_filter()` + `array_map()`, з
  `array_values()` наприкінці (щоб не лишати дірки в ключах).
- `sorted_by_score_desc()` не повинна змінювати переданий `$players` (усередині
  `usort()` виклич на **копії** масиву).
- `index_by_id()` — рівно один виклик `array_column()` з трьома аргументами.

## Як перевірити

```bash
php check.php
```

## Definition of Done

- [ ] усі перевірки зелені
- [ ] можеш пояснити, чому `array_filter()` без `array_values()` міг би зламати
      подальший код

## Бонус (не перевіряється автоматично)

Напиши `group_by(array $records, string $key): array`, яка групує записи за
значенням `$key` у масив списків (на відміну від `index_by_id`, де один `id`
відповідає рівно одному запису, тут кілька записів можуть мати однакове
значення групувального ключа).

<details>
<summary>Підказка 1</summary>

`active_usernames()`:
```php
$active = array_filter($users, fn ($u) => $u['active']);
$names = array_map(fn ($u) => $u['name'], $active);
return array_values($names);
```
</details>

<details>
<summary>Підказка 2</summary>

`sorted_by_score_desc()`: скопіюй масив у нову змінну (`$sorted = $players;`),
виклич `usort($sorted, fn ($a, $b) => $b['score'] <=> $a['score']);` (зверни
увагу на порядок `$b <=> $a` для спадання), поверни `$sorted`.
</details>

<details>
<summary>Підказка 3 (майже розв'язок)</summary>

```php
function index_by_id(array $records): array
{
    return array_column($records, null, 'id');
}
```
</details>
