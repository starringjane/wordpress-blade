<?php

namespace StarringJane\WordpressBlade;

use StarringJane\WordpressBlade\Component;
use Throwable;

abstract class LivewireComponent extends Component
{
    public $id = false;

    private $__wireErrors = [];

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
        try {
            foreach ($this->extractWireProperties() as $property => $value) {
                if (isset($request['serverMemo']['data'][$property])) {
                    $this->{$property} = $request['serverMemo']['data'][$property];
                }
            }
    
            if (isset($request['call']['method']) && isset($request['call']['arguments'])) {
                $method = $request['call']['method'];
                $arguments = $request['call']['arguments'];
    
                $this->{$method}(...$arguments);
            }
        } catch (Throwable $e) {
            $this->__wireErrors[] = $e->getMessage();
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

    protected function getWireErrors()
    {
        return $this->__wireErrors;
    }

    protected function getWireData()
    {
        return json_encode([
            'fingerprint' => [
                'id' => $this->getWireId(),
                'class' => get_class($this),
            ],
            'serverMemo' => [
                'errors' => $this->getWireErrors(),
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
            'x-wire-data' => $this->getWireData(),
        ]);

        return parent::view($view, $data, $mergeData);
    }
}
