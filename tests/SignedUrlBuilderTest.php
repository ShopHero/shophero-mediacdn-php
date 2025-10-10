<?php

declare(strict_types=1);

namespace ShopHero\MediaCDN\Tests;

use PHPUnit\Framework\TestCase;
use ShopHero\MediaCDN\UrlBuilder\SignedUrlBuilder;

class SignedUrlBuilderTest extends TestCase
{
    private string $domain = 'cdn.example.com';
    private string $path = '/secure/image.jpg';
    private string $sourceId = 'test-source';
    private string $psk = 'test-psk-key';

    public function testSignedUrlGeneration(): void
    {
        $builder = new SignedUrlBuilder($this->domain, $this->path, $this->sourceId, $this->psk);
        $url = $builder->build();

        $this->assertStringContainsString('source=test-source', $url);
        $this->assertStringContainsString('exp=', $url);
        $this->assertStringContainsString('sig=', $url);
    }

    public function testSignatureValidation(): void
    {
        $builder = new SignedUrlBuilder($this->domain, $this->path, $this->sourceId, $this->psk);
        $url = $builder->expiresIn(3600)->build();

        $isValid = SignedUrlBuilder::validate($url, $this->psk);
        $this->assertTrue($isValid);
    }

    public function testExpiredUrlValidation(): void
    {
        $builder = new SignedUrlBuilder($this->domain, $this->path, $this->sourceId, $this->psk);
        $url = $builder->expiresIn(-1)->build(); // Already expired

        $isValid = SignedUrlBuilder::validate($url, $this->psk);
        $this->assertFalse($isValid);
    }

    public function testInvalidSignatureValidation(): void
    {
        $builder = new SignedUrlBuilder($this->domain, $this->path, $this->sourceId, $this->psk);
        $url = $builder->build();

        $isValid = SignedUrlBuilder::validate($url, 'wrong-psk');
        $this->assertFalse($isValid);
    }

    public function testSignedUrlWithTransformations(): void
    {
        $builder = new SignedUrlBuilder($this->domain, $this->path, $this->sourceId, $this->psk);
        $url = $builder
            ->resize(800, 600)
            ->quality(85)
            ->format('webp')
            ->expiresIn(7200)
            ->build();

        $this->assertStringContainsString('w=800', $url);
        $this->assertStringContainsString('h=600', $url);
        $this->assertStringContainsString('q=85', $url);
        $this->assertStringContainsString('f=webp', $url);

        $isValid = SignedUrlBuilder::validate($url, $this->psk);
        $this->assertTrue($isValid);
    }

    public function testExpiresAtMethod(): void
    {
        $futureTime = time() + 3600;
        $builder = new SignedUrlBuilder($this->domain, $this->path, $this->sourceId, $this->psk);
        $url = $builder->expiresAt($futureTime)->build();

        $parsedUrl = parse_url($url);
        parse_str($parsedUrl['query'], $params);
        
        $this->assertEquals($futureTime, (int)$params['exp']);
    }

    public function testUrlWithoutQueryString(): void
    {
        $invalidUrl = 'https://cdn.example.com/image.jpg';
        $isValid = SignedUrlBuilder::validate($invalidUrl, $this->psk);
        $this->assertFalse($isValid);
    }

    public function testUrlMissingRequiredParams(): void
    {
        $invalidUrl = 'https://cdn.example.com/image.jpg?w=800&h=600';
        $isValid = SignedUrlBuilder::validate($invalidUrl, $this->psk);
        $this->assertFalse($isValid);
    }
}