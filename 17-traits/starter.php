<?php

declare(strict_types=1);

// Тема 17: traits. Опис завдання — у task.md.

// TODO (вимога 1): log()/logs() з внутрішнім масивом повідомлень.
trait Loggable
{
}

// TODO (вимога 2): touch()/createdAt() з внутрішньою датою.
trait Timestamped
{
}

// TODO (вимога 3): підключи обидва трейти.
class Article
{
}

trait Greets
{
    public function hello(): string
    {
        return 'Hello from Greets';
    }
}

trait Speaks
{
    public function hello(): string
    {
        return 'Hello from Speaks';
    }
}

// TODO (вимога 4): вирішити конфлікт hello() через insteadof/as.
class Robot
{
    use Greets, Speaks;
}
