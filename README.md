# ShopHero MediaCDN PHP Client

A PHP client library for ShopHero MediaCDN - A high-performance image CDN with on-the-fly transformations.

## Installation

Add the repository to your `composer.json`:

```json
{
    "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/ShopHero/shophero-mediacdn-php"
        }
    ],
    "require": {
        "shophero/mediacdn-php": "^1.4"
    }
}
```

Then install:

```bash
composer install
```

## Requirements

- PHP 7.4 or higher
- ext-hash extension

## Quick Start

### Basic Usage (unsigned URLs)

```php
use ShopHero\MediaCDN\MediaCDNClient;

// Initialize client (auto-format enabled by default)
$client = new MediaCDNClient('my-source.mediacdn1.shophero.com');

// Create image URL with transformations
$url = $client->createUrl('/path/to/image.jpg')
    ->resize(800, 600)
    ->quality(85)
    ->format('webp')
    ->fit('cover')
    ->build();

// Output: https://my-source.mediacdn1.shophero.com/path/to/image.jpg?f=webp&fit=cover&h=600&q=85&w=800
```

### Signed URLs (PSK-based Security)

```php
$client = new MediaCDNClient('my-source.mediacdn1.shophero.com', [
    'psk' => 'your-pre-shared-key'
]);

// Create signed URL with 1-hour expiration
$signedUrl = $client->createUrl('/secure/image.jpg')
    ->resize(1200, 630)
    ->expiresIn(3600)
    ->build();

// URL includes sig= and exp= parameters for validation
```

## Available Transformations

### Resize Options

```php
// Set dimensions
$url->width(800);
$url->height(600);
$url->resize(800, 600); // Shorthand for both

// Fit modes
$url->fit('inside');  // Thumbnail: fit within dimensions, preserve aspect ratio
$url->fit('fill');    // Stretch to exact dimensions, ignore aspect ratio
$url->fit('crop');    // Scale to cover dimensions, then center-crop excess
$url->fit('cover');   // Alias for crop
```

### Format and Quality

```php
// Output format
$url->format('auto');  // Auto-detect best format (AVIF > WebP > JPEG/PNG)
$url->format('avif');  // Force AVIF
$url->format('webp');  // Force WebP
$url->format('jpeg');  // Force JPEG
$url->format('png');   // Force PNG

// Quality (1-100, default 85)
$url->quality(85);
```

### Rotation, Flip, and Blur

```php
// Rotate (90, 180, or 270 degrees only)
$url->rotate(90);

// Flip
$url->flip('h');  // Horizontal flip
$url->flip('v');  // Vertical flip

// Gaussian blur (radius 0.1-100)
$url->blur(5.0);
```

## Configuration Options

```php
$client = new MediaCDNClient('my-source.mediacdn1.shophero.com', [
    'psk' => 'your-psk',      // Pre-shared key for signed URLs (optional)
    'useHttps' => true,        // Use HTTPS (default: true)
    'autoFormat' => true       // Add f=auto to all URLs (default: true)
]);
```

## URL Signing

### Expiration

```php
// Expire in N seconds from now
$url->expiresIn(3600);

// Expire at a specific timestamp
$url->expiresAt(time() + 7200);
```

### Signature Formats

```php
// Default: truncated base64url (22 chars) - recommended
$url->build();

// Legacy: full hex (64 chars) - for backward compatibility
$url->useLegacySignature()->build();
```

### URL Validation

```php
use ShopHero\MediaCDN\UrlBuilder\SignedUrlBuilder;

$isValid = SignedUrlBuilder::validate($signedUrl, $psk);
```

## Source Management API

```php
$sourceManager = $client->createSourceManager(
    'https://your-api-gateway-endpoint.com/prod',
    'your-api-key'
);

// List sources
$sources = $sourceManager->listSources(20);

// Create a source
$newSource = $sourceManager->createSource([
    'name' => 'Production Images',
    'type' => 's3',
    'bucket' => 'my-image-bucket',
    'require_signature' => true
]);

// Save the PSK - it's only returned on creation!
$psk = $newSource['psk'];

// Regenerate PSK
$result = $sourceManager->regeneratePsk('source-subdomain');
```

## Examples

### Responsive Images

```php
$widths = [400, 800, 1200, 1600];
$srcset = [];

foreach ($widths as $w) {
    $url = $client->createUrl('/hero-image.jpg')
        ->width($w)
        ->expiresIn(86400)
        ->build();
    $srcset[] = "$url {$w}w";
}

echo '<img srcset="' . implode(', ', $srcset) . '" sizes="(max-width: 600px) 100vw, 50vw">';
```

### Thumbnail Generation

```php
function generateThumbnail(MediaCDNClient $client, string $path, int $size = 150): string
{
    return $client->createUrl($path)
        ->resize($size, $size)
        ->fit('crop')
        ->quality(70)
        ->expiresIn(3600)
        ->build();
}
```

### Custom Parameters

```php
$url = $client->createUrl('/image.jpg')
    ->param('custom', 'value')
    ->build();
```

## Testing

```bash
composer test        # Run tests
composer phpcs       # Code style checks
composer phpstan     # Static analysis
composer check       # All of the above
```

## License

This library is open-source software licensed under the [MIT license](LICENSE).
