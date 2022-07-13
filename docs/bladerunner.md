# Migrating from bladerunner

Follow this guide to migrate from [bladerunner](https://github.com/ekandreas/bladerunner)

## Update your dependencies

Remove bladerunner

```console
composer remove ekandreas/bladerunner
```

Add StarringJane/WordpressBlade

```console
composer require starring-jane/wordpress-blade
```

## Register the package

Create a WordpressBlade instance in your theme `functions.php`

```php
use StarringJane\WordpressBlade\WordpressBlade;

WordpressBlade::create(
    base_path('resources/views'), // Path to all blade files
    base_path('storage/views/cache') // Path to blade cache
);
```

## Replace all references to bladerunner

Replace controllers

```diff
- use Bladerunner\Controller;
+ use StarringJane\WordpressBlade\Controller;
```

Replace helpers

```diff
+ use StarringJane\WordpressBlade\RendersBlade;

class ClassName {
+   use RendersBlade;

    function render()
    {
-       bladerunner($view, $data);
+       echo $this->view($view, $data);
    }
}
```

Replace bladerunner instances

```diff
- \Bladerunner\Container::current('blade')->compiler()->directive(...);
+ \StarringJane\WordpressBlade\WordpressBlade::getInstance()->directive(...);
```

## Remove Filters

Find and remove bladerunner filters

```diff
- add_filter('bladerunner/template/bladepath', ...);
- add_filter('bladerunner/cache/make', ...);
- add_filter('bladerunner/cache/path', ...);
- add_filter('bladerunner/controllers/heap', ...);
```
