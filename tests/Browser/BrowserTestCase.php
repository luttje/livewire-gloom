<?php

namespace Luttje\LivewireGloom\Tests\Browser;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\LivewireServiceProvider;
use Luttje\LivewireGloom\LivewireGloomServiceProvider;
use Luttje\LivewireGloom\Tests\Browser\Fixtures\IncrementComponent;
use Orchestra\Testbench\Dusk\TestCase;

class BrowserTestCase extends TestCase
{
    public $packagePath = '';

    public $testsDirectory = '';

    public $testsNamespace = '';

    protected function getPackageProviders($app)
    {
        return [
            LivewireServiceProvider::class,
            LivewireGloomServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app): void
    {
        $app['config']->set('app.key', 'base64:Hupx3yAySikrM2/edkZQNQHslgDWYfiBfCuSThJ5SK8=');

        $app['config']->set('app.env', env('APP_ENV', 'testing'));
        $app['config']->set('app.debug', env('APP_DEBUG', true));
    }

    /**
     * Define web routes setup.
     *
     * @param  \Illuminate\Routing\Router  $router
     * @return void
     */
    protected function defineWebRoutes($router)
    {
        $router->get('/livewire-gloom-test', function () {
            $component = IncrementComponent::class;

            return Blade::render(
                "<html><head><meta name=\"csrf-token\" content=\"{{ csrf_token() }}\" />\n@livewireStyles</head><body>\n".
                "@livewire(\\$component::class)\n".
                '@livewireScripts</body></html>'
            );
        });
    }

    protected function setUp(): void
    {
        $this->configurePackagePath();

        $this->configureTestsDirectory();

        $this->tryAndGuessTestsNamespace();

        parent::setUp();

        $testComponents = $this->getTestComponentsClassList();

        $this->tweakApplication(function () use ($testComponents) {
            // Autoload all Livewire components in this test suite.
            $testComponents->each(function ($componentClass) {
                app('livewire')->component($componentClass);
            });
        });
    }

    public function configurePackagePath()
    {
        if ($this->packagePath == '') {
            $this->packagePath = getcwd();
        }
    }

    public function getPackagePath()
    {
        return $this->packagePath;
    }

    public function configureTestsDirectory()
    {
        if ($this->testsDirectory == '') {
            $this->testsDirectory = $this->getPackagePath().'/tests';
        }
    }

    public function getTestsDirectory()
    {
        return $this->testsDirectory;
    }

    public function getTestsNamespace()
    {
        return $this->testsNamespace;
    }

    protected function tryAndGuessTestsNamespace()
    {
        $className = Str::of(get_class($this));

        if (! $className->contains('Tests')) {
            return false;
        }

        $testsNamespace = $className->before($className->after('Tests'));

        $this->testsNamespace = $testsNamespace;

        return $this->isTestsNamespacePopulated();
    }

    protected function isTestsNamespacePopulated()
    {
        return isset($this->testsNamespace) && $this->testsNamespace != '';
    }

    public function getTestComponentsClassList()
    {
        return $this->generateTestComponentsClassList();
    }

    protected function generateClassNameFromFile($file)
    {
        return $this->getTestsNamespace().'\\'.Str::of($file->getRelativePathname())->before('.php')->replace('/', '\\');
    }

    protected function generateTestComponentsClassList()
    {
        return collect(File::allFiles($this->getTestsDirectory()))
            ->map(function ($file) {
                return $this->generateClassNameFromFile($file);
            })
            ->filter(function ($computedClassName) {
                return class_exists($computedClassName);
            })
            ->filter(function ($class) {
                return is_subclass_of($class, Component::class);
            });
    }
}
