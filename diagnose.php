<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

echo "===== 1. CHECK SHIPPING ADDRESSES IN DB =====\n";
$addresses = DB::table('shipping_addresses')->get();
if ($addresses->isEmpty()) {
    echo "NO ADDRESSES FOUND IN DATABASE!\n";
} else {
    foreach ($addresses as $addr) {
        echo "ID: {$addr->id} | recipient: {$addr->recipient_name} | city_id: {$addr->city_id} | subdistrict_id: " . (isset($addr->subdistrict_id) ? $addr->subdistrict_id : 'COLUMN MISSING') . " | subdistrict: " . (isset($addr->subdistrict) ? $addr->subdistrict : 'COLUMN MISSING') . "\n";
    }
}

echo "\n===== 2. CHECK CONFIG VALUES =====\n";
echo "services.rajaongkir.key = " . config('services.rajaongkir.key') . "\n";
echo "services.rajaongkir.origin_district_id = " . config('services.rajaongkir.origin_district_id') . "\n";

echo "\n===== 3. SIMULATE getCost() from RajaOngkirService =====\n";
$apiKey = config('services.rajaongkir.key') ?? env('RAJAONGKIR_API_KEY');
$baseUrl = 'https://rajaongkir.komerce.id/api/v1/';

// Use actual subdistrict_id from first address
$firstAddr = $addresses->first();
$destination = $firstAddr ? $firstAddr->subdistrict_id : 3562;
$origin = config('services.rajaongkir.origin_district_id', 259);

echo "origin = $origin | destination = $destination\n";

$response = Http::withoutVerifying()
    ->withHeaders([
        'APIKEY' => $apiKey,
        'key'    => $apiKey,
    ])
    ->asForm()
    ->post("{$baseUrl}calculate/district/domestic-cost", [
        'origin'      => $origin,
        'destination' => $destination,
        'weight'      => 1000,
        'courier'     => 'jne',
    ]);

echo "Status: " . $response->status() . "\n";
$json = $response->json();
echo "Top-level keys: " . implode(', ', array_keys($json ?? [])) . "\n";

if (isset($json['data'])) {
    echo "data type: " . gettype($json['data']) . "\n";
    if (is_array($json['data'])) {
        echo "data count: " . count($json['data']) . "\n";
        if (count($json['data']) > 0) {
            echo "First item keys: " . implode(', ', array_keys($json['data'][0])) . "\n";
            echo "First item: " . json_encode($json['data'][0], JSON_PRETTY_PRINT) . "\n";
        } else {
            echo "data array is EMPTY\n";
            echo "Full response: " . json_encode($json, JSON_PRETTY_PRINT) . "\n";
        }
    }
} else {
    echo "No 'data' key in response!\n";
    echo "Full response: " . json_encode($json, JSON_PRETTY_PRINT) . "\n";
}

echo "\n===== 4. CHECK SERVICE getCost() return value =====\n";
$service = app(App\Services\RajaOngkirService::class);
$result = $service->getCost($origin, $destination ?? 3562, 1000, 'jne');
echo "getCost() returned (" . gettype($result) . ", count=" . count($result) . "):\n";
echo json_encode($result, JSON_PRETTY_PRINT) . "\n";

echo "\n===== 5. SIMULATE CONTROLLER RESPONSE =====\n";
$controllerResponse = ['success' => true, 'data' => $result];
echo "Controller would return:\n";
echo json_encode($controllerResponse, JSON_PRETTY_PRINT) . "\n";
