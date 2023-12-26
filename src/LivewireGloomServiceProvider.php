<?php

namespace Luttje\LivewireGloom;

use Closure;
use Laravel\Dusk\Browser;
use Luttje\LivewireGloom\Commands\CompileReadmeCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class LivewireGloomServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('livewire-gloom')
            ->hasCommand(CompileReadmeCommand::class);
    }

    public function bootingPackage()
    {
        $this->defineWaitUntilLivewireCommitMacro();
        $this->defineWaitUntilLivewireUpdateMacro();
    }

    protected function defineWaitUntilLivewireCommitMacro()
    {
        /**
         * Injects a script into the browser to wait until a Livewire commit succeeds or fails.
         * Calls an event when the commit succeeds or fails so we can wait for it.
         */
        $waitUntilLivewireCommit = function (Browser $browser, string $method, ?array $params = null, string $succeedOrFail = 'succeed', ?Closure $action = null) {
            $parametersAsJson = $params != null ? json_encode($params) : 'null';
            $methodAndParamsHash = md5($succeedOrFail.$method.$parametersAsJson);

            // Inject a script that will listen for the commit hook and see if the method and parameters match the ones we're waiting for.
            // Will dispatch an event with the result.
            $browser->script(<<<JS
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
                        console.error('fail called', method, params, 'support-livewire-dusk-testing-commit-succeed-$methodAndParamsHash');
                        window.dispatchEvent(new CustomEvent('support-livewire-dusk-testing-commit-fail-$methodAndParamsHash', {
                            detail: {
                                method,
                                params,
                            },
                        }));
                    });
                });

                window.__livewire_gloom_hooks = window.__livewire_gloom_hooks || [];
                window.__livewire_gloom_hooks.push('commit-$methodAndParamsHash');
            JS);

            $browser->waitUntil(<<<JS
                window.__livewire_gloom_hooks && window.__livewire_gloom_hooks.includes('commit-$methodAndParamsHash')
            JS);

            if ($action) {
                $action();
            }

            // Wait for the event to be dispatched
            $browser->waitForEvent('support-livewire-dusk-testing-commit-'.$succeedOrFail.'-'.$methodAndParamsHash, 'window');

            return $browser;
        };

        Browser::macro('waitUntilLivewireCommitSucceeds', function (string $method, ?array $params = null, ?Closure $action = null) use ($waitUntilLivewireCommit) {
            /** @var Browser $this */
            $waitUntilLivewireCommit($this, $method, $params, 'succeed', $action);

            return $this;
        });

        Browser::macro('waitUntilLivewireCommitFails', function (string $method, ?array $params = null, ?Closure $action = null) use ($waitUntilLivewireCommit) {
            /** @var Browser $this */
            $waitUntilLivewireCommit($this, $method, $params, 'fail', $action);

            return $this;
        });

        Browser::macro('clickAndWaitUntilLivewireCommitSucceeds', function (string $selector, string $method, ?array $params = null) use ($waitUntilLivewireCommit) {
            /** @var Browser $this */
            $waitUntilLivewireCommit($this, $method, $params, 'succeed', function () use ($selector) {
                /** @var Browser $this */
                $this->click($selector);
            });

            return $this;
        });
    }

    protected function defineWaitUntilLivewireUpdateMacro()
    {
        /**
         * Injects a script into the browser to wait until a Livewire update succeeds or fails.
         * Calls an event when the commit succeeds or fails so we can wait for it.
         */
        $waitUntilLivewireUpdate = function (Browser $browser, array $updatedKeys = [], string $succeedOrFail = 'succeed', ?Closure $action = null) {
            $updatedKeysAsJson = json_encode($updatedKeys);
            $updatedKeysHash = md5($succeedOrFail.$updatedKeysAsJson);

            // Inject a script that will listen for the update hook and see if the updated keys match the ones we're waiting for.
            // Will dispatch an event with the result.
            $browser->script(<<<JS
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

                window.__livewire_gloom_hooks = window.__livewire_gloom_hooks || [];
                window.__livewire_gloom_hooks.push('update-$updatedKeysHash');
            JS);

            $browser->waitUntil(<<<JS
                window.__livewire_gloom_hooks && window.__livewire_gloom_hooks.includes('update-$updatedKeysHash')
            JS);

            if ($action) {
                $action();
            }

            // Wait for the event to be dispatched
            $browser->waitForEvent('support-livewire-dusk-testing-update-'.$succeedOrFail.'-'.$updatedKeysHash, 'window');

            return $browser;
        };

        Browser::macro('waitUntilLivewireUpdateSucceeds', function (array $updatedKeys = [], ?Closure $action = null) use ($waitUntilLivewireUpdate) {
            /** @var Browser $this */
            $waitUntilLivewireUpdate($this, $updatedKeys, 'succeed', $action);

            return $this;
        });

        Browser::macro('waitUntilLivewireUpdateFails', function (array $updatedKeys = [], ?Closure $action = null) use ($waitUntilLivewireUpdate) {
            /** @var Browser $this */
            $waitUntilLivewireUpdate($this, $updatedKeys, 'fail', $action);

            return $this;
        });

        Browser::macro('clickAndWaitUntilLivewireUpdateSucceeds', function (string $selector, array $updatedKeys = []) use ($waitUntilLivewireUpdate) {
            /** @var Browser $this */
            $waitUntilLivewireUpdate($this, $updatedKeys, 'succeed', function () use ($selector) {
                /** @var Browser $this */
                $this->click($selector);
            });

            return $this;
        });
    }
}
