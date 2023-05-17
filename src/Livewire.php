<?php

namespace StarringJane\WordpressBlade;

use Exception;

class Livewire
{
    public function __construct()
    {
        LivewirePath::register();
        LivewireAssets::register();
        LivewireRestApi::register();
        LivewireBladeDirective::register();
    }

    public static function create()
    {
        return new self;
    }
}
