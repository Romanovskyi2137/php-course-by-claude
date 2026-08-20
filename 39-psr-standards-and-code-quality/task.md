# Завдання 39: PSR-стандарти та якість коду

## Мета

1. Порахувати шлях до файлу за PSR-4-мапінгом як чисту функцію
   (застосування правила з теми 38, а не Composer напряму).
2. Написати предикати, що перевіряють PSR-1-іменування самі по собі.
3. Відрефакторити реально працюючий, але погано названий і
   відформатований клас під PSR-1/12 — без зміни поведінки.

## Вимоги

1. `psr4_file_path(string $fqcn, string $namespacePrefix, string
   $baseDir)` — за повним іменем класу, префіксом простору імен
   (як у `composer.json` `autoload.psr-4`) і базовою директорією
   повертає очікуваний шлях до файлу. Приклад:
   `psr4_file_path('App\\Billing\\Invoice', 'App\\', 'src')` →
   `'src/Billing/Invoice.php'`.
2. `is_valid_psr1_class_name(string $name)` — `true`, якщо ім'я
   відповідає `StudlyCaps` (перша літера велика, далі — літери й
   цифри, без підкреслень).
3. `is_valid_psr1_method_name(string $name)` — `true`, якщо ім'я
   відповідає `camelCase` (перша літера мала, далі — літери й цифри,
   без підкреслень).
4. У файлі є клас `report_generator` з методом `generate_summary($items)`
   — код **робочий**, але порушує PSR-1 (іменування) і написаний з
   табуляцією замість пробілів. Перейменуй клас на `ReportGenerator`,
   метод — на `generateSummary(array $items): string`, і переформатуй
   під 4 пробіли — **поведінку не міняй**.

## Контракт (сигнатури)

```php
function psr4_file_path(string $fqcn, string $namespacePrefix, string $baseDir): string
function is_valid_psr1_class_name(string $name): bool
function is_valid_psr1_method_name(string $name): bool

final class ReportGenerator
{
    public function generateSummary(array $items): string
}
```

`generateSummary()` повертає `"Total: {$sum}"`, де `$sum` — сума всіх
елементів `$items`.

## Обмеження

- Файл починається з `declare(strict_types=1);`.
- Увесь файл — 4 пробіли для відступів, жодної табуляції (перевіряється
  автоматично).
- Клас саме `ReportGenerator` (не `Report_Generator`, не
  `reportGenerator`), метод саме `generateSummary`.

## Як перевірити

```bash
php check.php
```

## Definition of Done

- [ ] усі перевірки зелені
- [ ] можеш пояснити, чому `is_valid_psr1_class_name('report_generator')`
      повертає `false`, а `is_valid_psr1_class_name('ReportGenerator')`
      — `true`

## Бонус (не перевіряється автоматично)

Якщо встановлено `phpstan/phpstan` (тема 38, `composer require --dev`)
— прожени `vendor/bin/phpstan analyse starter.php --level=5` і подивись,
що він скаже про типи в цьому файлі.

<details>
<summary>Підказка 1</summary>

```php
function psr4_file_path(string $fqcn, string $namespacePrefix, string $baseDir): string
{
    $relative = substr($fqcn, strlen($namespacePrefix));
    $relativePath = str_replace('\\', '/', $relative);

    return rtrim($baseDir, '/') . '/' . $relativePath . '.php';
}
```
</details>

<details>
<summary>Підказка 2</summary>

```php
function is_valid_psr1_class_name(string $name): bool
{
    return preg_match('/^[A-Z][A-Za-z0-9]*$/', $name) === 1;
}

function is_valid_psr1_method_name(string $name): bool
{
    return preg_match('/^[a-z][A-Za-z0-9]*$/', $name) === 1;
}
```
</details>

<details>
<summary>Підказка 3</summary>

```php
final class ReportGenerator
{
    public function generateSummary(array $items): string
    {
        $total = 0;
        foreach ($items as $item) {
            $total += $item;
        }

        return "Total: {$total}";
    }
}
```
</details>
