<?php

declare(strict_types=1);

namespace App\Http\Response\Media\Messages\Breadcrumbs;

use App\Shared\Testing\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Twig\Markup;

use function assert;

trait MockBreadcrumbBuilderForTesting
{
    /**
     * @return BreadcrumbBuilder&MockObject
     */
    protected function mockBreadcrumbBuilder(): mixed
    {
        assert($this instanceof TestCase);

        $mock = $this->createMock(BreadcrumbBuilder::class);

        $mock->method($this::anything())->willReturnCallback(
            function (): Markup {
                assert($this instanceof TestCase);

                return $this->genericCall(
                    'BreadcrumbBuilder',
                    new Markup(
                        'BreadcrumbBuilderReturn',
                        'UTF-8',
                    ),
                );
            }
        );

        return $mock;
    }
}
