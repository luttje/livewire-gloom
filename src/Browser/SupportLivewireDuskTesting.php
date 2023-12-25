<?php

namespace Luttje\LivewireGloom\Browser;

use Closure;

trait SupportLivewireDuskTesting
{
    /**
     * Sets a value for the given input, then ensures the 'input' event is triggered
     * so Livewire will update (or defer update) the component.
     */
    public function setLivewireTextValue(string $selector, string $value)
    {
        $this->value($selector, $value);
        $element = $this->resolver->findOrFail($selector);

        //// Simulate typing a space and then backspace to trigger the 'input' event.
        //// $element->sendKeys([' ', \Facebook\WebDriver\WebDriverKeys::BACKSPACE]);

        $id = $element->getAttribute('id');

        if ($id) {
            $selector = <<<JS
                const element = document.querySelector('#' + CSS.escape('$id'));
            JS;
        } else {
            $duskSelector = $element->getAttribute('dusk');

            if ($duskSelector) {
                $selector = <<<JS
                    const element = document.querySelector('[dusk="' + CSS.escape('$duskSelector') + '"]');
                JS;
            } else {
                throw new \Exception('Could not find an ID or dusk attribute on the element. Cannot set value for Livewire.');
            }
        }

        $this->script(<<<JS
            $selector
            const event = new Event('input', {
                bubbles: true,
                cancelable: true,
            });

            element.dispatchEvent(event);
        JS);

        return $this;
    }

    /**
     * Wait until a Livewire commit succeeds.
     */
    public function waitUntilLivewireCommitSucceeds(string $method, ?array $params = null)
    {
        $this->waitUntilLivewireCommit($method, $params, 'succeed');

        return $this;
    }

    /**
     * Wait until a Livewire commit fails.
     */
    public function waitUntilLivewireCommitFails(string $method, ?array $params = null)
    {
        $this->waitUntilLivewireCommit($method, $params, 'fail');

        return $this;
    }

    /**
     * Click and wait until the Livewire commit succeeds.
     */
    public function clickAndWaitUntilLivewireCommitSucceeds(string $selector, string $method, ?array $params = null)
    {
        $this->waitUntilLivewireCommit($method, $params, 'succeed', function () use ($selector) {
            $this->click($selector);
        });

        return $this;
    }

    /**
     * Wait until a Livewire update commit succeeds.
     */
    public function waitUntilLivewireUpdateSucceeds(array $updatedKeys = [])
    {
        $this->waitUntilLivewireUpdate($updatedKeys, 'succeed');

        return $this;
    }

    /**
     * Wait until a Livewire update commit fails.
     */
    public function waitUntilLivewireUpdateFails(array $updatedKeys = [])
    {
        $this->waitUntilLivewireUpdate($updatedKeys, 'fail');

        return $this;
    }

    /**
     * Click and wait until the Livewire update commit succeeds.
     */
    public function clickAndWaitUntilLivewireUpdateSucceeds(string $selector, array $updatedKeys = [])
    {
        $this->waitUntilLivewireUpdate($updatedKeys, 'succeed', function () use ($selector) {
            $this->click($selector);
        });

        return $this;
    }

    /**
     * Injects a script into the browser to wait until a Livewire commit succeeds or fails.
     * Calls an event when the commit succeeds or fails so we can wait for it.
     */
    public function waitUntilLivewireCommit(string $method, ?array $params = null, string $succeedOrFail = 'succeed', ?Closure $callable = null)
    {
        $parametersAsJson = $params != null ? json_encode($params) : 'null';
        $methodAndParamsHash = md5($method.$parametersAsJson);

        // Inject a script that will listen for the commit hook and see if the method and parameters match the ones we're waiting for.
        // Will dispatch an event with the result.
        $this->script(<<<JS
            Livewire.hook('commit', function ({ component, commit, respond, succeed, fail }) {
                const method = '$method';
                const params = $parametersAsJson;
                const succeedOrFail = '$succeedOrFail';

                // If the parameters are null, we don't care about the parameters.
                if (params !== null) {
                    const commitCalls = commit.calls;
                    const commitCallsLength = commitCalls.length;
                    let matched = false;

                    // Check in the commit if this is the method and parameter combination we're waiting for.
                    for (let i = 0; i < commitCallsLength; i++) {
                        const call = commitCalls[i];

                        if (call.method === method) {
                            const callParams = call.params;
                            const callParamsLength = callParams.length;
                            let paramsMatch = true;

                            for (let j = 0; j < callParamsLength; j++) {
                                const param = callParams[j];
                                const expectedParam = params[j];

                                if (param !== expectedParam) {
                                    paramsMatch = false;
                                    break;
                                }
                            }

                            if (paramsMatch) {
                                matched = true;
                                break;
                            }
                        }
                    }

                    if (!matched) {
                        return;
                    }
                }

                succeed(({ snapshot, effect }) => {
                    window.dispatchEvent(new CustomEvent('support-livewire-dusk-testing-commit-succeed-$methodAndParamsHash', {
                        detail: {
                            method,
                            params,
                            snapshot,
                            effect,
                        },
                    }));
                });

                fail(() => {
                    window.dispatchEvent(new CustomEvent('support-livewire-dusk-testing-commit-fail-$methodAndParamsHash', {
                        detail: {
                            method,
                            params,
                        },
                    }));
                });
            });
        JS);

        if ($callable) {
            $callable();
        }

        // Wait for the event to be dispatched
        $this->waitForEvent('support-livewire-dusk-testing-commit-'.$succeedOrFail.'-'.$methodAndParamsHash, 'window');

        return $this;
    }

    /**
     * Injects a script into the browser to wait until a Livewire update succeeds or fails.
     * Calls an event when the commit succeeds or fails so we can wait for it.
     */
    public function waitUntilLivewireUpdate(array $updatedKeys = [], string $succeedOrFail = 'succeed', ?Closure $callable = null)
    {
        $updatedKeysAsJson = json_encode($updatedKeys);
        $updatedKeysHash = md5($updatedKeysAsJson);

        // Inject a script that will listen for the update hook and see if the updated keys match the ones we're waiting for.
        // Will dispatch an event with the result.
        $this->script(<<<JS
            Livewire.hook('commit', function ({ component, commit, respond, succeed, fail }) {
                const updatedKeys = $updatedKeysAsJson;
                const succeedOrFail = '$succeedOrFail';
                const commitUpdates = commit.updates;
                let matchedCount = 0;

                // Check in the commit if it updates the keys we're waiting for.
                for (const key in commitUpdates) {
                    if (updatedKeys.includes(key)) {
                        matchedCount++;
                    }
                }

                const matched = matchedCount === updatedKeys.length;

                if (!matched) {
                    return;
                }

                succeed(({ snapshot, effect }) => {
                    window.dispatchEvent(new CustomEvent('support-livewire-dusk-testing-update-succeed-$updatedKeysHash', {
                        detail: {
                            updatedKeys,
                            snapshot,
                            effect,
                        },
                    }));
                });

                fail(() => {
                    window.dispatchEvent(new CustomEvent('support-livewire-dusk-testing-update-fail-$updatedKeysHash', {
                        detail: {
                            updatedKeys,
                        },
                    }));
                });
            });
        JS);

        if ($callable) {
            $callable();
        }

        // Wait for the event to be dispatched
        $this->waitForEvent('support-livewire-dusk-testing-update-'.$succeedOrFail.'-'.$updatedKeysHash, 'window');

        return $this;
    }
}
