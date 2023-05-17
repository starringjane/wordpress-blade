<?php

namespace StarringJane\WordpressBlade;

use Jenssegers\Blade\Blade;
use Illuminate\Contracts\View\Factory as ViewFactory;
use Illuminate\Contracts\Foundation\Application as FoundationApplication;

class WordpressBlade extends Blade
{
    /**
     * @var Application
     */
    protected $container;

    public function __construct($viewPath, string $cachePath, $componentPath = null)
    {
        Utils::createDirectory($viewPath);
        Utils::createDirectory($cachePath);
        Utils::createDirectory($componentPath);

        $this->container = Application::getInstance();

        parent::__construct($viewPath, $cachePath, $this->container);

        if ($componentPath) {
            $this->componentPath($componentPath);
            $this->clearCacheOnComponentChange($componentPath, $cachePath);
        }

        Hooks::register();
    }

    public static function create($viewPath, string $cachePath, $componentPath = null)
    {
        return tap(new self($viewPath, $cachePath, $componentPath), function ($blade) {
            $blade->compiler()->withoutDoubleEncoding();
        });
    }

    public static function getInstance()
    {
        return Application::getInstance()->get(WordpressBlade::class);
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

    public function getComponents()
    {
        return $this->compiler()->getClassComponentAliases();
    }

    public function getComponent($name)
    {
        return collect($this->getComponents())->get($name);
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

    protected function setupContainer(array $viewPaths, string $cachePath)
    {
        $this->container->bindIf(WordpressBlade::class, function () {
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

        parent::setupContainer($viewPaths, $cachePath);
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
