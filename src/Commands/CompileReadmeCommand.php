<?php

namespace Luttje\LivewireGloom\Commands;

use ColinODell\Indentation\Indentation;
use Illuminate\Console\Command;
use Luttje\LivewireGloom\Attributes\ReadmeExampleCompiler;
use Luttje\LivewireGloom\Tests\Browser\ReadmeExamplesTest;

class CompileReadmeCommand extends Command
{
    protected $signature = 'livewire-gloom:compile-readme {output?}';

    protected $description = 'Compile the README.md file for the package based on attributes in the tests.';

    public function handle(): int
    {
        $outputFile = realpath($this->argument('output') ?? __DIR__.'/../../README.md');

        $this->info("Compiling examples to {$outputFile}...");

        $readme = file_get_contents($outputFile);

        $readme = preg_replace(
            '/<!-- #EXAMPLES_START -->(.*)<!-- #EXAMPLES_END -->/s',
            '<!-- #EXAMPLES_START -->'.$this->getEditWarningComment().$this->getExamplesMarkdown()."\n\n".'<!-- #EXAMPLES_END -->',
            $readme
        );

        file_put_contents($outputFile, $readme);

        $this->info('Done compiling examples!');

        return 0;
    }

    public function getEditWarningComment(): string
    {
        return "\n".Indentation::unindent(<<<'TEXT'
            <!--
            WARNING!

            The contents up until #EXAMPLES_END are auto-generated based on attributes
            in the tests.

            Do not edit this section manually or your changes will be overwritten.
            -->
        TEXT);
    }

    public function getExamplesMarkdown(): string
    {
        $discoverer = new ReadmeExampleCompiler(ReadmeExamplesTest::class);

        return $discoverer->toMarkdown();
    }
}
