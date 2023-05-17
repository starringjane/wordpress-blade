<?php

namespace StarringJane\WordpressBlade;

use StarringJane\WordpressBlade\Component;

abstract class LivewireComponent extends Component
{
    public $id = false;

    public $initialAttributes = [];

    public $initialFields = [];

    public function __construct($request = null)
    {
        $this->boot();

        if ($request === null) {
            $this->mount();
        }

        if ($request) {
            $this->handleWireRequest($request);
        }

        $this->booted();
    }

    public function boot()
    {
    }

    public function booted()
    {
    }

    public function mount()
    {
    }

    public function handleWireRequest($request)
    {
        if (isset($request->serverMemo->properties)) {
            foreach ($request->serverMemo->properties as $key => $value) {
                $this->{$key} = $value;
            }
        }

        if (isset($request->call)) {
            $method = $request->call->method;
            $arguments = $request->call->arguments;

            if (method_exists($this, $method)) {
                $this->{$method}(...$arguments);
            }
        }
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

        $this->id = uniqid();

        $class = get_class($this);
        $classIdMapKey = 'wire_components';
        $classIdMap = get_transient($classIdMapKey);

        if (empty($classIdMap)) {
            $classIdMap = [];
        }

        $this->id = uniqid();

        $classIdMap[$this->id] = $class;

        set_transient($classIdMapKey, $classIdMap);

        return $this->id;
    }

    protected function getWireData()
    {
        return json_encode([
            'properties' => $this->extractWireProperties(),
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
        if (!empty($this->initialAttributes)) {
            $this->withAttributes((array) $this->initialAttributes);
        }

        $this->withAttributes([
            'wire:id' => $this->getWireId(),
            'wire:data' => $this->getWireData(),
        ]);

        if (!empty($this->initialFields)) {
            $data = array_merge($data, [
                'fields' => $this->initialFields,
            ]);
        }

        return parent::view($view, $data, $mergeData);
    }
}
