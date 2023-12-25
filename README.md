# Livewire Gloom

Add functions to Laravel Dusk for working with Livewire.

![Livewire Gloom](banner.png)

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

## Installation

You can install the package via composer:

```bash
composer require luttje/livewire-gloom
```

## Usage

Create a new Dusk test case and add the `Luttje\LivewireGloom\Concerns\WithLivewireDuskTesting` trait. The trait will supply a `Luttje\LivewireGloom\Browser\LivewireSupportedBrowser` for you to use:

```php
use Luttje\LivewireGloom\Browser\LivewireSupportedBrowser;
use Luttje\LivewireGloom\Concerns\WithLivewireDuskTesting;
use Tests\DuskTestCase; // Or whatever your base test case is

class ExampleTest extends DuskTestCase
{
    use WithLivewireDuskTesting;

    public function testExample(): void
    {
        $this->browse(function (LivewireSupportedBrowser $browser) {
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
./vendor/bin/dusk-updater update
```

Then run the tests with:
```bash
composer test
```

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
