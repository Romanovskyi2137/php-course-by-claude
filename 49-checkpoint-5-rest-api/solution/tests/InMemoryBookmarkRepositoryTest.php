<?php

declare(strict_types=1);

namespace App\Tests;

use App\Bookmark;
use App\InMemoryBookmarkRepository;
use PHPUnit\Framework\TestCase;

final class InMemoryBookmarkRepositoryTest extends TestCase
{
    public function testAddAssignsIncrementingIds(): void
    {
        $repo = new InMemoryBookmarkRepository();
        $first = $repo->add('PHP.net', 'https://php.net');
        $second = $repo->add('Packagist', 'https://packagist.org');

        $this->assertSame(1, $first->id);
        $this->assertSame(2, $second->id);
    }

    public function testAllReturnsBookmarksSortedById(): void
    {
        $repo = new InMemoryBookmarkRepository();
        $repo->add('B', 'https://b.example');
        $repo->add('A', 'https://a.example');

        $titles = array_map(fn (Bookmark $b) => $b->title, $repo->all());

        $this->assertSame(['B', 'A'], $titles);
    }

    public function testFindReturnsBookmarkOrNull(): void
    {
        $repo = new InMemoryBookmarkRepository();
        $added = $repo->add('PHP.net', 'https://php.net');

        $this->assertSame($added->id, $repo->find($added->id)->id);
        $this->assertNull($repo->find(999999));
    }

    public function testDeleteRemovesBookmarkAndReturnsTrue(): void
    {
        $repo = new InMemoryBookmarkRepository();
        $added = $repo->add('PHP.net', 'https://php.net');

        $this->assertTrue($repo->delete($added->id));
        $this->assertNull($repo->find($added->id));
    }

    public function testDeleteReturnsFalseForUnknownId(): void
    {
        $repo = new InMemoryBookmarkRepository();

        $this->assertFalse($repo->delete(999999));
    }
}
