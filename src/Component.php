<?php

namespace StarringJane\WordpressBlade;

use Illuminate\View\Component as BaseComponent;

abstract class Component extends BaseComponent
{
    protected function view($view = null, $data = [], $mergeData = [])
    {
        $data = array_merge($data, $this->data());

        return WordpressBlade::getInstance()->make($view, $data, $mergeData);
    }
}
