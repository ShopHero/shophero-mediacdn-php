<?php

require_once __DIR__ . '/../vendor/autoload.php';

use ShopHero\MediaCDN\MediaCDNClient;

// Initialize the client
$client = new MediaCDNClient('cdn.example.com');

// Basic image transformation
$basicUrl = $client->createUrl('/samples/landscape.jpg')
    ->resize(800, 600)
    ->quality(85)
    ->build();

echo "Basic URL:\n";
echo $basicUrl . "\n\n";

// Responsive image with auto format
$responsiveUrl = $client->createUrl('/samples/hero-image.jpg')
    ->width(1200)
    ->format('auto')
    ->quality(90)
    ->build();

echo "Responsive URL with auto format:\n";
echo $responsiveUrl . "\n\n";

// Thumbnail with crop
$thumbnailUrl = $client->createUrl('/samples/product.jpg')
    ->resize(150, 150)
    ->fit('crop')
    ->quality(70)
    ->build();

echo "Thumbnail URL:\n";
echo $thumbnailUrl . "\n\n";

// High DPI image. There is no `dpr` parameter — the image handler never read
// one, and dpr() was removed from this client in v1.3.0. Request the pixel
// width you actually want and let the img tag's CSS size it back down.
$retinaUrl = $client->createUrl('/samples/logo.png')
    ->width(400) // 200 CSS px at 2x
    ->build();

echo "Retina URL:\n";
echo $retinaUrl . "\n\n";

// Generate srcset for responsive images
$image = $client->createUrl('/samples/banner.jpg');
$sizes = [400, 800, 1200, 1600];
$srcset = [];

foreach ($sizes as $width) {
    $url = $image->width($width)->quality(85)->build();
    $srcset[] = $url . ' ' . $width . 'w';
}

echo "Responsive srcset:\n";
echo implode(",\n", $srcset) . "\n";