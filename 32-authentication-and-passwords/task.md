# Завдання 32: автентифікація та паролі

## Мета

1. Хешувати пароль правильним, повільним алгоритмом.
2. Реалізувати логін по списку користувачів, не "витікаючи" хеш назовні.
3. Перевірити, чи хеш варто оновити новими параметрами.
4. Порівняти секрети (не паролі) за постійний час.

## Вимоги

1. `hash_password(string $plain)` — хешує пароль через
   `password_hash()` з `PASSWORD_DEFAULT`.
2. `attempt_login(array $users, string $email, string $password)` —
   `$users` — список асоціативних масивів `['id' => int, 'email' =>
   string, 'passwordHash' => string]`. Шукає користувача за `email`
   (без урахування регістру); якщо знайдено і пароль вірний — повертає
   `['id' => ..., 'email' => ...]` (**без** `passwordHash`); інакше —
   `null` (і якщо email не знайдено, і якщо пароль невірний).
3. `needs_rehash(string $hash)` — обгортка над
   `password_needs_rehash($hash, PASSWORD_DEFAULT)`.
4. `constant_time_equals(string $a, string $b)` — обгортка над
   `hash_equals()`, для порівняння токенів/секретів за постійний час.

## Контракт (сигнатури)

```php
function hash_password(string $plain): string
function attempt_login(array $users, string $email, string $password): ?array
function needs_rehash(string $hash): bool
function constant_time_equals(string $a, string $b): bool
```

## Обмеження

- Файл починається з `declare(strict_types=1);`.
- `attempt_login()` перевіряє пароль лише через `password_verify()`, не
  порівнює хеші напряму.
- Результат `attempt_login()` при успіху не містить ключа `passwordHash`.

## Як перевірити

```bash
php check.php
```

## Definition of Done

- [ ] усі перевірки зелені
- [ ] можеш пояснити, чому `attempt_login()` повертає новий масив без
      `passwordHash`, а не просто `unset()` над знайденим користувачем

## Бонус (не перевіряється автоматично)

Додай `password_strength_hint(string $password): array` — список
коротких порад (`'мінімум 8 символів'`, `'додай цифру'` тощо) для
паролів, що не відповідають базовим критеріям; порожній масив, якщо
пароль виглядає прийнятним.

<details>
<summary>Підказка 1</summary>

```php
function hash_password(string $plain): string
{
    return password_hash($plain, PASSWORD_DEFAULT);
}

function needs_rehash(string $hash): bool
{
    return password_needs_rehash($hash, PASSWORD_DEFAULT);
}

function constant_time_equals(string $a, string $b): bool
{
    return hash_equals($a, $b);
}
```
</details>

<details>
<summary>Підказка 2</summary>

```php
function attempt_login(array $users, string $email, string $password): ?array
{
    foreach ($users as $user) {
        if (strcasecmp($user['email'], $email) === 0) {
            if (!password_verify($password, $user['passwordHash'])) {
                return null;
            }

            return ['id' => $user['id'], 'email' => $user['email']];
        }
    }

    return null;
}
```
</details>
