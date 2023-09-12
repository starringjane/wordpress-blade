<?php

namespace StarringJane\WordpressBlade\Tests;

use Illuminate\View\View;
use PHPUnit\Framework\TestCase as BaseTestCase;

class TestCase extends BaseTestCase
{
    public function assertViewEqualsHtml(View $view, string $pathToHtmlFile): void
    {
        $this->assertEquals(file_get_contents(__DIR__ . '/Fixtures/output/' . $pathToHtmlFile), (string) $view);
    }
}
