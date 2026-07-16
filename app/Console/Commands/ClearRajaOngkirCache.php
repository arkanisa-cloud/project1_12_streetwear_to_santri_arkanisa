<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use App\Services\RajaOngkirService;

/**
 * Artisan command untuk clear cache RajaOngkir yang kosong/stale
 * Gunakan ini setelah fix connectivity issues agar data bisa di-fetch ulang dari API
 * 
 * Usage: php artisan rajaongkir:clear-cache
 */
class ClearRajaOngkirCache extends Command
{
    protected $signature = 'rajaongkir:clear-cache';
    protected $description = 'Clear all cached RajaOngkir data (provinces, cities, districts, subdistricts) to force fresh API fetch';

    public function handle()
    {
        $this->info('Clearing RajaOngkir cache...');

        // Clear provinces cache
        Cache::forget('rajaongkir_provinces');
        $this->line('✓ Cleared provinces cache');

        // Clear all cities cache (we don't know all province IDs, so use pattern)
        // Since Laravel's Cache doesn't support wildcard forget, we clear known ones
        // For database cache driver, we can query directly
        $cleared = 0;
        
        try {
            // Try to clear via database cache (since SESSION_DRIVER=database in .env)
            $cachePrefix = config('cache.prefix', '');
            $rows = \DB::table('cache')
                ->where('key', 'like', "%rajaongkir_%")
                ->get();
            
            foreach ($rows as $row) {
                \DB::table('cache')->where('key', $row->key)->delete();
                $cleared++;
            }
            
            $this->line("✓ Cleared {$cleared} cached entries from database");
        } catch (\Exception $e) {
            $this->warn("Could not clear database cache: {$e->getMessage()}");
            $this->line('Trying file cache fallback...');
            
            // Fallback: clear the main known keys
            Cache::forget('rajaongkir_provinces');
            for ($i = 1; $i <= 34; $i++) {
                Cache::forget("rajaongkir_cities_{$i}");
            }
            $this->line('✓ Cleared provinces and common city caches');
        }

        $this->info('');
        $this->info('Cache cleared! Next API request will fetch fresh data from RajaOngkir.');
        
        // Test connectivity
        $this->info('');
        $this->info('Testing RajaOngkir API connectivity...');
        
        try {
            $service = app(RajaOngkirService::class);
            $provinces = $service->getProvinces();
            
            if (!empty($provinces)) {
                $this->info("✓ API is working! Got " . count($provinces) . " provinces.");
            } else {
                $this->error("✗ API returned empty data. Check your API key and network connectivity.");
                $this->line("  API Key: " . substr(config('services.rajaongkir.key'), 0, 8) . '...');
            }
        } catch (\Exception $e) {
            $this->error("✗ API connection failed: {$e->getMessage()}");
        }

        return Command::SUCCESS;
    }
}
