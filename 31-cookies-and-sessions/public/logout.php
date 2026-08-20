<?php

declare(strict_types=1);

// Тема 31: логаут. Готовий код — не редагуй.

require __DIR__ . '/../starter.php';

session_set_cookie_params(build_session_cookie_options(secure: false));
session_start();

$_SESSION = logout($_SESSION);
session_regenerate_id(true);

header('Location: /');
