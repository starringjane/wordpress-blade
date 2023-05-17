<?php

namespace StarringJane\WordpressBlade;

class LivewireAssets
{
    public function __construct()
    {
        $this->addScripts();
    }

    public static function register()
    {
        return new self;
    }

    public function addScripts()
    {
        add_action('wp_head', [$this, 'addWireScript']);
        add_action('admin_head', [$this, 'addWireScript']);
    }

    public function addWireScript()
    {
        echo "<script>".file_get_contents(__DIR__ . '/scripts/wire.js')."</script>";
    }
}
