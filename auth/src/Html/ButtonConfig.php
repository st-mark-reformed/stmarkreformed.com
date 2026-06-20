<?php

declare(strict_types=1);

namespace App\Html;

use App\Html\Glyphs\Glyph;
use App\Html\Glyphs\GlyphPosition;

readonly class ButtonConfig
{
    public function __construct(
        public string $content = 'Submit',
        public string|null $href = null,
        public Glyph|null $glyph = null,
        public GlyphPosition $glyphPosition = GlyphPosition::Right,
    ) {
    }
}
