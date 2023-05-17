<?php

namespace StarringJane\WordpressBlade;

class Utils
{
    public static function getComponentsFromPath(string $dir)
    {
        return self::scanDirectoryRecursive($dir)
            ->map(function ($file) use ($dir) {
                return self::extractComponentsFromFile($file, $dir);
            })
            ->reduce(function ($carry, $item) {
                foreach ($item as $name => $component) {
                    $carry[$name] = $component;
                }

                return $carry;
            }, collect())
            ->filter(function ($class) {
                return $class;
            })
            ->toArray()
        ;
    }

    public static function scanDirectoryRecursive(string $dir)
    {
        $result = collect([]);

        foreach ((new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($dir))) as $file) {
            if ($file->isDir()) {
                continue;
            }

            $result->push($file->getPathname());
        }

        return $result;
    }

    public static function extractComponentsFromFile($file, $base)
    {
        $class = self::getClass($file);
        preg_match('/\/(.*).php/', str_replace($base, '', $file), $matches);

        if (!$class || count($matches) < 2) {
            return [false];
        }

        $name = $matches[1];
        $name = str_replace('/', '.', $name);

        return [
            $name => $class,
            kebab_case($name) => $class,
        ];
    }

    public static function createDirectory($path) {
        if (!$path) {
            return false;
        }

        if (is_array($path)) {
            foreach ($path as $item) {
                self::createDirectory($item);
                return true;
            }
        }

        if (file_exists($path)) {
            return false;
        }

        return mkdir($path, 0777, true);
    }

    public static function getClass($path) {
        $classes = get_declared_classes();

        require_once $path;

        $diff = array_diff(get_declared_classes(), $classes);

        return reset($diff);
    }

    public static function randomString($length = 16)
    {
        $string = '';

        while (($len = strlen($string)) < $length) {
            $size = $length - $len;

            $bytes = random_bytes($size);

            $string .= substr(str_replace(['/', '+', '='], '', base64_encode($bytes)), 0, $size);
        }

        return $string;
    }

    public static function getUrl()
    {
        return (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
    }

    public static function castToType($type, $value)
    {
        if ($type === 'boolean') {
            return (bool) $value;
        }

        if ($type === 'integer') {
            return (int) $value;
        }

        if ($type === 'double') {
            return (double) $value;
        }

        if ($type === 'string') {
            return (string) $value;
        }

        if ($type === 'array') {
            return (array) $value;
        }

        return $value;
    }
}
