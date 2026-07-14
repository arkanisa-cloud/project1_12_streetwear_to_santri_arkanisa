# Implementation Plan — RajaOngkir Integration & Checkout UI Polish

This plan addresses all issues with the checkout shipping address and automatic cost calculation, adds robust caching to the RajaOngkir service to prevent timeout/rate-limit errors, sorts dropdowns alphabetically, ensures full 4-level address coverage, and refines the checkout UI with address editing capabilities.

## User Review Required

> [!IMPORTANT]
> - **API Level Mismatch Fix**: The RajaOngkir / Komerce cost calculation API expects the **District (Kecamatan) ID** as the destination parameter. Sending a Kelurahan ID causes a `422 Origin or Destination not found` error. 
> - **Zero Database Changes Solution**: To solve this without breaking the existing database and checkout flow, we will store the **Kecamatan ID** in the database column `subdistrict_id` (so checkout cost calculation works flawlessly) and store the combined string `"Kecamatan Name, Kelurahan Name"` (e.g. `"Jogonalan, Ngering"`) in the `subdistrict` text column.
> - **Auto Pre-population on Edit**: In the address edit form, we will automatically parse `"Kecamatan, Kelurahan"` to pre-select both the Kecamatan (District) and Kelurahan (Sub-district) dropdowns.

---

## Proposed Changes

### 1. Service Layer & Backend Logic

#### [MODIFY] [RajaOngkirService.php](file:///d:/Documents/SMK%20XI/Project/MAMP/Laravel/STS/app/Services/RajaOngkirService.php)
- Implement robust `Cache::remember` caching (for 30 days) on geographical endpoints: `getProvinces()`, `getCities($provinceId)`, `getDistricts($cityId)`, and `getSubDistricts($districtId)`. This will prevent Guzzle timeouts, bypass 429 rate limits, and make dropdowns load in **0ms** after the first fetch.
- Add alphabetical sorting (`usort` using `strcasecmp` by name) to all geographical list methods so they are sorted alphabetically by default.

#### [MODIFY] [RajaOngkirController.php](file:///d:/Documents/SMK%20XI/Project/MAMP/Laravel/STS/app/Http/Controllers/Api/RajaOngkirController.php)
- Clean up `provinces()` and other methods to retrieve and return cached service results cleanly.

#### [MODIFY] [ShippingAddressController.php](file:///d:/Documents/SMK%20XI/Project/MAMP/Laravel/STS/app/Http/Controllers/Customer/ShippingAddressController.php)
- Update `store()` and `update()` validation rules to validate:
  - `province_id`, `province`
  - `city_id`, `city`
  - `district_id`, `district` (Kecamatan)
  - `subdistrict_id`, `subdistrict` (Kelurahan)
- Map `district_id` (Kecamatan ID) to `subdistrict_id` and map the combined string `"$district, $subdistrict"` to the `subdistrict` column before saving to the database.

---

### 2. Frontend Views & UI Polishing

#### [MODIFY] [create.blade.php](file:///d:/Documents/SMK%20XI/Project/MAMP/Laravel/STS/resources/views/customer/shipping-addresses/create.blade.php)
- Adjust the 4-level cascading dropdowns to submit both Kecamatan (`district_id`/`district`) and Kelurahan (`subdistrict_id`/`subdistrict`) fields.
- Make the dropdown options load dynamically, clean up Tailwind utility classes, and ensure premium responsive styling.

#### [MODIFY] [edit.blade.php](file:///d:/Documents/SMK%20XI/Project/MAMP/Laravel/STS/resources/views/customer/shipping-addresses/edit.blade.php)
- Fully rewrite the edit form to match `create.blade.php` with 4-level cascading dropdowns.
- Implement intelligent pre-population: parse the stored `"Kecamatan, Kelurahan"` string on load, fetch the corresponding geographic data sequentially, and pre-select all 4 dropdown elements automatically!

#### [MODIFY] [checkout.blade.php](file:///d:/Documents/SMK%20XI/Project/MAMP/Laravel/STS/resources/views/customer/checkout.blade.php)
- Add a premium pencil-icon "Edit Address" button next to each shipping address card. The button will direct the user to the address edit page with `?redirect=checkout` so they can edit and return seamlessly.
- Polish the address card layout, loading spinner, and responsive alignment of the checkout page to make it extremely elegant and neat.

---

## Verification Plan

### Automated Tests
- Run `php test_controller.php` to verify cached and sorted outputs.
- Test endpoint calls directly via browser or cURL.

### Manual Verification
- Add a new shipping address up to the Kelurahan (Sub-district) level. Verify all 4 dropdowns work without errors.
- Edit the newly created shipping address. Verify all 4 dropdowns pre-populate correctly.
- Go to the Checkout page. Verify that:
  - Shipping address card displays the detailed address correctly.
  - The "Edit" button successfully navigates to the edit page and returns back to checkout.
  - Cost calculation works perfectly and does not throw any error when a courier is selected.
