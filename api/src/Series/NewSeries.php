<?php

declare(strict_types=1);

namespace App\Series;

use App\EmptyUuid;
use Ramsey\Uuid\UuidInterface;

use function count;

readonly class NewSeries
{
    public bool $isValid;

    /** @var string[] */
    public array $validationMessages;

    public function __construct(
        public string $title = '',
        public SeriesSlug $slug = new SeriesSlug(),
        // Normally leave this empty, this is here for importing from CraftCMS
        public UuidInterface $id = new EmptyUuid(),
    ) {
        $messages = SeriesValidation::validate($this);

        $this->isValid = count($messages) < 1;

        $this->validationMessages = $messages;
    }
}
