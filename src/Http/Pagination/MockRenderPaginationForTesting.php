<?php

declare(strict_types=1);

namespace App\Http\Pagination;

use App\Shared\Testing\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Twig\Markup;

use function assert;

trait MockRenderPaginationForTesting
{
    /**
     * @return RenderPagination&MockObject
     */
    protected function mockRenderPagination(): mixed
    {
        assert($this instanceof TestCase);

        $mock = $this->createMock(
            RenderPagination::class,
        );

        $mock->method($this::anything())->willReturnCallback(
            function (): Markup {
                assert($this instanceof TestCase);

                return $this->genericCall(
                    'RenderPagination',
                    new Markup(
                        'RenderPaginationReturn',
                        'UTF-8',
                    ),
                );
            }
        );

        return $mock;
    }
}
