<?php

declare(strict_types=1);

namespace App\Http\Response\News;

use App\Http\Pagination\Pagination;
use App\Http\Response\News\NewsItem\CompileResponse;
use App\Http\Response\News\NewsList\NewsItem;
use App\Http\Response\News\NewsList\NewsResults;
use App\Http\Response\News\NewsList\RetrieveNewsItems;
use App\Shared\ElementQueryFactories\EntryQueryFactory;
use App\Shared\FieldHandlers\EntryBuilder\ExtractBodyContent;
use App\Shared\Utility\TruncateFactory;
use craft\elements\Entry;
use DateTimeInterface;
use Redis;

class GenerateNewsPagesForRedis
{
    public function __construct(
        private GenerateNewsTypePagesForRedis $generateNewsTypePagesForRedis
    ) {
    }

    public function __invoke(): void
    {
        $this->generate();
    }

    public function generate(): void
    {
        $this->generateNewsTypePagesForRedis->generate('news');
    }
}
