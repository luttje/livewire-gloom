<?php

namespace Luttje\LivewireGloom\Tests\Browser\Fixtures;

use Livewire\Component;

class IncrementComponent extends Component
{
    public $count = 0;

    public function increment()
    {
        $this->count++;
    }

    public function incrementBy(int $amount, int $multiplier = 1): void
    {
        $this->count += $amount * $multiplier;
    }

    public function render()
    {
        return <<<'HTML'
            <div>
                <button dusk="increment-button" wire:click="increment">Click me</button>
                <button dusk="increment-by-button" wire:click="incrementBy(2, 3)">Increment by 6</button>
                <div dusk="output">{{ $count }}</div>
                <input dusk="input" wire:model.live="count" />
            </div>
        HTML;
    }
}
