<?php

declare(strict_types=1);

// Checkpoint 3: вихід адміна. Готовий код — не редагуй.

session_start();

unset($_SESSION['admin']);
session_regenerate_id(true);

header('Location: /index.php');
