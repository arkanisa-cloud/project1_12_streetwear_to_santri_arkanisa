# RajaOngkir Shipping Cost Calculation Workflow

**Project**: E-Commerce Store (STS)  
**Feature**: Dynamic Shipping Cost Calculation  
**Last Updated**: May 21, 2026

---

## 📋 Overview

This document explains the complete data flow for calculating and applying shipping costs using the RajaOngkir API during the checkout process. The system automatically calculates the cost based on the weight of items in the cart, the selected shipping address, and the chosen courier.

---

## 🎯 Key Components

| Component | Location | Purpose |
|-----------|----------|---------|
| **RajaOngkirService** | `app/Services/RajaOngkirService.php` | Handles API communication with RajaOngkir |
| **RajaOngkirController** | `app/Http/Controllers/Api/RajaOngkirController.php` | Receives AJAX requests from frontend |
| **CheckoutController** | `app/Http/Controllers/Customer/CheckoutController.php` | Processes and saves order with shipping info |
| **Checkout View** | `resources/views/customer/checkout.blade.php` | Alpine.js component for user interaction |
| **Order Model** | `app/Models/Order.php` | Stores order data including shipping info |
| **Product Model** | `app/Models/Product.php` | Contains weight data for each product |

---

## 🔄 Complete Data Flow

### **Step 1: Checkout Page Loads**

**File**: `resources/views/customer/checkout.blade.php`

**What Happens:**
```javascript
// JavaScript loads cart items with product weight information
const cartItems = [
  { product_id: 1, qty: 2, weight: 500 },  // 500 grams per unit
  { product_id: 3, qty: 1, weight: 1000 }  // 1000 grams per unit
];

// Address data from database
const addressData = [
  { id: 1, city_id: 401 },  // City ID from RajaOngkir
  { id: 2, city_id: 402 }
];
```

**User Actions:**
1. Customer selects a shipping address
2. Customer selects a courier (JNE, POS Indonesia, or TIKI)

---

### **Step 2: Frontend Weight Calculation**

**File**: `resources/views/customer/checkout.blade.php` (Alpine.js)

**Function**: `calculateTotalWeight()`

```javascript
calculateTotalWeight() {
  return cartItems.reduce((total, item) => {
    return total + (item.qty * item.weight);
  }, 0);
}

// Example: (2 × 500) + (1 × 1000) = 2000 grams
```

**Why?**
- Each product has a `weight` field (in grams)
- Total weight = sum of (quantity × product_weight) for all items
- This weight is required by RajaOngkir API

---

### **Step 3: AJAX Request to Backend**

**Triggered**: When user selects a courier via `@change="calculateShipping()"`

**Request Details:**

```javascript
// File: resources/views/customer/checkout.blade.php
fetch('{{ route("customer.api.rajaongkir.cost") }}', {
  method: 'POST',
  headers: {
    'Content-Type': 'application/json',
    'X-CSRF-TOKEN': token  // Laravel CSRF protection
  },
  body: JSON.stringify({
    origin: 401,              // From .env: RAJAONGKIR_ORIGIN_CITY_ID
    destination: 402,         // User's selected address city_id
    weight: 2000,             // Total weight in grams
    courier: 'jne'            // User's selected courier
  })
});
```

**Route**: `Route::post('/cost', [RajaOngkirController::class, 'cost'])->name('cost');`

---

### **Step 4: Backend Validation**

**File**: `app/Http/Controllers/Api/RajaOngkirController.php`

**Method**: `cost(Request $request)`

```php
$validated = $request->validate([
  'origin' => 'required|integer',           // 401
  'destination' => 'required|integer',      // 402
  'weight' => 'required|integer|min:1',    // 2000
  'courier' => 'required|string|in:jne,pos,tiki'  // 'jne'
]);
```

**What Gets Validated:**
- ✓ Origin and destination are valid integers (city IDs)
- ✓ Weight is positive integer (minimum 1 gram)
- ✓ Courier is one of the allowed values

---

### **Step 5: Call RajaOngkir External API**

**File**: `app/Services/RajaOngkirService.php`

**Method**: `getCost($origin, $destination, $weight, $courier)`

**HTTP Request to RajaOngkir:**
```
POST https://rajaongkir.komerce.id/api/v1/calculate/district/domestic-cost

Headers:
- key: us8qeqkp729a0b9a7db24426CTEBPGTQ  // From .env: RAJAONGKIR_API_KEY
- Content-Type: application/json

Body:
{
  "origin": 401,
  "destination": 402,
  "weight": 2000,
  "courier": "jne"
}
```

**What RajaOngkir Returns:**
```json
{
  "data": [
    {
      "code": "jne",
      "name": "JNE",
      "costs": [
        {
          "service": "OKE",
          "description": "Ongkir Kilat Khusus",
          "cost": [
            {
              "value": 15000,
              "etd": "1-2"
            }
          ]
        },
        {
          "service": "REG",
          "description": "Reguler",
          "cost": [
            {
              "value": 12000,
              "etd": "3-4"
            }
          ]
        }
      ]
    }
  ]
}
```

---

### **Step 6: Backend Returns JSON Response**

**File**: `app/Http/Controllers/Api/RajaOngkirController.php`

**Response Format:**
```php
return response()->json([
    'success' => true,
    'data' => $costs,  // Array of courier options with services
]);
```

**Full Response Example:**
```json
{
  "success": true,
  "data": [
    {
      "code": "jne",
      "name": "JNE",
      "costs": [
        {
          "service": "OKE",
          "description": "Ongkir Kilat Khusus",
          "cost": [{"value": 15000, "etd": "1-2"}]
        },
        {
          "service": "REG",
          "description": "Reguler",
          "cost": [{"value": 12000, "etd": "3-4"}]
        }
      ]
    }
  ]
}
```

---

### **Step 7: Frontend Displays Shipping Options**

**File**: `resources/views/customer/checkout.blade.php` (Alpine.js)

**Process:**
```javascript
// In Alpine.js component
if (data.success && data.data.length > 0) {
  this.shippingServices = data.data[0].costs || [];
  // Now displays:
  // ☐ OKE - Rp 15.000 - 1-2 days
  // ☐ REG - Rp 12.000 - 3-4 days
} else {
  this.shippingError = 'No shipping services available.';
}
```

**UI Elements:**
- Loading spinner shown while fetching
- Radio buttons for each service option
- Service name, cost, and delivery time displayed
- Error message if no services available

---

### **Step 8: User Selects Shipping Service**

**User Action**: Click on a shipping service option

**Frontend Stores:**
```javascript
selectService(idx) {
  this.selectedServiceIdx = idx;              // Index of selected service
  this.shippingCost = this.shippingServices[idx].cost[0].value;  // 15000
}
```

**Hidden Form Fields Updated:**
```html
<input type="hidden" name="shipping_courier" :value="selectedCourier">
<!-- Value: "jne" -->

<input type="hidden" name="shipping_service" :value="shippingServices[selectedServiceIdx].service">
<!-- Value: "OKE" -->

<input type="hidden" name="shipping_cost" :value="shippingCost">
<!-- Value: 15000 -->
```

**Order Summary Updated:**
- Subtotal: Rp 500.000 (items only)
- Shipping: Rp 15.000 ← **Updated**
- **Grand Total: Rp 515.000** ← **Recalculated**

---

### **Step 9: Form Submission**

**User Action**: Click "Place Order" button

**Form Data Sent:**
```
POST /customer/checkout
Content-Type: application/x-www-form-urlencoded

shipping_address_id=1
payment_method=transfer
shipping_courier=jne
shipping_service=OKE
shipping_cost=15000
```

---

### **Step 10: Backend Checkout Processing**

**File**: `app/Http/Controllers/Customer/CheckoutController.php`

**Method**: `store(Request $request)`

**Step 10.1: Validation**
```php
$validated = $request->validate([
  'shipping_address_id' => 'required|exists:shipping_addresses,id',
  'payment_method' => 'required|in:transfer,ewallet,cod',
  'shipping_courier' => 'required|string|in:jne,pos,tiki',   // ✓ NEW
  'shipping_service' => 'required|string',                    // ✓ NEW
  'shipping_cost' => 'required|numeric|min:0',               // ✓ NEW
]);
```

**Step 10.2: Calculate Grand Total**
```php
$grandTotal = $cart->total + $validated['shipping_cost'];
// $grandTotal = 500000 + 15000 = 515000
```

**Step 10.3: Database Transaction Begins**
```php
DB::beginTransaction();
```

**Step 10.4: Create Order with Shipping Data**
```php
$order = Order::create([
  'user_id' => Auth::id(),
  'shipping_address_id' => 1,
  'order_number' => 'ORD-20260521-0042',
  'total' => 515000,
  'status' => 'pending',
  'payment_status' => 'unpaid',
  'shipping_courier' => 'jne',           // ✓ NEW
  'shipping_service' => 'OKE',          // ✓ NEW
  'shipping_cost' => 15000,             // ✓ NEW
]);
```

**Step 10.5: Create Order Items & Update Stock**
- Creates OrderItem records (product, qty, price)
- Reduces product stock by quantity ordered
- Records stock history

**Step 10.6: Create Payment Record**
```php
Payment::create([
  'order_id' => $order->id,
  'payment_method' => 'transfer',
  'status' => 'pending',
]);
```

**Step 10.7: Clear Cart**
```php
$cart->cartItems()->delete();
```

**Step 10.8: Commit Transaction**
```php
DB::commit();
```

---

### **Step 11: Order Saved to Database**

**Table**: `orders`

```sql
INSERT INTO orders (
  user_id,
  shipping_address_id,
  order_number,
  total,
  status,
  payment_status,
  shipping_courier,
  shipping_service,
  shipping_cost,
  created_at,
  updated_at
) VALUES (
  5,
  1,
  'ORD-20260521-0042',
  515000,
  'pending',
  'unpaid',
  'jne',
  'OKE',
  15000,
  now(),
  now()
);
```

**New Columns Added** (Migration: `2026_05_21_000001_add_shipping_fields_to_orders_table.php`):
- `shipping_courier` (string) - Stores: jne, pos, or tiki
- `shipping_service` (string) - Stores: OKE, REG, SICEPAT, etc.
- `shipping_cost` (decimal 10,2) - Stores: cost in Rupiah

---

## 📊 Data Model Relationships

```
┌──────────────────────┐
│ User (Customer)      │
└──────────┬───────────┘
           │ owns
           │
      ┌────▼─────────────────┐
      │ ShippingAddress       │
      │ - city_id (RO API)    │
      │ - province_id (RO)    │
      └────┬────────┬─────────┘
           │ uses  │
           │       └─────────────────┐
      ┌────▼──────────────────┐   ┌──▼──────────────────┐
      │ Order (NEW FIELDS)    │   │ Cart                │
      │ - shipping_courier ✓  │   │ ┌──────────────────┐│
      │ - shipping_service ✓  │   │ │ CartItem         ││
      │ - shipping_cost ✓     │   │ │ - qty            ││
      │                       │   │ │ - product_id     ││
      │ ┌──────────────────┐  │   │ │   └───────┐      ││
      │ │ OrderItem        │  │   │ │           │      ││
      │ │ - qty            │  │   │ │     ┌─────▼─┐   ││
      │ │ - product_id ────┼──┼───┼─┼─────│Product│   ││
      │ │ - price          │  │   │ │     │weight ├───┼┘
      │ │ - subtotal       │  │   │ │     └───────┘   │
      │ └──────────────────┘  │   │ └──────────────────┘
      │                       │   │
      └───────────────────────┘   └────────────────────┘
```

---

## 🔐 Security & Validation

### CSRF Protection
```javascript
headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
```
- Every AJAX request includes CSRF token
- Prevents unauthorized requests from other domains

### Input Validation

**Frontend** (Alpine.js):
- Verify address is selected
- Verify courier is selected
- Verify service is selected

**Backend** (Laravel):
```php
$validated = $request->validate([
  'shipping_address_id' => 'required|exists:shipping_addresses,id',  // Must exist & belong to user
  'payment_method' => 'required|in:transfer,ewallet,cod',            // Whitelist
  'shipping_courier' => 'required|string|in:jne,pos,tiki',          // Whitelist
  'shipping_service' => 'required|string',                            // Must not be empty
  'shipping_cost' => 'required|numeric|min:0',                       // Must be valid number
]);
```

### Ownership Verification
```php
if ($address->user_id !== Auth::id()) {
  abort(403, 'Unauthorized');  // Prevent using other user's address
}
```

---

## ⚙️ Configuration

**File**: `.env`

```env
RAJAONGKIR_API_KEY=us8qeqkp729a0b9a7db24426CTEBPGTQ
RAJAONGKIR_ORIGIN_CITY_ID=401
RAJAONGKIR_PACKAGE=starter
```

**Origin City**: City 401 (Bandung) - Where products ship from
**API Key**: Required for all RajaOngkir API calls
**Package**: Free tier of RajaOngkir service

---

## 🐛 Error Handling

### API Call Failures

**Frontend Error Messages:**
- `"Failed to calculate shipping cost."` - Network error
- `"No shipping services available for this route."` - No couriers service that route
- `"No shipping services available."` - API returned empty data

**Backend Error Handling:**
```php
catch (\Exception $e) {
  Log::error('RajaOngkir getCost Error: ' . $e->getMessage());
  return [];  // Returns empty array, preventing crashes
}
```

### Transaction Rollback

If any step in checkout fails:
```php
try {
  // All checkout operations
} catch (\Exception $e) {
  DB::rollback();  // Undo EVERYTHING
  return back()->with('error', $e->getMessage());
}
```

---

## 📈 Weight Calculation Logic

**Formula**: `Total Weight = Σ(Quantity × Product Weight)`

**Example:**
```
Item 1: Quantity 2 × Weight 500g = 1000g
Item 2: Quantity 1 × Weight 1000g = 1000g
Item 3: Quantity 3 × Weight 200g = 600g

Total Weight = 1000 + 1000 + 600 = 2600 grams
```

**Important Notes:**
- Weight stored per product (not per cart item)
- Weight is in **grams** (RajaOngkir API requirement)
- Fallback: If total weight is 0, use 1000g default

---

## 🔄 API Response Structure

**RajaOngkir Returns:**
```json
{
  "data": [
    {
      "code": "jne",
      "name": "JNE",
      "costs": [
        {
          "service": "OKE",
          "description": "Ongkir Kilat Khusus",
          "cost": [
            {
              "value": 15000,
              "etd": "1-2"
            }
          ]
        }
      ]
    }
  ]
}
```

**What Each Field Means:**
- `code` - Courier code (jne, pos, tiki)
- `name` - Courier display name
- `service` - Service type code (OKE, REG, etc.)
- `description` - Service description
- `value` - Cost in Rupiah
- `etd` - Estimated Time Delivery (days)

---

## 🚀 Testing Checklist

Before shipping to production:

- [ ] Add a product with weight > 0
- [ ] Add to cart (qty × weight should calculate correctly)
- [ ] Go to checkout with multiple items
- [ ] Select address with city_id
- [ ] Select JNE courier
- [ ] Verify shipping services load
- [ ] Select a service (cost should display)
- [ ] Submit checkout
- [ ] Verify Order saved with shipping_courier, shipping_service, shipping_cost
- [ ] Check database: `SELECT * FROM orders WHERE id = ?;`
- [ ] Verify stock reduced correctly
- [ ] Verify cart cleared

---

## 📝 Database Schema Changes

**Migration**: `2026_05_21_000001_add_shipping_fields_to_orders_table.php`

```sql
ALTER TABLE orders ADD COLUMN shipping_courier VARCHAR(255) NULL;
ALTER TABLE orders ADD COLUMN shipping_service VARCHAR(255) NULL;
ALTER TABLE orders ADD COLUMN shipping_cost DECIMAL(10,2) NULL DEFAULT 0;
```

**Rollback Command:**
```bash
php artisan migrate:rollback --step=1
```

---

## 🎓 Key Learning Points for Junior Developers

1. **Why Separate Frontend & Backend Calculations?**
   - Frontend calculates weight for user feedback
   - Backend validates everything again for security

2. **Why Use Transactions?**
   - Ensures data consistency
   - If any step fails, everything rolls back (no partial orders)

3. **Why Pass `city_id` from Frontend?**
   - RajaOngkir API requires city IDs, not city names
   - User selects a saved address which stores `city_id`

4. **Why Validate Courier Against Whitelist?**
   - Prevents users from submitting invalid couriers
   - `in:jne,pos,tiki` ensures only supported couriers

5. **Why Store Shipping Cost in Order?**
   - Historical record of what was charged
   - If RajaOngkir prices change, order history stays accurate

---

## 📚 Related Files

- Configuration: [.env](.env)
- Service: [app/Services/RajaOngkirService.php](app/Services/RajaOngkirService.php)
- Controller: [app/Http/Controllers/Api/RajaOngkirController.php](app/Http/Controllers/Api/RajaOngkirController.php)
- Checkout: [app/Http/Controllers/Customer/CheckoutController.php](app/Http/Controllers/Customer/CheckoutController.php)
- View: [resources/views/customer/checkout.blade.php](resources/views/customer/checkout.blade.php)
- Model: [app/Models/Order.php](app/Models/Order.php)
- Migration: [database/migrations/2026_05_21_000001_add_shipping_fields_to_orders_table.php](database/migrations/2026_05_21_000001_add_shipping_fields_to_orders_table.php)

---

**Last Updated:** May 21, 2026  
**Created For:** Junior Developer Learning
