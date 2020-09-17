<?php

namespace StarringJane\WordpressBlade;

use Illuminate\View\Component as BaseComponent;

abstract class Component extends BaseComponent
{
    protected function view($name, $data = [])
    {
        $props = array_merge($data, $this->data());

        return WordpressBlade::getInstance()->make($name, $props);
    }
}
