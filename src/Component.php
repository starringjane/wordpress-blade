<?php

namespace StarringJane\WordpressBlade;

use Illuminate\View\Component as BaseComponent;

abstract class Component extends BaseComponent
{
    public function toHtml()
    {
        try {
            return $this->render()->toHtml();
        } catch (\InvalidArgumentException $e) {
            throw new \InvalidArgumentException(
                "{$e->getMessage()} File: {$this->resolveView()->getPath()}"
            );
        }
    }

    protected function view($view = null, $data = [], $mergeData = [])
    {
        $data = array_merge($data, $this->data());

        return WordpressBlade::getInstance()->make($view, $data, $mergeData);
    }
}
