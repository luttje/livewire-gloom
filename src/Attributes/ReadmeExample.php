<?php

namespace Luttje\LivewireGloom\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]
class ReadmeExample
{
    public function __construct(
        public string $name,
    ) {
    }
}
