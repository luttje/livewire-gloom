<?php

namespace Luttje\LivewireGloom\Commands;

use ColinODell\Indentation\Indentation;
use Illuminate\Console\Command;
use Luttje\LivewireGloom\Attributes\ReadmeExampleCompiler;
use Luttje\LivewireGloom\Tests\Browser\ReadmeExamplesTest;

class CompileReadmeCommand extends Command
{
    protected $signature = 'livewire-gloom:compile-readme';

    protected $description = 'Compile the README.md file for the package based on attributes in the tests.';

    public function handle(): int
    {
        $this->info('Generating README.md examples...');

        $readme = file_get_contents(__DIR__.'/../../README.md');

        $readme = preg_replace(
            '/<!-- #EXAMPLES_START -->(.*)<!-- #EXAMPLES_END -->/s',
            '<!-- #EXAMPLES_START -->'.$this->getEditWarningComment().$this->getExamplesMarkdown()."\n\n".'<!-- #EXAMPLES_END -->',
            $readme
        );

        file_put_contents(__DIR__.'/../../README.md', $readme);

        $this->info('README.md generated!');

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
