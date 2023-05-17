<?php

namespace StarringJane\WordpressBlade;

use WP_REST_Response;

class LivewireRestApi
{
    protected $namespace = 'wire/v1';

    protected $route = '/wire/';

    protected $methods = 'POST';

    public function __construct()
    {
        $this->registerRestRoute();
    }

    public static function register()
    {
        return new self;
    }

    public function registerRestRoute()
    {
        add_action('rest_api_init', function () {
            register_rest_route($this->namespace, $this->route, [
                'methods' => $this->methods,
                'callback' => [$this, 'onRequest'],
            ]);
        });
    }

    public function onRequest()
    {
        $request = $this->getRequest();
        $component = $this->getComponent($request);

        return new WP_REST_Response(['html' => (string) $component->render()], 200);
    }

    public function getRequest()
    {
        return isset($_POST['json']) ? json_decode(str_replace('\"', '"', $_POST['json'])) : null;
    }

    public function getComponent($request)
    {
        $class = str_replace('\\\\', '\\', $request->fingerprint->class);

        return new $class($request);
    }
}
