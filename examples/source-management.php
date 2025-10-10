<?php

require_once __DIR__ . '/../vendor/autoload.php';

use ShopHero\MediaCDN\MediaCDNClient;
use ShopHero\MediaCDN\Exception\ApiException;

// Initialize client
$client = new MediaCDNClient('cdn.example.com');

// Create source manager
$sourceManager = $client->createSourceManager(
    'https://api.example.com',
    'your-api-key-here'
);

try {
    // List all sources
    echo "=== Listing Sources ===\n";
    $sources = $sourceManager->listSources(10);
    
    foreach ($sources['sources'] as $source) {
        echo sprintf(
            "- %s (%s): %s, Secure: %s\n",
            $source['name'],
            $source['id'],
            $source['type'],
            $source['require_signature'] ? 'Yes' : 'No'
        );
    }
    echo "\n";

    // Create a new secure source
    echo "=== Creating Secure Source ===\n";
    $newSource = $sourceManager->createSource([
        'name' => 'Production Images',
        'type' => 's3',
        'bucket' => 'my-production-images',
        'prefix' => 'products/',
        'require_signature' => true
    ]);

    echo "Created source: " . $newSource['name'] . "\n";
    echo "Source ID: " . $newSource['id'] . "\n";
    echo "PSK: " . $newSource['psk'] . "\n";
    echo "IMPORTANT: Save this PSK! It won't be shown again.\n\n";

    // Update source to disable signature requirement
    echo "=== Updating Source ===\n";
    $updatedSource = $sourceManager->updateSource($newSource['id'], [
        'require_signature' => false
    ]);
    echo "Signature requirement disabled for source: " . $updatedSource['id'] . "\n\n";

    // Re-enable and regenerate PSK
    echo "=== Re-enabling Security ===\n";
    $sourceManager->updateSource($newSource['id'], [
        'require_signature' => true
    ]);

    $regenerated = $sourceManager->regeneratePsk($newSource['id']);
    echo "New PSK generated: " . $regenerated['psk'] . "\n\n";

    // Get specific source details
    echo "=== Getting Source Details ===\n";
    $sourceDetails = $sourceManager->getSource($newSource['id']);
    echo "Source: " . json_encode($sourceDetails, JSON_PRETTY_PRINT) . "\n\n";

    // Delete the test source
    echo "=== Deleting Test Source ===\n";
    $sourceManager->deleteSource($newSource['id']);
    echo "Source deleted successfully\n";

} catch (ApiException $e) {
    echo "API Error: " . $e->getMessage() . "\n";
    echo "HTTP Code: " . $e->getCode() . "\n";
}