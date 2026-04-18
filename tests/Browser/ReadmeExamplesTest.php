<?php

namespace Luttje\LivewireGloom\Tests\Browser;

use Laravel\Dusk\Browser;
use Luttje\LivewireGloom\Tests\Browser\Fixtures\NameComponent;

/**
 * Tests the examples used in the README.md file.
 *
 * @group readme
 */
final class ReadmeExamplesTest extends BrowserTestCase
{
    public static function exampleWaitUntilLivewireCommitSucceeds1(Browser $browser)
    {
        $browser->type('@name-input', 'John Doe')
            ->click('@split-button-debounced')
            ->waitUntilLivewireCommitSucceeds('splitNameParts', ['John Doe'])
            ->assertSeeIn('@first-name', 'John');
    }

    public static function exampleWaitUntilLivewireCommitSucceeds2(Browser $browser)
    {
        $browser->type('@name-input', 'John Doe')
            ->click('@split-button-debounced')
            ->waitUntilLivewireCommitSucceeds('splitNameParts')
            ->assertSeeIn('@first-name', 'John');
    }

    public function test_can_wait_until_a_livewire_commit_succeeds_with_action(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(route('livewire-gloom.component', NameComponent::class, false));

            static::exampleWaitUntilLivewireCommitSucceeds1($browser);
            static::exampleWaitUntilLivewireCommitSucceeds2($browser);
        });
    }

    public static function exampleWaitUntilLivewireCommitFails(Browser $browser)
    {
        $browser->type('@name-input', 'John Doe')
            ->click('@button-to-404-debounced')
            ->waitUntilLivewireCommitFails('throws404')
            ->assertSeeIn('@first-name', 'empty');
    }

    public function test_can_wait_until_a_livewire_commit_fails(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(route('livewire-gloom.component', NameComponent::class, false));

            static::exampleWaitUntilLivewireCommitFails($browser);
        });
    }

    public static function exampleClickAndWaitUntilLivewireCommitSucceeds(Browser $browser)
    {
        $parameters = ['John Doe']; // Optional, leave this out if you don't have parameters or wish to match any parameters

        $browser->type('@name-input', 'John Doe')
            ->clickAndWaitUntilLivewireCommitSucceeds('@split-button-debounced', 'splitNameParts', $parameters)
            ->assertSeeIn('@first-name', 'John');
    }

    public function test_can_click_and_wait_until_a_livewire_commit_succeeds(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(route('livewire-gloom.component', NameComponent::class, false));

            static::exampleClickAndWaitUntilLivewireCommitSucceeds($browser);
        });
    }

    public static function exampleWaitUntilLivewireUpdateSucceeds1(Browser $browser)
    {
        $browser->type('@age-input', '42')
            ->click('@split-button-debounced')
            ->waitUntilLivewireUpdateSucceeds(['age'])
            ->assertSeeIn('@age', '42');
    }

    public static function exampleWaitUntilLivewireUpdateSucceeds2(Browser $browser)
    {
        $browser->type('@age-input', '42')
            ->type('@job-input', 'Plumber')
            ->click('@split-button-debounced')
            ->waitUntilLivewireUpdateSucceeds(['age', 'job'])
            ->assertSeeIn('@age', '42')
            ->assertSeeIn('@job', 'Plumber');
    }

    public static function exampleWaitUntilLivewireUpdateSucceedsRegex(Browser $browser)
    {
        $browser->type('@hobby-name-2', 'Gaming Professionally')
            ->click('@split-button-debounced')
            ->waitUntilLivewireUpdateSucceeds(['/hobbies\.[^\.]+\.name/'])
            ->assertValue('@hobby-name-2', 'Gaming Professionally');
    }

    public function test_can_wait_until_a_livewire_update_succeeds1(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(route('livewire-gloom.component', NameComponent::class, false));

            static::exampleWaitUntilLivewireUpdateSucceeds1($browser);
        });
    }

    public function test_can_wait_until_a_livewire_update_succeeds2(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(route('livewire-gloom.component', NameComponent::class, false));

            static::exampleWaitUntilLivewireUpdateSucceeds2($browser);
        });
    }

    public function test_can_wait_until_a_livewire_update_succeeds_regex(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(route('livewire-gloom.component', NameComponent::class, false));

            static::exampleWaitUntilLivewireUpdateSucceedsRegex($browser);
        });
    }

    public static function exampleWaitUntilLivewireUpdateFails(Browser $browser)
    {
        $browser->type('@age-input', '42')
            ->click('@button-to-404-debounced')
            ->waitUntilLivewireUpdateFails(['age'])
            ->assertSeeIn('@age', '-1');
    }

    public function test_can_wait_until_a_livewire_update_fails(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(route('livewire-gloom.component', NameComponent::class, false));

            static::exampleWaitUntilLivewireUpdateFails($browser);
        });
    }

    public static function exampleClickAndWaitUntilLivewireUpdateSucceeds(Browser $browser)
    {
        $browser->type('@age-input', '42')
            ->clickAndWaitUntilLivewireUpdateSucceeds('@split-button-debounced', ['age'])
            ->assertSeeIn('@age', '42');
    }

    public function test_can_click_and_wait_until_a_livewire_update_succeeds(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(route('livewire-gloom.component', NameComponent::class, false));

            static::exampleClickAndWaitUntilLivewireUpdateSucceeds($browser);
        });
    }

    public static function exampleActionFailing(Browser $browser)
    {
        // ! This test fails sometimes when the button is kinda slow. In that case the waitUntilLivewireCommitSucceeds is in time (but that's not reliable).
        $browser->type('@name-input', 'John Doe')
            ->click('@split-button')
            // *🚀 hyperfast split-button somehow already completed a full commit here*
            ->waitUntilLivewireCommitSucceeds('splitNameParts', ['John Doe']) // test fails here due to timeout
            ->assertSeeIn('@first-name', 'John');
    }

    public static function exampleAction(Browser $browser)
    {
        $browser->type('@name-input', 'John Doe')
            ->waitUntilLivewireCommitSucceeds(
                'splitNameParts',
                ['John Doe'],
                action: function () use ($browser) {
                    $browser->click('@split-button');
                }
            )
            ->assertSeeIn('@first-name', 'John');
    }

    public function test_can_use_action_parameter(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(route('livewire-gloom.component', NameComponent::class, false));

            static::exampleAction($browser);
        });
    }

    public static function exampleActionForCommitFail(Browser $browser)
    {
        $browser->type('@name-input', 'John Doe')
            ->waitUntilLivewireCommitFails(
                'throwsWithParam',
                ['John Doe'],
                action: function () use ($browser) {
                    $browser->click('@button-404-with-param');
                }
            )
            ->assertSeeIn('@first-name', 'empty');
    }

    public function test_can_use_action_parameter_for_commit_fail(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(route('livewire-gloom.component', NameComponent::class, false));

            static::exampleActionForCommitFail($browser);
        });
    }

    public static function exampleActionForUpdateSucceeds(Browser $browser)
    {
        $browser->type('@age-input', '42')
            ->waitUntilLivewireUpdateSucceeds(
                ['age'],
                action: function () use ($browser) {
                    $browser->click('@split-button');
                }
            )
            ->assertSeeIn('@age', '42');
    }

    public function test_can_use_action_parameter_for_update_succeeds(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(route('livewire-gloom.component', NameComponent::class, false));

            static::exampleActionForUpdateSucceeds($browser);
        });
    }

    public static function exampleActionForUpdateFails(Browser $browser)
    {
        $browser->type('@age-input', '42')
            ->waitUntilLivewireUpdateFails(
                ['age'],
                action: function () use ($browser) {
                    $browser->click('@button-to-404');
                }
            )
            ->assertSeeIn('@age', '-1');
    }

    public function test_can_use_action_parameter_for_update_fails(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(route('livewire-gloom.component', NameComponent::class, false));

            static::exampleActionForUpdateFails($browser);
        });
    }

    public static function exampleWaitUntilLivewireCommitFailsWithParams(Browser $browser)
    {
        $browser->type('@name-input', 'John Doe')
            ->click('@button-404-with-param-debounced')
            ->waitUntilLivewireCommitFails('throwsWithParam', ['John Doe'])
            ->assertSeeIn('@first-name', 'empty');
    }

    public function test_can_wait_until_a_livewire_commit_fails_with_params(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(route('livewire-gloom.component', NameComponent::class, false));

            static::exampleWaitUntilLivewireCommitFailsWithParams($browser);
        });
    }

    public static function exampleWaitUntilLivewireUpdateFailsMultipleKeys(Browser $browser)
    {
        $browser->type('@age-input', '42')
            ->type('@job-input', 'Plumber')
            ->click('@button-to-404-debounced')
            ->waitUntilLivewireUpdateFails(['age', 'job'])
            ->assertSeeIn('@age', '-1')
            ->assertSeeIn('@job', 'empty');
    }

    public function test_can_wait_until_a_livewire_update_fails_multiple_keys(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(route('livewire-gloom.component', NameComponent::class, false));

            static::exampleWaitUntilLivewireUpdateFailsMultipleKeys($browser);
        });
    }

    public static function exampleWaitUntilLivewireUpdateFailsRegex(Browser $browser)
    {
        $browser->type('@hobby-name-2', 'Gaming Professionally')
            ->click('@button-to-404-debounced')
            ->waitUntilLivewireUpdateFails(['/hobbies\.[^\.]+\.name/'])
            ->assertSeeIn('@age', '-1'); // unchanged default confirms the request failed
    }

    public function test_can_wait_until_a_livewire_update_fails_regex(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(route('livewire-gloom.component', NameComponent::class, false));

            static::exampleWaitUntilLivewireUpdateFailsRegex($browser);
        });
    }

    public static function exampleClickAndWaitUntilLivewireCommitFails(Browser $browser)
    {
        $browser->type('@name-input', 'John Doe')
            ->clickAndWaitUntilLivewireCommitFails('@button-404-with-param-debounced', 'throwsWithParam', ['John Doe'])
            ->assertSeeIn('@first-name', 'empty');
    }

    public function test_can_click_and_wait_until_a_livewire_commit_fails(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(route('livewire-gloom.component', NameComponent::class, false));

            static::exampleClickAndWaitUntilLivewireCommitFails($browser);
        });
    }

    public static function exampleClickAndWaitUntilLivewireUpdateFails(Browser $browser)
    {
        $browser->type('@age-input', '42')
            ->clickAndWaitUntilLivewireUpdateFails('@button-to-404-debounced', ['age'])
            ->assertSeeIn('@age', '-1');
    }

    public function test_can_click_and_wait_until_a_livewire_update_fails(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(route('livewire-gloom.component', NameComponent::class, false));

            static::exampleClickAndWaitUntilLivewireUpdateFails($browser);
        });
    }
}
