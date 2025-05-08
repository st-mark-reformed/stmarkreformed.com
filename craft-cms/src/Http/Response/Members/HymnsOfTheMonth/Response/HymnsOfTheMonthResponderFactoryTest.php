<?php

declare(strict_types=1);

namespace App\Http\Response\Members\HymnsOfTheMonth\Response;

use App\Http\Response\Members\HymnsOfTheMonth\HymnResults;
use App\Shared\Testing\TestCase;

class HymnsOfTheMonthResponderFactoryTest extends TestCase
{
    private HymnsOfTheMonthResponderFactory $factory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->factory = new HymnsOfTheMonthResponderFactory(
            respondWithResults: $this->createMock(
                RespondWithResults::class,
            ),
            respondWithNoResults: $this->createMock(
                RespondWithNoResults::class,
            ),
        );
    }

    public function testWhenHasNoResults(): void
    {
        self::assertInstanceOf(
            RespondWithNoResults::class,
            $this->factory->make(
                results: new HymnResults(hasResults: false),
            ),
        );
    }

    public function testWhenHasResults(): void
    {
        self::assertInstanceOf(
            RespondWithResults::class,
            $this->factory->make(
                results: new HymnResults(hasResults: true),
            ),
        );
    }
}
