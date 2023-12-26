# Livewire Gloom

Add functions to Laravel Dusk for working with Livewire.

<div align="center">

![Livewire Gloom](banner.png)

[![run-tests](https://github.com/luttje/livewire-gloom/actions/workflows/run-tests.yml/badge.svg)](https://github.com/luttje/livewire-gloom/actions/workflows/run-tests.yml)
[![Coverage Status](https://coveralls.io/repos/github/luttje/livewire-gloom/badge.svg?branch=main)](https://coveralls.io/github/luttje/livewire-gloom?branch=main)

</div>

> [!Warning]
> This package is still in development. It is not yet ready for production use and the API may change at any time.

## Helpers

### `setLivewireTextValue`

*The normal `$browser->value('@name', 'John Doe')` will change the field, but Livewire won't update it since it never received an `input` event for it.*

The `setLivewireTextValue` method sets the value of a Livewire text/number input field and ensures the value is updated by dispatching an `input` event.

```php
$browser->setLivewireTextValue('@name', 'John Doe');
```

### `waitUntilLivewireCommitSucceeds`

*It can be tricky to know if Livewire finished a request cycle. You can work with `$browser->pause(...)` but that's not very reliable.*

The `waitUntilLivewireCommitSucceeds` method waits until Livewire finished a request cycle for the specified method (and optionally parameters).

```php
$browser->waitUntilLivewireCommitSucceeds('getLastName', ['John Doe']);
```

The above won't work if the method has no parameters, or has different parameters. If you don't care about the parameters, you can omit them.

```php
$browser->waitUntilLivewireCommitSucceeds('save');
```

### `waitUntilLivewireCommitFails`

The inverse of `waitUntilLivewireCommitSucceeds`.

```php
$browser->waitUntilLivewireCommitFails('getLastName', ['John Doe']);
// Or:
$browser->waitUntilLivewireCommitFails('save');
```

### `clickAndWaitUntilLivewireCommitSucceeds`

This sets up `waitUntilLivewireCommitSucceeds` to listen for a Livewire request cycle and clicks the element.

```php
$optionalParameters = ['John Doe']; // Optional, leave this away if you don't have parameters or wish to match any parameters
$browser->clickAndWaitUntilLivewireCommitSucceeds('@save-button', 'save', $optionalParameters);
```

### `waitUntilLivewireUpdateSucceeds`

*It can be tricky to know if Livewire finished a request cycle surrounding the updating of a property. You can work with `$browser->pause(...)` but that's not very reliable.*

The `waitUntilLivewireUpdateSucceeds` method waits until Livewire finished a request cycle for the specified property keys.

```php
$browser->waitUntilLivewireUpdateSucceeds(['data.user_name']);
```

Or for multiple properties:

```php
$browser->waitUntilLivewireUpdateSucceeds(['data.user_name', 'data.user_email']);
```

With this last example the browser will wait until an update cycle is finished in which both `data.user_name` and `data.user_email` are updated.

### `waitUntilLivewireUpdateFails`

The inverse of `waitUntilLivewireUpdateSucceeds`.

### `clickAndWaitUntilLivewireUpdateSucceeds`

This sets up `waitUntilLivewireUpdateSucceeds` to listen for a Livewire request cycle and clicks the element.

```php
$browser->clickAndWaitUntilLivewireUpdateSucceeds('@save-button', ['data.user_name']);
```

### The `action` parameter

Sometimes a sequence of actions may trigger too fast for you to listen for a Livewire commit or update:

```php
$browser->click('@save-button')
    // *🚀 hyperfast save button somehow already completed a full commit here*
    ->waitUntilLivewireCommitSucceeds('save') // test fails here
    ->assertSee('Saved!');
```

Because the `waitUntilLivewireCommitSucceeds` sets up the listener, it will miss the commit that happened before it was set up. The test will then fail with a timeout.

In such a situation you will want to be sure that before we start an action we start listening, so we don't miss the commit:

1. Set up the listener
2. Click the button
3. Wait for the listener to be triggered
4. Assert

You can do this providing a callback that executes the action after the listener is setup. The following functions support this:

- `waitUntilLivewireCommitSucceeds`
- `waitUntilLivewireCommitFails`
- `waitUntilLivewireUpdateSucceeds`
- `waitUntilLivewireUpdateFails`

Here is an example how you can use this `action` parameter:

```php
$browser->waitUntilLivewireCommitSucceeds('save', action: function () use ($browser) {
    $browser->click('@save-button');
});
```

*Internally the `clickAndWaitUntilLivewireCommitSucceeds` and `clickAndWaitUntilLivewireUpdateSucceeds` functions use the `action` parameter to call `click` on the Browser. So the above example can be simplified by using either of those functions*

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
                ->setLivewireTextValue('@name', 'John Doe')
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
