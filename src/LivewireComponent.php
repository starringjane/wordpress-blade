<?php

namespace StarringJane\WordpressBlade;

use StarringJane\WordpressBlade\Component;

abstract class LivewireComponent extends Component
{
    public $id = false;

    public function mount()
    {
    }

    public function boot()
    {
    }

    public function booted()
    {
    }

    public function handleWireRequest($request)
    {
        if (isset($request['serverMemo']['data'])) {
            foreach ($request['serverMemo']['data'] as $property => $value) {
                $this->{$property} = $value;
            }
        }

        if (isset($request['call'])) {
            $method = $request['call']['method'];
            $arguments = $request['call']['arguments'];

            if (method_exists($this, $method)) {
                $this->{$method}(...$arguments);
            }
        }
    }

    /**
     * Using this function to call "mount" only once
     * withName is only called in the initial request
     */
    public function withName($name)
    {
        $this->mount();
        $this->boot();
        $this->booted();

        return parent::withName($name);
    }

    public function withAttributes(array $attributes)
    {
        $this->attributes = $this->attributes ?: $this->newAttributeBag();

        $this->attributes = $this->attributes->merge($attributes);

        return $this;
    }

    protected function getWireId()
    {
        if ($this->id) {
            return $this->id;
        }

        return $this->id = Utils::randomString(40);
    }

    protected function getWireData()
    {
        return json_encode([
            'fingerprint' => [
                'id' => $this->id,
                'class' => get_class($this),
            ],
            'serverMemo' => [
                'data' => $this->extractWireProperties(),
            ],
        ]);
    }

    protected function extractWireProperties()
    {
        $properties = $this->extractPublicProperties();

        unset($properties['componentName']);
        unset($properties['attributes']);

        return $properties;
    }

    protected function view($view = null, $data = [], $mergeData = [])
    {
        $this->withAttributes([
            'x-wire' => $this->getWireId(),
            'wire:data' => $this->getWireData(),
        ]);

        return parent::view($view, $data, $mergeData);
    }
}
