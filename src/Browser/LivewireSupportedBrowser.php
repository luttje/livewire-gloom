<?php

namespace Luttje\LivewireGloom\Browser;

use Laravel\Dusk\Browser;

class LivewireSupportedBrowser extends Browser
{
    use SupportLivewireDuskTesting;
}
