<?php

declare(strict_types=1);

namespace App\Http\Response\Media\Messages\Sidebar;

use Twig\Environment as TwigEnvironment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Twig\Markup;

class MessagesSidebar
{
    public function __construct(
        private TwigEnvironment $twig,
        private RetrieveMostRecentSeries $retrieveMostRecentSeries,
        private RetrieveLeadersWithMessages $retrieveLeadersWithMessages,
    ) {
    }

    /**
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function render(): Markup
    {
        return new Markup(
            $this->twig->render(
                '@app/Http/Response/Media/Messages/Sidebar/MessagesSidebar.twig',
                [
                    'leaders' => $this->retrieveLeadersWithMessages->retrieve(),
                    'series' => $this->retrieveMostRecentSeries->retrieve(),
                ],
            ),
            'UTF-8',
        );
    }
}
