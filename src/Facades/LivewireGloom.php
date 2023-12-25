<?php

namespace Luttje\LivewireGloom\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Luttje\LivewireGloom\LivewireGloom
 */
class LivewireGloom extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Luttje\LivewireGloom\LivewireGloom::class;
    }
}
