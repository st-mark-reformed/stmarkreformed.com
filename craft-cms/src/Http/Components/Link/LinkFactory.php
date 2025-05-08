<?php

declare(strict_types=1);

namespace App\Http\Components\Link;

use typedlinkfield\models\Link as LinkFieldModel;

class LinkFactory
{
    /**
     * @phpstan-ignore-next-line
     */
    public function fromLinkFieldModel(LinkFieldModel $linkFieldModel): Link
    {
        if ($linkFieldModel->isEmpty()) {
            return new Link(isEmpty: true);
        }

        $href = (string) $linkFieldModel->getUrl();

        $content = (string) $linkFieldModel->getText();

        if ($content === '') {
            $content = $href;
        }

        return new Link(
            isEmpty: $href === '',
            content: $content,
            href: $href,
            newWindow: $linkFieldModel->getTarget() === '_blank',
        );
    }
}
