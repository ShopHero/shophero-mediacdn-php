<?php

declare(strict_types=1);

namespace ShopHero\MediaCDN\UrlBuilder;

/**
 * Signed URL Builder for secure MediaCDN access
 * 
 * @package ShopHero\MediaCDN\UrlBuilder
 */
class SignedUrlBuilder extends UrlBuilder
{
    private string $psk;
    private int $expiresIn = 3600; // Default 1 hour
    private bool $useShortSignature = true; // Use new truncated base64url signatures by default

    public function __construct(string $domain, string $path, string $psk)
    {
        parent::__construct($domain, $path);
        $this->psk = $psk;
    }

    /**
     * Use legacy hex signatures (64 chars) for backward compatibility
     * 
     * @return self
     */
    public function useLegacySignature(): self
    {
        $this->useShortSignature = false;
        return $this;
    }

    /**
     * Set expiration time in seconds
     * 
     * @param int $seconds
     * @return self
     */
    public function expiresIn(int $seconds): self
    {
        $this->expiresIn = $seconds;
        return $this;
    }

    /**
     * Set expiration to a specific timestamp
     * 
     * @param int $timestamp
     * @return self
     */
    public function expiresAt(int $timestamp): self
    {
        $this->expiresIn = $timestamp - time();
        return $this;
    }

    /**
     * Build the signed URL
     * 
     * @return string
     */
    public function build(): string
    {
        // Add expiration to params
        $this->params['exp'] = time() + $this->expiresIn;

        // Sort parameters for consistent signature generation
        ksort($this->params);

        // Build URL without signature
        $protocol = $this->useHttps ? 'https' : 'http';
        $baseUrl = $protocol . '://' . $this->domain . '/' . $this->path;
        $queryString = $this->buildQueryString();
        
        // Create string to sign
        $stringToSign = '/' . $this->path . '?' . $queryString;
        
        // Generate signature
        if ($this->useShortSignature) {
            // New format: truncated base64url (22 chars)
            $fullHash = hash_hmac('sha256', $stringToSign, $this->psk, true);
            
            // Truncate to 128 bits (16 bytes) for security/length balance
            $truncatedHash = substr($fullHash, 0, 16);
            
            // Convert to base64url without padding
            $signature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($truncatedHash));
        } else {
            // Legacy format: full hex (64 chars)
            $signature = hash_hmac('sha256', $stringToSign, $this->psk);
        }
        
        // Add signature to params
        $this->params['sig'] = $signature;
        
        // Build final URL
        return $baseUrl . '?' . $this->buildQueryString();
    }

    /**
     * Validate a signed URL
     * 
     * @param string $url
     * @param string $psk
     * @return bool
     */
    public static function validate(string $url, string $psk): bool
    {
        $parsedUrl = parse_url($url);
        if (!$parsedUrl || !isset($parsedUrl['query'])) {
            return false;
        }

        parse_str($parsedUrl['query'], $params);
        
        // Check required parameters
        if (!isset($params['sig'], $params['exp'])) {
            return false;
        }

        // Check expiration
        if (time() > (int)$params['exp']) {
            return false;
        }

        // Recreate signature
        $signature = $params['sig'];
        unset($params['sig']);
        
        // Sort parameters for consistent signature validation
        ksort($params);
        
        $queryString = http_build_query($params, '', '&', PHP_QUERY_RFC3986);
        $stringToSign = $parsedUrl['path'] . '?' . $queryString;
        
        // Try both signature formats
        $fullHash = hash_hmac('sha256', $stringToSign, $psk, true);
        
        // Check new format (truncated base64url)
        if (strlen($signature) <= 24) { // Base64 encoded 16 bytes = 22 chars (no padding)
            $truncatedHash = substr($fullHash, 0, 16);
            $expectedShortSig = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($truncatedHash));
            
            if (hash_equals($expectedShortSig, $signature)) {
                return true;
            }
        }
        
        // Check legacy format (full hex)
        $expectedLegacySig = bin2hex($fullHash);
        return hash_equals($expectedLegacySig, $signature);
    }
}