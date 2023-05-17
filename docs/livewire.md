# Livewire

## Setup

### Alpine Morph plugin

Include the Alpine Morph plugin. More information can be found here: https://alpinejs.dev/plugins/morph

In WordPress you can enqueue the CDN version

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
    @livewire('my-livewire-component')
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

Use private properties for data that should not be available to the $wire attribute

Only use public properties if you want to access it with the $wire attribute

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

    public function setPage(int $page): void
    {
        $this->page = $page;
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
            @change="$wire.setPage($wire.page)"
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
@livewire('my-livewire-component', [
    'postType' => 'page',
])
```

```php
// themes/theme-name/components/my-livewire-component.php

<?php

namespace ThemeName\Components;

use Illuminate\View\View;
use StarringJane\WordpressBlade\LivewireComponent;

class MyLivewireComponent extends LivewireComponent
{
    public string $postType = '';

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

## Loading state

Sometimes you might want to show a loading state. Use the magic `$wire.$loading` property for this.

```php
// views/components/my-livewire-component.blade.php

<div {{ $attributes }}>
    <div x-data>
        <button @click="$wire.calculate()">
            Calculate
        </button>

        <div x-show="$wire.$loading">Please wait, result is loading</div>
    </div>
</div>
```

### Loading state for specific actions

```php
// views/components/my-livewire-component.blade.php

<div {{ $attributes }}>
    <div x-data>
        <button @click="$wire.calculate()">
            Calculate
        </button>

        <button @click="$wire.reset()">
            Reset
        </button>

        <div x-show="$wire.$loading === 'calculate'">Calculating...</div>
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
        // Runs only once when the component is created, before the data is hydrated
    }

    public function boot(): void
    {
        // Runs on every request, before the data is hydrated
    }

    public function mounted(): void
    {
        // Runs only once when the component is created, after the data is hydrated
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
