<?php

namespace Luttje\LivewireGloom\Tests\Browser;

use Laravel\Dusk\Browser;
use Luttje\LivewireGloom\Tests\Browser\Fixtures\IncrementComponent;
use Luttje\LivewireGloom\Tests\Browser\Fixtures\NameComponent;

final class LivewireGloomTest extends BrowserTestCase
{
    public function test_can_wait_until_a_livewire_commit_succeeds(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(route('livewire-gloom.component', IncrementComponent::class, false))
                ->assertSeeIn('@output', '0')
                ->clickAndWaitUntilLivewireCommitSucceeds('@increment-button', 'increment')
                ->assertSeeIn('@output', '1');
        });
    }

    public function test_can_wait_until_a_livewire_commit_succeeds_without_parameters(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(route('livewire-gloom.component', IncrementComponent::class, false))
                ->assertSeeIn('@output', '0')
                ->waitUntilLivewireCommitSucceeds('increment', null, function () use ($browser) {
                    $browser->click('@increment-button');
                })
                ->assertSeeIn('@output', '1');
        });
    }

    public function test_can_wait_until_a_livewire_update_succeeds(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(route('livewire-gloom.component', NameComponent::class, false))
                ->type('@age-input', '42')
                ->clickAndWaitUntilLivewireUpdateSucceeds('@split-button', ['age'])
                ->assertSeeIn('@age', '42');
        });
    }

    public function test_can_wait_until_a_livewire_commit_succeeds_with_parameters(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(route('livewire-gloom.component', NameComponent::class, false))
                ->type('@name-input', 'John Doe')
                ->clickAndWaitUntilLivewireCommitSucceeds('@split-button', 'splitNameParts', ['John Doe'])
                ->assertSeeIn('@first-name', 'John')
                ->assertSeeIn('@last-name', 'Doe');
        });
    }

    public function test_can_wait_until_a_livewire_commit_succeeds_with_multiple_parameters(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(route('livewire-gloom.component', IncrementComponent::class, false))
                ->assertSeeIn('@output', '0')
                ->clickAndWaitUntilLivewireCommitSucceeds('@increment-by-button', 'incrementBy', [2, 3])
                ->assertSeeIn('@output', '6');
        });
    }

    public function test_can_wait_until_a_livewire_commit_succeeds_with_multiple_calls(): void
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

    public function test_can_wait_until_a_livewire_commit_succeeds_with_multiple_calls_and_multiple_parameters(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(route('livewire-gloom.component', IncrementComponent::class, false))
                ->assertSeeIn('@output', '0')
                ->clickAndWaitUntilLivewireCommitSucceeds('@increment-by-button', 'incrementBy', [2, 3])
                ->assertSeeIn('@output', '6')
                ->clickAndWaitUntilLivewireCommitSucceeds('@increment-by-button', 'incrementBy', [2, 3])
                ->assertSeeIn('@output', '12');
        });
    }

    public function test_can_wait_for_wire_model_live_update(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(route('livewire-gloom.component', IncrementComponent::class, false))
                ->assertSeeIn('@output', '0')
                ->waitUntilLivewireUpdateSucceeds(['count'], function () use ($browser) {
                    $browser->type('@input', '5');
                })
                ->assertSeeIn('@output', '5');
        });
    }

    public function test_empty_updated_keys_matches_any_update(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(route('livewire-gloom.component', IncrementComponent::class, false))
                ->assertSeeIn('@output', '0')
                ->waitUntilLivewireUpdateSucceeds([], function () use ($browser) {
                    $browser->type('@input', '7');
                })
                ->assertSeeIn('@output', '7');
        });
    }

    public function test_can_click_and_wait_until_livewire_commit_fails(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(route('livewire-gloom.component', NameComponent::class, false))
                ->clickAndWaitUntilLivewireCommitFails('@button-to-404', 'throws404')
                ->assertSeeIn('@first-name', 'empty');
        });
    }

    public function test_can_click_and_wait_until_livewire_update_fails(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(route('livewire-gloom.component', NameComponent::class, false))
                ->type('@age-input', '42')
                ->clickAndWaitUntilLivewireUpdateFails('@button-to-404', ['age'])
                ->assertSeeIn('@age', '-1');
        });
    }
}
