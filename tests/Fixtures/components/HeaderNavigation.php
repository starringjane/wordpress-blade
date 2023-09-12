<?php

namespace ThemeName\Components;

use StarringJane\WordpressBlade\Component;

class HeaderNavigation extends Component
{
    public $type;

    public function __construct($type)
    {
        $this->type = $type;
    }

    public function render()
    {
        return $this->view('components.navigation', [
            'color' => $this->color(),
        ]);
    }

    protected function color()
    {
        return 'red';
    }
}
