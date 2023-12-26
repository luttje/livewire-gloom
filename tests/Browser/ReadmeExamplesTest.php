<?php

namespace Luttje\LivewireGloom\Tests\Browser;

use Laravel\Dusk\Browser;
use Luttje\ExampleTester\Attributes\ReadmeExample;
use Luttje\ExampleTester\Attributes\ReadmeExampleDescription;
use Luttje\LivewireGloom\Tests\Browser\Fixtures\NameComponent;

/**
 * Tests the examples used in the README.md file.
 *
 * @group readme
 */
final class ReadmeExamplesTest extends BrowserTestCase
{
    #[ReadmeExample('`waitUntilLivewireCommitSucceeds`')]
    #[ReadmeExampleDescription(<<<'TEXT'
        *It can be tricky to know if Livewire finished a request cycle. You can work
        with `$browser->pause(...)` but that's not very reliable.*
    TEXT)]
    #[ReadmeExampleDescription(<<<'TEXT'
        The `waitUntilLivewireCommitSucceeds` method waits until Livewire finished a
        request cycle for the specified method (and optionally parameters).
    TEXT)]
    public function exampleWaitUntilLivewireCommitSucceeds1(Browser $browser)
    {
        $browser->type('@name-input', 'John Doe')
            ->click('@split-button-debounced')
            ->waitUntilLivewireCommitSucceeds('splitNameParts', ['John Doe'])
            ->assertSeeIn('@first-name', 'John');
    }

    #[ReadmeExample('`waitUntilLivewireCommitSucceeds`')]
    #[ReadmeExampleDescription(<<<'TEXT'
        The above call won't match the request if the call has no parameters,
        or has different parameters. If you don't care about the parameters,
        you can omit them.
    TEXT)]
    public function exampleWaitUntilLivewireCommitSucceeds2(Browser $browser)
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

    #[ReadmeExample('`waitUntilLivewireCommitFails`')]
    #[ReadmeExampleDescription(<<<'TEXT'
        The inverse of `waitUntilLivewireCommitSucceeds`.
    TEXT)]
    public function exampleWaitUntilLivewireCommitFails(Browser $browser)
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

    #[ReadmeExample('`clickAndWaitUntilLivewireCommitSucceeds`')]
    #[ReadmeExampleDescription(<<<'TEXT'
        This sets up `waitUntilLivewireCommitSucceeds` to listen for a Livewire request
        cycle and clicks the element.
    TEXT)]
    public function exampleClickAndWaitUntilLivewireCommitSucceeds(Browser $browser)
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

    #[ReadmeExample('`waitUntilLivewireUpdateSucceeds`')]
    #[ReadmeExampleDescription(<<<'TEXT'
        *It can be tricky to know if Livewire finished a request cycle surrounding the
        updating of a property. You can work with `$browser->pause(...)` but that's not
        very reliable.*
    TEXT)]
    #[ReadmeExampleDescription(<<<'TEXT'
        The `waitUntilLivewireUpdateSucceeds` method waits until Livewire finished a
        request cycle for the specified property keys.
    TEXT)]
    public function exampleWaitUntilLivewireUpdateSucceeds1(Browser $browser)
    {
        $browser->type('@age-input', '42')
            ->click('@split-button-debounced')
            ->waitUntilLivewireUpdateSucceeds(['age'])
            ->assertSeeIn('@age', '42');
    }

    #[ReadmeExample('`waitUntilLivewireUpdateSucceeds`')]
    #[ReadmeExampleDescription(<<<'TEXT'
        Or for multiple properties:
    TEXT, footer: <<<'TEXT'
        With this last example the browser will wait until an update cycle is finished
        in which both the `age` and `job` livewire properties are updated.
        If those properties are deferred (by default) then Livewire will wait
        a request is made.
        In the example above they are deferred until clicking `@split-button-debounced`.
    TEXT)]
    public function exampleWaitUntilLivewireUpdateSucceeds2(Browser $browser)
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

    #[ReadmeExample('`waitUntilLivewireUpdateFails`')]
    #[ReadmeExampleDescription(<<<'TEXT'
        The inverse of `waitUntilLivewireUpdateSucceeds`.
    TEXT)]
    public function exampleWaitUntilLivewireUpdateFails(Browser $browser)
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

    #[ReadmeExample('`clickAndWaitUntilLivewireUpdateSucceeds`')]
    #[ReadmeExampleDescription(<<<'TEXT'
        This sets up `waitUntilLivewireUpdateSucceeds` to listen for a Livewire request
        cycle and clicks the element.
    TEXT)]
    public function exampleClickAndWaitUntilLivewireUpdateSucceeds(Browser $browser)
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

    #[ReadmeExample('The `action` parameter')]
    #[ReadmeExampleDescription(<<<'TEXT'
        Sometimes a sequence of actions may trigger too fast for you to listen for a
        Livewire commit or update:
    TEXT)]
    public function exampleActionFailing(Browser $browser)
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

    #[ReadmeExample('The `action` parameter')]
    #[ReadmeExampleDescription(<<<'TEXT'
        Because the `waitUntilLivewireCommitSucceeds` sets up the listener, it will
        miss the commit that happened before it was set up. The test will then fail
        with a timeout exception.

        In such a situation you will want to be sure that before an action is started,
        we setup the listener. That way we don't miss the commit. To reiterate we want
        to:

        1. **Set up the listener** for the Livewire commit
        2. **Click the button** which triggers the Livewire commit
        3. **Wait for the listener** to be triggered by the Livewire commit (succeeding or failing)
        4. **Assert** now that we know the Livewire commit is finished

        You can ensure of the above sequence by providing a closure, which we call an
        action. It will be executed after the listener is set up.
        The following functions support this:

        - `waitUntilLivewireCommitSucceeds`
        - `waitUntilLivewireCommitFails`
        - `waitUntilLivewireUpdateSucceeds`
        - `waitUntilLivewireUpdateFails`

        Here is an example how you can use this `action` parameter with
        `waitUntilLivewireCommitSucceeds`:
    TEXT, footer: <<<'TEXT'
        *Internally the `clickAndWaitUntilLivewireCommitSucceeds` and
        `clickAndWaitUntilLivewireUpdateSucceeds` functions use the `action` parameter
        to call `click` on the Browser. So the above example can be simplified by using
        either of those functions.*
    TEXT)]
    public function exampleAction(Browser $browser)
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
