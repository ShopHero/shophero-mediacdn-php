<?php

require_once __DIR__ . '/../vendor/autoload.php';

use ShopHero\MediaCDN\MediaCDNClient;
use ShopHero\MediaCDN\UrlBuilder\SignedUrlBuilder;

// Initialize client with PSK credentials
// Use your source's subdomain (e.g., 'my-source.mediacdn.example.com')
$client = new MediaCDNClient('my-source.mediacdn.example.com', [
    'psk' => 'your-pre-shared-key-here'
]);

// Create a signed URL with 1-hour expiration
$signedUrl = $client->createUrl('/secure/private-image.jpg')
    ->resize(1200, 630)
    ->quality(90)
    ->format('webp')
    ->expiresIn(3600) // 1 hour
    ->build();

echo "Signed URL (1 hour expiry):\n";
echo $signedUrl . "\n\n";

// Create a signed URL that expires at a specific time
$tomorrow = strtotime('+1 day');
$dailyUrl = $client->createUrl('/secure/daily-deal.jpg')
    ->resize(600, 400)
    ->fit('cover')
    ->expiresAt($tomorrow)
    ->build();

echo "Daily URL (expires tomorrow):\n";
echo $dailyUrl . "\n";
echo "Expires at: " . date('Y-m-d H:i:s', $tomorrow) . "\n\n";

// Validate a signed URL
$isValid = SignedUrlBuilder::validate($signedUrl, 'your-pre-shared-key-here');
echo "URL validation: " . ($isValid ? 'VALID' : 'INVALID') . "\n\n";

// Generate multiple signed URLs for a gallery
$galleryImages = [
    '/secure/gallery/photo1.jpg',
    '/secure/gallery/photo2.jpg',
    '/secure/gallery/photo3.jpg'
];

echo "Gallery URLs:\n";
foreach ($galleryImages as $imagePath) {
    $galleryUrl = $client->createUrl($imagePath)
        ->resize(400, 300)
        ->fit('cover')
        ->quality(80)
        ->expiresIn(7200) // 2 hours
        ->build();
    
    echo "- " . $galleryUrl . "\n";
}

// Example of URL that will be rejected (unsigned)
$unsignedUrl = "https://my-source.mediacdn.example.com/secure/private-image.jpg?w=800&h=600";
echo "\nUnsigned URL (will be rejected): " . $unsignedUrl . "\n";