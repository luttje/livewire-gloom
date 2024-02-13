<?php

namespace Luttje\LivewireGloom\Tests\Browser\Fixtures;

use Livewire\Component;

class NameComponent extends Component
{
    public $age = -1;

    public $firstName = 'empty';

    public $lastName = 'empty';

    public $job = 'empty';

    public $hobbies = [
        'f3e3e3e3-3e3e-3e3e-3e3e-3e3e3e3e3e3a' => [
            'name' => 'Reading',
            'icon' => '📚',
        ],
        'f3e3e3e3-3e3e-3e3e-3e3e-3e3e3e3e3e3b' => [
            'name' => 'Gaming',
            'icon' => '🎮',
        ],
        'f3e3e3e3-3e3e-3e3e-3e3e-3e3e3e3e3e3c' => [
            'name' => 'Cooking',
            'icon' => '🍳',
        ],
    ];

    public function splitNameParts($name)
    {
        $parts = explode(' ', $name);
        $last = count($parts) - 1;

        $this->firstName = implode(' ', array_slice($parts, 0, $last));
        $this->lastName = $parts[$last];
    }

    public function throws404()
    {
        abort(404);
    }

    public function render()
    {
        return <<<'HTML'
            <div x-data="{ name: '' }">
                <input dusk="age-input" wire:model="age">
                <input dusk="job-input" wire:model="job">
                <input dusk="name-input" x-model="name">
                <button dusk="split-button" wire:click="splitNameParts(name)">Split</button>
                <button dusk="split-button-debounced" wire:click.debounce.500ms="splitNameParts(name)">Split (Slow)</button>
                <button dusk="button-to-404" wire:click="throws404">404</button>
                <button dusk="button-to-404-debounced" wire:click.debounce.500ms="throws404">404</button>
                <div dusk="first-name">{{ $firstName }}</div>
                <div dusk="last-name">{{ $lastName }}</div>
                <div dusk="age">{{ $age }}</div>
                <div dusk="job">{{ $job }}</div>
                <div dusk="hobbies">
                    @foreach($hobbies as $key => $hobby)
                    <div wire:key="hobby-{{ $loop->index }}">
                        <input dusk="hobby-name-{{ $loop->index + 1 }}" wire:model="hobbies.{{ $key }}.name">
                    </div>
                    @endforeach
                </div>
            </div>
        HTML;
    }
}
