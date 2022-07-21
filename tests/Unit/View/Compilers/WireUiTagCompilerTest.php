<?php

use Illuminate\Support\Facades\Blade;
use WireUi\Facades\WireUiDirectives;
use WireUi\Support\BladeDirectives;
use WireUi\View\Compilers\WireUiTagCompiler;

it('should match scripts and styles tags', function () {
    $compiler = resolve(WireUiTagCompiler::class);

    $scripts = $compiler->compile('<wireui:scripts />');
    $this->assertEquals(WireUiDirectives::scripts(), $scripts);

    $scripts = $compiler->compile('<wireui:scripts/>');
    $this->assertEquals(WireUiDirectives::scripts(), $scripts);

    $styles = $compiler->compile('<wireui:styles />');
    $this->assertEquals(WireUiDirectives::styles(), $styles);

    $styles = $compiler->compile('<wireui:styles/>');
    $this->assertEquals(WireUiDirectives::styles(), $styles);
});

it('dont have matches', function () {
    $compiler = resolve(WireUiTagCompiler::class);

    $foo = $compiler->compile('<wireui:foo />');
    $this->assertEquals($foo, '<wireui:foo />');

    $bar = $compiler->compile('<wireui:bar />');
    $this->assertEquals($bar, '<wireui:bar />');
});

it('should match rendered scripts link', function () {
    $bladeDirectives = new BladeDirectives();
    $hooksScript     = $bladeDirectives->hooksScript();
    $wireuiScript    = '<script src="/wireui/assets/scripts" defer ></script>';

    if ($version = $bladeDirectives->getManifestVersion('wireui.js')) {
        $wireuiScript = str_replace('assets/scripts', "assets/scripts?id={$version}", $wireuiScript);
    }

    $scripts = $bladeDirectives->scripts($absolute = false);

    $this->assertStringContainsString($hooksScript, $scripts);
    $this->assertStringContainsString($wireuiScript, $scripts);
});

it('should match rendered styles link', function () {
    $bladeDirectives = new BladeDirectives();
    $expected        = '<link href="/wireui/assets/styles" rel="stylesheet" type="text/css">';

    if ($version = $bladeDirectives->getManifestVersion('wireui.css')) {
        $expected = str_replace('assets/styles', "assets/styles?id={$version}", $expected);
    }

    $this->assertEquals($expected, $bladeDirectives->styles($absolute = false));
});

it('should render all wireui scripts variation', function (string $text) {
    $html = Blade::render($text);

    $this->assertStringContainsString('<script src="', $html);
    $this->assertStringContainsString('/wireui/assets/scripts', $html);
})->with([
    ['@wireUiScripts'],
    ['@wireUiScripts()'],
    ['@wireUiScripts([])'],
    ["@wireUiScripts(['foo' => 'bar'])"],
    ['<wireui:scripts />'],
]);

it('should render all wireui styles variation', function (string $text) {
    $html = Blade::render($text);

    $this->assertStringContainsString('<link href="', $html);
    $this->assertStringContainsString('/wireui/assets/styles', $html);
})->with([
    ['@wireUiStyles'],
    ['<wireui:styles />'],
]);
