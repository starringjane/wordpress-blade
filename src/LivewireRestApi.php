<?php

namespace StarringJane\WordpressBlade;

use WP_REST_Request;
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

    public function onRequest(WP_REST_Request $request)
    {
        $request = $request->get_json_params();

        $component = $this->getComponent($request);
        $component->boot();
        $component->handleWireRequest($request);
        $component->booted();

        return new WP_REST_Response($component->toResponse(), 200);
    }

    public function getComponent($request)
    {
        $class = str_replace('\\\\', '\\', $request['fingerprint']['class']);

        return Application::getInstance()->make($class, (array) $request['serverMemo']['data']);
    }
}
