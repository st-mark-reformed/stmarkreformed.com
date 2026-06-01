<?php

declare(strict_types=1);

namespace App\InternalSeries;

use App\EmptyUuid;
use Ramsey\Uuid\UuidInterface;

use function count;

readonly class NewInternalSeries
{
    public bool $isValid;

    /** @var string[] */
    public array $validationMessages;

    public function __construct(
        public string $title = '',
        public InternalSeriesSlug $slug = new InternalSeriesSlug(),
        // Normally leave this empty, this is here for importing from CraftCMS
        public UuidInterface $id = new EmptyUuid(),
    ) {
        $messages = InternalSeriesValidation::validate($this);

        $this->isValid = count($messages) < 1;

        $this->validationMessages = $messages;
    }
}
