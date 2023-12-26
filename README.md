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

The examples below assume the following Livewire components (found in the `tests/Browser/Fixtures/` directory):

- [`IncrementComponent`](tests/Browser/Fixtures/IncrementComponent.php)
- [`NameComponent`](tests/Browser/Fixtures/NameComponent.php)

<!-- #EXAMPLES_START -->
<!--
WARNING!

The contents up until #EXAMPLES_END are auto-generated based on attributes
in the tests.

Do not edit this section manually or your changes will be overwritten.
-->

### `waitUntilLivewireCommitSucceeds`

*It can be tricky to know if Livewire finished a request cycle. You can work
with `$browser->pause(...)` but that's not very reliable.*

The `waitUntilLivewireCommitSucceeds` method waits until Livewire finished a
request cycle for the specified method (and optionally parameters).

```php
$browser->type('@name-input', 'John Doe')
    ->click('@split-button-debounced')
    ->waitUntilLivewireCommitSucceeds('splitNameParts', ['John Doe'])
    ->assertSeeIn('@first-name', 'John');
```

The above call won't match the request if the call has no parameters,
or has different parameters. If you don't care about the parameters,
you can omit them.

```php
$browser->type('@name-input', 'John Doe')
    ->click('@split-button-debounced')
    ->waitUntilLivewireCommitSucceeds('splitNameParts')
    ->assertSeeIn('@first-name', 'John');
```

### `waitUntilLivewireCommitFails`

The inverse of `waitUntilLivewireCommitSucceeds`.

```php
$browser->type('@name-input', 'John Doe')
    ->click('@button-to-404-debounced')
    ->waitUntilLivewireCommitFails('throws404')
    ->assertSeeIn('@first-name', 'empty');
```

### `clickAndWaitUntilLivewireCommitSucceeds`

This sets up `waitUntilLivewireCommitSucceeds` to listen for a Livewire request
cycle and clicks the element.

```php
$optionalParameters = ['John Doe']; // Optional, leave this away if you don't have parameters or wish to match any parameters

$browser->type('@name-input', 'John Doe')
    ->clickAndWaitUntilLivewireCommitSucceeds('@split-button-debounced', 'splitNameParts', $optionalParameters)
    ->assertSeeIn('@first-name', 'John');
```

### `waitUntilLivewireUpdateSucceeds`

*It can be tricky to know if Livewire finished a request cycle surrounding the
updating of a property. You can work with `$browser->pause(...)` but that's not
very reliable.*

The `waitUntilLivewireUpdateSucceeds` method waits until Livewire finished a
request cycle for the specified property keys.

```php
$browser->type('@age-input', '42')
    ->click('@split-button-debounced')
    ->waitUntilLivewireUpdateSucceeds(['age'])
    ->assertSeeIn('@age', '42');
```

Or for multiple properties:

```php
$browser->type('@age-input', '42')
    ->type('@job-input', 'Plumber')
    ->click('@split-button-debounced')
    ->waitUntilLivewireUpdateSucceeds(['age', 'job'])
    ->assertSeeIn('@age', '42')
    ->assertSeeIn('@job', 'Plumber');
```

With this last example the browser will wait until an update cycle is finished
in which both `data.user_name` and `data.user_email` are updated.

### `waitUntilLivewireUpdateFails`

The inverse of `waitUntilLivewireUpdateSucceeds`.

```php
$browser->type('@age-input', '42')
    ->click('@button-to-404-debounced')
    ->waitUntilLivewireUpdateFails(['age'])
    ->assertSeeIn('@age', '-1');
```

### `clickAndWaitUntilLivewireUpdateSucceeds`

This sets up `waitUntilLivewireUpdateSucceeds` to listen for a Livewire request
cycle and clicks the element.

```php
$browser->type('@age-input', '42')
    ->clickAndWaitUntilLivewireUpdateSucceeds('@split-button-debounced', ['age'])
    ->assertSeeIn('@age', '42');
```

### The `action` parameter

Sometimes a sequence of actions may trigger too fast for you to listen for a
Livewire commit or update:

```php
$browser->type('@name-input', 'John Doe')
    ->click('@split-button')
    // *🚀 hyperfast save button somehow already completed a full commit here*
    ->waitUntilLivewireCommitSucceeds('splitNameParts', ['John Doe']) // test fails here due to timeout
    ->assertSeeIn('@first-name', 'John');
```

Because the `waitUntilLivewireCommitSucceeds` sets up the listener, it will
miss the commit that happened before it was set up. The test will then fail
with a timeout.

In such a situation you will want to be sure that before we start an action we
start listening, so we don't miss the commit:

1. Set up the listener
2. Click the button
3. Wait for the listener to be triggered
4. Assert

You can do this providing a callback that executes the action after the listener
is setup. The following functions support this:

- `waitUntilLivewireCommitSucceeds`
- `waitUntilLivewireCommitFails`
- `waitUntilLivewireUpdateSucceeds`
- `waitUntilLivewireUpdateFails`

Here is an example how you can use this `action` parameter:

```php
$browser->type('@name-input', 'John Doe')
    ->waitUntilLivewireCommitSucceeds('splitNameParts', ['John Doe'], action: function () use ($browser) {
        $browser->click('@split-button');
    })
    ->assertSeeIn('@first-name', 'John');
```

Internally the `clickAndWaitUntilLivewireCommitSucceeds` and
`clickAndWaitUntilLivewireUpdateSucceeds` functions use the `action` parameter
to call `click` on the Browser. So the above example can be simplified by using
either of those functions.

<!-- #EXAMPLES_END -->

> [!Note]
> The above examples are compiled from metadata in the tests. If you want to see 
> more code surrounding the examples,
> [check out the tests](tests/Browser/ReadmeExamplesTest.php).
>
> To generate the examples, run `composer compile-readme`.

## Installation

You can install the package via composer:

```bash
composer require luttje/livewire-gloom
```

## Usage

Create a new Dusk test case and use the macros described above:

```php
use Laravel\Dusk\Browser;
use Tests\DuskTestCase; // Or whatever your base test case is

class ExampleTest extends DuskTestCase
{
    public function testExample(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/example')
                ->type('@name', 'John Doe')
                ->clickAndWaitUntilLivewireCommitSucceeds('@save-button', 'save')
                ->assertSee('Saved!');
        });
    }
}
```

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
