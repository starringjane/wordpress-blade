<?php

namespace StarringJane\WordpressBlade;

class Hooks
{
    public function __construct()
    {
        $this->actions();
        $this->filters();
    }

    public static function register()
    {
        return new self;
    }

    public function actions()
    {
        add_action('template_include', [$this, 'action_template_include']);
    }

    public function filters()
    {
        $this->addTemplateDirectories();
    }

    /**
     * Render component if present in the template
     */
    public function action_template_include($template)
    {
        $class = Utils::getClass($template);
        $component = $class ? new $class : null;

        if (!$component || !method_exists($component, 'render')) {
            return $template;
        }

        try {
            $view = $component->render();

            if (method_exists($view, 'toHtml')) {
                echo $view->toHtml();
            } else {
                echo $view;
            }
        } catch (\InvalidArgumentException $e) {
            throw new \InvalidArgumentException(
                "{$e->getMessage()} File: {$component->resolveView()->getPath()}"
            );
        }
    }

    /**
     * Also look for template files in template directories
     */
    public function addTemplateDirectories()
    {
        array_map(function ($type) {
            add_filter("{$type}_template_hierarchy", function ($templates) {
                $directories = apply_filters('wordpress-blade/template-directories', ['templates']);

                foreach ($templates as $key => $filename) {
                    $templates[$key] = [$filename];

                    foreach ($directories as $directory) {
                        array_unshift($templates[$key], $directory . DIRECTORY_SEPARATOR . $filename);
                    }
                }

                return array_flatten($templates);
            });
        }, [
            'index',
            '404',
            'archive',
            'author',
            'category',
            'tag',
            'taxonomy',
            'date',
            'home',
            'frontpage',
            'page',
            'paged',
            'search',
            'single',
            'singular',
            'attachment'
        ]);
    }
}
