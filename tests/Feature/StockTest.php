<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Product;
use App\Models\Category;
use App\Models\Supplier;
use App\Models\StockIn;
use App\Models\StockOut;
use App\Models\StockHistory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * StockTest
 * Test manajemen stok (stok masuk/keluar)
 */
class StockTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Create admin
        $this->admin = User::factory()->create([
            'role' => 'admin',
        ]);

        // Create category
        $this->category = Category::factory()->create();

        // Create supplier
        $this->supplier = Supplier::factory()->create();

        // Create product
        $this->product = Product::factory()->create([
            'category_id' => $this->category->id,
            'name' => 'Test Product',
            'price' => 100000,
            'stock' => 10,
        ]);
    }

    /**
     * Test: Admin bisa input stok masuk
     */
    public function test_admin_can_input_stock_in()
    {
        $response = $this->actingAs($this->admin)
            ->post(route('admin.stock-ins.store'), [
                'product_id' => $this->product->id,
                'supplier_id' => $this->supplier->id,
                'tanggal_masuk' => now()->format('Y-m-d'),
                'qty' => 5,
                'keterangan' => 'Stok masuk test',
            ]);

        $response->assertRedirect();

        // Cek stock_in created
        $this->assertDatabaseHas('stock_ins', [
            'product_id' => $this->product->id,
            'supplier_id' => $this->supplier->id,
            'qty' => 5,
        ]);

        // Cek stock bertambah
        $this->assertDatabaseHas('products', [
            'id' => $this->product->id,
            'stock' => 15, // 10 + 5
        ]);

        // Cek stock history
        $this->assertDatabaseHas('stock_histories', [
            'product_id' => $this->product->id,
            'type' => 'in',
            'qty' => 5,
            'stock_before' => 10,
            'stock_after' => 15,
        ]);
    }

    /**
     * Test: Admin bisa input stok keluar
     */
    public function test_admin_can_input_stock_out()
    {
        $response = $this->actingAs($this->admin)
            ->post(route('admin.stock-outs.store'), [
                'product_id' => $this->product->id,
                'tanggal_keluar' => now()->format('Y-m-d'),
                'qty' => 3,
                'alasan' => 'rusak',
                'keterangan' => 'Barang rusak',
            ]);

        $response->assertRedirect();

        // Cek stock_out created
        $this->assertDatabaseHas('stock_outs', [
            'product_id' => $this->product->id,
            'qty' => 3,
            'alasan' => 'rusak',
        ]);

        // Cek stock berkurang
        $this->assertDatabaseHas('products', [
            'id' => $this->product->id,
            'stock' => 7, // 10 - 3
        ]);

        // Cek stock history
        $this->assertDatabaseHas('stock_histories', [
            'product_id' => $this->product->id,
            'type' => 'out',
            'qty' => 3,
            'stock_before' => 10,
            'stock_after' => 7,
        ]);
    }

    /**
     * Test: Tidak bisa input stok keluar jika stok tidak cukup
     */
    public function test_cannot_input_stock_out_if_insufficient_stock()
    {
        $response = $this->actingAs($this->admin)
            ->post(route('admin.stock-outs.store'), [
                'product_id' => $this->product->id,
                'tanggal_keluar' => now()->format('Y-m-d'),
                'qty' => 15, // Lebih dari stok (10)
                'alasan' => 'rusak',
                'keterangan' => 'Test',
            ]);

        $response->assertSessionHas('error');

        // Stock out tidak dibuat
        $this->assertDatabaseMissing('stock_outs', [
            'product_id' => $this->product->id,
        ]);

        // Stock tidak berkurang
        $this->assertDatabaseHas('products', [
            'id' => $this->product->id,
            'stock' => 10, // Tetap 10
        ]);
    }

    /**
     * Test: Stok tidak boleh minus
     */
    public function test_stock_cannot_be_negative()
    {
        // Coba input stok keluar lebih besar dari stok
        $response = $this->actingAs($this->admin)
            ->post(route('admin.stock-outs.store'), [
                'product_id' => $this->product->id,
                'tanggal_keluar' => now()->format('Y-m-d'),
                'qty' => 20, // Lebih dari stok (10)
                'alasan' => 'rusak',
                'keterangan' => 'Test',
            ]);

        $response->assertSessionHas('error');

        // Pastikan stock tetap 10 (tidak berubah)
        $product = Product::find($this->product->id);
        $this->assertEquals(10, $product->stock);
        $this->assertGreaterThanOrEqual(0, $product->stock);
    }

    /**
     * Test: Stock history tercatat dengan benar
     */
    public function test_stock_history_is_recorded_correctly()
    {
        // Stock in
        StockIn::create([
            'product_id' => $this->product->id,
            'supplier_id' => $this->supplier->id,
            'tanggal_masuk' => now()->format('Y-m-d'),
            'qty' => 5,
        ]);

        // Cek history type 'in'
        $this->assertDatabaseHas('stock_histories', [
            'product_id' => $this->product->id,
            'type' => 'in',
            'qty' => 5,
            'stock_before' => 10,
            'stock_after' => 15,
        ]);

        // Stock out
        StockOut::create([
            'product_id' => $this->product->id,
            'tanggal_keluar' => now()->format('Y-m-d'),
            'qty' => 3,
            'alasan' => 'rusak',
        ]);

        // Cek history type 'out'
        $this->assertDatabaseHas('stock_histories', [
            'product_id' => $this->product->id,
            'type' => 'out',
            'qty' => 3,
            'stock_before' => 15,
            'stock_after' => 12,
        ]);

        // Cek total stock history
        $histories = StockHistory::where('product_id', $this->product->id)->count();
        $this->assertEquals(2, $histories);
    }

    /**
     * Test: Stok masuk dan keluar menggunakan transaction
     */
    public function test_stock_operations_use_transaction()
    {
        // Test transaction berhasil
        try {
            DB::beginTransaction();

            StockIn::create([
                'product_id' => $this->product->id,
                'qty' => 5,
                'tanggal_masuk' => now()->format('Y-m-d'),
            ]);

            $this->product->update(['stock' => $this->product->stock + 5]);

            DB::commit();

            $this->assertEquals(15, $this->product->fresh()->stock);
        } catch (\Exception $e) {
            DB::rollBack();
            $this->fail('Transaction failed: ' . $e->getMessage());
        }

        // Test transaction rollback
        $initialStock = $this->product->stock;

        try {
            DB::beginTransaction();

            StockOut::create([
                'product_id' => $this->product->id,
                'qty' => 3,
                'tanggal_keluar' => now()->format('Y-m-d'),
                'alasan' => 'rusak',
            ]);

            // Force error
            throw new \Exception('Test error');

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();

            // Pastikan stock tidak berubah
            $this->assertEquals($initialStock, $this->product->fresh()->stock);
        }
    }
}
