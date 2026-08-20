<?php

declare(strict_types=1);

namespace App\Tests;

use App\Bookmark;
use PHPUnit\Framework\TestCase;

final class BookmarkTest extends TestCase
{
    public function testPropertiesAreAccessibleThroughPromotion(): void
    {
        $bookmark = new Bookmark(1, 'PHP.net', 'https://php.net');

        $this->assertSame(1, $bookmark->id);
        $this->assertSame('PHP.net', $bookmark->title);
        $this->assertSame('https://php.net', $bookmark->url);
    }

    public function testToArrayReturnsScalarValues(): void
    {
        $bookmark = new Bookmark(1, 'PHP.net', 'https://php.net');

        $this->assertSame([
            'id' => 1,
            'title' => 'PHP.net',
            'url' => 'https://php.net',
        ], $bookmark->toArray());
    }
}
