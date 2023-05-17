<?php

namespace StarringJane\WordpressBlade;

class Livewire
{
    public function __construct()
    {
        LivewirePath::register();
        LivewireAssets::register();
        LivewireRestApi::register();
    }

    public static function create()
    {
        return new self;
    }
}
