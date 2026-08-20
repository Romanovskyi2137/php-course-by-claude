<?php

declare(strict_types=1);

// Тема 31: "логін" (без пароля — фокус теми на самій механіці сесій,
// повноцінний потік логіну з паролем — тема 32). Готовий код — не редагуй.

require __DIR__ . '/../starter.php';

session_set_cookie_params(build_session_cookie_options(secure: false));
session_start();

$userId = (int) ($_POST['user_id'] ?? 0);

session_regenerate_id(true); // новий ID при зміні рівня привілеїв — захист від session fixation
$_SESSION = mark_authenticated($_SESSION, $userId);

header('Location: /');
