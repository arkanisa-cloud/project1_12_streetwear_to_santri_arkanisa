<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

/**
 * RajaOngkirService
 * Service untuk integrasi dengan Komerce API Mirror (RajaOngkir)
 * Base URL: https://rajaongkir.komerce.id/api/v1
 */
class RajaOngkirService
{
    protected $baseUrl = 'https://rajaongkir.komerce.id/api/v1/';

    /**
     * Helper untuk membuat request HTTP dengan Header API Key yang valid
     * Includes retry logic untuk handle transient connection failures (Docker DNS, timeout)
     */
    protected function client()
    {
        return Http::withoutVerifying()
            ->withHeaders([
                'key' => config('services.rajaongkir.key') ?? env('RAJAONGKIR_API_KEY'),
            ])
            ->timeout(30)
            ->connectTimeout(15)
            ->retry(3, 500, function ($exception) {
                // Retry on connection errors (timeout, DNS, etc.)
                return $exception instanceof \Illuminate\Http\Client\ConnectionException;
            });
    }

    /**
     * Helper: Fetch data from API with proper caching logic.
     * CRITICAL FIX: Only cache successful non-empty results.
     * If the API call fails or returns empty, do NOT cache the empty result.
     */
    protected function fetchWithCache(string $cacheKey, string $endpoint, callable $mapper, int $cacheDays = 30): array
    {
        // Check cache first
        $cached = Cache::get($cacheKey);
        if ($cached !== null && !empty($cached)) {
            return $cached;
        }

        try {
            $response = $this->client()->get("{$this->baseUrl}{$endpoint}");

            Log::debug("RajaOngkir [{$endpoint}] status: {$response->status()}");

            if ($response->successful()) {
                $json = $response->json();
                $data = $json['data'] ?? [];

                if (empty($data)) {
                    Log::warning("RajaOngkir [{$endpoint}] returned empty data. Response: " . json_encode($json));
                    return [];
                }

                $result = array_map($mapper, $data);

                // Urutkan dropdown berdasarkan huruf depan secara alfabetis
                usort($result, fn($a, $b) => strcasecmp($a['name'] ?? '', $b['name'] ?? ''));

                // Only cache non-empty successful results
                Cache::put($cacheKey, $result, now()->addDays($cacheDays));

                return $result;
            }

            Log::error("RajaOngkir [{$endpoint}] Failed Status: {$response->status()} | Body: {$response->body()}");
            return [];
        } catch (\Exception $e) {
            Log::error("RajaOngkir [{$endpoint}] Error: {$e->getMessage()}");
            return [];
        }
    }

    /**
     * Clear cached empty results to force re-fetch from API.
     * Useful after fixing connectivity issues.
     */
    public function clearCache(?string $key = null): void
    {
        if ($key) {
            Cache::forget($key);
            return;
        }

        // Clear all rajaongkir cache keys
        Cache::forget('rajaongkir_provinces');
        Log::info('RajaOngkir cache cleared.');
    }

    public function getProvinces()
    {
        return $this->fetchWithCache(
            'rajaongkir_provinces',
            'destination/province',
            fn($prov) => [
                'id' => $prov['id'] ?? null,
                'name' => $prov['name'] ?? null,
            ]
        );
    }

    public function getCities($provinceId)
    {
        return $this->fetchWithCache(
            "rajaongkir_cities_{$provinceId}",
            "destination/city/{$provinceId}",
            fn($city) => [
                'id' => $city['id'] ?? null,
                'name' => $city['name'] ?? null,
            ]
        );
    }

    public function getDistricts($cityId)
    {
        return $this->fetchWithCache(
            "rajaongkir_districts_{$cityId}",
            "destination/district/{$cityId}",
            fn($district) => [
                'id' => $district['id'] ?? null,
                'name' => $district['name'] ?? null,
            ]
        );
    }

    public function getSubDistricts($districtId)
    {
        return $this->fetchWithCache(
            "rajaongkir_subdistricts_{$districtId}",
            "destination/sub-district/{$districtId}",
            fn($subdistrict) => [
                'id' => $subdistrict['id'] ?? null,
                'name' => $subdistrict['name'] ?? null,
                'postal_code' => $subdistrict['postal_code'] ?? null,
            ]
        );
    }

    public function getCost($origin, $destination, $weight, $courier)
    {
        try {
            $response = $this->client()->asForm()->post("{$this->baseUrl}calculate/district/domestic-cost", [
                'origin' => $origin,
                'destination' => $destination,
                'weight' => $weight,
                'courier' => $courier
            ]);

            Log::debug("RajaOngkir [getCost] status: {$response->status()}");

            if ($response->successful()) {
                return $response->json()['data'] ?? [];
            }

            Log::error("RajaOngkir [getCost] Failed Status: {$response->status()} | Body: {$response->body()}");
            return [];
        } catch (\Exception $e) {
            Log::error('RajaOngkir getCost Error: ' . $e->getMessage());
            return [];
        }
    }
}