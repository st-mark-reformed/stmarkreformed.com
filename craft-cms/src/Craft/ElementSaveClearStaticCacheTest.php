<?php

declare(strict_types=1);

namespace App\Craft;

use App\Http\Utility\ClearStaticCache;
use craft\base\Element;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use yii\base\Event;

class ElementSaveClearStaticCacheTest extends TestCase
{
    private ElementSaveClearStaticCache $service;

    /** @var MockObject&Event */
    private mixed $event;

    /** @var mixed[] */
    private array $clearStaticCacheCalls = [];

    private bool $isDraft = false;

    public function setUp(): void
    {
        parent::setUp();

        $this->isDraft = false;

        $element = $this->createMock(Element::class);

        $element->method('getIsDraft')->willReturnCallback(
            function (): bool {
                return $this->isDraft;
            }
        );

        $this->event = $this->createMock(Event::class);

        $this->event->sender = $element;

        $clearStaticCache = $this->createMock(
            ClearStaticCache::class,
        );

        $clearStaticCache->method('clear')->willReturnCallback(
            function (): void {
                $this->clearStaticCacheCalls[] = ['method' => 'clear'];
            }
        );

        $this->service = new ElementSaveClearStaticCache(
            clearStaticCache: $clearStaticCache,
        );
    }

    public function testClearWhenIsDraft(): void
    {
        $this->isDraft = true;

        $this->service->clear(event: $this->event);

        self::assertCount(
            0,
            $this->clearStaticCacheCalls,
        );
    }

    public function testClear(): void
    {
        $this->isDraft = false;

        $this->service->clear(event: $this->event);

        self::assertCount(
            1,
            $this->clearStaticCacheCalls,
        );

        self::assertSame(
            [['method' => 'clear']],
            $this->clearStaticCacheCalls,
        );
    }
}
