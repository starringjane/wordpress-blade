<?php

namespace StarringJane\WordpressBlade;

use StarringJane\WordpressBlade\Blade;

class WordpressBlade extends Blade
{
    public function __construct($viewPath, string $cachePath, $componentPath = null)
    {
        parent::__construct($viewPath, $cachePath, $componentPath);

        /**
         * Register hooks
         */
        Hooks::register();

        /**
         * Disable double encoding
         */
        $this->compiler()->withoutDoubleEncoding();
    }
}
