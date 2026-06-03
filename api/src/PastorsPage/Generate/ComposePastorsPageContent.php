<?php

declare(strict_types=1);

namespace App\PastorsPage\Generate;

use App\PastorsPage\PastorsPageItem;

/**
 * Composes the public `content` HTML for a pastors page item, reproducing the
 * markup the legacy Craft "basicEntryBlock" produced (see the
 * BasicEntryBlock.twig template). The heading/subheading are baked into the HTML
 * as <h2>/<h3> above the body so the public detail page renders unchanged.
 * Smart-quote and widont typography is applied by the front-end `typography()`
 * filter, so it is not duplicated here.
 */
readonly class ComposePastorsPageContent
{
    public function content(PastorsPageItem $pastorsPageItem): string
    {
        $inner = '';

        // Matches the Craft template: the subheading only renders when a
        // heading is present, as both live inside the same heading wrapper.
        if ($pastorsPageItem->heading !== '') {
            $inner .= '<div class="mb-8">';
            $inner .= '<h2 class="mt-2 text-black text-3xl font-extrabold tracking-tight sm:text-4xl">'
                . $pastorsPageItem->heading
                . '</h2>';

            if ($pastorsPageItem->subheading !== '') {
                $inner .= '<h3 class="text-base font-semibold uppercase tracking-wider text-gray-600">'
                    . $pastorsPageItem->subheading
                    . '</h3>';
            }

            $inner .= '</div>';
        }

        if ($pastorsPageItem->body !== '') {
            $inner .= '<div class="mt-3 text-lg text-gray-600 prose max-w-none">'
                . $pastorsPageItem->body
                . '</div>';
        }

        return '<div class="relative">'
            . '<div class="relative mx-auto px-4 py-12 sm:max-w-5xl sm:px-14 sm:py-20 md:py-28 lg:py-32">'
            . $inner
            . '</div>'
            . '</div>';
    }
}
