<?php

declare(strict_types=1);

namespace ShopHero\MediaCDN\UrlBuilder;

/**
 * URL Builder for MediaCDN image transformations
 * 
 * @package ShopHero\MediaCDN\UrlBuilder
 */
class UrlBuilder
{
    protected string $domain;
    protected string $path;
    protected array $params = [];
    protected bool $useHttps = true;

    public function __construct(string $domain, string $path)
    {
        $this->domain = $domain;
        $this->path = ltrim($path, '/');
    }

    /**
     * Set image width
     * 
     * @param int $width
     * @return self
     */
    public function width(int $width): self
    {
        $this->params['w'] = $width;
        return $this;
    }

    /**
     * Set image height
     * 
     * @param int $height
     * @return self
     */
    public function height(int $height): self
    {
        $this->params['h'] = $height;
        return $this;
    }

    /**
     * Set both width and height
     * 
     * @param int $width
     * @param int $height
     * @return self
     */
    public function resize(int $width, int $height): self
    {
        return $this->width($width)->height($height);
    }

    /**
     * Set image quality (1-100)
     * 
     * @param int $quality
     * @return self
     * @throws \InvalidArgumentException
     */
    public function quality(int $quality): self
    {
        if ($quality < 1 || $quality > 100) {
            throw new \InvalidArgumentException('Quality must be between 1 and 100');
        }
        $this->params['q'] = $quality;
        return $this;
    }

    /**
     * Set output format
     * 
     * @param string $format Supported: auto, jpeg, jpg, png, webp, avif
     * @return self
     * @throws \InvalidArgumentException
     */
    public function format(string $format): self
    {
        $allowedFormats = ['auto', 'jpeg', 'jpg', 'png', 'webp', 'avif'];
        if (!in_array($format, $allowedFormats, true)) {
            throw new \InvalidArgumentException('Invalid format. Supported formats: ' . implode(', ', $allowedFormats));
        }
        $this->params['f'] = $format;
        return $this;
    }

    /**
     * Set fit mode
     * 
     * @param string $fit Supported: clip, crop, scale, fill, cover, contain, pad
     * @return self
     * @throws \InvalidArgumentException
     */
    public function fit(string $fit): self
    {
        $allowedFits = ['clip', 'crop', 'scale', 'fill', 'cover', 'contain', 'pad'];
        if (!in_array($fit, $allowedFits, true)) {
            throw new \InvalidArgumentException('Invalid fit mode. Supported modes: ' . implode(', ', $allowedFits));
        }
        $this->params['fit'] = $fit;
        return $this;
    }

    /**
     * Set device pixel ratio
     * 
     * @param float $dpr
     * @return self
     * @throws \InvalidArgumentException
     */
    public function dpr(float $dpr): self
    {
        if ($dpr < 0.1 || $dpr > 8) {
            throw new \InvalidArgumentException('DPR must be between 0.1 and 8');
        }
        $this->params['dpr'] = $dpr;
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
     * Add custom parameter
     * 
     * @param string $key
     * @param mixed $value
     * @return self
     */
    public function param(string $key, $value): self
    {
        $this->params[$key] = $value;
        return $this;
    }

    /**
     * Build the URL
     * 
     * @return string
     */
    public function build(): string
    {
        $protocol = $this->useHttps ? 'https' : 'http';
        $url = $protocol . '://' . $this->domain . '/' . $this->path;

        if (!empty($this->params)) {
            $url .= '?' . $this->buildQueryString();
        }

        return $url;
    }

    /**
     * Build query string from parameters
     * 
     * @return string
     */
    protected function buildQueryString(): string
    {
        return http_build_query($this->params, '', '&', PHP_QUERY_RFC3986);
    }

    /**
     * Get the URL string
     * 
     * @return string
     */
    public function __toString(): string
    {
        return $this->build();
    }
}