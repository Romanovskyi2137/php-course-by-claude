# Завдання 30: форми, валідація та завантаження файлів

## Мета

1. Валідувати дані форми через `filter_var()`, зібрати помилки в масив.
2. Побудувати URL для PRG-редіректу.
3. Перевірити завантажений файл за реальним MIME-типом, не за заявленим клієнтом.

## Вимоги

1. `validate_registration(array $input)` — валідує асоціативний масив
   з ключами `email`, `password`, `age` (усі — рядки, як прийшли б із
   `$_POST`). Правила:
   - `email` — валидний email (`FILTER_VALIDATE_EMAIL`);
   - `password` — не коротший за 8 символів;
   - `age` — ціле число від 13 до 120 (`FILTER_VALIDATE_INT` з
     `min_range`/`max_range`).
   Повертає `['valid' => bool, 'errors' => array]`, де `errors` —
   асоціативний масив `['поле' => 'повідомлення']` лише для полів, що не
   пройшли валідацію (порожній, якщо `valid === true`).
2. `redirect_url(string $path, array $query = [])` — будує URL для
   PRG-редіректу: `$path`, і якщо `$query` не порожній — `?` та
   query-рядок (`http_build_query()`).
3. `is_allowed_upload(array $file, array $allowedMimeTypes)` — `true`,
   якщо `$file['error'] === UPLOAD_ERR_OK` **і** реальний MIME-тип файлу
   за шляхом `$file['tmp_name']` (через `finfo`) є в `$allowedMimeTypes`.
   Не довіряє `$file['type']`.
4. `human_readable_size(int $bytes)` — форматує розмір у байтах як
   рядок з одиницею: `< 1024` → `"{n} B"`, `< 1024*1024` → `"{n} KB"`
   (округлено до 1 знаку), інакше → `"{n} MB"`.

## Контракт (сигнатури)

```php
function validate_registration(array $input): array
function redirect_url(string $path, array $query = []): string
function is_allowed_upload(array $file, array $allowedMimeTypes): bool
function human_readable_size(int $bytes): string
```

## Обмеження

- Файл починається з `declare(strict_types=1);`.
- `validate_registration()` перевіряє результат `filter_var()` саме
  через `=== false`, а не через `!$result`.
- `is_allowed_upload()` визначає тип файлу через `\finfo`, не через
  `$file['type']`.

## Як перевірити

```bash
php check.php
```

Далі — реальна форма з файлом, поза автоматичною перевіркою:

```bash
php -S localhost:8000 -t public
```

Відкрий `http://localhost:8000/`, заповни форму реєстрації й спробуй
завантажити файл — `public/submit.php` покаже або помилки валідації
(та саму форму знову, зі старими значеннями), або PRG-редірект на
сторінку успіху.

## Definition of Done

- [ ] усі перевірки check.php зелені
- [ ] реальна форма в браузері показує помилки валідації без втрати
      введених значень і робить PRG-редірект при успіху
- [ ] можеш пояснити, чому `is_allowed_upload()` не можна довірити
      `$file['type']`

## Бонус (не перевіряється автоматично)

Додай `old_input(array $input, string $field): string` — повертає
значення поля з попередньої (провальної) відправки форми, або порожній
рядок, якщо поля немає — типовий хелпер для "не втрачати введене" при
ре-рендері форми з помилками.

<details>
<summary>Підказка 1</summary>

```php
function validate_registration(array $input): array
{
    $errors = [];

    if (filter_var($input['email'] ?? '', FILTER_VALIDATE_EMAIL) === false) {
        $errors['email'] = 'Некоректний email';
    }
    if (strlen($input['password'] ?? '') < 8) {
        $errors['password'] = 'Пароль має бути не коротшим за 8 символів';
    }
    $age = filter_var($input['age'] ?? '', FILTER_VALIDATE_INT, [
        'options' => ['min_range' => 13, 'max_range' => 120],
    ]);
    if ($age === false) {
        $errors['age'] = 'Вік має бути числом від 13 до 120';
    }

    return ['valid' => $errors === [], 'errors' => $errors];
}
```
</details>

<details>
<summary>Підказка 2</summary>

```php
function is_allowed_upload(array $file, array $allowedMimeTypes): bool
{
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return false;
    }

    $realType = (new \finfo(FILEINFO_MIME_TYPE))->file($file['tmp_name']);

    return in_array($realType, $allowedMimeTypes, strict: true);
}
```
</details>

<details>
<summary>Підказка 3</summary>

```php
function human_readable_size(int $bytes): string
{
    if ($bytes < 1024) {
        return "{$bytes} B";
    }
    if ($bytes < 1024 * 1024) {
        return round($bytes / 1024, 1) . ' KB';
    }

    return round($bytes / (1024 * 1024), 1) . ' MB';
}
```
</details>
