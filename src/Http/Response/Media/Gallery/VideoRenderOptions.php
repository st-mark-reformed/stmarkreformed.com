<?php

declare(strict_types=1);

namespace App\Http\Response\Media\Gallery;

// phpcs:disable SlevomatCodingStandard.TypeHints.ReturnTypeHint.MissingTraversableTypeHintSpecification

class VideoRenderOptions
{
    private int $width = 1280;

    public function width(): int
    {
        return $this->width;
    }

    public function withWidth(int $width): self
    {
        $new = clone $this;

        $new->width = $width;

        return $new;
    }

    private int $height = 720;

    public function height(): int
    {
        return $this->height;
    }

    public function withHeight(int $height): self
    {
        $new = clone $this;

        $new->height = $height;

        return $new;
    }

    /** @phpstan-ignore-next-line */
    public function toArray(): array
    {
        return [
            'width' => $this->width(),
            'height' => $this->height(),
        ];
    }
}
