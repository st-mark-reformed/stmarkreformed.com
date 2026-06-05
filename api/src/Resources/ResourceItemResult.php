<?php

declare(strict_types=1);

namespace App\Resources;

readonly class ResourceItemResult
{
    public bool $hasResourceItem;

    public ResourceItem $resourceItem;

    public function __construct(ResourceItem|null $resourceItem = null)
    {
        $this->hasResourceItem = $resourceItem !== null;
        $this->resourceItem    = $resourceItem ?? new ResourceItem();
    }
}
