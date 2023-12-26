<?php

namespace Luttje\LivewireGloom\Tests\Browser;

use Laravel\Dusk\Browser;
use Luttje\LivewireGloom\Tests\Browser\Fixtures\IncrementComponent;
use Luttje\LivewireGloom\Tests\Browser\Fixtures\NameComponent;

final class LivewireGloomTest extends BrowserTestCase
{
    public function testCanWaitUntilALivewireCommitSucceeds(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(route('livewire-gloom.component', IncrementComponent::class, false))
                ->assertSeeIn('@output', '0')
                ->clickAndWaitUntilLivewireCommitSucceeds('@increment-button', 'increment')
                ->assertSeeIn('@output', '1');
        });
    }

    public function testCanWaitUntilALivewireCommitSucceedsWithoutParameters(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(route('livewire-gloom.component', IncrementComponent::class, false))
                ->assertSeeIn('@output', '0')
                ->clickAndWaitUntilLivewireCommitSucceeds('@increment-button', 'increment')
                ->assertSeeIn('@output', '1');
        });
    }

    public function testCanWaitUntilALivewireUpdateSucceeds(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(route('livewire-gloom.component', NameComponent::class, false))
                ->type('@age-input', '42')
                ->clickAndWaitUntilLivewireUpdateSucceeds('@split-button', ['age'])
                ->assertSeeIn('@age', '42');
        });
    }

    public function testCanWaitUntilALivewireCommitSucceedsWithParameters(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(route('livewire-gloom.component', NameComponent::class, false))
                ->type('@name-input', 'John Doe')
                ->clickAndWaitUntilLivewireCommitSucceeds('@split-button', 'splitNameParts', ['John Doe'])
                ->assertSeeIn('@first-name', 'John')
                ->assertSeeIn('@last-name', 'Doe');
        });
    }

    public function testCanWaitUntilALivewireCommitSucceedsWithMultipleParameters(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(route('livewire-gloom.component', NameComponent::class, false))
                ->type('@name-input', 'John Doe')
                ->clickAndWaitUntilLivewireCommitSucceeds('@split-button', 'splitNameParts', ['John Doe'])
                ->assertSeeIn('@first-name', 'John')
                ->assertSeeIn('@last-name', 'Doe');
        });
    }

    public function testCanWaitUntilALivewireCommitSucceedsWithMultipleCalls(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(route('livewire-gloom.component', NameComponent::class, false))
                ->type('@name-input', 'John Doe')
                ->clickAndWaitUntilLivewireCommitSucceeds('@split-button', 'splitNameParts', ['John Doe'])
                ->assertSeeIn('@first-name', 'John')
                ->assertSeeIn('@last-name', 'Doe')
                ->type('@name-input', 'Jane Doe')
                ->clickAndWaitUntilLivewireCommitSucceeds('@split-button', 'splitNameParts', ['Jane Doe'])
                ->assertSeeIn('@first-name', 'Jane')
                ->assertSeeIn('@last-name', 'Doe');
        });
    }

    public function testCanWaitUntilALivewireCommitSucceedsWithMultipleCallsAndMultipleParameters(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(route('livewire-gloom.component', NameComponent::class, false))
                ->type('@name-input', 'John Doe')
                ->clickAndWaitUntilLivewireCommitSucceeds('@split-button', 'splitNameParts', ['John Doe'])
                ->assertSeeIn('@first-name', 'John')
                ->assertSeeIn('@last-name', 'Doe')
                ->type('@name-input', 'Jane Doe')
                ->clickAndWaitUntilLivewireCommitSucceeds('@split-button', 'splitNameParts', ['Jane Doe'])
                ->assertSeeIn('@first-name', 'Jane')
                ->assertSeeIn('@last-name', 'Doe');
        });
    }
}
