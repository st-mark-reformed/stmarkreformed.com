<?php

declare(strict_types=1);

namespace App\Http\PageBuilder\BlockResponse\Leadership;

use craft\elements\MatrixBlock;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Twig\Environment as TwigEnvironment;

use function assert;
use function is_array;

/**
 * @psalm-suppress PropertyNotSetInConstructor
 * @psalm-suppress MixedArrayAccess
 * @psalm-suppress MixedAssignment
 * @psalm-suppress MixedArgument
 * @psalm-suppress MixedMethodCall
 */
class LeadershipTest extends TestCase
{
    private Leadership $service;

    /** @var mixed[] */
    private array $twigCalls = [];

    /** @var mixed[] */
    private array $retrievePeopleCalls = [];

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = new Leadership(
            twig: $this->mockTwig(),
            retrievePeople: $this->mockRetrievePeople(),
        );
    }

    /**
     * @return MockObject&TwigEnvironment
     */
    private function mockTwig(): mixed
    {
        $this->twigCalls = [];

        $twig = $this->createMock(
            TwigEnvironment::class,
        );

        $twig->method('render')->willReturnCallback(
            function (string $name, array $context): string {
                $this->twigCalls[] = [
                    'method' => 'render',
                    'name' => $name,
                    'context' => $context,
                ];

                return 'twigRenderReturn';
            }
        );

        return $twig;
    }

    /**
     * @return MockObject&RetrievePeople
     */
    private function mockRetrievePeople(): mixed
    {
        $retrievePeople = $this->createMock(
            RetrievePeople::class,
        );

        $retrievePeople->method('retrieve')->willReturnCallback(
            function (string $position): array {
                $this->retrievePeopleCalls[] = [
                    'method' => 'retrieve',
                    'position' => $position,
                ];

                if ($position === 'elder') {
                    return [];
                }

                $models = [
                    $this->createMock(
                        LeadershipPersonContentModel::class,
                    ),
                ];

                if ($position === 'deacon') {
                    $models[] = $this->createMock(
                        LeadershipPersonContentModel::class,
                    );
                }

                return $models;
            }
        );

        return $retrievePeople;
    }

    public function testBuildResponse(): void
    {
        self::assertSame(
            'twigRenderReturn',
            $this->service->buildResponse($this->createMock(
                MatrixBlock::class,
            )),
        );

        self::assertCount(1, $this->twigCalls);

        self::assertSame(
            'render',
            $this->twigCalls[0]['method'],
        );

        self::assertSame(
            '@app/Http/PageBuilder/BlockResponse/Leadership/Leadership.twig',
            $this->twigCalls[0]['name'],
        );

        $context = $this->twigCalls[0]['context'];

        assert(is_array($context));

        self::assertCount(1, $context);

        $contentModel = $context['contentModel'];

        assert($contentModel instanceof LeadershipContentModel);

        $sections = $contentModel->leadershipSections();

        self::assertCount(3, $sections);

        self::assertSame(
            'Pastor',
            $sections[0]->sectionTitle(),
        );

        self::assertCount(1, $sections[0]->people());

        self::assertSame(
            'Ruling Elder',
            $sections[1]->sectionTitle(),
        );

        self::assertCount(1, $sections[1]->people());

        self::assertSame(
            'Deacons',
            $sections[2]->sectionTitle(),
        );

        self::assertCount(2, $sections[2]->people());

        self::assertSame(
            [
                [
                    'method' => 'retrieve',
                    'position' => 'pastor',
                ],
                [
                    'method' => 'retrieve',
                    'position' => 'rulingElder',
                ],
                [
                    'method' => 'retrieve',
                    'position' => 'elder',
                ],
                [
                    'method' => 'retrieve',
                    'position' => 'deacon',
                ],
            ],
            $this->retrievePeopleCalls,
        );
    }
}
