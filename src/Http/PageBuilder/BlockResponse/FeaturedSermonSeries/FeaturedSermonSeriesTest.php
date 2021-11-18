<?php

/** @noinspection PhpArrayIsAlwaysEmptyInspection */

declare(strict_types=1);

namespace App\Http\PageBuilder\BlockResponse\FeaturedSermonSeries;

use App\Http\PageBuilder\BlockResponse\FeaturedSermonSeries\RetrieveMostRecentSeries\MostRecentSeries;
use App\Http\PageBuilder\BlockResponse\FeaturedSermonSeries\RetrieveMostRecentSeries\RetrieveMostRecentSeries;
use App\Http\PageBuilder\Shared\AudioPlayer\AudioPlayerContentModel;
use App\Http\PageBuilder\Shared\AudioPlayer\RenderAudioPlayerFromContentModel;
use craft\elements\MatrixBlock;
use PHPUnit\Framework\TestCase;
use stdClass;
use Twig\Environment as TwigEnvironment;

use function assert;

class FeaturedSermonSeriesTest extends TestCase
{
    public function testBuildResponse(): void
    {
        $series = new MostRecentSeries(
            seriesTitle: 'testSeriesTitle',
            seriesUrl: 'testSeriesUrl',
            playerContentModel: new AudioPlayerContentModel(
                href: 'testHref',
                title: 'testTitle',
                subTitle: 'testSubTitle',
                audioFileHref: 'testAudioFileHref',
            ),
        );

        $retrieveMostRecentSeries = $this->createMock(
            RetrieveMostRecentSeries::class,
        );

        $retrieveMostRecentSeries->method('retrieve')->willReturn(
            $series,
        );

        $renderAudioPlayerCalls = new stdClass();

        $renderAudioPlayerCalls->calls = [];

        $renderAudioPlayer = $this->createMock(
            RenderAudioPlayerFromContentModel::class,
        );

        $renderAudioPlayer->method('render')->willReturnCallback(
            static function (
                AudioPlayerContentModel $contentModel
            ) use ($renderAudioPlayerCalls): string {
                $renderAudioPlayerCalls->calls[] = [
                    'method' => 'render',
                    'contentModel' => $contentModel,
                ];

                return 'testPlayerHtml';
            }
        );

        $matrixBlock = $this->createMock(MatrixBlock::class);

        $twigCalls = new stdClass();

        $twigCalls->calls = [];

        $twig = $this->createMock(TwigEnvironment::class);

        $twig->method('render')->willReturnCallback(
            static function (
                string $name,
                array $context,
            ) use ($twigCalls): string {
                $twigCalls->calls[] = [
                    'method' => 'render',
                    'name' => $name,
                    'context' => $context,
                ];

                return 'testTwigRenderOutput';
            }
        );

        $builder = new FeaturedSermonSeries(
            twig: $twig,
            retrieveMostRecentSeries: $retrieveMostRecentSeries,
            renderAudioPlayer: $renderAudioPlayer,
        );

        self::assertSame(
            'testTwigRenderOutput',
            $builder->buildResponse(matrixBlock: $matrixBlock),
        );

        self::assertCount(
            1,
            $renderAudioPlayerCalls->calls,
        );

        /** @phpstan-ignore-next-line */
        $renderCall1 = $renderAudioPlayerCalls->calls[0];

        self::assertSame(
            'render',
            $renderCall1['method'],
        );

        self::assertSame(
            $series->playerContentModel(),
            $renderCall1['contentModel'],
        );

        self::assertCount(1, $twigCalls->calls);

        /** @phpstan-ignore-next-line */
        $twigCall1 = $twigCalls->calls[0];

        self::assertSame(
            'render',
            $twigCall1['method'],
        );

        self::assertSame(
            '@app/Http/PageBuilder/BlockResponse/FeaturedSermonSeries/FeaturedSermonSeries.twig',
            $twigCall1['name'],
        );

        $context = $twigCall1['context'];

        self::assertCount(1, $context);

        $contentModel = $context['contentModel'];

        assert(
            $contentModel instanceof FeaturedSermonSeriesContentModel,
        );

        self::assertSame(
            'Featured Sermon Series',
            $contentModel->headline(),
        );

        self::assertSame(
            'testSeriesTitle',
            $contentModel->seriesTitle(),
        );

        self::assertSame(
            'testSeriesUrl',
            $contentModel->seriesHref(),
        );

        self::assertSame(
            'testPlayerHtml',
            (string) $contentModel->latestInSeriesPlayerHtml(),
        );

        self::assertSame(
            '/assets/img/featured-sermon-series.jpg',
            $contentModel->backgroundImageHref(),
        );
    }
}
