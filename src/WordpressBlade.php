<?php

namespace StarringJane\WordpressBlade;

use Jenssegers\Blade\Blade;
use Illuminate\Support\Facades\Facade;
use StarringJane\WordpressBlade\Wordpress\Hooks;
use Illuminate\Contracts\View\Factory as FactoryInterface;
use Illuminate\Contracts\Foundation\Application as ApplicationInterface;

class WordpressBlade extends Blade
{
    /**
     * @var Application
     */
    protected $container;

    public function __construct($viewPath, string $cachePath, $componentPath = null)
    {
        $this->container = Application::getInstance();

        parent::__construct($viewPath, $cachePath, $this->container);

        if ($componentPath) {
            $this->componentPath($componentPath);
        }

        Hooks::register();
    }

    public static function create($viewPath, string $cachePath, $componentPath = null)
    {
        return new self($viewPath, $cachePath, $componentPath);
    }

    public static function getInstance()
    {
        return Application::getInstance()->get(WordpressBlade::class);
    }

    public function components(array $components, string $prefix = '')
    {
        $this->compiler()->components($components, $prefix);

        return $this;
    }

    public function component(string $class, string $alias = null, string $prefix = '')
    {
        $this->compiler()->component($class, $alias, $prefix);

        return $this;
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

        $this->container->bindIf(FactoryInterface::class, function () {
            return $this;
        }, true);

        $this->container->bindIf(ApplicationInterface::class, function ($app) {
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
}
