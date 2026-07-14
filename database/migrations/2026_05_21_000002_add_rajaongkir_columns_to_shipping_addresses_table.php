<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('shipping_addresses', function (Blueprint $table) {
            $table->unsignedInteger('province_id')->nullable()->after('address');
            $table->unsignedInteger('subdistrict_id')->nullable()->after('city_id');
            $table->string('subdistrict')->nullable()->after('city');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shipping_addresses', function (Blueprint $table) {
            $table->dropColumn(['province_id', 'subdistrict_id', 'subdistrict']);
        });
    }
};
