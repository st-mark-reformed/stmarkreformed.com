<?php

declare(strict_types=1);

namespace App\Http\Response\Media\Messages\SearchForm;

use App\Shared\Testing\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

use function assert;

trait MockRetrieveSpeakerOptionsForTesting
{
    private OptionGroupCollection $optionGroupCollection;

    /**
     * @return RetrieveSpeakerOptions&MockObject
     */
    protected function mockRetrieveSpeakerOptions(): mixed
    {
        assert($this instanceof TestCase);

        $this->optionGroupCollection = new OptionGroupCollection(
            optionGroups: [
                new OptionGroup(
                    groupTitle: 'TestOptionGroup1',
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
                ),
                new OptionGroup(
                    groupTitle: 'TestOptionGroup2',
                    selectOptions: [
                        new SelectOption(
                            name: 'Test Option 3',
                            slug: 'test-option-3',
                            isActive: true,
                        ),
                        new SelectOption(
                            name: 'Test Option 4',
                            slug: 'test-option-4',
                            isActive: false,
                        ),
                    ],
                ),
            ],
        );

        $mock = $this->createMock(
            RetrieveSpeakerOptions::class,
        );

        $mock->method($this::anything())->willReturnCallback(
            function (): OptionGroupCollection {
                assert($this instanceof TestCase);

                return $this->genericCall(
                    object: 'RetrieveSpeakerOptions',
                    return: $this->optionGroupCollection,
                );
            }
        );

        return $mock;
    }
}
