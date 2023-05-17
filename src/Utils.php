<?php

namespace StarringJane\WordpressBlade;

class Utils
{
    public static function getComponentsFromPath(string $dir)
    {
        return self::scanDirectoryRecursive($dir)
            ->mapWithKeys(function ($file) use ($dir) {
                return self::extractComponentFromFile($file, $dir);
            })
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

    public static function extractComponentFromFile($file, $base)
    {
        $class = self::getClass($file);
        preg_match('/\/(.*).php/', str_replace($base, '', $file), $matches);

        if (!$class || count($matches) < 2) {
            return [false];
        }

        $name = $matches[1];
        $name = str_replace('/', '.', $name);

        return [$name => $class];
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
}
