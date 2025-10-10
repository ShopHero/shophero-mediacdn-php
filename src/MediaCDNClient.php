<?php

declare(strict_types=1);

namespace ShopHero\MediaCDN;

use ShopHero\MediaCDN\UrlBuilder\UrlBuilder;
use ShopHero\MediaCDN\UrlBuilder\SignedUrlBuilder;

/**
 * ShopHero MediaCDN Client
 * 
 * @package ShopHero\MediaCDN
 */
class MediaCDNClient
{
    private string $domain;
    private ?string $psk;
    private bool $useHttps;
    private bool $autoFormat;

    /**
     * Create a new MediaCDN client instance
     * 
     * @param string $domain The CloudFront domain or subdomain (e.g., 'my-source.mediacdn.example.com')
     * @param array $options Configuration options
     */
    public function __construct(string $domain, array $options = [])
    {
        $this->domain = rtrim($domain, '/');
        $this->psk = $options['psk'] ?? null;
        $this->useHttps = $options['useHttps'] ?? true;
        $this->autoFormat = $options['autoFormat'] ?? true;
    }

    /**
     * Create a URL builder for the given image path
     * 
     * @param string $path The image path
     * @return UrlBuilder|SignedUrlBuilder
     */
    public function createUrl(string $path): UrlBuilder
    {
        $builder = $this->requiresSigning() 
            ? new SignedUrlBuilder($this->domain, $path, $this->psk)
            : new UrlBuilder($this->domain, $path);

        $builder->setUseHttps($this->useHttps);

        if ($this->autoFormat) {
            $builder->format('auto');
        }

        return $builder;
    }


    /**
     * Set the PSK for signed URLs
     * 
     * @param string $psk
     * @return self
     */
    public function setPsk(string $psk): self
    {
        $this->psk = $psk;
        return $this;
    }

    /**
     * Enable or disable HTTPS
     * 
     * @param bool $useHttps
     * @return self
     */
    public function setUseHttps(bool $useHttps): self
    {
        $this->useHttps = $useHttps;
        return $this;
    }

    /**
     * Enable or disable auto format detection
     * 
     * @param bool $autoFormat
     * @return self
     */
    public function setAutoFormat(bool $autoFormat): self
    {
        $this->autoFormat = $autoFormat;
        return $this;
    }

    /**
     * Check if URL signing is required
     * 
     * @return bool
     */
    private function requiresSigning(): bool
    {
        return $this->psk !== null;
    }

    /**
     * Create a source manager instance for API operations
     * 
     * @param string $apiEndpoint
     * @param string $apiKey
     * @return SourceManager
     */
    public function createSourceManager(string $apiEndpoint, string $apiKey): SourceManager
    {
        return new SourceManager($apiEndpoint, $apiKey);
    }
}