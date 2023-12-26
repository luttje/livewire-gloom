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
        $outputFile = __DIR__.'/../../build/README-tmp.md';

        if (! is_dir(dirname($outputFile))) {
            mkdir(dirname($outputFile), 0777, true);
        }

        copy(__DIR__.'/../../README.md', $outputFile);

        $this->artisan('livewire-gloom:compile-readme', [$outputFile])
            ->assertExitCode(0);

        $this->assertFileExists($outputFile);

        unlink($outputFile);
    }
}
