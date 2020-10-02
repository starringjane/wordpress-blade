![Starring Jane](http://www.starringjane.com/wp-content/themes/starring-jane/images/logo-black.png)

# Wordpress blade

Adds the Laravel blade template engine to wordpress

## Installation

`composer require starring-jane/wordpress-blade`

Create a WordpressBlade instance in `functions.php`

```php
use StarringJane\WordpressBlade\WordpressBlade;

WordpressBlade::create(
    get_theme_file_path('views'), // Path to all blade files
    get_theme_file_path('cache/views'), // Path to blade cache
    get_theme_file_path('components') // Path to component classes (optional)
);
```

Autoload your theme namespace in `composer.json`

```
{
    ...
    "autoload": {
        "psr-4": {
            "ThemeName\\": "wp-content/themes/theme-name"
        }
    },
    ...
}
```

## Usage

### Structure

Basic wordpress

```
/wp-content/themes/theme-name
├── /cache/views
├── /components
|   └── button.php
├── /templates
|   ├── 404.php
|   ├── front-page.php
|   ├── page.php
|   └── single.php
├── /views
|   └── /components
|   |   └── button.blade.php
|   └── /pages
|   |   └── page.blade.php
|   └── index.blade.php
└── functions.php
└── index.php
└── style.css
```

Wordplate setup

```
/public
├── /themes
|   └── /themes-name
|       ├── /components
|       |   └── button.php
|       ├── /templates
|       |   ├── 404.php
|       |   ├── front-page.php
|       |   ├── page.php
|       |   └── single.php
|       └── functions.php
|       └── index.php
|       └── style.css
├── /resources
|   └── /views
|       └── /components
|       |   └── button.blade.php
|       └── /pages
|       |   └── page.blade.php
|       └── index.blade.php
└── /storage/cache/views
```

Add a page template in the `/templates` directory with a component class and add at least a public render function

`/templates/page.php`

```php
<?php

namespace ThemeName;

use StarringJane\WordpressBlade\Component;

class Page extends Component
{
    public function render()
    {
        return $this->view('pages.default', [
            'post' => get_post(),
        ]);
    }
}
```

## Components

### Anonymous Components

Add your components to `views/components`

```php
// views/components.button.blade.php
@props([
    'type' => 'default',
])

<button
    {{ $attributes->merge(['class' => 'btn btn-'.$type]) }}
>
    {{ $slot }}
</button>

// views/pages/default.blade.php
<x-button type="primary">
    Click me
</x-button>
```

### Class Components

```php
<?php

namespace ThemeName\Components;

use StarringJane\WordpressBlade\Component;

class Button extends Component
{
    public $type;

    public function __construct($type)
    {
        $this->type = $type;
    }

    public function render()
    {
        return $this->view('components.button', [
            'color' => $this->color(),
        ]);
    }

    protected function color()
    {
        return 'red';
    }
}

```

## Filters

### Add page template directories

Add directories where wordpress should look for page templates, starting from the theme base

```php
add_filter('wordpress-blade/template-directories', function ($directories) {
    $directories[] = 'controllers';

    return $directories;
});
```

## Contributors

* Laurens Bultynck (laurens@starringjane.com) [![Twitter Follow](https://img.shields.io/twitter/follow/RensBultynck.svg?style=social&logo=twitter&label=Follow)](https://twitter.com/RensBultynck)
* Maxim Vanhove (maxim@starringjane.com) [![Twitter Follow](https://img.shields.io/twitter/follow/MrMaximVanhove.svg?style=social&logo=twitter&label=Follow)](https://twitter.com/MrMaximVanhove)
