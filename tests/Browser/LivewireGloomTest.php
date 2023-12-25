<?php

namespace Luttje\LivewireGloom\Tests\Browser;

use Laravel\Dusk\Browser;

final class LivewireGloomTest extends BrowserTestCase
{
    public function testCanWaitUntilALivewireCommitSucceeds(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/livewire-gloom-test')
                ->assertSeeIn('@output', '0')
                ->clickAndWaitUntilLivewireCommitSucceeds('@increment-button', 'increment')
                ->assertSeeIn('@output', '1');
        });
    }
}
