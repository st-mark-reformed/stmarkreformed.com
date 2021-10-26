<?php

declare(strict_types=1);

namespace App\Http\PageBuilder\Shared\AudioPlayer;

use PHPUnit\Framework\TestCase;
use Twig\Environment as TwigEnvironment;

/**
 * @psalm-suppress PropertyNotSetInConstructor
 * @psalm-suppress MixedArrayAccess
 */
class RenderAudioPlayerFromContentModelTest extends TestCase
{
    private RenderAudioPlayerFromContentModel $renderer;

    private AudioPlayerContentModel $contentModelStub;

    /** @var mixed[] */
    private array $twigCalls = [];

    public function setUp(): void
    {
        $this->twigCalls = [];

        $twigEnvironmentStub = $this->createMock(
            TwigEnvironment::class,
        );

        $twigEnvironmentStub->method('render')->willReturnCallback(
            function (string $name, array $context): string {
                $this->twigCalls[] = [
                    'method' => 'render',
                    'name' => $name,
                    'context' => $context,
                ];

                return 'twigRenderedString';
            }
        );

        $this->contentModelStub = new AudioPlayerContentModel(
            href: 'testHref',
            title: 'testTitle',
            subTitle: 'testSubTitle',
            audioFileHref: 'testAudioFileHref',
            audioFileMimeType: 'testAudioFileMimeType',
            keyValueItems: [
                new AudioPlayerKeyValItem(
                    key: 'testItemKey1',
                    value: 'testItemVal1',
                    href: 'testItemHref1',
                ),
                new AudioPlayerKeyValItem(
                    key: 'testItemKey2',
                    value: 'testItemVal2',
                ),
            ],
        );

        $this->renderer = new RenderAudioPlayerFromContentModel(
            twig: $twigEnvironmentStub,
        );
    }

    public function testRender(): void
    {
        self::assertSame(
            'twigRenderedString',
            $this->renderer->render(
                contentModel: $this->contentModelStub
            ),
        );

        self::assertSame(
            [
                [
                    'method' => 'render',
                    'name' => '@app/Http/PageBuilder/Shared/AudioPlayer/AudioPlayer.twig',
                    'context' => [
                        'contentModel' => $this->contentModelStub,
                    ],
                ],
            ],
            $this->twigCalls,
        );

        self::assertSame(
            'testHref',
            $this->contentModelStub->href(),
        );

        self::assertSame(
            'testTitle',
            $this->contentModelStub->title(),
        );

        self::assertSame(
            'testSubTitle',
            $this->contentModelStub->subTitle(),
        );

        self::assertSame(
            'testAudioFileHref',
            $this->contentModelStub->audioFileHref(),
        );

        self::assertSame(
            'testAudioFileMimeType',
            $this->contentModelStub->audioFileMimeType(),
        );

        self::assertTrue($this->contentModelStub->hasKeyValueItems());

        $keyValItems = $this->contentModelStub->keyValueItems();

        self::assertCount(2, $keyValItems);

        $item1 = $keyValItems[0];

        self::assertSame(
            'testItemKey1',
            $item1->key(),
        );

        self::assertSame(
            'testItemVal1',
            $item1->value(),
        );

        self::assertSame(
            'testItemHref1',
            $item1->href(),
        );

        $item2 = $keyValItems[1];

        self::assertSame(
            'testItemKey2',
            $item2->key(),
        );

        self::assertSame(
            'testItemVal2',
            $item2->value(),
        );

        self::assertSame(
            '',
            $item2->href(),
        );
    }
}
