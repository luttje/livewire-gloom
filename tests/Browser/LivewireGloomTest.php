<?php

namespace Luttje\LivewireGloom\Tests\Browser;

use Luttje\LivewireGloom\Browser\LivewireSupportedBrowser;

/*
class WorkOrderCreateTest extends DuskTestCase
{
    use WithLivewireDuskTesting;

    public function testCreateValidWorkOrder(): void
    {
        $this->browse(function (LivewireSupportedBrowser $browser) {
            $user = User::where('email', 'admin@fiteducatie.nl')->first();
            $tenant = $user->tenant;
            $firstCustomer = $tenant->customers()->first();
            $shouldToggleVatReverse = $firstCustomer->vat_reverse_charged;

            $browser->loginAs($user)
                ->visit(route('filament.admin.resources.work-orders.create'));

            // This simplifies the tests, we won't have to scroll manually to see things.
            $this->makeBrowserSizeFitContent($browser);

            $browser->assertSee('Werkbon aanmaken');

            $firstOptionSelector = '@customer_id .choices__list .choices__item--choice:nth-child(1)';
            $browser->clickAndWaitUntilLivewireCommitSucceeds('@customer_id', 'getFormSelectOptions', ['data.customer_id'])
                ->waitUntilEnabled($firstOptionSelector)
                ->clickAndWaitUntilLivewireUpdateSucceeds($firstOptionSelector, ['data.customer_id']);

            $browser->select('@status', 'pending');
            $browser->setLivewireTextValue('@date', '2021-09-01');

            $browser->select('@tenant_fee_id', '1');
            //$browser->select('@project_client', $validContact->id)
            $browser->setLivewireTextValue('@project_client_email', 'test@dusk.example')
                ->setLivewireTextValue('@project_client_phone_number', '0612345678')
                ->setLivewireTextValue('@remarks', 'test remarks');
            // ->setLivewireValue('@project_location', 'Kerkstraat 1'); // Won't work without Google Maps API key

            if (!$shouldToggleVatReverse) {
                $browser->click('@section_extra.vat_reverse_charged');
                // ->click('@section_extra.vat_reverse_charged_internationally')
            }

            // Show the tab with the work order rules
            $browser->click('@work_order_form button[x-on\\:click*="werkbonregels-tab"]')
                ->waitFor('#-werkbonregels-tab');

            $browser->with('@work_order_rules', function (Browser $browser) {
                $browser->click('button')
                    ->waitFor('@work_order_rules.date')
                    ->setLivewireTextValue('@work_order_rules.date', '2021-09-01')
                    ->setLivewireTextValue('@work_order_rules.purchase_number', '1')
                    ->setLivewireTextValue('@work_order_rules.description', 'test description');
                // $setLivewireValue('@work_order_rules.employees', '1');

                $browser->click('@work_order_rules.verified_at');
            });

            // Submit the form when we're ready.
            $browser->waitUntilEnabled('@work_order_form + .fi-form-actions [type="submit"]')
                ->click('@work_order_form + .fi-form-actions [type="submit"]')
                ->waitForText('Werkbon bekijken');

            // $browser->captureFullPageScreenshot('work-order-post-create');
        });
    }
}
*/

// The test above is an example of how this package can be used.
// Now we will test the package itself using Pest and the above test as a reference.

final class LivewireGloomTest extends BrowserTestCase
{
    public function testCanWaitUntilALivewireCommitSucceeds(): void
    {
        $this->browse(function (LivewireSupportedBrowser $browser) {
            $browser->visit('/livewire-gloom-test')
                ->assertSeeIn('@output', '0')
                ->clickAndWaitUntilLivewireCommitSucceeds('@increment-button', 'increment')
                ->assertSeeIn('@output', '1');
        });
    }
}
