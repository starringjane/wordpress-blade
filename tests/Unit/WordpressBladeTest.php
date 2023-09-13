<?php

namespace StarringJane\WordpressBlade\Tests\Unit;

use StarringJane\WordpressBlade\WordpressBlade;
use StarringJane\WordpressBlade\Tests\TestCase;

final class WordpressBladeTest extends TestCase
{
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        WordpressBlade::create(
            realpath(__DIR__ . '/../Fixtures/views'),
            realpath(__DIR__ . '/../Fixtures/cache'),
            realpath(__DIR__ . '/../Fixtures/components'),
        );
    }

    static public function blade_files(): array
    {
        return [
            ['basic', 'basic.html'],
            ['anonymous-component', 'anonymous-component.html'],
            ['class-component', 'class-component.html'],
        ];
    }

    /**
     * @dataProvider blade_files
     */
    public function test_it_can_render_blade_files($name, $output): void
    {
        $view = WordpressBlade::getInstance()->make($name);

        $this->assertViewEqualsHtml($view, $output);
    }

    public function test_it_can_register_directives(): void
    {
        WordpressBlade::getInstance()
            ->directive('datetimeforeu', function ($expression) {
                return "<?php echo ($expression)->format('d/m/Y H:i'); ?>";
            })
        ;
        WordpressBlade::directive('datetimeforus', function ($expression) {
            return "<?php echo ($expression)->format('m/d/Y H:i'); ?>";
        });

        $this->assertArrayHasKey('datetimeforeu', WordpressBlade::getCustomDirectives());
        $this->assertArrayHasKey('datetimeforus', WordpressBlade::getCustomDirectives());
    }
}
