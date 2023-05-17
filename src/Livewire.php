<?php

namespace StarringJane\WordpressBlade;

class Livewire
{
    public function __construct()
    {
        LivewireAssets::register();
        LivewireRestApi::register();
    }

    public static function register()
    {
        return new self;
    }
}
