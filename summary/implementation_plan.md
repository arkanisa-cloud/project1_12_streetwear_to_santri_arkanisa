# Pembaruan UI, Responsivitas, dan Fitur (Customer & Admin)

Pembaruan besar-besaran untuk mengubah bahasa website menjadi Bahasa Indonesia yang profesional dan kekinian (youthful), menjadikan seluruh tampilan responsif di mobile, serta menambahkan DataTables dan Toastr.

## Open Questions
- Apakah Anda memiliki preferensi spesifik untuk skema warna/tema Toastr atau DataTables? Jika tidak, saya akan menggunakan tema minimalis bawaan yang selaras dengan tema "STS" (hitam/putih/abu-abu).

## Proposed Changes

### Layouts & Global Dependencies
- Tambahkan CDN jQuery, Toastr CSS & JS, dan DataTables CSS & JS ke layout utama.
- Tambahkan logic Toastr global untuk mendeteksi `session('success')` dan `session('error')`.

#### [MODIFY] `resources/views/layouts/app.blade.php`
- Tambahkan CDN jQuery & Toastr.

#### [MODIFY] `resources/views/layouts/customer.blade.php`
- Tambahkan CDN jQuery & Toastr.
- Tambahkan Hamburger Menu untuk navigasi mobile di header.
- Tangkap `session('success')` untuk Toastr otomatis.

#### [MODIFY] `resources/views/layouts/admin.blade.php`
- Tambahkan CDN jQuery, DataTables, & Toastr.
- Tambahkan Hamburger Menu Alpine.js untuk menampilkan/menyembunyikan sidebar di layar kecil (mobile-responsive).
- Tangkap `session('success')` untuk Toastr otomatis.

---

### Area Customer (Translasi & Fitur)

#### [MODIFY] `routes/web.php`
- Tambahkan method `PUT /customer/profile` yang diarahkan ke controller profile agar fitur update profil berfungsi.

#### [NEW] `app/Http/Controllers/Customer/ProfileController.php`
- Controller baru untuk menangani logika pembaruan profil user (Nama, Email, Telepon, Password).

#### [MODIFY] `resources/views/home.blade.php` (Customer Dashboard)
- Ubah bahasa ke Bahasa Indonesia bergaya kekinian (youthful).
- Pastikan produk yang habis stoknya (`stock == 0`) tidak ditampilkan.
- Pastikan produk diurutkan berdasarkan `sold` atau logika produk "Best Seller".

#### [MODIFY] `routes/web.php` & `app/Http/Controllers/HomeController.php` (atau closure root)
- Sesuaikan query pengambilan `$products` agar memfilter `stock > 0` dan mengurutkan berdasarkan produk terlaris. (Atau kita sesuaikan closure di `routes/web.php`).

#### [MODIFY] `resources/views/customer/product-detail.blade.php`
- Translate UI ke Bahasa Indonesia.
- Sesuaikan alert keranjang dengan notifikasi Toastr saat berhasil menambah keranjang.

#### [MODIFY] `resources/views/customer/cart.blade.php`
- Translate UI ke Bahasa Indonesia.
- Tampilan responsif pada device kecil (tabel/grid).

#### [MODIFY] `resources/views/customer/checkout.blade.php`
- Translate UI ke Bahasa Indonesia.
- Tambahkan trigger Toastr jika *submit* berhasil.

#### [MODIFY] `resources/views/customer/shipping-addresses/create.blade.php` & `index.blade.php`
- Translate UI ke Bahasa Indonesia.
- Tambahkan Toastr.

#### [MODIFY] `resources/views/customer/orders/show.blade.php`
- Fitur upload bukti pembayaran (`Upload Payment`): translate UI dan tambahkan Toastr setelah upload sukses.

#### [MODIFY] `resources/views/customer/profile.blade.php`
- Perbaiki form profil agar mengarah ke route yang baru dibuat (`PUT /customer/profile`).
- Buat tampilan responsif dan translate UI.
- Tambahkan trigger Toastr saat profil berhasil di-update.

---

### Area Admin (DataTables, Toastr & Mobile)

#### [MODIFY] `app/Http/Controllers/Admin/DashboardController.php` & `resources/views/admin/dashboard.blade.php`
- Perbaiki variabel yang dilempar (`$completedOrders`, `$pendingOrders`) yang sebelumnya error di view.
- Tambahkan Tabel Pesanan Terbaru (Latest Orders) menggunakan `$recentOrders`.
- Pastikan Dashboard responsif di layar mobile (grid layout responsif).

#### [MODIFY] `resources/views/admin/categories/index.blade.php`
#### [MODIFY] `resources/views/admin/products/index.blade.php`
#### [MODIFY] `resources/views/admin/stock-ins/index.blade.php`
#### [MODIFY] `resources/views/admin/stock-outs/index.blade.php`
#### [MODIFY] `resources/views/admin/orders/index.blade.php`
#### [MODIFY] `resources/views/admin/reports/stock.blade.php`
#### [MODIFY] `resources/views/admin/reports/sales.blade.php`
- Terapkan DataTables pada tabel-tabel data di seluruh halaman ini.
- Pastikan DataTables dapat di-scroll secara horizontal di tampilan mobile.
- Semua aksi CRUD di controller terkait akan mengirimkan `session('success')` yang otomatis memicu Toastr dari layout admin.

## Verification Plan
1. Buka halaman home/dashboard customer di ukuran mobile. Verifikasi navigasi responsif, bahasa, dan list produk ("Best Seller", stok ada).
2. Lakukan Add to Cart, Add Address, Submit Checkout, dan Upload Payment -> Verifikasi munculnya Toastr di sisi pojok kanan atas.
3. Akses halaman `/customer/profile`, update profil, lalu pastikan tersimpan dan memunculkan Toastr.
4. Login sebagai Admin, akses dari ukuran mobile. Buka hamburger menu.
5. Cek Admin Dashboard, pastikan data pesanan masuk, grafik berjalan, variabel tidak ada yang error.
6. Cek halaman manajemen (Kategori, Produk, Orders, Reports), pastikan tabel dapat difilter dan menggunakan DataTables.
7. Lakukan satu aksi CRUD di Admin, verifikasi notifikasi Toastr muncul.
