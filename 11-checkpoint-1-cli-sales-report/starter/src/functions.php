<?php

declare(strict_types=1);

// Checkpoint 1: CLI-звіт по продажах. Опис вимог — у task.md.

// TODO (вимога 1): розібрати $argv у асоціативний масив фільтрів.
function parse_filters(array $argv): array
{
    throw new \RuntimeException('Не реалізовано');
}

// TODO (вимога 2): відфільтрувати продажі за category/from/to, список без дірок у ключах.
function filter_sales(array $sales, array $filters): array
{
    throw new \RuntimeException('Не реалізовано');
}

// TODO (вимога 3): сумарна виручка (amount * qty) за категоріями.
function group_totals_by_category(array $sales): array
{
    throw new \RuntimeException('Не реалізовано');
}

// TODO (вимога 4): список [['category'=>..., 'total'=>...]], сортований за total за спаданням.
function sorted_rows_by_total_desc(array $totals): array
{
    throw new \RuntimeException('Не реалізовано');
}

// TODO (вимога 5): вирівняна таблиця через mb_str_pad(), без заголовка.
function format_report_table(array $rows): string
{
    throw new \RuntimeException('Не реалізовано');
}
