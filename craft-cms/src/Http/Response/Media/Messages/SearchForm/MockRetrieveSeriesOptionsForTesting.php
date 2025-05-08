<?php

declare(strict_types=1);

namespace App\Http\Response\Media\Messages\SearchForm;

use App\Shared\Testing\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

use function assert;

trait MockRetrieveSeriesOptionsForTesting
{
    protected OptionGroup $optionGroup;

    /**
     * @return RetrieveSeriesOptions&MockObject
     */
    protected function mockRetrieveSeriesOptions(): mixed
    {
        assert($this instanceof TestCase);

        $this->optionGroup = new OptionGroup(
            groupTitle: 'TestOptionGroup',
            selectOptions: [
                new SelectOption(
                    name: 'Test Option 1',
                    slug: 'test-option-1',
                    isActive: true,
                ),
                new SelectOption(
                    name: 'Test Option 2',
                    slug: 'test-option-2',
                    isActive: false,
                ),
            ],
        );

        $mock = $this->createMock(
            RetrieveSeriesOptions::class,
        );

        $mock->method($this::anything())->willReturnCallback(
            function (): OptionGroup {
                assert($this instanceof TestCase);

                return $this->genericCall(
                    object: 'RetrieveSeriesOptions',
                    return: $this->optionGroup,
                );
            }
        );

        return $mock;
    }
}
