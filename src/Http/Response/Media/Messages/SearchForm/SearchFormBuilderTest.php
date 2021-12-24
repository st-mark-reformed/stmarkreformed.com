<?php

declare(strict_types=1);

namespace App\Http\Response\Media\Messages\SearchForm;

use App\Http\Response\Media\Messages\Params;
use App\Shared\Testing\MockTwigForTesting;
use App\Shared\Testing\TestCase;
use craft\errors\InvalidFieldException;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

use function array_map;
use function assert;
use function is_array;

class SearchFormBuilderTest extends TestCase
{
    use MockTwigForTesting;
    use MockRetrieveSpeakerOptionsForTesting;
    use MockRetrieveSeriesOptionsForTesting;

    private SearchFormBuilder $builder;

    protected function setUp(): void
    {
        parent::setUp();

        $this->builder = new SearchFormBuilder(
            twig: $this->mockTwig(),
            seriesOptions: $this->mockRetrieveSeriesOptions(),
            speakerOptions: $this->mockRetrieveSpeakerOptions(),
        );
    }

    /**
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws InvalidFieldException
     */
    public function testFromParams(): void
    {
        $params = new Params(
            by: ['speaker1', 'speaker2'],
            series: ['series1', 'series2'],
        );

        self::assertSame(
            'TwigRender',
            (string) $this->builder->fromParams(params: $params),
        );

        self::assertCount(3, $this->calls);

        $call1 = $this->calls[0];

        assert(is_array($call1));

        self::assertSame(
            [
                'object' => 'RetrieveSeriesOptions',
                'method' => 'retrieve',
                'args' => [
                    [
                        'series1',
                        'series2',
                    ],
                ],
            ],
            $call1,
        );

        $call2 = $this->calls[1];

        assert(is_array($call2));

        self::assertSame(
            [
                'object' => 'RetrieveSpeakerOptions',
                'method' => 'retrieve',
                'args' => [
                    [
                        'speaker1',
                        'speaker2',
                    ],
                ],
            ],
            $call2,
        );

        $call3 = $this->calls[2];

        assert(is_array($call3));

        self::assertSame(
            'TwigEnvironment',
            $call3['object'],
        );

        self::assertSame(
            'render',
            $call3['method'],
        );

        $call3Args = $call3['args'];

        assert(is_array($call3Args));

        self::assertCount(2, $call3Args);

        self::assertSame(
            '@app/Http/Response/Media/Messages/SearchForm/SearchForm.twig',
            $call3Args[0],
        );

        $twigContext = $call3Args[1];

        assert(is_array($twigContext));

        self::assertCount(3, $twigContext);

        self::assertSame(
            $params,
            $twigContext['params'],
        );

        $seriesOptions = $twigContext['seriesOptions'];

        assert($seriesOptions instanceof OptionGroup);

        self::assertSame(
            'TestOptionGroup',
            $seriesOptions->groupTitle(),
        );

        self::assertSame(
            [
                [
                    'name' => 'Test Option 1',
                    'slug' => 'test-option-1',
                    'isActive' => true,
                ],
                [
                    'name' => 'Test Option 2',
                    'slug' => 'test-option-2',
                    'isActive' => false,
                ],
            ],
            array_map(
                static fn (SelectOption $option) => [
                    'name' => $option->name(),
                    'slug' => $option->slug(),
                    'isActive' => $option->isActive(),
                ],
                $seriesOptions->options(),
            )
        );

        $speakerGroupOptions = $twigContext['speakerGroupOptions'];

        assert(
            $speakerGroupOptions instanceof OptionGroupCollection,
        );

        self::assertSame(
            [
                [
                    'title' => 'TestOptionGroup1',
                    'groups' => [
                        [
                            'name' => 'Test Option 1',
                            'slug' => 'test-option-1',
                            'isActive' => true,
                        ],
                        [
                            'name' => 'Test Option 2',
                            'slug' => 'test-option-2',
                            'isActive' => false,
                        ],
                    ],
                ],
                [
                    'title' => 'TestOptionGroup2',
                    'groups' => [
                        [
                            'name' => 'Test Option 3',
                            'slug' => 'test-option-3',
                            'isActive' => true,
                        ],
                        [
                            'name' => 'Test Option 4',
                            'slug' => 'test-option-4',
                            'isActive' => false,
                        ],
                    ],
                ],
            ],
            $speakerGroupOptions->map(
                static fn (OptionGroup $optionGroup) => [
                    'title' => $optionGroup->groupTitle(),
                    'groups' => array_map(
                        static fn (SelectOption $option) => [
                            'name' => $option->name(),
                            'slug' => $option->slug(),
                            'isActive' => $option->isActive(),
                        ],
                        $optionGroup->options(),
                    ),
                ],
            ),
        );
    }
}
