<?php

namespace StarringJane\WordpressBlade;

class Livewire
{
    public function __construct()
    {
        $this->addScripts();
        $this->addRestApi();
    }

    public static function register()
    {
        return new self;
    }

    public function addScripts()
    {
        add_action('wp_head', function () {
            echo "<script>".file_get_contents(__DIR__ . '/scripts/wire.js')."</script>";
        });
    }

    public function addRestApi()
    {
        add_action('rest_api_init', function () {
            register_rest_route('wire/v1', '/wire/', [
                'methods' => 'POST',
                'callback' => function () {
                    $request = isset($_POST['json']) ? json_decode(str_replace('\"', '"', $_POST['json'])) : null;
        
                    $classIdMap = get_transient('wire_components');
                    $class = $classIdMap[$request->fingerprint->id];
        
                    $component = new $class($request);
        
                    return new \WP_REST_Response(['html' => (string) $component->render()], 200);
                },
            ]);
        });
    }
}
