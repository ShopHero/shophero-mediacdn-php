<?php

declare(strict_types=1);

namespace ShopHero\MediaCDN;

use ShopHero\MediaCDN\Exception\ApiException;

/**
 * Source Manager for MediaCDN API operations
 * 
 * @package ShopHero\MediaCDN
 */
class SourceManager
{
    private string $apiEndpoint;
    private string $apiKey;
    private array $headers;

    public function __construct(string $apiEndpoint, string $apiKey)
    {
        $this->apiEndpoint = rtrim($apiEndpoint, '/');
        $this->apiKey = $apiKey;
        $this->headers = [
            'Authorization: Bearer ' . $this->apiKey,
            'Content-Type: application/json',
            'Accept: application/json'
        ];
    }

    /**
     * List all sources
     * 
     * @param int $limit
     * @param string|null $lastKey
     * @return array
     * @throws ApiException
     */
    public function listSources(int $limit = 20, ?string $lastKey = null): array
    {
        $query = ['limit' => $limit];
        if ($lastKey !== null) {
            $query['lastKey'] = $lastKey;
        }

        return $this->request('GET', '/sources', $query);
    }

    /**
     * Get a specific source
     * 
     * @param string $sourceId
     * @return array
     * @throws ApiException
     */
    public function getSource(string $sourceId): array
    {
        return $this->request('GET', '/sources/' . $sourceId);
    }

    /**
     * Create a new source
     * 
     * @param array $data
     * @return array
     * @throws ApiException
     */
    public function createSource(array $data): array
    {
        return $this->request('POST', '/sources', null, $data);
    }

    /**
     * Update a source
     * 
     * @param string $sourceId
     * @param array $data
     * @return array
     * @throws ApiException
     */
    public function updateSource(string $sourceId, array $data): array
    {
        return $this->request('PUT', '/sources/' . $sourceId, null, $data);
    }

    /**
     * Delete a source
     * 
     * @param string $sourceId
     * @return void
     * @throws ApiException
     */
    public function deleteSource(string $sourceId): void
    {
        $this->request('DELETE', '/sources/' . $sourceId);
    }

    /**
     * Regenerate PSK for a source
     * 
     * @param string $sourceId
     * @return array
     * @throws ApiException
     */
    public function regeneratePsk(string $sourceId): array
    {
        return $this->request('POST', '/sources/' . $sourceId . '/regenerate-psk');
    }

    /**
     * Make API request
     * 
     * @param string $method
     * @param string $path
     * @param array|null $query
     * @param array|null $data
     * @return array
     * @throws ApiException
     */
    private function request(string $method, string $path, ?array $query = null, ?array $data = null): array
    {
        $url = $this->apiEndpoint . $path;
        if ($query !== null && !empty($query)) {
            $url .= '?' . http_build_query($query);
        }

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 3);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);

        if ($data !== null) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error) {
            throw new ApiException('cURL error: ' . $error);
        }

        if ($httpCode === 204) {
            return [];
        }

        $decoded = json_decode($response, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new ApiException('Invalid JSON response: ' . json_last_error_msg());
        }

        if ($httpCode >= 400) {
            $message = $decoded['error'] ?? 'API request failed';
            throw new ApiException($message, $httpCode);
        }

        return $decoded;
    }
}