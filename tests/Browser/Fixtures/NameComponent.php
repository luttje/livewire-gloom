<?php

namespace Luttje\LivewireGloom\Tests\Browser\Fixtures;

use Livewire\Component;

class NameComponent extends Component
{
    public $age = -1;
    public $firstName = '';
    public $lastName = '';

    public function splitNameParts($name)
    {
        $parts = explode(' ', $name);
        $last = count($parts) - 1;

        $this->firstName = implode(' ', array_slice($parts, 0, $last));
        $this->lastName = $parts[$last];
    }

    public function render()
    {
        return <<<'HTML'
            <div x-data="{ name: '' }">
                <input dusk="age-input" wire:model="age">
                <input dusk="name-input" x-model="name">
                <button dusk="split-button" wire:click="splitNameParts(name)">Split</button>
                <div dusk="first-name">{{ $firstName }}</div>
                <div dusk="last-name">{{ $lastName }}</div>
                <div dusk="age">{{ $age }}</div>
            </div>
        HTML;
    }
}
