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
        $validFits = ['inside', 'contain', 'fill', 'crop', 'cover'];

        foreach ($validFits as $fit) {
            $url = $this->builder->fit($fit)->build();
            $this->assertStringContainsString('fit=' . $fit, $url);
        }
    }

    public function testInvalidFitModeThrows(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->builder->fit('invalid');
    }

    /**
     * Fit modes retired in v1.3.0 must throw rather than pass through. The
     * image handler silently falls back to `inside` for anything it does not
     * recognise, so an accepted-but-unsupported mode would ship a wrong-looking
     * image instead of an error.
     *
     * @see lambda/image-handler/index.py - `if fit_mode not in (...)`
     */
    public function testRetiredFitModesAreRejected(): void
    {
        foreach (['clip', 'scale', 'pad'] as $retired) {
            try {
                $this->builder->fit($retired);
                $this->fail("fit('" . $retired . "') should throw - the image handler no longer supports it");
            } catch (\InvalidArgumentException $e) {
                $this->assertStringContainsString('Invalid fit mode', $e->getMessage());
            }
        }
    }

    public function testRotate(): void
    {
        foreach ([90, 180, 270] as $degrees) {
            $url = $this->builder->rotate($degrees)->build();
            $this->assertStringContainsString('rot=' . $degrees, $url);
        }
    }

    public function testInvalidRotationThrows(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->builder->rotate(45);
    }

    public function testFlip(): void
    {
        $this->assertStringContainsString('flip=h', $this->builder->flip('h')->build());
        $this->assertStringContainsString('flip=v', $this->builder->flip('v')->build());
    }

    public function testInvalidFlipDirectionThrows(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->builder->flip('diagonal');
    }

    public function testBlur(): void
    {
        $url = $this->builder->blur(2.5)->build();
        $this->assertStringContainsString('blur=2.5', $url);
    }

    public function testInvalidBlurRadiusThrows(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->builder->blur(101);
    }

    /**
     * Removed in v1.3.0: the image handler never read `dpr`, so a URL carrying
     * it was silently ignored. Asserted so a future change cannot quietly
     * reintroduce it.
     */
    public function testDprIsGone(): void
    {
        $this->assertFalse(method_exists(UrlBuilder::class, 'dpr'));
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
            ->build();

        $this->assertStringContainsString('w=1200', $url);
        $this->assertStringContainsString('h=630', $url);
        $this->assertStringContainsString('q=90', $url);
        $this->assertStringContainsString('f=webp', $url);
        $this->assertStringContainsString('fit=cover', $url);
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