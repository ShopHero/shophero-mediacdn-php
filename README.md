# ShopHero MediaCDN PHP Client

A PHP client library for ShopHero MediaCDN - A high-performance image CDN with on-the-fly transformations.

## Installation

### Option 1: Install via Composer from GitHub (Recommended)

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
        "shophero/mediacdn-php": "^1.0"
    }
}
```

Then install:

```bash
composer install
```

### Option 2: Install specific version or branch

```bash
composer require shophero/mediacdn-php:dev-main
# or for a specific version:
composer require shophero/mediacdn-php:v1.0.0
```

### Option 3: Install via Packagist (if published)

```bash
composer require shophero/mediacdn-php
```

## Requirements

- PHP 7.4 or higher
- ext-hash extension

## Quick Start

### Basic Usage

```php
use ShopHero\MediaCDN\MediaCDNClient;

// Initialize client
$client = new MediaCDNClient('d18ixy3vlla0t9.cloudfront.net');

// Create image URL with transformations
$url = $client->createUrl('/path/to/image.jpg')
    ->resize(800, 600)
    ->quality(85)
    ->format('webp')
    ->fit('cover')
    ->build();

echo $url;
// Output: https://d18ixy3vlla0t9.cloudfront.net/path/to/image.jpg?w=800&h=600&q=85&f=webp&fit=cover
```

### Signed URLs (PSK-based Security)

```php
$client = new MediaCDNClient('d18ixy3vlla0t9.cloudfront.net', [
    'sourceId' => 'source-123',
    'psk' => 'your-pre-shared-key'
]);

// Create signed URL with 1-hour expiration
$signedUrl = $client->createUrl('/secure/image.jpg')
    ->resize(1200, 630)
    ->expiresIn(3600)
    ->build();
```

## Available Transformations

### Resize Options

```php
// Set dimensions
$url->width(800);
$url->height(600);
$url->resize(800, 600); // Shorthand for both

// Fit modes
$url->fit('cover');   // Fill dimensions, crop excess
$url->fit('contain'); // Fit within dimensions
$url->fit('crop');    // Crop to exact dimensions
$url->fit('scale');   // Scale ignoring aspect ratio
$url->fit('fill');    // Fill dimensions with padding
```

### Format and Quality

```php
// Output format
$url->format('auto');  // Auto-detect best format
$url->format('webp');  // Force WebP
$url->format('avif');  // Force AVIF
$url->format('jpeg');  // Force JPEG

// Quality (1-100)
$url->quality(85);

// Device pixel ratio
$url->dpr(2.0); // For retina displays
```

## Source Management API

### List Sources

```php
$sourceManager = $client->createSourceManager(
    'https://f830zq0b47.execute-api.us-east-1.amazonaws.com/prod',
    'your-api-key'
);

$sources = $sourceManager->listSources(20);
foreach ($sources['sources'] as $source) {
    echo $source['name'] . ': ' . $source['type'] . PHP_EOL;
}
```

### Create Secure Source

```php
$newSource = $sourceManager->createSource([
    'name' => 'Production Images',
    'type' => 's3',
    'bucket' => 'my-image-bucket',
    'require_signature' => true
]);

// Save the PSK - it's only returned on creation!
$psk = $newSource['psk'];
```

### Regenerate PSK

```php
$result = $sourceManager->regeneratePsk('source-123');
$newPsk = $result['psk'];
```

## Advanced Usage

### Custom Parameters

```php
$url = $client->createUrl('/image.jpg')
    ->param('blur', 10)
    ->param('brightness', 1.2)
    ->build();
```

### URL Validation

```php
use ShopHero\MediaCDN\UrlBuilder\SignedUrlBuilder;

$isValid = SignedUrlBuilder::validate($signedUrl, $psk);
```

### Error Handling

```php
use ShopHero\MediaCDN\Exception\ApiException;

try {
    $source = $sourceManager->getSource('source-123');
} catch (ApiException $e) {
    echo 'API Error: ' . $e->getMessage();
    echo 'HTTP Code: ' . $e->getCode();
}
```

## Configuration Options

```php
$client = new MediaCDNClient('cdn.example.com', [
    'sourceId' => 'source-123',     // For signed URLs
    'psk' => 'your-psk',           // Pre-shared key
    'useHttps' => true,            // Use HTTPS (default: true)
    'autoFormat' => true           // Auto-detect format (default: true)
]);
```

## Examples

### Responsive Images

```php
$baseUrl = $client->createUrl('/hero-image.jpg');

// Generate srcset for responsive images
$srcset = [
    $baseUrl->width(400)->build() . ' 400w',
    $baseUrl->width(800)->build() . ' 800w',
    $baseUrl->width(1200)->build() . ' 1200w',
    $baseUrl->width(1600)->build() . ' 1600w'
];

echo '<img src="' . $baseUrl->width(800)->build() . '" 
          srcset="' . implode(', ', $srcset) . '" 
          sizes="(max-width: 600px) 100vw, 50vw">';
```

### Thumbnail Generation

```php
function generateThumbnail($path, $size = 150) {
    global $client;
    
    return $client->createUrl($path)
        ->resize($size, $size)
        ->fit('crop')
        ->quality(70)
        ->build();
}
```

### Watermarked Images

```php
$watermarkedUrl = $client->createUrl('/product.jpg')
    ->resize(1000, 1000)
    ->param('watermark', 'logo.png')
    ->param('watermark_position', 'bottom-right')
    ->param('watermark_opacity', 0.8)
    ->build();
```

## Testing

Run the test suite:

```bash
composer test
```

Run code style checks:

```bash
composer phpcs
```

Run static analysis:

```bash
composer phpstan
```

## License

This library is open-source software licensed under the [MIT license](LICENSE).

## Support

For support, please email support@shophero.com or create an issue on GitHub.