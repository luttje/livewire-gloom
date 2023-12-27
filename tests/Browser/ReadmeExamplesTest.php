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

    public function testCanWaitUntilALivewireCommitSucceedsWithAction(): void
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

    public function testCanWaitUntilALivewireCommitFails(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(route('livewire-gloom.component', NameComponent::class, false));

            static::exampleWaitUntilLivewireCommitFails($browser);
        });
    }

    public static function exampleClickAndWaitUntilLivewireCommitSucceeds(Browser $browser)
    {
        $optionalParameters = ['John Doe']; // Optional, leave this away if you don't have parameters or wish to match any parameters

        $browser->type('@name-input', 'John Doe')
            ->clickAndWaitUntilLivewireCommitSucceeds('@split-button-debounced', 'splitNameParts', $optionalParameters)
            ->assertSeeIn('@first-name', 'John');
    }

    public function testCanClickAndWaitUntilALivewireCommitSucceeds(): void
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

    public function testCanWaitUntilALivewireUpdateSucceeds1(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(route('livewire-gloom.component', NameComponent::class, false));

            static::exampleWaitUntilLivewireUpdateSucceeds1($browser);
        });
    }

    public function testCanWaitUntilALivewireUpdateSucceeds2(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(route('livewire-gloom.component', NameComponent::class, false));

            static::exampleWaitUntilLivewireUpdateSucceeds2($browser);
        });
    }

    public static function exampleWaitUntilLivewireUpdateFails(Browser $browser)
    {
        $browser->type('@age-input', '42')
            ->click('@button-to-404-debounced')
            ->waitUntilLivewireUpdateFails(['age'])
            ->assertSeeIn('@age', '-1');
    }

    public function testCanWaitUntilALivewireUpdateFails(): void
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

    public function testCanClickAndWaitUntilALivewireUpdateSucceeds(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(route('livewire-gloom.component', NameComponent::class, false));

            static::exampleClickAndWaitUntilLivewireUpdateSucceeds($browser);
        });
    }

    public static function exampleActionFailing(Browser $browser)
    {
        $browser->type('@name-input', 'John Doe')
            ->click('@split-button')
            // *🚀 hyperfast split-button somehow already completed a full commit here*
            ->waitUntilLivewireCommitSucceeds('splitNameParts', ['John Doe']) // test fails here due to timeout
            ->assertSeeIn('@first-name', 'John');
    }

    public function testCanFailWithoutActionParameter(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(route('livewire-gloom.component', NameComponent::class, false));

            $success = false;

            try {
                static::exampleActionFailing($browser);
            } catch (\Facebook\WebDriver\Exception\TimeoutException $e) {
                // We expect this to happen because the button is too fast.
                // Users should use the `action` parameter to work around this.
                $success = true;
            }

            $this->assertTrue($success, 'The test is expected to timeout.');
        });
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

    public function testCanUseActionParameter(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(route('livewire-gloom.component', NameComponent::class, false));

            static::exampleAction($browser);
        });
    }
}
