<?php

namespace Luttje\LivewireGloom\Tests\Browser;

use Laravel\Dusk\Browser;

class ExampleTest extends BrowserTestCase
{
    public function testExample(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/example')
                ->type('@name-input', 'John Doe')
                ->clickAndWaitUntilLivewireCommitSucceeds('@split-button', 'splitNameParts', ['John Doe'])
                ->assertSeeIn('@first-name', 'John');
        });
    }
}
