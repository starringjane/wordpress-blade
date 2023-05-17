<?php

namespace StarringJane\WordpressBlade;

use Exception;

class LivewireBladeDirective
{
    public function __construct()
    {
        WordpressBlade::getInstance()
            ->directive('livewire', function ($options) {
                return "<?php echo \StarringJane\WordpressBlade\LivewireBladeDirective::livewire($options); ?>";
            })
        ;
    }

    public static function register()
    {
        return new self;
    }

    public static function livewire($name = null, $data = null)
    {
        $componentClassName = WordpressBlade::getInstance()->getComponent($name);

        if (empty($componentClassName)) {
            throw new Exception("A component with the name $name could not be found");
        }

        $component = Application::getInstance()->make($componentClassName);

        $component->fill($data);
        $component->mount();
        $component->boot();
        $component->wireHydrateQueryArguments();
        $component->mounted();
        $component->booted();
        $component->wireDehydrateQueryArguments();

        echo $component->render();
    }
}
