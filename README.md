# WordPress Blade

Adds the Laravel Blade template engine to WordPress

## Installation for WordPlate (skip for basic WordPress)

Start with adding the composer package

`composer require starring-jane/wordpress-blade`

Create a WordpressBlade instance in your theme `functions.php`

```php
use StarringJane\WordpressBlade\WordpressBlade;

WordpressBlade::create(
    base_path('resources/views'), // Path to all blade files
    base_path('storage/views/cache'), // Path to blade cache
    base_path('public/themes/janes/components') // Path to component classes (optional, but recommended)
);
```

Create the following structure

```
/public
├── /themes
|   └── /theme-name
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

## Installation for WordPress (skip for WordPlate)

Start with adding the composer package

`composer require starring-jane/wordpress-blade`

Create a WordpressBlade instance in `functions.php`

```php
use StarringJane\WordpressBlade\WordpressBlade;

WordpressBlade::create(
    get_theme_file_path('views'), // Path to all blade files
    get_theme_file_path('cache/views'), // Path to blade cache
    get_theme_file_path('components') // Path to component classes (optional, but recommended)
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

Create the following structure

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
|   |   └── default.blade.php
|   └── index.blade.php
└── functions.php
└── index.php
└── style.css
```

## Templates

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

Now you can access the `$post` variable in `views/pages/default.blade.php`

```php
@extends('layouts.main')

@section('content')
    <h1>{{ $post->post_title }}</h1>
    <div>{!! $post->post_content !!}<div>
@endsection
```

## Components

### Anonymous Components

Add your components to `views/components`

```php
// views/components/button.blade.php
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

## Custom directives

The following example creates a @datetime($var) directive which formats a given $var

```php
<?php

use StarringJane\WordpressBlade\WordpressBlade;

add_action('after_setup_theme', function () {
    WordpressBlade::directive('datetime', function ($expression) {
        return "<?php echo ($expression)->format('m/d/Y H:i'); ?>";
    });
});
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

## Migrating from bladerunner

Follow [this guide](https://github.com/starringjane/wordpress-blade/blob/master/docs/bladerunner.md) to migrate from [bladerunner](https://github.com/ekandreas/bladerunner)

## Contributors

* Laurens Bultynck (laurens@starringjane.com) [![Twitter Follow](https://img.shields.io/twitter/follow/RensBultynck.svg?style=social&logo=twitter&label=Follow)](https://twitter.com/RensBultynck)
* Maxim Vanhove (maxim@starringjane.com) [![Twitter Follow](https://img.shields.io/twitter/follow/MrMaximVanhove.svg?style=social&logo=twitter&label=Follow)](https://twitter.com/MrMaximVanhove)
