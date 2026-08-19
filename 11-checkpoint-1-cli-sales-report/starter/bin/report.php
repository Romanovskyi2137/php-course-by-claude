<?php

declare(strict_types=1);

// Checkpoint 1: CLI-обгортка. Готовий код — не потрібно редагувати.
// Логіка живе в ../src/functions.php, тут лише вивід.

require __DIR__ . '/../src/functions.php';

$sales = require __DIR__ . '/../../data/sales.php';

$filters = parse_filters($argv);
$filtered = filter_sales($sales, $filters);
$totals = group_totals_by_category($filtered);
$rows = sorted_rows_by_total_desc($totals);

if ($rows === []) {
    echo "Немає продажів за заданими фільтрами.\n";
    exit(0);
}

echo format_report_table($rows) . "\n";
