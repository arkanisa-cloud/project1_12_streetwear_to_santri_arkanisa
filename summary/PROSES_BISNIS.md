# 📋 PROSES BISNIS - Toko Online + Inventory

## 1. Pendahuluan

Dokumen ini menjelaskan **alur proses bisnis** yang terjadi dalam aplikasi Toko Online + Inventory Management. Pemahaman proses bisnis sangat penting sebelum mulai coding.

---

## 2. Aktor Sistem

### 2.1 Admin
**Peran**: Pengelola toko
**Akses**: Dashboard admin, kelola produk, stok, pesanan

### 2.2 Customer
**Peran**: Pembeli
**Akses**: Browse produk, belanja, tracking pesanan

### 2.3 Kurir (Opsional)
**Peran**: Pengirim barang
**Akses**: Update status pengiriman

---

## 3. Alur Proses Penjualan

### 3.1 Diagram Alur Penjualan

```
┌─────────────────────────────────────────────────────────────┐
│                    ALUR PENJUALAN                          │
└─────────────────────────────────────────────────────────────┘

CUSTOMER                              ADMIN
   │                                    │
   │ 1. REGISTER/LOGIN                  │
   ├─────────────────────────────────>  │
   │                                    │
   │ 2. BROWSE PRODUK                   │
   ├─────────────────────────────────>  │
   │     - Lihat katalog               │
   │     - Search produk               │
   │     - Filter kategori             │
   │                                    │
   │ 3. LIHAT DETAIL PRODUK            │
   ├─────────────────────────────────>  │
   │     - Cek harga                   │
   │     - Cek stok                    │
   │     - Baca deskripsi              │
   │                                    │
   │ 4. TAMBAH KE KERANJANG            │
   ├─────────────────────────────────>  │
   │     - Pilih qty                   │
   │     - Masukkan cart               │
   │                                    │
   │ 5. LIHAT KERANJANG                │
   ├─────────────────────────────────>  │
   │     - Review items                │
   │     - Update qty                  │
   │     - Hapus item                  │
   │                                    │
   │ 6. CHECKOUT                       │
   ├─────────────────────────────────>  │
   │     - Pilih alamat                │
   │     - Pilih metode bayar          │
   │                                    │
   │ 7. KONFIRMASI ORDER               │
   ├─────────────────────────────────>  │
   │     - Order dibuat                │
   │     - Stok BERKURANG              │
   │                                    │
   │ 8. UPLOAD BUKTI BAYAR             │
   ├─────────────────────────────────>  │
   │                                    │
   │                    9. VERIFIKASI  │
   │                    <──────────────┤
   │                                    │
   │                    10. PROSES     │
   │                    <──────────────┤
   │         - Siapkan barang          │
   │         - Input resi              │
   │                                    │
   │                    11. KIRIM      │
   │                    <──────────────┤
   │         - Status: shipped         │
   │                                    │
   │ 12. ORDER SELESAI                 │
   ├─────────────────────────────────>  │
   │     - Status: completed           │
   │                                    │
   │ 13. BERI ULASAN (opsional)        │
   ├─────────────────────────────────>  │
```

---

### 3.2 Detail Langkah Penjualan

#### LANGKAH 1: Customer Register/Login

**Tujuan**: Customer memiliki akun untuk berbelanja

**Aturan Bisnis**:
- Email harus unik
- Password minimal 8 karakter
- Role otomatis: customer

**Input**:
- Nama lengkap
- Email
- Password

**Output**:
- Akun customer dibuat
- Session login aktif

---

#### LANGKAH 2: Browse Produk

**Tujuan**: Customer melihat katalog produk

**Aturan Bisnis**:
- Tampilkan semua produk aktif
- Bisa search berdasarkan nama
- Bisa filter berdasarkan kategori
- Stok = 0 tidak ditampilkan (opsional)

**Tampilan**:
- Gambar produk
- Nama produk
- Harga
- Stok tersedia

---

#### LANGKAH 3: Lihat Detail Produk

**Tujuan**: Customer melihat info lengkap produk

**Aturan Bisnis**:
- Tampilkan semua info produk
- Kategori produk
- Deskripsi lengkap
- Stok tersedia real-time

**Validasi**:
- Jika stok = 0, tombol "Add to Cart" disabled

---

#### LANGKAH 4: Tambah ke Keranjang

**Tujuan**: Customer menambahkan produk ke cart

**Aturan Bisnis**:
- Qty minimal 1
- Qty maksimal = stok tersedia
- Jika produk sudah ada di cart, qty ditambah

**Logic**:
```
if (product sudah ada di cart) {
    qty_baru = qty_lama + qty_input
    if (qty_baru > stok) {
        throw error: "Stok tidak mencukupi"
    }
    update cart_item set qty = qty_baru
} else {
    insert cart_item baru
}
```

**Database**:
- cart_items: { cart_id, product_id, qty, price }

---

#### LANGKAH 5: Review Keranjang

**Tujuan**: Customer memastikan pesanan sebelum checkout

**Aturan Bisnis**:
- Bisa update qty
- Bisa hapus item
- Cek total harga
- Validasi stok lagi

**Perhitungan**:
```
subtotal per item = qty × harga saat add to cart
total cart = Σ subtotal semua items
```

---

#### LANGKAH 6: Checkout - Input Data

**Tujuan**: Customer mengisi data pengiriman & pembayaran

**Aturan Bisnis**:
- Alamat pengiriman wajib
- Metode pembayaran wajib dipilih
- Rekening transfer ditampilkan (jika transfer)

**Input**:
- Nama penerima
- No. HP
- Alamat lengkap
- Kota/Kabupaten
- Provinsi
- Kode pos
- Metode pembayaran

---

#### LANGKAH 7: Proses Checkout (TRANSACTION)

**Tujuan**: Membuat order & mengurangi stok

**CRITICAL SECTION**: Gunakan Database Transaction!

**Langkah-langkah**:

```
BEGIN TRANSACTION

1. Validasi stok lagi untuk setiap item
   for each cart_item:
       if (product.stock < cart_item.qty) {
           ROLLBACK
           throw error: "Stok tidak mencukupi"
       }

2. Buat order
   order_number = generate_order_number()
   total = hitung dari cart items
   status = 'pending'
   payment_status = 'unpaid'

3. Buat order_items
   for each cart_item:
       insert order_item {
           product_id,
           qty,
           price,
           subtotal = qty × price
       }

4. Kurangi stok produk
   for each cart_item:
       stock_before = product.stock
       stock_after = stock_before - cart_item.qty
       update product set stock = stock_after

5. Catat di stock_history
   for each cart_item:
       insert stock_history {
           type: 'sale',
           qty: cart_item.qty,
           stock_before: ...,
           stock_after: ...,
           reference_id: order.id,
           reference_type: 'Order'
       }

6. Buat payment record
   insert payment {
       order_id,
       payment_method,
       status: 'pending'
   }

7. Kosongkan cart
   delete from cart_items where cart_id = ...

COMMIT TRANSACTION
```

**Output**:
- Order berhasil dibuat
- Order number unik
- Stok produk berkurang
- Cart kosong
- Redirect ke halaman upload bukti bayar

---

#### LANGKAH 8: Upload Bukti Pembayaran

**Tujuan**: Customer mengupload bukti transfer

**Aturan Bisnis**:
- File berupa gambar (JPEG/PNG)
- Maksimal 2MB
- Hanya bisa upload untuk order sendiri

**Proses**:
```
1. Customer pilih file
2. Validasi file (type, size)
3. Upload ke storage/app/public/payments
4. Update payment table
   set proof = 'filename.jpg'
   set status = 'pending' (menunggu verifikasi)
5. Update order set payment_status = 'pending'
```

---

#### LANGKAH 9: Admin Verifikasi Pembayaran

**Tujuan**: Admin memverifikasi kebenaran pembayaran

**Aturan Bisnis**:
- Admin cek bukti transfer
- Sesuai nominal & tanggal → Verified
- Tidak sesuai → Rejected

**Proses**:
```
if (pembayaran valid) {
    update payment set:
        status = 'verified',
        verified_at = now()

    update order set:
        payment_status = 'paid'

    notifikasi customer: "Pembayaran berhasil"
} else {
    update payment set:
        status = 'rejected',
        admin_notes = 'alasan penolakan'

    update order set:
        payment_status = 'rejected'

    notifikasi customer: "Silakan upload ulang"
}
```

---

#### LANGKAH 10: Admin Proses Order

**Tujuan**: Admin menyiapkan barang untuk dikirim

**Aturan Bisnis**:
- Hanya order dengan payment_status = 'paid' yang bisa diproses
- Status berubah: pending → processed

**Proses**:
```
1. Cek list order yang paid
2. Pilih order yang akan diproses
3. Siapkan barang
4. Update order status = 'processed'
```

---

#### LANGKAH 11: Admin Kirim Barang

**Tujuan**: Barang dikirim ke customer

**Aturan Bisnis**:
- Input nomor resi
- Status berubah: processed → shipped

**Proses**:
```
1. Input nomor resi pengiriman
2. Update order:
   status = 'shipped'
   tracking_number = 'resi'

3. Kirim notifikasi ke customer:
   "Barang dikirim dengan no resi: XXX"
```

---

#### LANGKAH 12: Order Selesai

**Tujuan**: Pesanan diterima customer

**Aturan Bisnis**:
- Customer konfirmasi barang diterima
- Atau otomatis setelah X hari
- Status: shipped → completed

**Proses**:
```
update order set status = 'completed'
```

---

## 4. Alur Proses Inventory

### 4.1 Diagram Alur Inventory

```
┌──────────────────────────────────────────────────────────┐
│                    ALUR INVENTORY                       │
└──────────────────────────────────────────────────────────┘

ADMIN                                          SISTEM
  │                                              │
  │ 1. STOK MASUK                                │
  ├────────────────────────────────────────────>│
  │     - Input data masuk                       │
  │     - Pilih supplier (opsional)              │
  │     - Input qty                              │
  │                                              │
  │                 2. VALIDASI                  │
  │                 <─────────────────────────────┤
  │     - Qty harus > 0                          │
  │     - Product ada                            │
  │                                              │
  │                 3. PROSES                    │
  │                 <─────────────────────────────┤
  │     stock_baru = stock_lama + qty_masuk     │
  │                                              │
  │                 4. SIMPAN                    │
  │                 <─────────────────────────────┤
  │     - Insert stock_ins                       │
  │     - Update product.stock                   │
  │     - Insert stock_history                   │
  │                                              │
  │ 5. STOK KELUAR (NON-SALE)                    │
  ├────────────────────────────────────────────>│
  │     - Rusak, hilang, kadaluarsa              │
  │     - Input qty                              │
  │     - Pilih alasan                           │
  │                                              │
  │                 6. VALIDASI                  │
  │                 <─────────────────────────────┤
  │     - Qty harus > 0                          │
  │     - Stock cukup                            │
  │                                              │
  │                 7. PROSES                    │
  │                 <─────────────────────────────┤
  │     if (qty > stock) {                       │
  │         throw error: "Stok tidak cukup"     │
  │     }                                        │
  │     stock_baru = stock_lama - qty_keluar    │
  │                                              │
  │                 8. SIMPAN                    │
  │                 <─────────────────────────────┤
  │     - Insert stock_outs                      │
  │     - Update product.stock                   │
  │     - Insert stock_history                   │
  │                                              │
  │                                              │
┌─┴──────────────────────────────────────────────┐
│      PENJUALAN (OTOMATIS)                      │
├────────────────────────────────────────────────┤
│                                               │
│  Customer checkout → Stok otomatis berkurang  │
│                                               │
│  Logic:                                       │
│  stock_baru = stock_lama - qty_order          │
│                                               │
│  Validasi:                                    │
│  if (qty_order > stock) {                     │
│      rollback transaction                     │
│      throw error: "Stok tidak cukup"         │
│  }                                            │
│                                               │
└───────────────────────────────────────────────┘
```

---

### 4.2 Detail Proses Stok Masuk

#### Tujuan
Mencatat barang masuk dari supplier

#### Aturan Bisnis
- Qty harus positif (> 0)
- Tanggal masuk tidak boleh masa depan
- Supplier bisa null (bisa dari supplier lain)

#### Proses

**STEP 1: Input Data**
```
Input:
- Product
- Supplier (opsional)
- Tanggal masuk
- Qty
- Keterangan
```

**STEP 2: Validasi**
```php
qty > 0
tanggal_masuk <= today()
product exists in database
```

**STEP 3: Hitung Stok Baru**
```
stock_lama = product->stock
qty_masuk = input->qty
stock_baru = stock_lama + qty_masuk

Contoh:
stock_lama = 10
qty_masuk = 5
stock_baru = 10 + 5 = 15
```

**STEP 4: Simpan ke Database (Gunakan Transaction)**

```sql
BEGIN TRANSACTION;

-- 1. Insert ke stock_ins
INSERT INTO stock_ins (
    product_id,
    supplier_id,
    tanggal_masuk,
    qty,
    keterangan
) VALUES (?, ?, ?, ?, ?);

-- 2. Update stok produk
UPDATE products
SET stock = stock + ?
WHERE id = ?;

-- 3. Catat history
INSERT INTO stock_histories (
    product_id,
    type,
    qty,
    stock_before,
    stock_after,
    reference_id,
    reference_type
) VALUES (?, 'in', ?, ?, ?, ?, 'StockIn');

COMMIT;
```

**STEP 5: Feedback**
- Tampilkan pesan sukses
- Redirect ke list stok masuk

---

### 4.3 Detail Proses Stok Keluar

#### Tujuan
Mencatat barang keluar (bukan karena penjualan)

#### Aturan Bisnis
- Qty harus positif (> 0)
- Stok harus mencukupi
- Harus pilih alasan

#### Alasan Keluar
- **Rusak**: Barang cacat/rusak
- **Hilang**: Barang hilang/dicuri
- **Kadaluarsa**: Barang expired
- **Lainnya**: Alasan lain

#### Proses

**STEP 1: Input Data**
```
Input:
- Product
- Tanggal keluar
- Qty
- Alasan
- Keterangan
```

**STEP 2: Validasi**
```php
qty > 0
product exists
stock >= qty  ← CRITICAL!
tanggal_keluar <= today()
```

**STEP 3: Cek Stok**
```
if (qty > product->stock) {
    throw error: "Stok tidak mencukupi!"
}
```

**STEP 4: Hitung Stok Baru**
```
stock_lama = product->stock
qty_keluar = input->qty
stock_baru = stock_lama - qty_keluar

Contoh:
stock_lama = 15
qty_keluar = 3
stock_baru = 15 - 3 = 12
```

**STEP 5: Simpan ke Database (Transaction)**

```sql
BEGIN TRANSACTION;

-- 1. Insert ke stock_outs
INSERT INTO stock_outs (
    product_id,
    tanggal_keluar,
    qty,
    alasan,
    keterangan
) VALUES (?, ?, ?, ?, ?);

-- 2. Update stok produk
UPDATE products
SET stock = stock - ?
WHERE id = ?;

-- 3. Catat history
INSERT INTO stock_histories (
    product_id,
    type,
    qty,
    stock_before,
    stock_after,
    reference_id,
    reference_type
) VALUES (?, 'out', ?, ?, ?, ?, 'StockOut');

COMMIT;
```

**STEP 6: Feedback**
- Tampilkan pesan sukses
- Redirect ke list stok keluar

---

### 4.4 Perbedaan Stok Keluar vs Penjualan

| Aspect | Stok Keluar | Penjualan |
|--------|-------------|-----------|
| **Trigger** | Manual input admin | Customer checkout |
| **Alasan** | Rusak, hilang, kadaluarsa | Terjual |
| **Table** | stock_outs | order_items |
| **History Type** | 'out' | 'sale' |
| **Reference** | stock_out_id | order_id |
| **Dampak Revenue** | Tidak ada | Ada penambahan revenue |

---

## 5. Aturan Bisnis Penting

### 5.1 Aturan Stok

1. **Stok Tidak Boleh Negatif**
   ```
   stock >= 0 SELALU!
   ```

2. **Validasi Sebelum Transaksi**
   ```php
   if (qty > product->stock) {
       throw error
   }
   ```

3. **Gunakan Database Transaction**
   ```php
   DB::beginTransaction()
   try {
       // operasi database
       DB::commit()
   } catch {
       DB::rollback()
   }
   ```

4. **Catat Semua Perubahan di stock_history**
   - Type: 'in', 'out', atau 'sale'
   - Simpan stock_before dan stock_after
   - Simpan reference_id untuk traceability

---

### 5.2 Aturan Pembayaran

1. **Order tidak bisa diproses jika belum bayar**
   ```
   if (payment_status != 'paid') {
       cannot process order
   }
   ```

2. **Admin harus verifikasi bukti transfer**
   - Status: 'pending' → 'verified' atau 'rejected'

3. **Customer bisa upload ulang jika ditolak**
   ```
   if (payment_status == 'rejected') {
       customer can re-upload
   }
   ```

---

### 5.3 Aturan Order Status

| Status | Keterangan | Bisa Edit? |
|--------|-----------|------------|
| `pending` | Order baru, belum diproses | Ya |
| `processed` | Sedang disiapkan | Ya |
| `shipped` | Sudah dikirim | Ya |
| `completed` | Selesai | Tidak |
| `cancelled` | Dibatalkan | Tidak |

---

## 6. Edge Cases & Exception Handling

### 6.1 Stok Tidak Cukup Saat Checkout

**Masalah**: Customer add to cart, stok habis saat checkout

**Solusi**:
```php
// Validasi stok lagi saat checkout
foreach (cart_items as item) {
    if (item->qty > item->product->stock) {
        throw error: "Stok {$product->name} tidak mencukupi"
    }
}
```

---

### 6.2 Race Condition (2 Customer Beli Bareng)

**Masalah**: 2 customer checkout produk yang sama bersamaan

**Solusi**:
- Gunakan Database Transaction dengan locking
```php
DB::transaction(function () {
    // Lock row product
    $product = Product::lockForUpdate()->find($id);

    // Cek stok
    if ($product->stock < $qty) {
        throw error
    }

    // Update stok
    $product->decrement('stock', $qty);
});
```

---

### 6.3 Payment Timeout

**Masalah**: Customer upload bukti tapi admin lama verifikasi

**Solusi**:
- Tampilkan tanggal upload
- Send reminder ke admin setelah 24 jam
- Auto-cancel setelah 7 hari (opsional)

---

## 7. Formula Bisnis

### 7.1 Perhitungan Cart

```
Subtotal per Item = qty × harga

Total Cart = Σ (qty × harga) untuk semua items

Total Items = Σ qty untuk semua items
```

**Contoh**:
```
Item 1: 2 × Rp 10.000 = Rp 20.000
Item 2: 1 × Rp 50.000 = Rp 50.000
Item 3: 3 × Rp 5.000 = Rp 15.000

Total Cart = Rp 20.000 + Rp 50.000 + Rp 15.000
           = Rp 85.000

Total Items = 2 + 1 + 3 = 6 items
```

---

### 7.2 Perhitungan Order

```
Order Total = Σ (order_item.qty × order_item.price)

Setiap order_item menyimpan:
- qty: jumlah
- price: harga saat transaksi
- subtotal: qty × price
```

**Kenapa simpan price?**
Karena harga produk bisa berubah di masa depan. Kita simpan harga transaksi untuk keperluan histori.

---

### 7.3 Perhitungan Stok

```
STOK AWAL = 0

Stok Masuk:
stok = stok + qty_masuk

Stok Keluar (non-sale):
stok = stok - qty_keluar

Penjualan:
stok = stok - qty_terjual
```

**Contoh Lengkap**:
```
Awal:        0
Masuk:      +10  → 10
Masuk:       +5  → 15
Keluar:      -2  → 13
Terjual:     -3  → 10
Masuk:      +20  → 30
Terjual:     -5  → 25
```

---

## 8. Checklist Implementasi

Berikan checklist ini ke siswa:

### Week 1-2: Setup & Auth
- [ ] Install Laravel
- [ ] Setup database
- [ ] Install Breeze
- [ ] Membuat migration users (add role)
- [ ] Testing register/login

### Week 3-4: CRUD Dasar
- [ ] Migration categories & products
- [ ] Model Category & Product
- [ ] CategoryController (CRUD)
- [ ] ProductController (CRUD)
- [ ] Upload gambar produk
- [ ] Views: list, create, edit, delete

### Week 5-6: Cart & Checkout
- [ ] Migration cart & cart_items
- [ ] CartController
- [ ] Add to cart functionality
- [ ] Update qty cart
- [ ] Migration orders & order_items
- [ ] CheckoutController
- [ ] Checkout process (transaction)

### Week 7-8: Inventory
- [ ] Migration stock_ins & stock_outs
- [ ] Migration stock_histories
- [ ] StockInController
- [ ] StockOutController
- [ ] Validasi stok tidak minus
- [ ] Stock history tracking

### Week 9-10: Admin Features
- [ ] Admin Dashboard
- [ ] Order management
- [ ] Payment verification
- [ ] Reports (stok & penjualan)

### Week 11-12: Testing & Deployment
- [ ] Unit tests
- [ ] Feature tests
- [ ] Deployment ke shared hosting/VPS

---

## 9. Pertanyaan Diskusi untuk Siswa

1. **Kenapa perlu transaction saat checkout?**
   - Diskusi: apa yang terjadi jika step 3 gagal?

2. **Kenapa simpan harga di order_item, bukan ambil dari product?**
   - Diskusi: apa yang terjadi jika harga produk berubah?

3. **Apa yang terjadi jika 2 customer checkout bersamaan?**
   - Diskusi: race condition dan solusinya

4. **Kenapa perlu stock_histories?**
   - Diskusi: audit trail dan traceability

5. **Bagaimana handle barang yang rusak?**
   - Diskusi: stok_out vs penjualan

---

**Dokumen ini adalah fondasi. Pahami dulu alur bisnisnya, baru coding! 🎓**
