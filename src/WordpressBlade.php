<?php

namespace StarringJane;

use Jenssegers\Blade\Blade;
use Illuminate\Container\Container;

class WordpressBlade extends Blade
{
    protected $views_path;

    protected $cache_path;

    protected $components_path;

    protected $container;

    public function __construct($views_path, $cache_path)
    {
        $this->views_path = $views_path;
        $this->cache_path = $cache_path;
        $this->container = Container::getInstance();

        parent::__construct($views_path, $cache_path, $this->container);
    }

    public static function register()
    {
        $views_path = get_stylesheet_uri() . '/views';
        $cache_path = get_stylesheet_uri() . '/views/cache';

        return new self($views_path, $cache_path);
    }

    public function views($path)
    {
        $this->views_path = $path;

        return $this;
    }

    public function cache($path)
    {
        $this->cache_path = $path;

        return $this;
    }

    public function components($path)
    {
        $this->components_path = $path;

        return $this;
    }

    protected function setupContainer(array $viewPaths, string $cachePath)
    {
        $this->container->bindIf(
            \Illuminate\Contracts\View\Factory::class,
            function () {
                return $this;
            },
            true
        );

        $this->container->bindIf('view', function () {
            return $this;
        }, true);

        parent::setupContainer($viewPaths, $cachePath);
    }
}
