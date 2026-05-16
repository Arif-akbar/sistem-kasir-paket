<?php

namespace App\Services;

use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PythonApiService
{
    /**
     * @param array<string, mixed> $filters
     * @return array<string, mixed>
     */
    public function forecastVolume(array $filters = []): array
    {
        return $this->request('get', '/forecast/volume', $filters, [
            'status' => 'unavailable',
            'model' => 'offline',
            'predictions' => [],
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function inspectPackage(string $frameDataUri): array
    {
        return $this->request('post', '/vision/classify-package', [
            'frame' => $frameDataUri,
        ], [
            'status' => 'unavailable',
            'label' => 'manual_review',
            'confidence' => 0,
        ]);
    }

    /**
     * @param array<string, mixed> $payload
     * @param array<string, mixed> $fallback
     * @return array<string, mixed>
     */
    private function request(string $method, string $path, array $payload, array $fallback): array
    {
        $baseUrl = rtrim((string) config('services.python_api.base_url'), '/');
        $timeout = (int) config('services.python_api.timeout', 5);

        try {
            $client = Http::timeout($timeout)->acceptJson();
            $response = $method === 'get'
                ? $client->get($baseUrl.$path, $payload)
                : $client->post($baseUrl.$path, $payload);

            return $response->throw()->json() ?? $fallback;
        } catch (RequestException $exception) {
            Log::warning('Python API request failed.', [
                'path' => $path,
                'status' => $exception->response?->status(),
                'message' => $exception->getMessage(),
            ]);
        } catch (\Throwable $exception) {
            Log::warning('Python API unavailable.', [
                'path' => $path,
                'message' => $exception->getMessage(),
            ]);
        }

        return $fallback;
    }
}
