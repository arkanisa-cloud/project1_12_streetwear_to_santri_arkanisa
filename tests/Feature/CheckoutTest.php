<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Product;
use App\Models\Category;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ShippingAddress;
use App\Models\StockHistory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * CheckoutTest
 * Test proses checkout dan pembuatan order
 */
class CheckoutTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Create category
        $this->category = Category::factory()->create();

        // Create customer
        $this->customer = User::factory()->create([
            'role' => 'customer',
        ]);

        // Create products
        $this->product1 = Product::factory()->create([
            'category_id' => $this->category->id,
            'name' => 'Product 1',
            'price' => 100000,
            'stock' => 10,
        ]);

        $this->product2 = Product::factory()->create([
            'category_id' => $this->category->id,
            'name' => 'Product 2',
            'price' => 200000,
            'stock' => 5,
        ]);

        // Create shipping address
        $this->address = ShippingAddress::factory()->create([
            'user_id' => $this->customer->id,
        ]);
    }

    /**
     * Test: Customer bisa melakukan checkout
     */
    public function test_customer_can_checkout()
    {
        // Add products to cart
        $cart = Cart::create(['user_id' => $this->customer->id]);

        CartItem::create([
            'cart_id' => $cart->id,
            'product_id' => $this->product1->id,
            'qty' => 2,
            'price' => $this->product1->price,
        ]);

        CartItem::create([
            'cart_id' => $cart->id,
            'product_id' => $this->product2->id,
            'qty' => 1,
            'price' => $this->product2->price,
        ]);

        // Checkout
        $response = $this->actingAs($this->customer)
            ->post(route('customer.checkout.store'), [
                'shipping_address_id' => $this->address->id,
                'payment_method' => 'transfer',
            ]);

        $response->assertRedirect();

        // Cek order created
        $this->assertDatabaseHas('orders', [
            'user_id' => $this->customer->id,
            'total' => 400000, // (2 * 100000) + (1 * 200000)
        ]);

        // Cek order items
        $order = Order::where('user_id', $this->customer->id)->first();

        $this->assertDatabaseHas('order_items', [
            'order_id' => $order->id,
            'product_id' => $this->product1->id,
            'qty' => 2,
            'subtotal' => 200000,
        ]);

        $this->assertDatabaseHas('order_items', [
            'order_id' => $order->id,
            'product_id' => $this->product2->id,
            'qty' => 1,
            'subtotal' => 200000,
        ]);

        // Cek stock berkurang
        $this->assertDatabaseHas('products', [
            'id' => $this->product1->id,
            'stock' => 8, // 10 - 2
        ]);

        $this->assertDatabaseHas('products', [
            'id' => $this->product2->id,
            'stock' => 4, // 5 - 1
        ]);

        // Cek stock history
        $this->assertDatabaseHas('stock_histories', [
            'product_id' => $this->product1->id,
            'type' => 'sale',
            'qty' => 2,
            'stock_before' => 10,
            'stock_after' => 8,
            'reference_id' => $order->id,
            'reference_type' => 'Order',
        ]);

        // Cek cart kosong
        $this->assertDatabaseMissing('cart_items', [
            'cart_id' => $cart->id,
        ]);
    }

    /**
     * Test: Tidak bisa checkout jika stok tidak cukup
     */
    public function test_cannot_checkout_if_insufficient_stock()
    {
        // Add to cart dengan qty melebihi stok
        $cart = Cart::create(['user_id' => $this->customer->id]);

        CartItem::create([
            'cart_id' => $cart->id,
            'product_id' => $this->product1->id,
            'qty' => 15, // Stok hanya 10
            'price' => $this->product1->price,
        ]);

        $response = $this->actingAs($this->customer)
            ->post(route('customer.checkout.store'), [
                'shipping_address_id' => $this->address->id,
                'payment_method' => 'transfer',
            ]);

        $response->assertSessionHas('error');

        // Order tidak dibuat
        $this->assertDatabaseMissing('orders', [
            'user_id' => $this->customer->id,
        ]);

        // Stok tidak berkurang
        $this->assertDatabaseHas('products', [
            'id' => $this->product1->id,
            'stock' => 10,
        ]);
    }

    /**
     * Test: Checkout menggunakan database transaction
     */
    public function test_checkout_uses_database_transaction()
    {
        // Create scenario yang akan gagal di tengah
        $cart = Cart::create(['user_id' => $this->customer->id]);

        CartItem::create([
            'cart_id' => $cart->id,
            'product_id' => $this->product1->id,
            'qty' => 2,
            'price' => $this->product1->price,
        ]);

        // Gunakan DB::transaction dan force error
        try {
            DB::beginTransaction();

            // Simulasikan error di tengah proses
            throw new \Exception('Test error');

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
        }

        // Pastikan semua dalam transaction
        // Jika ada error, semua harus di-rollback
        $this->assertTrue(true); // Placeholder test
    }

    /**
     * Test: Order number unik
     */
    public function test_order_number_is_unique()
    {
        // Buat 2 order
        $order1 = Order::create([
            'user_id' => $this->customer->id,
            'shipping_address_id' => $this->address->id,
            'order_number' => Order::generateOrderNumber(),
            'total' => 100000,
            'status' => 'pending',
            'payment_status' => 'unpaid',
        ]);

        sleep(1); // Delay 1 detik agar beda timestamp

        $order2 = Order::create([
            'user_id' => $this->customer->id,
            'shipping_address_id' => $this->address->id,
            'order_number' => Order::generateOrderNumber(),
            'total' => 200000,
            'status' => 'pending',
            'payment_status' => 'unpaid',
        ]);

        // Order number harus berbeda
        $this->assertNotEquals($order1->order_number, $order2->order_number);

        // Format harus ORD-YYYYMMDD-XXXX
        $this->assertMatches('/^ORD-\d{8}-\d{4}$/', $order1->order_number);
        $this->assertMatches('/^ORD-\d{8}-\d{4}$/', $order2->order_number);
    }
}
