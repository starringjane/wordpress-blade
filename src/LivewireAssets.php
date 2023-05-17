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
        $path = LivewirePath::getInstance()->getPath();
        echo "<script>window.LIVEWIRE_PATH = '$path'</script>";
        echo "<script>".file_get_contents(__DIR__ . '/dist/wire.js')."</script>";
    }
}
