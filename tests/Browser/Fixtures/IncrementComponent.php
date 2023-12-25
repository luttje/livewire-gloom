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

    public function render()
    {
        return <<<'HTML'
            <div>
                <button dusk="increment-button" wire:click="increment">Click me</button>
                <div dusk="output">{{ $count }}</div>
            </div>
        HTML;
    }
}
