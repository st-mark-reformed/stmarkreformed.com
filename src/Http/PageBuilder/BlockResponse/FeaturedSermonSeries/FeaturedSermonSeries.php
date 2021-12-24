<?php

declare(strict_types=1);

namespace App\Http\PageBuilder\BlockResponse\FeaturedSermonSeries;

use App\Http\PageBuilder\BlockResponse\BlockResponseBuilderContract;
use App\Http\PageBuilder\BlockResponse\FeaturedSermonSeries\RetrieveMostRecentSeries\RetrieveMostRecentSeries;
use App\Http\PageBuilder\Shared\AudioPlayer\RenderAudioPlayerFromContentModel;
use craft\elements\MatrixBlock;
use craft\errors\InvalidFieldException;
use Twig\Environment as TwigEnvironment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use yii\base\InvalidConfigException;

class FeaturedSermonSeries implements BlockResponseBuilderContract
{
    public function __construct(
        private TwigEnvironment $twig,
        private RetrieveMostRecentSeries $retrieveMostRecentSeries,
        private RenderAudioPlayerFromContentModel $renderAudioPlayer,
    ) {
    }

    /**
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws InvalidFieldException
     * @throws InvalidConfigException
     *
     * @phpstan-ignore-next-line
     */
    public function buildResponse(MatrixBlock $matrixBlock): string
    {
        $series = $this->retrieveMostRecentSeries->retrieve();

        return $this->twig->render(
            '@app/Http/PageBuilder/BlockResponse/FeaturedSermonSeries/FeaturedSermonSeries.twig',
            [
                'contentModel' => new FeaturedSermonSeriesContentModel(
                    headline: 'Featured Sermon Series',
                    seriesTitle: $series->seriesTitle(),
                    seriesHref: $series->seriesUrl(),
                    backgroundImageHref: '/assets/img/featured-sermon-series.jpg',
                    latestInSeriesPlayerHtml: $this->renderAudioPlayer->render(
                        contentModel: $series->playerContentModel(),
                    ),
                ),
            ],
        );
    }
}
