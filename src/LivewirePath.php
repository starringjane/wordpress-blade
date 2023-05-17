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

    public static function register(): self
    {
        return new self;
    }

    public static function getInstance(): self
    {
        return Application::getInstance()->get(self::class);
    }

    public function setPath($value): void
    {
        $this->path = $value;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getQueryArguments(): array
    {
        $urlObject = parse_url($this->getPath());

        if (empty($urlObject['query'])) {
            return [];
        }

        parse_str($urlObject['query'], $queryArguments);

        return $queryArguments;
    }

    public function addQueryArg(string $key, $value): void
    {
        $this->path = add_query_arg($key, $value, $this->path);
    }

    public function removeQueryArg(string $key): void
    {
        $this->path = remove_query_arg($key, $this->path);
    }

    private function bindToContainer(): void
    {
        Application::getInstance()->bindIf(self::class, function () {
            return $this;
        }, true);
    }
}
