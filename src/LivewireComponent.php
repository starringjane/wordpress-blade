<?php

namespace StarringJane\WordpressBlade;

use StarringJane\WordpressBlade\Component;
use Throwable;

abstract class LivewireComponent extends Component
{
    protected $queryString = [];
    
    private $__wireId;

    private $__wireErrors = [];

    private $__wirePath = '';

    public function mount()
    {
    }

    public function mounted()
    {
    }

    public function boot()
    {
    }

    public function booted()
    {
    }

    public function refresh()
    {
        // Add refresh function just to update the view from $wire
        // @click="$wire.refresh()"
    }

    public function setInitalWirePath()
    {
        $this->__wirePath = Utils::getUrl();
    }

    public function hydrateQueryParams()
    {
        if (empty($this->queryString)) {
            return;
        }

        foreach ($this->queryString as $key => $value) {
            $property = is_string($value) ? $value : $key;
            $options = is_array($value) ? $value : [];
            $queryKey = isset($options['as']) ? $options['as'] : $property;

            if (isset($_GET[$queryKey]) && property_exists($this, $property)) {
                $this->{$property} = Utils::castToType(gettype($this->{$property}), $_GET[$queryKey]);
            }
        }
    }

    public function handleWireRequest($request)
    {
        try {
            if (isset($request['fingerprint']['id'])) {
                $this->__wireId = $request['fingerprint']['id'];
            }

            if (isset($request['path'])) {
                $this->__wirePath = $request['path'];
            }

            foreach ($this->extractWireProperties() as $property => $value) {
                if (isset($request['serverMemo']['data'][$property])) {
                    $this->{$property} = Utils::castToType(gettype($this->{$property}), $request['serverMemo']['data'][$property]);
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

    public function toHtml()
    {
        return (string) $this->render();
    }

    public function toResponse()
    {
        return [
            'html' => $this->toHtml(),
        ];
    }

    /**
     * Using this function to call "mount" only once
     * withName is only called in the initial request
     */
    public function withName($name)
    {
        $this->mount();
        $this->boot();
        $this->setInitalWirePath();
        $this->hydrateQueryParams();
        $this->mounted();
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
        if ($this->__wireId) {
            return $this->__wireId;
        }

        return $this->__wireId = Utils::randomString(40);
    }

    protected function getWireClass()
    {
        return get_class($this);
    }

    protected function getWireErrors()
    {
        return $this->__wireErrors;
    }

    protected function getWirePath()
    {
        $path = $this->__wirePath;

        if (empty($this->queryString)) {
            return $path;
        }

        foreach ($this->queryString as $key => $value) {
            $property = is_string($value) ? $value : $key;
            $options = is_array($value) ? $value : [];
            $queryKey = isset($options['as']) ? $options['as'] : $property;

            if (property_exists($this, $property)) {
                if (isset($options['except']) && $this->{$property} == $options['except']) {
                    $path = remove_query_arg($queryKey, $path);
                } else {
                    $path = add_query_arg($queryKey, $this->{$property}, $path);
                }
            }
        }

        return $path;
    }

    protected function getWireData()
    {
        return json_encode([
            'fingerprint' => [
                'id' => $this->getWireId(),
                'class' => $this->getWireClass(),
            ],
            'serverMemo' => [
                'path' => $this->getWirePath(),
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
