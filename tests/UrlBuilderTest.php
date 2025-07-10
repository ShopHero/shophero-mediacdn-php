<?php

declare(strict_types=1);

namespace ShopHero\MediaCDN\Tests;

use PHPUnit\Framework\TestCase;
use ShopHero\MediaCDN\UrlBuilder\UrlBuilder;

class UrlBuilderTest extends TestCase
{
    private UrlBuilder $builder;

    protected function setUp(): void
    {
        $this->builder = new UrlBuilder('cdn.example.com', '/test/image.jpg');
    }

    public function testBasicUrl(): void
    {
        $url = $this->builder->build();
        $this->assertEquals('https://cdn.example.com/test/image.jpg', $url);
    }

    public function testResizeParameters(): void
    {
        $url = $this->builder
            ->width(800)
            ->height(600)
            ->build();
        
        $this->assertStringContainsString('w=800', $url);
        $this->assertStringContainsString('h=600', $url);
    }

    public function testQualityValidation(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->builder->quality(101);
    }

    public function testFormatValidation(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->builder->format('invalid');
    }

    public function testFitModes(): void
    {
        $validFits = ['clip', 'crop', 'scale', 'fill', 'cover', 'contain', 'pad'];
        
        foreach ($validFits as $fit) {
            $url = $this->builder->fit($fit)->build();
            $this->assertStringContainsString('fit=' . $fit, $url);
        }
    }

    public function testHttpsToggle(): void
    {
        $httpsUrl = $this->builder->setUseHttps(true)->build();
        $this->assertStringStartsWith('https://', $httpsUrl);

        $httpUrl = $this->builder->setUseHttps(false)->build();
        $this->assertStringStartsWith('http://', $httpUrl);
    }

    public function testChaining(): void
    {
        $url = $this->builder
            ->resize(1200, 630)
            ->quality(90)
            ->format('webp')
            ->fit('cover')
            ->dpr(2.0)
            ->build();

        $this->assertStringContainsString('w=1200', $url);
        $this->assertStringContainsString('h=630', $url);
        $this->assertStringContainsString('q=90', $url);
        $this->assertStringContainsString('f=webp', $url);
        $this->assertStringContainsString('fit=cover', $url);
        $this->assertStringContainsString('dpr=2', $url);
    }

    public function testCustomParameters(): void
    {
        $url = $this->builder
            ->param('custom', 'value')
            ->param('blur', 10)
            ->build();

        $this->assertStringContainsString('custom=value', $url);
        $this->assertStringContainsString('blur=10', $url);
    }

    public function testToString(): void
    {
        $url = $this->builder->width(400);
        $this->assertEquals($url->build(), (string) $url);
    }
}