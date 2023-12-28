# Livewire Gloom

Add functions to Laravel Dusk for working with Livewire.

<div align="center">

![Livewire Gloom](banner.png)

[![run-tests](https://github.com/luttje/livewire-gloom/actions/workflows/run-tests.yml/badge.svg)](https://github.com/luttje/livewire-gloom/actions/workflows/run-tests.yml)
[![Coverage Status](https://coveralls.io/repos/github/luttje/livewire-gloom/badge.svg?branch=main)](https://coveralls.io/github/luttje/livewire-gloom?branch=main)

</div>

> [!Warning]
> This package is still in development. It is not yet ready for production use and the API may change at any time.

## Provided macros

*The examples below test the [`NameComponent`](tests/Browser/Fixtures/NameComponent.php) Livewire component.*

### `waitUntilLivewireCommitSucceeds`

*It can be tricky to know if Livewire finished a request cycle. You can work
with `$browser->pause(...)` but that's not very reliable.*

The `waitUntilLivewireCommitSucceeds` method waits until Livewire finished a
request cycle for the specified method (and optionally parameters).

<!-- #EXAMPLE_COPY_START = \Luttje\LivewireGloom\Tests\Browser\ReadmeExamplesTest::exampleWaitUntilLivewireCommitSucceeds1 -->

```php
$browser->type('@name-input', 'John Doe')
    ->click('@split-button-debounced')
    ->waitUntilLivewireCommitSucceeds('splitNameParts', ['John Doe'])
    ->assertSeeIn('@first-name', 'John');
```

<!-- #EXAMPLE_COPY_END -->

The above call won't match the request if the call has no parameters,
or has different parameters. If you don't care about the parameters,
you can omit them.

<!-- #EXAMPLE_COPY_START = \Luttje\LivewireGloom\Tests\Browser\ReadmeExamplesTest::exampleWaitUntilLivewireCommitSucceeds2 -->

```php
$browser->type('@name-input', 'John Doe')
    ->click('@split-button-debounced')
    ->waitUntilLivewireCommitSucceeds('splitNameParts')
    ->assertSeeIn('@first-name', 'John');
```

<!-- #EXAMPLE_COPY_END -->

### `waitUntilLivewireCommitFails`

The inverse of `waitUntilLivewireCommitSucceeds`.

<!-- #EXAMPLE_COPY_START = \Luttje\LivewireGloom\Tests\Browser\ReadmeExamplesTest::exampleWaitUntilLivewireCommitFails -->

```php
$browser->type('@name-input', 'John Doe')
    ->click('@button-to-404-debounced')
    ->waitUntilLivewireCommitFails('throws404')
    ->assertSeeIn('@first-name', 'empty');
```

<!-- #EXAMPLE_COPY_END -->

### `clickAndWaitUntilLivewireCommitSucceeds`

This sets up `waitUntilLivewireCommitSucceeds` to listen for a Livewire request
cycle and clicks the element.

<!-- #EXAMPLE_COPY_START = \Luttje\LivewireGloom\Tests\Browser\ReadmeExamplesTest::exampleClickAndWaitUntilLivewireCommitSucceeds -->

```php
$parameters = ['John Doe']; // Optional, leave this out if you don't have parameters or wish to match any parameters

$browser->type('@name-input', 'John Doe')
    ->clickAndWaitUntilLivewireCommitSucceeds('@split-button-debounced', 'splitNameParts', $parameters)
    ->assertSeeIn('@first-name', 'John');
```

<!-- #EXAMPLE_COPY_END -->

### `waitUntilLivewireUpdateSucceeds`

*It can be tricky to know if Livewire finished a request cycle surrounding the
updating of a property. You can work with `$browser->pause(...)` but that's not
very reliable.*

The `waitUntilLivewireUpdateSucceeds` method waits until Livewire finished a
request cycle for the specified property keys.

<!-- #EXAMPLE_COPY_START = \Luttje\LivewireGloom\Tests\Browser\ReadmeExamplesTest::exampleWaitUntilLivewireUpdateSucceeds1 -->

```php
$browser->type('@age-input', '42')
    ->click('@split-button-debounced')
    ->waitUntilLivewireUpdateSucceeds(['age'])
    ->assertSeeIn('@age', '42');
```

<!-- #EXAMPLE_COPY_END -->

Or for multiple properties:

<!-- #EXAMPLE_COPY_START = \Luttje\LivewireGloom\Tests\Browser\ReadmeExamplesTest::exampleWaitUntilLivewireUpdateSucceeds2 -->

```php
$browser->type('@age-input', '42')
    ->type('@job-input', 'Plumber')
    ->click('@split-button-debounced')
    ->waitUntilLivewireUpdateSucceeds(['age', 'job'])
    ->assertSeeIn('@age', '42')
    ->assertSeeIn('@job', 'Plumber');
```

<!-- #EXAMPLE_COPY_END -->

*With this last example the browser will wait until an update cycle is finished
in which both the `age` and `job` livewire properties are updated.
If those properties are deferred (by default) then Livewire will wait
a request is made.
In the example above they are deferred until clicking `@split-button-debounced`.*

### `waitUntilLivewireUpdateFails`

The inverse of `waitUntilLivewireUpdateSucceeds`.

<!-- #EXAMPLE_COPY_START = \Luttje\LivewireGloom\Tests\Browser\ReadmeExamplesTest::exampleWaitUntilLivewireUpdateFails -->

```php
$browser->type('@age-input', '42')
    ->click('@button-to-404-debounced')
    ->waitUntilLivewireUpdateFails(['age'])
    ->assertSeeIn('@age', '-1');
```

<!-- #EXAMPLE_COPY_END -->

### `clickAndWaitUntilLivewireUpdateSucceeds`

This sets up `waitUntilLivewireUpdateSucceeds` to listen for a Livewire request
cycle and clicks the element.

<!-- #EXAMPLE_COPY_START = \Luttje\LivewireGloom\Tests\Browser\ReadmeExamplesTest::exampleClickAndWaitUntilLivewireUpdateSucceeds -->

```php
$browser->type('@age-input', '42')
    ->clickAndWaitUntilLivewireUpdateSucceeds('@split-button-debounced', ['age'])
    ->assertSeeIn('@age', '42');
```

<!-- #EXAMPLE_COPY_END -->

### The `action` parameter

Sometimes a sequence of actions may trigger too fast for you to listen for a
Livewire commit or update:

<!-- #EXAMPLE_COPY_START = \Luttje\LivewireGloom\Tests\Browser\ReadmeExamplesTest::exampleActionFailing -->

```php
$browser->type('@name-input', 'John Doe')
    ->click('@split-button')
    // *🚀 hyperfast split-button somehow already completed a full commit here*
    ->waitUntilLivewireCommitSucceeds('splitNameParts', ['John Doe']) // test fails here due to timeout
    ->assertSeeIn('@first-name', 'John');
```

<!-- #EXAMPLE_COPY_END -->

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

<!-- #EXAMPLE_COPY_START = \Luttje\LivewireGloom\Tests\Browser\ReadmeExamplesTest::exampleAction -->

```php
$browser->type('@name-input', 'John Doe')
    ->waitUntilLivewireCommitSucceeds(
        'splitNameParts',
        ['John Doe'],
        action: function () use ($browser) {
            $browser->click('@split-button');
        }
    )
    ->assertSeeIn('@first-name', 'John');
```

<!-- #EXAMPLE_COPY_END -->

*Internally the `clickAndWaitUntilLivewireCommitSucceeds` and
`clickAndWaitUntilLivewireUpdateSucceeds` functions use the `action` parameter
to call `click` on the Browser. So the above example can be simplified by using
either of those functions.*

<!-- #EXAMPLES_END -->

> [!Note]
> All of the above examples are taken from the tests. If you want to see
> more code surrounding the examples,
> [check out those tests](tests/Browser/ReadmeExamplesTest.php).
>
> To generate the examples, run `composer compile-readme`. This is done
> through [luttje/php-example-tester](https://github.com/luttje/php-example-tester).

## Installation

You can install the package via composer:

```bash
composer require luttje/livewire-gloom
```

## Usage

Create a new Dusk test case and use the macros described above:

<!-- #EXAMPLE_COPY_START = { "symbol": "\\Luttje\\LivewireGloom\\Tests\\Browser\\ExampleTest", "short": false } -->

```php
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
```

<!-- #EXAMPLE_COPY_END -->

## Testing

Make sure you have installed the Dusk Chrome driver by running:

```bash
./vendor/bin/dusk-updater detect --auto-update
```

Then run the tests with:

```bash
composer test
```

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

