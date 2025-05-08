<?php

declare(strict_types=1);

namespace App\Http\Response\Media\Messages\SearchForm;

use App\Shared\Testing\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Twig\Markup;

use function assert;

trait MockSearchFormBuilderForTesting
{
    /**
     * @return SearchFormBuilder&MockObject
     */
    protected function mockSearchFormBuilder(): mixed
    {
        assert($this instanceof TestCase);

        $mock = $this->createMock(SearchFormBuilder::class);

        $mock->method($this::anything())->willReturnCallback(
            function (): Markup {
                assert($this instanceof TestCase);

                return $this->genericCall(
                    'SearchFormBuilder',
                    new Markup(
                        'SearchFormBuilderReturn',
                        'UTF-8',
                    ),
                );
            }
        );

        return $mock;
    }
}
