<?php

namespace Luttje\LivewireGloom\Tests\Unit;

use Luttje\LivewireGloom\Tests\TestCase;

/**
 * @group compile-readme
 */
final class CompileReadmeCommandTest extends TestCase
{
    public function testCanCompileReadme(): void
    {
        $this->artisan('livewire-gloom:compile-readme')
            ->assertExitCode(0);

        $this->assertFileExists(__DIR__.'/../../README.md');
    }
}
