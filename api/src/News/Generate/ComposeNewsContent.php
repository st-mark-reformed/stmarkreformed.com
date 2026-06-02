<?php

declare(strict_types=1);

namespace App\News\Generate;

use App\News\NewsItem;

/**
 * Composes the public `content` HTML for a news item, reproducing the markup
 * the legacy Craft "basicEntryBlock" produced (see the BasicEntryBlock.twig
 * template). The heading/subheading are baked into the HTML as <h2>/<h3> above
 * the body so the public detail page renders unchanged. Smart-quote and widont
 * typography is applied by the front-end `typography()` filter, so it is not
 * duplicated here.
 */
readonly class ComposeNewsContent
{
    public function content(NewsItem $newsItem): string
    {
        $inner = '';

        // Matches the Craft template: the subheading only renders when a
        // heading is present, as both live inside the same heading wrapper.
        if ($newsItem->heading !== '') {
            $inner .= '<div class="mb-8">';
            $inner .= '<h2 class="mt-2 text-black text-3xl font-extrabold tracking-tight sm:text-4xl">'
                . $newsItem->heading
                . '</h2>';

            if ($newsItem->subheading !== '') {
                $inner .= '<h3 class="text-base font-semibold uppercase tracking-wider text-gray-600">'
                    . $newsItem->subheading
                    . '</h3>';
            }

            $inner .= '</div>';
        }

        if ($newsItem->body !== '') {
            $inner .= '<div class="mt-3 text-lg text-gray-600 prose max-w-none">'
                . $newsItem->body
                . '</div>';
        }

        return '<div class="relative">'
            . '<div class="relative mx-auto px-4 py-12 sm:max-w-5xl sm:px-14 sm:py-20 md:py-28 lg:py-32">'
            . $inner
            . '</div>'
            . '</div>';
    }
}
