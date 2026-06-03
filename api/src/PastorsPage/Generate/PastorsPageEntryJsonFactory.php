<?php

declare(strict_types=1);

namespace App\PastorsPage\Generate;

use App\PastorsPage\PastorsPageItem;
use DateTimeInterface;

use function html_entity_decode;
use function mb_strlen;
use function mb_strrpos;
use function mb_substr;
use function preg_replace;
use function rtrim;
use function strip_tags;
use function trim;

use const ENT_HTML5;
use const ENT_QUOTES;

readonly class PastorsPageEntryJsonFactory
{
    private const int EXCERPT_LENGTH = 300;

    public function __construct(
        private ComposePastorsPageContent $composePastorsPageContent,
    ) {
    }

    /**
     * @return array{
     *     uid: string,
     *     title: string,
     *     slug: string,
     *     excerpt: string,
     *     content: string,
     *     bodyOnlyContent: string,
     *     readableDate: string,
     *     postDate: string,
     * }
     */
    public function create(PastorsPageItem $pastorsPageItem): array
    {
        return [
            'uid' => $pastorsPageItem->id->toString(),
            'title' => $pastorsPageItem->title,
            'slug' => $pastorsPageItem->slug,
            'excerpt' => $this->excerpt(body: $pastorsPageItem->body),
            'content' => $this->composePastorsPageContent->content(
                pastorsPageItem: $pastorsPageItem,
            ),
            'bodyOnlyContent' => $pastorsPageItem->body,
            'readableDate' => $pastorsPageItem->date->format('F jS, Y'),
            'postDate' => $pastorsPageItem->date->format(DateTimeInterface::RFC2822),
        ];
    }

    private function excerpt(string $body): string
    {
        $text = html_entity_decode(strip_tags($body), ENT_QUOTES | ENT_HTML5, 'UTF-8');

        $text = trim(preg_replace('/\s+/', ' ', $text) ?? '');

        if (mb_strlen($text) <= self::EXCERPT_LENGTH) {
            return $text;
        }

        $truncated = mb_substr($text, 0, self::EXCERPT_LENGTH);

        $lastSpace = mb_strrpos($truncated, ' ');

        if ($lastSpace !== false) {
            $truncated = mb_substr($truncated, 0, $lastSpace);
        }

        return rtrim($truncated) . '…';
    }
}
