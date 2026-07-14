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
    }

    public function getProvinces()
    {
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
    }

    public function getCities($provinceId)
    {
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
    }

    public function getDistricts($cityId)
    {
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
    }

    public function getSubDistricts($districtId)
    {
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

            if ($response->successful()) {
                return $response->json()['data'] ?? [];
            }

            return [];
        } catch (\Exception $e) {
            Log::error('RajaOngkir getCost Error: ' . $e->getMessage());
            return [];
        }
    }
}