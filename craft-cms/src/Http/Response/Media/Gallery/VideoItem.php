<?php

declare(strict_types=1);

namespace App\Http\Response\Media\Gallery;

use Twig\Markup;
use wrav\oembed\models\OembedModel;

class VideoItem
{
    /** @phpstan-ignore-next-line */
    public function __construct(private OembedModel $model)
    {
    }

    public function renderEmbed(VideoRenderOptions|null $options = null): Markup
    {
        $options ??= new VideoRenderOptions();

        /**
         * @noinspection PhpIncompatibleReturnTypeInspection
         * The source package has the return type mistyped
         * @phpstan-ignore-next-line
         */
        return $this->model->render($options->toArray());
    }
}
