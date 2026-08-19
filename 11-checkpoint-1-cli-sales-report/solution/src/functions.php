<?php

declare(strict_types=1);

// Checkpoint 1: CLI-звіт по продажах — референсний розв'язок.

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

function filter_sales(array $sales, array $filters): array
{
    $filtered = array_filter($sales, function (array $sale) use ($filters): bool {
        if (isset($filters['category']) && $sale['category'] !== $filters['category']) {
            return false;
        }

        if (isset($filters['from']) && $sale['date'] < $filters['from']) {
            return false;
        }

        if (isset($filters['to']) && $sale['date'] > $filters['to']) {
            return false;
        }

        return true;
    });

    return array_values($filtered);
}

function group_totals_by_category(array $sales): array
{
    $totals = [];

    foreach ($sales as $sale) {
        $category = $sale['category'];
        $totals[$category] = ($totals[$category] ?? 0.0) + $sale['amount'] * $sale['qty'];
    }

    return $totals;
}

function sorted_rows_by_total_desc(array $totals): array
{
    $rows = [];

    foreach ($totals as $category => $total) {
        $rows[] = ['category' => $category, 'total' => $total];
    }

    usort(
        $rows,
        fn (array $a, array $b): int => ($b['total'] <=> $a['total']) ?: ($a['category'] <=> $b['category'])
    );

    return $rows;
}

function format_report_table(array $rows): string
{
    $lines = [];

    foreach ($rows as $row) {
        $lines[] = mb_str_pad($row['category'], 20)
            . mb_str_pad(number_format($row['total'], 2), 10, ' ', STR_PAD_LEFT);
    }

    return implode("\n", $lines);
}
