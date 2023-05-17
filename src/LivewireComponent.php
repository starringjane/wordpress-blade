<?php

namespace StarringJane\WordpressBlade;

use StarringJane\WordpressBlade\Component;
use Throwable;

abstract class LivewireComponent extends Component
{
    protected $queryString = [];
    
    private $wireId;

    private $wireErrors = [];

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

    /**
     * Add refresh function just to update the view from $wire
     * @click="$wire.$refresh()"
     */
    public function dollarRefresh()
    {
    }

    /**
     * Set any public property
     * @click="$wire.$set('prop', 'value')"
     */
    public function dollarSet(string $property, mixed $value)
    {
        if (property_exists($this, $property)) {
            $this->{$property} = $value;
        }
    }

    public function wireHydrateQueryArguments()
    {
        if (empty($this->queryString)) {
            return;
        }

        $queryArguments = LivewirePath::getInstance()->getQueryArguments();

        foreach ($this->queryString as $key => $value) {
            $property = is_string($value) ? $value : $key;
            $options = is_array($value) ? $value : [];
            $queryKey = isset($options['as']) ? $options['as'] : $property;

            if (isset($queryArguments[$queryKey]) && property_exists($this, $property)) {
                $this->{$property} = Utils::castToType(gettype($this->{$property}), $queryArguments[$queryKey]);
            }
        }
    }

    public function wireDehydrateQueryArguments()
    {
        if (empty($this->queryString)) {
            return;
        }

        foreach ($this->queryString as $key => $value) {
            $property = is_string($value) ? $value : $key;
            $options = is_array($value) ? $value : [];
            $queryKey = isset($options['as']) ? $options['as'] : $property;

            if (property_exists($this, $property)) {
                if (isset($options['except']) && $this->{$property} == $options['except']) {
                    LivewirePath::getInstance()->removeQueryArg($queryKey);
                } else {
                    LivewirePath::getInstance()->addQueryArg($queryKey, $this->{$property});
                }
            }
        }
    }

    public function wireHandleRequest($request)
    {
        try {
            if (isset($request['fingerprint']['id'])) {
                $this->wireId = $request['fingerprint']['id'];
            }

            if (isset($request['path'])) {
                LivewirePath::getInstance()->setPath($request['path']);
            }

            foreach ($this->extractWireProperties() as $property => $value) {
                if (isset($request['serverMemo']['serialized'][$property])) {
                    $this->{$property} = unserialize($request['serverMemo']['serialized'][$property]);
                }
            }

            if (isset($request['call']['method']) && isset($request['call']['arguments'])) {
                $method = $request['call']['method'];
                $arguments = $request['call']['arguments'];
    
                $this->{$method}(...$arguments);
            }
        } catch (Throwable $e) {
            $this->wireErrors[] = $e->getMessage();
        }
    }

    public function fill($data = null)
    {
        if ($data) {
            foreach ($data as $property => $value) {
                if (property_exists($this, $property)) {
                    $this->{$property} = $value;
                }
            }
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
            'path' => LivewirePath::getInstance()->getPath(),
        ];
    }

    public function withAttributes(array $attributes)
    {
        $this->attributes = $this->attributes ?: $this->newAttributeBag();

        $this->attributes = $this->attributes->merge($attributes);

        return $this;
    }

    protected function getWireId()
    {
        if ($this->wireId) {
            return $this->wireId;
        }

        return $this->wireId = Utils::randomString(40);
    }

    protected function getWireClass()
    {
        return get_class($this);
    }

    protected function getWireErrors()
    {
        return $this->wireErrors;
    }

    protected function getWireData()
    {
        return json_encode([
            'fingerprint' => [
                'id' => $this->getWireId(),
                'class' => $this->getWireClass(),
            ],
            'serverMemo' => [
                'errors' => $this->getWireErrors(),
                'data' => $this->extractWireProperties(),
                'serialized' => $this->extractSerializedWireProperties(),
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

    protected function extractSerializedWireProperties()
    {
        $props = $this->extractWireProperties();

        foreach ($props as $key => $value) {
            $props[$key] = serialize($value);
        }

        return $props;
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
