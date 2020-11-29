<?php

namespace StarringJane\WordpressBlade;

use Illuminate\View\Component as BaseComponent;

abstract class Component extends BaseComponent
{
    use RendersBlade;

    protected function view($view = null, $data = [], $mergeData = [])
    {
        $data = array_merge($data, $this->data());

        return $this->blade()->make($view, $data, $mergeData);
    }
}
