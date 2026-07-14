<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Product;
use App\Models\Cart;
use App\Models\CartItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * CartTest
 * Test fitur keranjang belanja
 */
class CartTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Setup: Create customer dan produk
     */
    protected function setUp(): void
    {
        parent::setUp();

        // Buat user customer
        $this->customer = User::factory()->create([
            'role' => 'customer',
        ]);

        // Buat produk sample
        $this->product = Product::factory()->create([
            'name' => 'Laptop Test',
            'price' => 5000000,
            'stock' => 10,
        ]);
    }

    /**
     * Test: Customer bisa melihat cart
     */
    public function test_customer_can_view_cart()
    {
        $response = $this->actingAs($this->customer)
            ->get(route('customer.cart.index'));

        $response->assertStatus(200);
        $response->assertViewIs('customer.cart');
    }

    /**
     * Test: Customer bisa tambah produk ke cart
     */
    public function test_customer_can_add_product_to_cart()
    {
        $response = $this->actingAs($this->customer)
            ->post(route('customer.cart.store'), [
                'product_id' => $this->product->id,
                'qty' => 2,
            ]);

        $response->assertRedirect(route('customer.cart.index'));

        // Cek cart item created
        $this->assertDatabaseHas('carts', [
            'user_id' => $this->customer->id,
        ]);

        $cart = Cart::where('user_id', $this->customer->id)->first();

        $this->assertDatabaseHas('cart_items', [
            'cart_id' => $cart->id,
            'product_id' => $this->product->id,
            'qty' => 2,
        ]);
    }

    /**
     * Test: Tidak bisa tambah jika stok tidak cukup
     */
    public function test_cannot_add_if_insufficient_stock()
    {
        $response = $this->actingAs($this->customer)
            ->post(route('customer.cart.store'), [
                'product_id' => $this->product->id,
                'qty' => 15, // Stok hanya 10
            ]);

        $response->assertSessionHas('error');
        $this->assertDatabaseMissing('cart_items', [
            'product_id' => $this->product->id,
        ]);
    }

    /**
     * Test: Bisa update qty cart
     */
    public function test_can_update_cart_item_qty()
    {
        // Add to cart first
        $cart = Cart::create(['user_id' => $this->customer->id]);
        CartItem::create([
            'cart_id' => $cart->id,
            'product_id' => $this->product->id,
            'qty' => 2,
            'price' => $this->product->price,
        ]);

        // Update qty
        $cartItem = CartItem::first();

        $response = $this->actingAs($this->customer)
            ->put(route('customer.cart.update', $cartItem), [
                'qty' => 5,
            ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('cart_items', [
            'id' => $cartItem->id,
            'qty' => 5,
        ]);
    }

    /**
     * Test: Tidak bisa update jika stok tidak cukup
     */
    public function test_cannot_update_if_insufficient_stock()
    {
        // Add to cart first
        $cart = Cart::create(['user_id' => $this->customer->id]);
        $cartItem = CartItem::create([
            'cart_id' => $cart->id,
            'product_id' => $this->product->id,
            'qty' => 2,
            'price' => $this->product->price,
        ]);

        // Update melebihi stok
        $response = $this->actingAs($this->customer)
            ->put(route('customer.cart.update', $cartItem), [
                'qty' => 15, // Stok hanya 10
            ]);

        $response->assertSessionHas('error');
    }

    /**
     * Test: Bisa hapus item dari cart
     */
    public function test_can_remove_item_from_cart()
    {
        // Add to cart first
        $cart = Cart::create(['user_id' => $this->customer->id]);
        $cartItem = CartItem::create([
            'cart_id' => $cart->id,
            'product_id' => $this->product->id,
            'qty' => 1,
            'price' => $this->product->price,
        ]);

        $response = $this->actingAs($this->customer)
            ->delete(route('customer.cart.destroy', $cartItem));

        $response->assertRedirect();

        $this->assertDatabaseMissing('cart_items', [
            'id' => $cartItem->id,
        ]);
    }

    /**
     * Test: Bisa kosongkan cart
     */
    public function test_can_clear_cart()
    {
        // Add items to cart
        $cart = Cart::create(['user_id' => $this->customer->id]);
        CartItem::create([
            'cart_id' => $cart->id,
            'product_id' => $this->product->id,
            'qty' => 1,
            'price' => $this->product->price,
        ]);

        $response = $this->actingAs($this->customer)
            ->post(route('customer.cart.clear'));

        $response->assertRedirect();

        $this->assertDatabaseMissing('cart_items', [
            'cart_id' => $cart->id,
        ]);
    }
}
