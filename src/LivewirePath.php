<?php

namespace StarringJane\WordpressBlade;

class LivewirePath
{
    private string $path;

    public function __construct()
    {
        $this->bindToContainer();
        $this->setPath(Utils::getUrl());
    }

    public static function register()
    {
        return new self;
    }

    public static function getInstance()
    {
        return Application::getInstance()->get(self::class);
    }

    public function getPath()
    {
        return $this->path;
    }

    public function setPath($value)
    {
        return $this->path = $value;
    }

    private function bindToContainer()
    {
        Application::getInstance()->bindIf(self::class, function () {
            return $this;
        }, true);
    }
}
