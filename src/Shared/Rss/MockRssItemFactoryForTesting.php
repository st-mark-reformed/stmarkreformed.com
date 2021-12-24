<?php

declare(strict_types=1);

namespace App\Shared\Rss;

use App\Shared\Testing\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

use function assert;

trait MockRssItemFactoryForTesting
{
    /**
     * @return RssItemFactory&MockObject
     */
    public function mockRssItemFactory(): mixed
    {
        assert($this instanceof TestCase);

        $mock = $this->createMock(RssItemFactory::class);

        $mock->method($this::anything())->willReturnCallback(
            function (): void {
                assert($this instanceof TestCase);

                $this->genericCall(object: 'RssItemFactory');
            }
        );

        return $mock;
    }
}
