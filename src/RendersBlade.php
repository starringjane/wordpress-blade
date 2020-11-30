<?php

namespace StarringJane\WordpressBlade;

trait RendersBlade
{
    protected function blade()
    {
        return WordpressBlade::getInstance();
    }

    protected function view($view = null, $data = [], $mergeData = [])
    {
        return $this->blade()->make($view, $data, $mergeData);
    }
}
