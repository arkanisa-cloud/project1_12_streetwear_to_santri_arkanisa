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
<<<<<<< HEAD
     */
    protected function client()
    {
        // Pastikan membawa API Key dari config/services.php dan bypass SSL jika MAMP bermasalah
        return Http::withoutVerifying()
            ->withHeaders([
                'APIKEY' => config('services.rajaongkir.key') ?? env('RAJAONGKIR_API_KEY'),
                'key' => config('services.rajaongkir.key') ?? env('RAJAONGKIR_API_KEY')
            ])
            ->timeout(15); // Tingkatkan timeout ke 15 detik agar lebih aman
=======
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
>>>>>>> 1cd85e2 (feat: Final Payment Gateway with Midtrans)
    }

    public function getProvinces()
    {
<<<<<<< HEAD
        return Cache::remember('rajaongkir_provinces', now()->addDays(30), function () {
            try {
                $response = $this->client()->get("{$this->baseUrl}destination/province");

                if ($response->successful()) {
                    $data = $response->json()['data'] ?? [];
                    $result = array_map(fn($prov) => [
                        'id' => $prov['id'] ?? null,
                        'name' => $prov['name'] ?? null,
                    ], $data);

                    // Urutkan dropdown berdasarkan huruf depan secara alfabetis
                    usort($result, fn($a, $b) => strcasecmp($a['name'] ?? '', $b['name'] ?? ''));

                    return $result;
                }
                
                Log::error('RajaOngkir getProvinces Failed Status: ' . $response->status());
                return [];
            } catch (\Exception $e) {
                Log::error('RajaOngkir getProvinces Error: ' . $e->getMessage());
                return [];
            }
        });
=======
        return $this->fetchWithCache(
            'rajaongkir_provinces',
            'destination/province',
            fn($prov) => [
                'id' => $prov['id'] ?? null,
                'name' => $prov['name'] ?? null,
            ]
        );
>>>>>>> 1cd85e2 (feat: Final Payment Gateway with Midtrans)
    }

    public function getCities($provinceId)
    {
<<<<<<< HEAD
        return Cache::remember("rajaongkir_cities_{$provinceId}", now()->addDays(30), function () use ($provinceId) {
            try {
                $response = $this->client()->get("{$this->baseUrl}destination/city/{$provinceId}");

                if ($response->successful()) {
                    $data = $response->json()['data'] ?? [];
                    $result = array_map(fn($city) => [
                        'id' => $city['id'] ?? null,
                        'name' => $city['name'] ?? null,
                    ], $data);

                    // Urutkan dropdown berdasarkan huruf depan secara alfabetis
                    usort($result, fn($a, $b) => strcasecmp($a['name'] ?? '', $b['name'] ?? ''));

                    return $result;
                }

                return [];
            } catch (\Exception $e) {
                Log::error('RajaOngkir getCities Error: ' . $e->getMessage());
                return [];
            }
        });
=======
        return $this->fetchWithCache(
            "rajaongkir_cities_{$provinceId}",
            "destination/city/{$provinceId}",
            fn($city) => [
                'id' => $city['id'] ?? null,
                'name' => $city['name'] ?? null,
            ]
        );
>>>>>>> 1cd85e2 (feat: Final Payment Gateway with Midtrans)
    }

    public function getDistricts($cityId)
    {
<<<<<<< HEAD
        return Cache::remember("rajaongkir_districts_{$cityId}", now()->addDays(30), function () use ($cityId) {
            try {
                $response = $this->client()->get("{$this->baseUrl}destination/district/{$cityId}");

                if ($response->successful()) {
                    $data = $response->json()['data'] ?? [];
                    $result = array_map(fn($district) => [
                        'id' => $district['id'] ?? null,
                        'name' => $district['name'] ?? null,
                    ], $data);

                    // Urutkan dropdown berdasarkan huruf depan secara alfabetis
                    usort($result, fn($a, $b) => strcasecmp($a['name'] ?? '', $b['name'] ?? ''));

                    return $result;
                }

                return [];
            } catch (\Exception $e) {
                Log::error('RajaOngkir getDistricts Error: ' . $e->getMessage());
                return [];
            }
        });
=======
        return $this->fetchWithCache(
            "rajaongkir_districts_{$cityId}",
            "destination/district/{$cityId}",
            fn($district) => [
                'id' => $district['id'] ?? null,
                'name' => $district['name'] ?? null,
            ]
        );
>>>>>>> 1cd85e2 (feat: Final Payment Gateway with Midtrans)
    }

    public function getSubDistricts($districtId)
    {
<<<<<<< HEAD
        return Cache::remember("rajaongkir_subdistricts_{$districtId}", now()->addDays(30), function () use ($districtId) {
            try {
                $response = $this->client()->get("{$this->baseUrl}destination/sub-district/{$districtId}");

                if ($response->successful()) {
                    $data = $response->json()['data'] ?? [];
                    $result = array_map(fn($subdistrict) => [
                        'id' => $subdistrict['id'] ?? null,
                        'name' => $subdistrict['name'] ?? null,
                        'postal_code' => $subdistrict['postal_code'] ?? null,
                    ], $data);

                    // Urutkan dropdown berdasarkan huruf depan secara alfabetis
                    usort($result, fn($a, $b) => strcasecmp($a['name'] ?? '', $b['name'] ?? ''));

                    return $result;
                }

                return [];
            } catch (\Exception $e) {
                Log::error('RajaOngkir getSubDistricts Error: ' . $e->getMessage());
                return [];
            }
        });
=======
        return $this->fetchWithCache(
            "rajaongkir_subdistricts_{$districtId}",
            "destination/sub-district/{$districtId}",
            fn($subdistrict) => [
                'id' => $subdistrict['id'] ?? null,
                'name' => $subdistrict['name'] ?? null,
                'postal_code' => $subdistrict['postal_code'] ?? null,
            ]
        );
>>>>>>> 1cd85e2 (feat: Final Payment Gateway with Midtrans)
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

<<<<<<< HEAD
=======
            Log::debug("RajaOngkir [getCost] status: {$response->status()}");

>>>>>>> 1cd85e2 (feat: Final Payment Gateway with Midtrans)
            if ($response->successful()) {
                return $response->json()['data'] ?? [];
            }

<<<<<<< HEAD
=======
            Log::error("RajaOngkir [getCost] Failed Status: {$response->status()} | Body: {$response->body()}");
>>>>>>> 1cd85e2 (feat: Final Payment Gateway with Midtrans)
            return [];
        } catch (\Exception $e) {
            Log::error('RajaOngkir getCost Error: ' . $e->getMessage());
            return [];
        }
    }
}