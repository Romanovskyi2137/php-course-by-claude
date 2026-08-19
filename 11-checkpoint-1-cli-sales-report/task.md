# Checkpoint 1: вимоги

## Мета

Зібрати в одному невеликому проєкті: типізовані функції, `match`, `foreach`,
масиви та їхні функції, роботу з рядками/UTF-8 — усе з блоку A (теми 01-10).

## Що робимо

Реалізуємо 5 чистих функцій у `starter/src/functions.php`. "Чисті" — жодна
з них не звертається до `$argv`, не читає файли, не робить `echo`. Вся
робота з CLI-аргументами й виводом уже написана за тебе в
`starter/bin/report.php` — подивись туди, щоб зрозуміти, як функції
використовуються разом.

## Вимоги

1. `parse_filters(array $argv)` розбирає аргументи командного рядка виду
   `--category=Електроніка --from=2026-01-01 --to=2026-01-31` (елемент
   `$argv[0]` — це назва скрипта, його треба пропустити) у асоціативний масив
   `['category' => 'Електроніка', 'from' => '2026-01-01', 'to' => '2026-01-31']`.
   Підтримуються лише ключі, що починаються з `--` і містять `=`; усе інше
   ігнорується. Якщо аргументів фільтрації немає — повертається порожній масив.

2. `filter_sales(array $sales, array $filters)` повертає **список** (без дірок
   у ключах) записів, що задовольняють усі передані фільтри:
   - `$filters['category']`, якщо є — запис повинен мати точно такий `category`;
   - `$filters['from']`, якщо є — `date` запису `>=` цього значення;
   - `$filters['to']`, якщо є — `date` запису `<=` цього значення.
   Дати у форматі `Y-m-d` можна порівнювати як звичайні рядки (лексикографічний
   порядок для цього формату збігається з хронологічним).

3. `group_totals_by_category(array $sales)` повертає асоціативний масив
   `['категорія' => сумарна_виручка]`, де сумарна виручка — це сума `amount * qty`
   для всіх записів цієї категорії.

4. `sorted_rows_by_total_desc(array $totals)` приймає результат
   `group_totals_by_category()` (асоціативний масив `категорія => сума`) і
   повертає **список** асоціативних масивів `['category' => ..., 'total' => ...]`,
   відсортований за `total` **за спаданням**. Якщо суми рівні — за категорією
   за зростанням (алфавітний порядок), щоб результат був детермінованим.

5. `format_report_table(array $rows)` приймає результат
   `sorted_rows_by_total_desc()` і повертає багаторядковий рядок — по одному
   рядку на запис, без заголовка. Формат кожного рядка:
   назва категорії, доповнена пробілами праворуч до **20 символів** (не байтів —
   кирилиця має рахуватись правильно), одразу після — сума, відформатована
   через `number_format($total, 2)` і доповнена пробілами ліворуч до **10 символів**.

## Контракт (сигнатури)

```php
function parse_filters(array $argv): array
function filter_sales(array $sales, array $filters): array
function group_totals_by_category(array $sales): array
function sorted_rows_by_total_desc(array $totals): array
function format_report_table(array $rows): string
```

## Обмеження

- Файл починається з `declare(strict_types=1);`.
- `format_report_table()` — використай `mb_str_pad()` (PHP 8.3+), не `str_pad()`
  (він рахує байти, а не символи — зламається на кириличних назвах категорій).
- Жодна з п'яти функцій не робить `echo`/`print` і не звертається до `$argv`
  напряму — весь ввід приходить через аргументи функції.

## Як перевірити

```bash
php check.php
```

Після зеленого `check.php` — запусти сам CLI-звіт на реальних даних:

```bash
php starter/bin/report.php
php starter/bin/report.php --category=Електроніка
php starter/bin/report.php --from=2026-01-10 --to=2026-01-20
```

## Definition of Done

- [ ] усі перевірки check.php зелені
- [ ] `php starter/bin/report.php` реально виводить вирівняну таблицю з
      кириличними назвами категорій без "з'їхалих" колонок
- [ ] можеш пояснити, чому логіку винесено в чисті функції окремо від
      `bin/report.php`, а не написано все одним скриптом

## Підказки

<details>
<summary>Підказка 1 — parse_filters()</summary>

```php
function parse_filters(array $argv): array
{
    $filters = [];
    foreach (array_slice($argv, 1) as $arg) {
        if (!str_starts_with($arg, '--') || !str_contains($arg, '=')) {
            continue;
        }
        [$key, $value] = explode('=', substr($arg, 2), 2);
        $filters[$key] = $value;
    }
    return $filters;
}
```
</details>

<details>
<summary>Підказка 2 — sorted_rows_by_total_desc()</summary>

Спочатку перетвори асоціативний масив у список пар:
```php
$rows = [];
foreach ($totals as $category => $total) {
    $rows[] = ['category' => $category, 'total' => $total];
}
usort($rows, function (array $a, array $b): int {
    return $b['total'] <=> $a['total'] ?: $a['category'] <=> $b['category'];
});
```
</details>

<details>
<summary>Підказка 3 — format_report_table()</summary>

```php
function format_report_table(array $rows): string
{
    $lines = [];
    foreach ($rows as $row) {
        $lines[] = mb_str_pad($row['category'], 20)
            . mb_str_pad(number_format($row['total'], 2), 10, ' ', STR_PAD_LEFT);
    }
    return implode("\n", $lines);
}
```
</details>
