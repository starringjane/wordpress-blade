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
        $extractor = new \ClassNames\ClassNames;
        $classes = $extractor->getClassNames($file);
        $class = count($classes) ? $classes[0] : null;
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
}
