# Dokumentasi Alur Data Order Admin - Customer

## 📋 Ringkasan
Dokumen ini menjelaskan alur lengkap data pesanan (order) mulai dari halaman admin hingga perubahan status yang terlihat di halaman customer, termasuk penjelasan error yang terjadi dan solusinya.

---

## 🔄 Alur Proses Order dari Awal hingga Akhir

### 1. Customer Membuat Order (Checkout)
**File yang terlibat:**
- `app/Http/Controllers/Customer/CheckoutController.php` - Proses checkout
- `routes/web.php` - Route POST `/customer/checkout`
- Database: Tabel `orders` dan `order_items`

**Alur:**
```
Customer di halaman checkout
    ↓
Klik tombol "Pesan Sekarang"
    ↓
Form POST ke route 'customer.checkout.store'
    ↓
CheckoutController->store() memproses data:
  - Validasi input (alamat pengiriman, metode pembayaran, dll)
  - Generate nomor order unik (ORD-YYYYMMDD-XXXX)
  - Simpan data ke tabel `orders` dengan status default 'pending'
  - Simpan item-item ke tabel `order_items`
  - Jika payment_method = 'transfer' → buat record di tabel `payments`
    ↓
Order berhasil dibuat
Status order = 'pending'
Payment status = 'unpaid'
```

**Data yang disimpan di tabel `orders`:**
```sql
id | order_number | user_id | status  | payment_status | total | shipping_cost | created_at
1  | ORD-20260531-0001 | 5  | pending | unpaid  | 250000 | 15000 | 2026-05-31 10:00:00
```

---

## 2. Customer Upload Bukti Pembayaran

**File yang terlibat:**
- `resources/views/customer/orders/show.blade.php` - Halaman detail order customer
- `app/Http/Controllers/Customer/CheckoutController.php::uploadPayment()` - Endpoint upload
- `routes/web.php` - Route POST `/customer/orders/{order}/payment`
- Database: Tabel `payments`

**Alur:**
```
Customer di halaman detail order (status payment = 'unpaid')
    ↓
Lihat tombol "Upload Bukti Pembayaran"
    ↓
Klik tombol → Form upload muncul
    ↓
Select file gambar bukti transfer
    ↓
Klik "Kirim Bukti Konfirmasi"
    ↓
Form POST ke route 'customer.orders.payment'
    ↓
CheckoutController->uploadPayment() memproses:
  - Validasi file (jpeg/png, max 2MB)
  - Simpan file ke storage: storage/app/public/payments/
  - Update tabel `payments`:
    * proof = 'payments/bukti-xxx.jpg'
    * status = 'pending' (menunggu verifikasi admin)
    ↓
Payment status berubah: unpaid → pending
Customer melihat badge "⏱ Menunggu Verifikasi Admin"
```

**Data di tabel `payments` setelah upload:**
```sql
id | order_id | payment_method | proof | status | verified_at
1  | 1 | transfer | payments/bukti-xxx.jpg | pending | NULL
```

---

## 3. Admin Melihat Order di Dashboard Admin

**File yang terlibat:**
- `resources/views/admin/orders/index.blade.php` - Daftar order admin
- `resources/views/admin/orders/show.blade.php` - Detail order admin
- `app/Http/Controllers/Admin/OrderController.php::show()` - Load data order
- `routes/web.php` - Route GET `/admin/orders/{order}`

**Alur:**
```
Admin login → Dashboard → Menu "Orders" (atau semacamnya)
    ↓
Menampilkan daftar pesanan dari database (sorted latest)
    ↓
Admin klik order tertentu (misal ORD-20260531-0001)
    ↓
OrderController->show($order) memproses:
  - Load order dengan relasi: user, shippingAddress, orderItems, payment
  - Pass ke view admin.orders.show
    ↓
Halaman detail order admin menampilkan:
  - Informasi customer (nama, email)
  - Daftar item yang dipesan
  - Alamat pengiriman
  - Status pembayaran: 'pending' (Menunggu Verifikasi)
  - TOMBOL: "Lihat Gambar Bukti 👁" + "Terima (Paid)" + "Tolak Berkas"
```

---

## 4. Admin Verifikasi Pembayaran (ERROR #1 - SUDAH DIPERBAIKI)

### ❌ ERROR YANG TERJADI (Sebelum perbaikan)

**Error Message:**
```
Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException
The PUT method is not supported for route admin/orders/2/status. 
Supported methods: GET, HEAD, POST.
```

**Penyebab Error:**
```
File: resources/views/admin/orders/show.blade.php (Baris 189)
Form mengirim nilai status="accept" 
Tapi OrderController::verifyPayment() validasi untuk 'verified' atau 'rejected'
```

**File yang terlibat:**
- `resources/views/admin/orders/show.blade.php` - View form verifikasi (BARIS 189)
- `app/Http/Controllers/Admin/OrderController.php::verifyPayment()` - Endpoint verifikasi
- `routes/web.php` - Route POST `/admin/orders/{order}/verify-payment`
- Database: Tabel `payments` dan `orders`

### ✅ ALUR YANG BENAR (Setelah perbaikan)

**Skenario 1: Admin Klik "Terima (Paid)"**

```
Admin lihat form verifikasi pembayaran di halaman detail order
    ↓
Status pembayaran saat ini: 'pending'
    ↓
Admin klik tombol "Terima (Paid)"
    ↓
Form di kirim dengan:
  - action: route('admin.orders.verifyPayment', $order)
  - method: POST
  - CSRF token
  - hidden input: name="status" value="verified" ✅ (DIPERBAIKI dari "accept")
    ↓
OrderController->verifyPayment($request, $order) dijalankan:
    ↓
  1. Validasi input:
     - status harus ada dalam ['verified', 'rejected'] ✅
     - Request body: { status: 'verified', admin_notes: null }
    ↓
  2. Cek kondisi:
     - Jika $order->payment->status !== 'pending':
       Balik dengan error "Pembayaran sudah diverifikasi sebelumnya"
     - Jika valid → lanjut
    ↓
  3. Verifikasi berhasil:
     - $payment->verify(null) memanggil Payment->verify() method:
       * Update tabel payments:
         - status = 'verified'
         - verified_at = now()
     - Update tabel orders:
       - payment_status = 'paid'
    ↓
  4. Return back() dengan pesan success
    ↓
Halaman reload → Admin kembali ke detail order
```

**Status yang berubah di database:**

```sql
-- PAYMENTS table
id | order_id | status | verified_at
1  | 1 | verified | 2026-05-31 11:00:00

-- ORDERS table
id | order_number | payment_status
1  | ORD-20260531-0001 | paid
```

**Skenario 2: Admin Klik "Tolak Berkas"**

```
Admin lihat form verifikasi pembayaran
    ↓
Admin klik tombol "Tolak Berkas"
    ↓
Modal form penolakan muncul dengan textarea untuk alasan
Admin ketik alasan penolakan: "Gambar buram, nominal tidak sesuai"
    ↓
Admin klik "Tolak Sekarang"
    ↓
Form dikirim dengan:
  - action: route('admin.orders.verifyPayment', $order)
  - method: POST
  - hidden input: name="status" value="reject"
  - textarea: name="admin_notes" value="Gambar buram, nominal tidak sesuai"
    ↓
OrderController->verifyPayment($request, $order) dijalankan:
    ↓
  1. Validasi berhasil: status = 'reject' ada di array ['verified', 'rejected']
    ↓
  2. Payment->reject('Gambar buram, nominal tidak sesuai') dijalankan:
     * Update tabel payments:
       - status = 'rejected'
       - admin_notes = 'Gambar buram, nominal tidak sesuai'
    ↓
  3. Update tabel orders:
     - payment_status = 'rejected'
    ↓
  4. Return dengan pesan success: "Pembayaran ditolak"
```

**Status yang berubah di database:**

```sql
-- PAYMENTS table
id | order_id | status | admin_notes
1  | 1 | rejected | Gambar buram, nominal tidak sesuai

-- ORDERS table
id | order_number | payment_status
1  | ORD-20260531-0001 | rejected
```

---

## 5. Update Status Order (ERROR #2 - SUDAH DIPERBAIKI)

**File yang terlibat:**
- `resources/views/admin/orders/show.blade.php` - Form update status (BARIS 46-49)
- `app/Http/Controllers/Admin/OrderController.php::updateStatus()` - Endpoint update
- `routes/web.php` - Route POST `/admin/orders/{order}/status`
- Database: Tabel `orders`

### ❌ ERROR YANG TERJADI (Sebelum perbaikan)

```
Admin klik tombol "Update" pada form "Proses Pesanan"
    ↓
ERROR: Method Not Allowed (PUT bukan POST)
```

**Root Cause:** Form di view menggunakan `@method('PUT')` tapi route hanya accept `POST`

### ✅ ALUR YANG BENAR (Setelah perbaikan)

**Form Update Status Dihapus `@method('PUT')`**

```
Form di admin/orders/show.blade.php DIPERBAIKI:
  - Sebelum: @csrf @method('PUT')
  - Sesudah: @csrf (hanya, tanpa @method PUT)
```

**Alur Proses Update Status:**

```
Admin di halaman detail order
    ↓
Status saat ini = 'pending'
    ↓
Admin pilih opsi dari dropdown:
  "Proses Pesanan" (nilai: 'processed')
  "Kirim Pesanan" (nilai: 'shipped')
  "Selesaikan Pesanan" (nilai: 'completed')
  "Batalkan Pesanan" (nilai: 'cancelled')
    ↓
Admin klik tombol "Update"
    ↓
Form POST ke route 'admin.orders.updateStatus' ✅ (DIPERBAIKI)
  - method: POST (bukan PUT)
  - Kirim: { status: 'processed' }
    ↓
OrderController->updateStatus($request, $order) dijalankan:
    ↓
  1. Validasi:
     - status harus ada dalam ['processed','shipped','completed','cancelled']
     - payment_status harus 'paid' (kecuali status 'cancelled')
    ↓
  2. Validasi flow status:
     pending → boleh ke [processed, cancelled]
     processed → boleh ke [shipped]
     shipped → boleh ke [completed]
     completed → tidak bisa ke mana-mana
     cancelled → tidak bisa ke mana-mana
    ↓
  3. Jika valid:
     - Update tabel orders: status = 'processed'
     - Return dengan success message
    ↓
  4. Halaman reload
```

**Contoh Update Status:**

```sql
-- Sebelum Update:
UPDATE orders SET status = 'pending' WHERE id = 1

-- Setelah Admin Update ke "Proses Pesanan":
UPDATE orders SET status = 'processed' WHERE id = 1
```

---

## 6. Customer Melihat Perubahan Status di Halaman Order

**File yang terlibat:**
- `resources/views/customer/orders/show.blade.php` - Halaman order customer
- Database: Tabel `orders` (field `status`)

**Alur:**

```
Customer login → Menu Orders → Klik order tertentu
    ↓
Route GET /customer/orders/{order} dijalankan
    ↓
View menampilkan status order dari database (real-time):

SEBELUM ADMIN UPDATE:
┌─────────────────────────────┐
│ Status: Menunggu Diproses   │
├─────────────────────────────┤
│ Timeline:                   │
│ ✓ Order Masuk (31 May 10:00)│
└─────────────────────────────┘

SESUDAH ADMIN UPDATE KE "PROCESSED":
┌─────────────────────────────┐
│ Status: Sedang Diproses     │
├─────────────────────────────┤
│ Timeline:                   │
│ ✓ Order Masuk (31 May 10:00)│
│ ✓ Pesanan Diproses          │
│   Produk sedang disiapkan   │
└─────────────────────────────┘

SESUDAH ADMIN UPDATE KE "SHIPPED":
┌─────────────────────────────┐
│ Status: Sedang Dikirim      │
├─────────────────────────────┤
│ Timeline:                   │
│ ✓ Order Masuk (31 May 10:00)│
│ ✓ Pesanan Diproses          │
│ ✓ Dalam Pengiriman          │
│   Paket diserahkan ekspedisi│
└─────────────────────────────┘

SESUDAH ADMIN UPDATE KE "COMPLETED":
┌─────────────────────────────┐
│ Status: Selesai             │
├─────────────────────────────┤
│ Timeline:                   │
│ ✓ Order Masuk (31 May 10:00)│
│ ✓ Pesanan Diproses          │
│ ✓ Dalam Pengiriman          │
│ ✓ Paket Selesai (NEW)       │
│   Pesanan telah diterima    │
└─────────────────────────────┘
```

---

## 📊 Diagram Alur Lengkap Order-Payment-Status

```
┌─────────────────────────────────────────────────────────────────┐
│ CUSTOMER SIDE                                                   │
├─────────────────────────────────────────────────────────────────┤
│                                                                 │
│ Checkout → Order Created (status: pending)                     │
│                 ↓                                               │
│ Upload Bukti Pembayaran (payment_status: unpaid → pending)    │
│                 ↓                                               │
│ Lihat Status: "Menunggu Verifikasi Admin"                     │
│ (Menunggu admin terima/tolak di halaman admin)                │
│                                                                 │
└─────────────────────────────────────────────────────────────────┘
                          ↕ (Real-time dari DB)
┌─────────────────────────────────────────────────────────────────┐
│ DATABASE (Order & Payment Tables)                              │
├─────────────────────────────────────────────────────────────────┤
│                                                                 │
│ orders:                          payments:                     │
│ id | status | payment_status     id | status | admin_notes   │
│ 1  | pending| pending       ←→   1  | pending | null         │
│                                                                 │
└─────────────────────────────────────────────────────────────────┘
                          ↕ (Admin aksi)
┌─────────────────────────────────────────────────────────────────┐
│ ADMIN SIDE                                                      │
├─────────────────────────────────────────────────────────────────┤
│                                                                 │
│ Detail Order Admin                                             │
│  ├─ [Lihat Bukti] [Terima] [Tolak]  ← Verifikasi Pembayaran  │
│  └─ [Select Status] [Update]        ← Update Order Status    │
│                                                                 │
│ AKSI 1: Klik "Terima"                                          │
│ → POST /admin/orders/{id}/verify-payment                      │
│ → payment_status: pending → paid                              │
│ → status: pending → tetap pending (menunggu diproses)        │
│                                                                 │
│ AKSI 2: Klik "Update Status" = "Proses Pesanan"              │
│ → POST /admin/orders/{id}/status                              │
│ → status: pending → processed                                  │
│                                                                 │
└─────────────────────────────────────────────────────────────────┘
                          ↕ (Update DB)
┌─────────────────────────────────────────────────────────────────┐
│ CUSTOMER SIDE (Auto-update saat reload page)                   │
├─────────────────────────────────────────────────────────────────┤
│                                                                 │
│ After Admin Accept Payment:                                    │
│ • payment_status: paid                                         │
│ • Status Badge: "✓ Pembayaran Terverifikasi"                  │
│ • Timeline: tetap "Order Masuk"                               │
│                                                                 │
│ After Admin Update Status → Processed:                         │
│ • order status: processed                                      │
│ • Status Badge: "Sedang Diproses"                             │
│ • Timeline: + "Pesanan Diproses"                              │
│                                                                 │
│ After Admin Update Status → Shipped:                           │
│ • order status: shipped                                        │
│ • Status Badge: "Sedang Dikirim"                              │
│ • Timeline: + "Dalam Pengiriman"                              │
│                                                                 │
│ After Admin Update Status → Completed:                         │
│ • order status: completed                                      │
│ • Status Badge: "Selesai"                                     │
│ • Timeline: + "Paket Selesai"                                 │
│                                                                 │
└─────────────────────────────────────────────────────────────────┘
```

---

## 🐛 Error Details & Solutions

### Error 1: "The PUT method is not supported for route admin/orders/2/status"

**Lokasi:** Admin Orders Show Page - Update Status Form

**Penyebab:**
```
File: resources/views/admin/orders/show.blade.php (Baris 46-49)

SEBELUM (Error):
<form action="{{ route('admin.orders.updateStatus', $order) }}" method="POST">
    @csrf
    @method('PUT')  ← ❌ MASALAH
```

**Kenapa Error:**
- Route di `routes/web.php` line 155 mendefinisikan: `Route::post('/orders/{order}/status', ...)`
- Route hanya menerima method POST
- Tapi form menggunakan `@method('PUT')` yang mengubah method menjadi PUT
- Akibatnya: Laravel menolak dengan "Method Not Allowed"

**Solusi:**
```
SESUDAH (Fixed):
<form action="{{ route('admin.orders.updateStatus', $order) }}" method="POST">
    @csrf
    ← @method('PUT') dihapus ✅
```

**Testing:**
```
1. Login sebagai admin
2. Buka detail order
3. Pilih status dari dropdown
4. Klik tombol "Update"
5. ✅ Status berubah tanpa error
```

---

### Error 2: Tombol "Terima" Tidak Bekerja

**Lokasi:** Admin Orders Show Page - Payment Verification

**Penyebab:**
```
File: resources/views/admin/orders/show.blade.php (Baris 189)

SEBELUM (Bug):
<input type="hidden" name="status" value="accept">  ← ❌ MASALAH

OrderController->verifyPayment() line 82:
$validated = $request->validate([
    'status' => 'required|in:verified,rejected',  ← Hanya accept 'verified' atau 'rejected'
]);

Ketika form kirim status="accept":
→ Validasi gagal (karena "accept" bukan "verified" atau "rejected")
→ Tidak ada error message, form tidak terproses
→ Halaman reload tanpa perubahan
```

**Solusi:**
```
SESUDAH (Fixed):
<input type="hidden" name="status" value="verified">  ← ✅ Sesuai validasi
```

**Testing:**
```
1. Login sebagai admin
2. Buka order dengan payment_status = pending
3. Klik tombol "Terima (Paid)"
4. ✅ payment_status berubah menjadi "paid"
5. ✅ Button "Terima" hilang diganti dengan badge "✓ Pembayaran Terverifikasi"
```

---

## 📝 File-File Yang Dimodifikasi

1. **resources/views/admin/orders/show.blade.php**
   - Baris 49: Hapus `@method('PUT')`
   - Baris 189: Ubah `value="accept"` → `value="verified"`

---

## ✅ Verification Checklist

**Untuk verifikasi bahwa semua sudah berfungsi:**

- [ ] Admin bisa login dan akses menu orders
- [ ] Admin bisa lihat daftar pesanan
- [ ] Admin bisa buka detail order
- [ ] **Tombol "Terima (Paid)"** berfungsi:
  - [ ] Payment verification berhasil
  - [ ] Badge berubah ke "✓ Pembayaran Terverifikasi"
  - [ ] Tidak ada error
- [ ] **Tombol "Tolak Berkas"** berfungsi:
  - [ ] Modal form penolakan muncul
  - [ ] Bisa input alasan penolakan
  - [ ] Payment status berubah ke "rejected"
  - [ ] Customer bisa lihat alasan penolakan
- [ ] **Form Update Status** berfungsi:
  - [ ] Dropdown status berfungsi
  - [ ] Tombol "Update" tidak error
  - [ ] Status order berubah
  - [ ] Tidak ada MethodNotAllowedHttpException
- [ ] **Customer bisa lihat perubahan:**
  - [ ] Refresh halaman customer orders
  - [ ] Status badge berubah sesuai admin update
  - [ ] Timeline log terupdate

---

## 📚 Referensi Model & Database

### Model Order (`app/Models/Order.php`)
- `status`: pending → processed → shipped → completed (atau cancelled)
- `payment_status`: unpaid → pending → paid (atau rejected)
- Relations: user(), payment(), orderItems(), shippingAddress()

### Model Payment (`app/Models/Payment.php`)
- `status`: pending → verified (atau rejected)
- `proof`: File path bukti transfer
- `admin_notes`: Catatan dari admin (untuk rejected)
- `verified_at`: Timestamp verifikasi
- Methods: verify(), reject(), isVerified(), isRejected(), isPending()

### Database Schema
```sql
-- orders table
id | user_id | order_number | status | payment_status | total | created_at

-- payments table
id | order_id | payment_method | proof | status | admin_notes | verified_at

-- order_items table
id | order_id | product_id | qty | price
```

---

## 🎯 Summary

| Masalah | Penyebab | Solusi | Status |
|---------|---------|--------|--------|
| Update Status Error (PUT vs POST) | Form @method('PUT') vs Route POST | Hapus @method('PUT') | ✅ Fixed |
| Tombol Terima Tidak Bekerja | Form kirim "accept", validasi "verified" | Ubah value ke "verified" | ✅ Fixed |
| Status Tidak Berubah di Customer | Sama dengan error di atas | Setelah fix, status auto-update | ✅ Fixed |

---

**Updated:** 2026-05-31 | **Version:** 1.0
