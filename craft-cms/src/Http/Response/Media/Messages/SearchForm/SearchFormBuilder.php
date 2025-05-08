<?php

declare(strict_types=1);

namespace App\Http\Response\Media\Messages\SearchForm;

use App\Http\Response\Media\Messages\Params;
use craft\errors\InvalidFieldException;
use Twig\Environment as TwigEnvironment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Twig\Markup;

class SearchFormBuilder
{
    public function __construct(
        private TwigEnvironment $twig,
        private RetrieveSeriesOptions $seriesOptions,
        private RetrieveSpeakerOptions $speakerOptions,
    ) {
    }

    /**
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws InvalidFieldException
     */
    public function fromParams(Params $params): Markup
    {
        return new Markup(
            $this->twig->render(
                '@app/Http/Response/Media/Messages/SearchForm/SearchForm.twig',
                [
                    'params' => $params,
                    'seriesOptions' => $this->seriesOptions->retrieve(
                        selectedSlugs: $params->series(),
                    ),
                    'speakerGroupOptions' => $this->speakerOptions->retrieve(
                        selectedSlugs: $params->by(),
                    ),
                ],
            ),
            'UTF-8',
        );
    }
}
