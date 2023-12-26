<?php

namespace Luttje\LivewireGloom\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
class ReadmeExampleDescription
{
    public function __construct(
        public string $description,
        public ?string $footnotes = null,
    ) {
    }
}
