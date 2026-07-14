<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\RajaOngkirService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * RajaOngkirController
 * 
 * API Controller untuk endpoint AJAX RajaOngkir
 * Digunakan oleh Frontend (Alpine.js) untuk cascading dropdowns:
 * Province -> City -> District -> Sub-district
 * 
 * Setiap endpoint merespons dengan format:
 * {
 *   "success": true,
 *   "data": [...]
 * }
 */
class RajaOngkirController extends Controller
{
    protected RajaOngkirService $rajaOngkir;

    public function __construct(RajaOngkirService $rajaOngkir)
    {
        $this->rajaOngkir = $rajaOngkir;
    }

    /**
     * GET /api/rajaongkir/provinces
     * Ambil daftar Provinsi
     * 
     * Response:
     * {
     *   "success": true,
     *   "data": [
     *     { "id": 1, "name": "Aceh" },
     *     { "id": 2, "name": "Sumatra Utara" },
     *     ...
     *   ]
     * }
     */
    public function provinces(): JsonResponse
    {
        $provinces = $this->rajaOngkir->getProvinces();

        return response()->json([
            'success' => true,
            'data' => $provinces,
        ]);
    }

    /**
     * GET /api/rajaongkir/cities/{provinceId}
     * Ambil daftar Kota berdasarkan Province ID
     * 
     * Response:
     * {
     *   "success": true,
     *   "data": [
     *     { "id": 1, "name": "Banda Aceh" },
     *     { "id": 2, "name": "Sabang" },
     *     ...
     *   ]
     * }
     */
    public function cities(int $provinceId): JsonResponse
    {
        $cities = $this->rajaOngkir->getCities($provinceId);

        return response()->json([
            'success' => true,
            'data' => $cities,
        ]);
    }

    /**
     * GET /api/rajaongkir/districts/{cityId}
     * Ambil daftar Kecamatan (District) berdasarkan City ID
     * 
     * Response:
     * {
     *   "success": true,
     *   "data": [
     *     { "id": 1, "name": "Banda Raya" },
     *     { "id": 2, "name": "Baiturrahman" },
     *     ...
     *   ]
     * }
     */
    public function districts(int $cityId): JsonResponse
    {
        $districts = $this->rajaOngkir->getDistricts($cityId);

        return response()->json([
            'success' => true,
            'data' => $districts,
        ]);
    }

    /**
     * GET /api/rajaongkir/subdistricts/{districtId}
     * Ambil daftar Desa/Kelurahan (Sub-district) berdasarkan District ID
     * Endpoint ini juga mengembalikan postal_code untuk auto-fill
     * 
     * Response:
     * {
     *   "success": true,
     *   "data": [
     *     { "id": 1, "name": "Banda", "postal_code": "23111" },
     *     { "id": 2, "name": "Pango", "postal_code": "23112" },
     *     ...
     *   ]
     * }
     */
    public function subdistricts(int $districtId): JsonResponse
    {
        $subdistricts = $this->rajaOngkir->getSubDistricts($districtId);

        return response()->json([
            'success' => true,
            'data' => $subdistricts,
        ]);
    }

    /**
     * POST /api/rajaongkir/cost
     * Hitung Ongkos Kirim
     * 
     * Request Body:
     * {
     *   "origin": int (district_id),
     *   "destination": int (district_id),
     *   "weight": int (gram),
     *   "courier": string (jne|pos|tiki)
     * }
     * 
     * Response:
     * {
     *   "success": true,
     *   "data": [
     *     {
     *       "code": "jne",
     *       "name": "JNE",
     *       "costs": [
     *         {
     *           "service": "OKE",
     *           "description": "Ongkir Kilat Khusus",
     *           "cost": [{ "value": 5000, "etd": "1-2" }]
     *         }
     *       ]
     *     }
     *   ]
     * }
     */
    public function cost(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'origin' => 'required|integer',
            'destination' => 'required|integer',
            'weight' => 'required|integer|min:1',
            'courier' => 'required|string|in:jne,jnt,pos,tiki,lion',
        ]);

        $costs = $this->rajaOngkir->getCost(
            $validated['origin'],
            $validated['destination'],
            $validated['weight'],
            $validated['courier']
        );

        return response()->json([
            'success' => true,
            'data' => $costs,
        ]);
    }
}
