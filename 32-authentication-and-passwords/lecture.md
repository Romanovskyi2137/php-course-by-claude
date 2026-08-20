# 32. Автентифікація та паролі

> Що ти вже вмієш: bcrypt/argon2 через сторонню бібліотеку (`bcrypt`
> в npm, `passlib` в Python) · Що додає ця тема: PHP має надійне
> хешування паролів **вбудованим** — і чому "просто SHA-256" ніколи не
> правильна відповідь · ~20 хв

## 1. Якщо ти прийшов з JS / Python / Java

- `password_hash()`/`password_verify()` — вбудований еквівалент того,
  що в Node дає пакет `bcrypt`, а в Python — `passlib`/`bcrypt`: PHP не
  вимагає стороннього пакета для правильного хешування паролів, це
  частина мови з 5.5.
- Алгоритм за замовчуванням (`PASSWORD_DEFAULT`) станом на PHP 8.3 —
  bcrypt; PHP також підтримує Argon2i/Argon2id (`PASSWORD_ARGON2ID`) —
  той самий алгоритм, що рекомендують OWASP і сучасні гайдлайни в будь-якій мові.
- `hash_equals()` — timing-safe порівняння рядків, аналог
  `crypto.timingSafeEqual()` (Node) чи `hmac.compare_digest()` (Python)
  — потрібне скрізь, де порівнюються секрети (токени, підписи), не лише паролі.

## 2. Ключові терміни

- **Hashing (хешування)** — одностороннє перетворення пароля в рядок
  фіксованої структури; неможливо (обчислювально) відновити оригінал
  з хеша.
- **Salt (сіль)** — випадкові дані, вбудовані в сам хеш, які роблять
  однаковий пароль різним хешем щоразу — захист від rainbow-table атак.
  `password_hash()` генерує й вбудовує сіль автоматично, вручну
  керувати нею не потрібно.
- **Cost factor (фактор вартості)** — параметр, що контролює, наскільки
  "повільний" (навмисно!) алгоритм хешування — вищий cost = довше
  хешування = дорожче для атаки перебором, але й для легітимного логіну.
- **Timing attack (атака за часом)** — техніка вгадування секрету за
  тим, скільки часу займає порівняння (звичайний `===` завершується
  раніше на першому неспівпадінні символу — цю різницю в часі можна
  виміряти й використати).

## 3. Теорія

### 3.1 Хешування й перевірка

```php
$hash = password_hash('correct horse battery staple', PASSWORD_DEFAULT);
// '$2y$10$N9qo8uLOickgx2ZMRZoMy...' — містить у собі алгоритм, cost і сіль

password_verify('correct horse battery staple', $hash); // true
password_verify('wrong guess', $hash);                  // false
```

`password_hash()` з тим самим паролем щоразу дає **різний** результат
(різна сіль) — тому паролі ніколи не порівнюють напряму
(`$hash1 === $hash2`), лише через `password_verify()`.

### 3.2 `password_needs_rehash()`: план на майбутнє

```php
if (password_verify($plainPassword, $user->passwordHash)) {
    if (password_needs_rehash($user->passwordHash, PASSWORD_DEFAULT)) {
        $user->passwordHash = password_hash($plainPassword, PASSWORD_DEFAULT);
        // зберегти оновлений хеш у БД — САМЕ ТУТ, поки маємо plaintext-пароль
    }
    // ...залогинити користувача
}
```

Коли `PASSWORD_DEFAULT` PHP колись зміниться (алгоритм чи типовий cost)
— старі хеші лишаться валідними, але `password_needs_rehash()` підкаже,
що варто перехешувати з новими параметрами, скориставшись моментом,
коли пароль у відкритому вигляді й так доступний (під час успішного логіну).

### 3.3 `hash_equals()`: timing-safe порівняння

```php
function constant_time_equals(string $a, string $b): bool
{
    return hash_equals($a, $b);
}
```

Порівняння рядків через `===` завершується на **першому** неспівпадінні
символу — швидше для рядків, що відрізняються раніше. Для секретів
(API-ключі, CSRF-токени, remember-me токени) це дає зловмиснику канал
для побайтового підбору через вимірювання часу відповіді.
`hash_equals()` завжди порівнює за постійний час, незалежно від того, де
саме рядки розходяться.

### 3.4 Потік логіну

```php
function attempt_login(array $users, string $email, string $password): ?array
{
    foreach ($users as $user) {
        if (strcasecmp($user['email'], $email) === 0) {
            return password_verify($password, $user['passwordHash'])
                ? ['id' => $user['id'], 'email' => $user['email']] // без passwordHash!
                : null;
        }
    }

    return null; // email не знайдено
}
```

## 4. Підводні камені

- `md5($password)`/`sha1($password)`/`hash('sha256', $password)` — **не
  годяться** для паролів: вони навмисно **швидкі** (мільярди хешів за
  секунду на GPU), тоді як для паролів потрібен навмисно **повільний**
  алгоритм (bcrypt/Argon2) саме щоб зробити перебір дорогим.
- Повернення повного обʼєкта користувача з `passwordHash`-полем із
  функції логіну — джерело витоку хеша далі кодом (логи, серіалізація
  в API-відповідь). `attempt_login()` навмисно повертає новий масив без
  цього поля (розділ 3.4).
- Порівняння токенів (не паролів — паролі й так через
  `password_verify()`) через звичайний `===` замість `hash_equals()` —
  дрібна, але реальна вразливість timing attack для будь-якого коду, що
  звіряє "секрет проти секрету" (remember-me cookie, API-ключ, підпис вебхука).

## 5. Сучасний PHP 8.3+

`PASSWORD_DEFAULT` і сама функція `password_hash()` — стабільний API з
PHP 5.5+; PHP 7.2+ додав `PASSWORD_ARGON2I`/`PASSWORD_ARGON2ID` як
альтернативу bcrypt. Практична рекомендація для 8.3 незмінна:
`PASSWORD_DEFAULT` (не хардкодь конкретний алгоритм) — PHP сам обере
найкращий з доступних, і значення може змінюватись у майбутніх версіях
мови без зміни твого коду.

## 6. Контекст екосистеми

Жоден серйозний фреймворк не винаходить власне хешування паролів —
Laravel `Hash::make()`, Symfony `PasswordHasher` — усі зрештою
викликають ту саму `password_hash()`/`password_verify()` під капотом.
Це один із небагатьох випадків у вебі, де "не пиши сам, візьми
стандартне" стосується навіть найпростішої, здавалося б, логіки —
криптографія завжди винятковий випадок.

## 7. Документація

- https://www.php.net/manual/en/function.password-hash.php
- https://www.php.net/manual/en/function.password-verify.php
- https://www.php.net/manual/en/function.hash-equals.php
- https://owasp.org/www-project-cheat-sheets/cheatsheets/Password_Storage_Cheat_Sheet.html

## 8. Питання для самоперевірки

1. Чому `password_hash()` того самого пароля щоразу дає різний результат?
2. Навіщо потрібен `password_needs_rehash()`, якщо старий хеш і так
   валідний?
3. Що таке timing attack і як `hash_equals()` від нього захищає?
4. Чому `md5()`/`sha256()` не годяться для зберігання паролів, навіть
   попри те, що це криптографічно стійкі хеш-функції?
