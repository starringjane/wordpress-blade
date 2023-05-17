# Livewire

## Setup

### Alpine Morph plugin

Include the Alpine Morph plugin. More information can be found here: https://alpinejs.dev/plugins/morph

In WordPress you can add enqueue the CDN version

```php
wp_enqueue_script('alpine-morph', 'https://unpkg.com/@alpinejs/morph@3.x.x/dist/cdn.min.js');
wp_enqueue_script('alpine', 'https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js');
```

### Register the package

```php
use StarringJane\WordpressBlade\Livewire;
use StarringJane\WordpressBlade\WordpressBlade;

/**
 * WordpressBlade
 */
WordpressBlade::create(
    base_path('resources/views'), // Path to all blade files
    base_path('storage/views/cache'), // Path to blade cache
    base_path('public/themes/janes/components') // Path to component classes (required for Livewire!)
);

/**
 * Livewire
 */
Livewire::create();
```

## Usage

### Add a component class

```php
// themes/theme-name/components/my-livewire-component.php

<?php

namespace ThemeName\Components;

use Illuminate\View\View;
use StarringJane\WordpressBlade\LivewireComponent;

class MyLivewireComponent extends LivewireComponent
{
    public int $count = 0;

    public function render(): View
    {
        return $this->view('components.my-livewire-component');
    }
}
```

### Add the component blade file

```php
// views/components/my-livewire-component.blade.php

<div {{ $attributes }}>
    <div x-data>
        <div>Count: {{ $count }}</div>
    </div>
</div>
```

### Include the component

```php
<html>
<body>
    <x-my-livewire-component />
</body>
</html>
```

### Check the result

You should see a component with "Count: 0"

## Calling component functions

```php
// themes/theme-name/components/my-livewire-component.php

<?php

namespace ThemeName\Components;

use Illuminate\View\View;
use StarringJane\WordpressBlade\LivewireComponent;

class MyLivewireComponent extends LivewireComponent
{
    public int $count = 0;

    public function increment(): void
    {
        $this->count++;
    }

    public function render(): View
    {
        return $this->view('components.my-livewire-component');
    }
}
```

```php
// views/components/my-livewire-component.blade.php

<div {{ $attributes }}>
    <div x-data>
        <div>Count: {{ $count }}</div>

        <div>
            <button @click="$wire.increment()">
                increment
            </button>
        </div>
    </div>
</div>
```

## Private properties and model binding

Use private properties for if data should not be available to the $wire attribute

Only use public properties if you want to change or access it with the $wire attribute

Public properties do not need to be added to the data of the render function

```php
// themes/theme-name/components/my-livewire-component.php

<?php

namespace ThemeName\Components;

use Illuminate\View\View;
use StarringJane\WordpressBlade\LivewireComponent;

class MyLivewireComponent extends LivewireComponent
{
    public int $page = 1;

    private array $posts;

    public function booted(): void
    {
        $this->posts = get_posts([
            'paged' => $this->$page;
        ]);
    }

    public function render(): View
    {
        return $this->view('components.my-livewire-component', [
            'posts' => $this->posts,
        ]);
    }
}
```

```php
// views/components/my-livewire-component.blade.php

<div {{ $attributes }}>
    <div x-data>
        <x-posts-list :posts="$posts" />

        <select
            x-model="$wire.page"
            @change="$wire.refresh()"
        >
            <option value="1">1</option>
            <option value="2">2</option>
            <option value="3">3</option>
        </select>
    </div>
</div>
```

## Passing data to the component

```php
<x-my-livewire-component post-type="page" />
```

```php
// themes/theme-name/components/my-livewire-component.php

<?php

namespace ThemeName\Components;

use Illuminate\View\View;
use StarringJane\WordpressBlade\LivewireComponent;

class MyLivewireComponent extends LivewireComponent
{
    public string $postType;

    public function __construct($postType)
    {
        $this->postType = $postType;
    }

    public function render(): View
    {
        return $this->view('components.my-livewire-component');
    }
}
```

```php
// views/components/my-livewire-component.blade.php

<div {{ $attributes }}>
    <div x-data>
        Current post type: {{ $postType }}
    </div>
</div>
```

## Lifecycle

```php
// themes/theme-name/components/my-livewire-component.php

<?php

namespace ThemeName\Components;

use Illuminate\View\View;
use StarringJane\WordpressBlade\LivewireComponent;

class MyLivewireComponent extends LivewireComponent
{
    public function mount(): void
    {
        // Runs only once when the component created
    }

    public function boot(): void
    {
        // Runs on every request, before the data is hydrated
    }

    public function booted(): void
    {
        // Runs on every request, after the data is hydrated
    }

    public function render(): View
    {
        return $this->view('components.my-livewire-component');
    }
}
```
