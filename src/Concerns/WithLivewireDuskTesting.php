<?php

namespace Luttje\LivewireGloom\Concerns;

use Luttje\LivewireGloom\Browser\LivewireSupportedBrowser;

/**
 * Adds support for Livewire Dusk testing.
 */
trait WithLivewireDuskTesting
{
    /**
     * Create a new Browser instance.
     *
     * @param  \Facebook\WebDriver\Remote\RemoteWebDriver  $driver
     * @return \Laravel\Dusk\Browser
     */
    protected function newBrowser($driver)
    {
        return new LivewireSupportedBrowser($driver);
    }
}
