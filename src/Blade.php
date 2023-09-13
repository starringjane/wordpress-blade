<?php

namespace StarringJane\WordpressBlade;

use Illuminate\View\Factory;
use Illuminate\Events\Dispatcher;
use Illuminate\Contracts\View\View;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Facade;
use StarringJane\WordpressBlade\Utils;
use Illuminate\View\ViewServiceProvider;
use Illuminate\View\Compilers\BladeCompiler;
use StarringJane\WordpressBlade\Application;
use Illuminate\Contracts\View\Factory as FactoryContract;

class Blade implements FactoryContract
{
    /**
     * @var Application
     */
    protected $container;

    /**
     * @var Factory
     */
    private $factory;

    /**
     * @var BladeCompiler
     */
    private $compiler;

    public function __construct($viewPaths, string $cachePath, $componentPath = null)
    {
        Utils::createDirectory($viewPaths);
        Utils::createDirectory($cachePath);
        Utils::createDirectory($componentPath);

        $this->setupContainer((array) $viewPaths, $cachePath);

        (new ViewServiceProvider($this->container))->register();

        $this->factory = $this->container->get('view');
        $this->compiler = $this->container->get('blade.compiler');

        if ($componentPath) {
            $this->componentPath($componentPath);
            $this->clearCacheOnComponentChange($componentPath, $cachePath);
        }
    }

    public static function create($viewPath, string $cachePath, $componentPath = null)
    {
        return new static($viewPath, $cachePath, $componentPath);
    }

    public static function getInstance()
    {
        return Application::getInstance()->get(static::class);
    }

    public function render(string $view, array $data = [], array $mergeData = []): string
    {
        return $this->make($view, $data, $mergeData)->render();
    }

    public function make($view, $data = [], $mergeData = []): View
    {
        return $this->factory->make($view, $data, $mergeData);
    }

    public function compiler(): BladeCompiler
    {
        return $this->compiler;
    }

    public function directive(string $name, callable $handler)
    {
        $this->compiler->directive($name, $handler);
    }
    
    public function if($name, callable $callback)
    {
        $this->compiler->if($name, $callback);
    }

    public function components(array $components, string $prefix = '')
    {
        $compiler = $this->compiler();

        // Only supported in illuminate/view 7 or higher
        if (method_exists($compiler, 'components')) {
            $compiler->components($components, $prefix);
        }

        return $this;
    }

    public function component(string $class, string $alias = null, string $prefix = '')
    {
        $this->compiler()->component($class, $alias, $prefix);

        return $this;
    }

    public function exists($view): bool
    {
        return $this->factory->exists($view);
    }

    public function file($path, $data = [], $mergeData = []): View
    {
        return $this->factory->file($path, $data, $mergeData);
    }

    public function share($key, $value = null)
    {
        return $this->factory->share($key, $value);
    }

    public function composer($views, $callback): array
    {
        return $this->factory->composer($views, $callback);
    }

    public function creator($views, $callback): array
    {
        return $this->factory->creator($views, $callback);
    }

    public function addNamespace($namespace, $hints): self
    {
        $this->factory->addNamespace($namespace, $hints);

        return $this;
    }

    public function replaceNamespace($namespace, $hints): self
    {
        $this->factory->replaceNamespace($namespace, $hints);

        return $this;
    }

    public function __call(string $method, array $params)
    {
        if (method_exists($this->factory, $method)) {
            return call_user_func_array([$this->factory, $method], $params);
        }

        return call_user_func_array([$this->compiler, $method], $params);
    }

    public static function __callStatic(string $method, array $params)
    {
        return call_user_func_array([static::getInstance(), $method], $params);
    }

    protected function setupContainer(array $viewPaths, string $cachePath)
    {
        $this->container = Application::getInstance();

        $this->container->bindIf(static::class, function () {
            return $this;
        }, true);

        $this->container->bindIf(ViewFactory::class, function () {
            return $this;
        }, true);

        $this->container->bindIf(FoundationApplication::class, function ($app) {
            return $app;
        }, true);

        $this->container->bindIf('config', function () use ($viewPaths, $cachePath) {
            return collect([
                'view.paths' => $viewPaths,
                'view.compiled' => $cachePath,
            ]);
        }, true);

        $this->container->bindIf('files', function () {
            return new Filesystem;
        }, true);

        $this->container->bindIf('events', function () {
            return new Dispatcher;
        }, true);

        $this->container->bindIf('config', function () use ($viewPaths, $cachePath) {
            return [
                'view.paths' => $viewPaths,
                'view.compiled' => $cachePath,
            ];
        }, true);
        
        Facade::setFacadeApplication($this->container);
    }

    protected function componentPath($componentPath)
    {
        foreach ((array)$componentPath as $path) {
            if (!is_dir($path)) {
                continue;
            }

            $this->components(
                Utils::getComponentsFromPath($path)
            );
        }

        return $this;
    }

    protected function clearCacheOnComponentChange($componentPath, $cachePath)
    {
        $componentHashFile = $cachePath . '/components.cache';

        $hash = collect((array) $componentPath)->map(function ($path) {
            return Utils::scanDirectoryRecursive($path)->map(function ($file) {
                return $file . ':' . filemtime($file);
            });
        })->flatten()->join('|');

        $oldHash = file_exists($componentHashFile)
            ? file_get_contents($componentHashFile)
            : null;

        if ($hash !== $oldHash) {
            $this->clearCache($cachePath);
        }

        file_put_contents($componentHashFile, $hash);
    }

    protected function clearCache($cachePath)
    {
        Utils::scanDirectoryRecursive($cachePath)->each(function ($file) {
            if (str_ends_with($file, '.php')) {
                unlink($file);
            }
        });
    }
}
