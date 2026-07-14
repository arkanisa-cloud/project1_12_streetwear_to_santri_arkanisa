# STS — The E-Commerce Vault System

[![Laravel Version](https://img.shields.io/badge/Laravel-13.x-red.svg)](https://laravel.com)
[![Alpine.js Version](https://img.shields.io/badge/Alpine.js-3.x-blue.svg)](https://alpinejs.dev)
[![TailwindCSS Version](https://img.shields.io/badge/TailwindCSS-3.x-38bdf8.svg)](https://tailwindcss.com)

STS adalah aplikasi web e-commerce modern berorientasi performa tinggi yang dibangun menggunakan Laravel 13, Tailwind CSS, dan Alpine.js. Antarmuka pengguna (UI) mengadopsi gaya premium **Brutalist-Minimalism / Modern Vault Design**, yang menyajikan pengalaman berbelanja yang cepat, tegas, unik, dan elegan.

---

## 🚀 Fitur Unggulan Toko Online (Core E-Commerce Features)

Aplikasi ini dilengkapi dengan fitur-fitur utama toko online standar industri yang siap pakai untuk mendukung pengalaman berbelanja pengguna secara interaktif:

### 1. Kalkulator Ongkos Kirim Otomatis (Real-time Shipping Cost)
Sistem dapat menghitung biaya ongkos kirim secara otomatis dan *real-time* langsung di halaman checkout. Fitur ini terintegrasi dengan API logistik (Komerce/RajaOngkir mirror) yang mendukung berbagai kurir populer seperti JNE, J&T, POS Indonesia, TIKI, dan Lion Parcel. Ongkir akan langsung berubah secara dinamis begitu pengguna memilih kurir dan layanan yang tersedia.

### 2. Dropdown Alamat Bertingkat Dinamis (4-Level Cascading Address)
Saat menambahkan atau mengubah alamat pengiriman, pengguna disajikan dengan dropdown wilayah yang saling terhubung secara otomatis (Provinsi -> Kota/Kabupaten -> Kecamatan -> Kelurahan). Data wilayah ini dimuat secara efisien dari server dan diurutkan secara alfabetis (A-Z) untuk mempermudah pencarian alamat.

### 3. Manajemen Keranjang Belanja Akurat (Interactive Cart System)
Fitur keranjang belanja yang memungkinkan pengguna menambah, mengubah jumlah kuantitas (`qty`), atau menghapus item produk sebelum checkout. Sistem secara otomatis mengakumulasikan total harga barang serta total berat produk dalam satuan gram murni untuk memastikan perhitungan ongkir di tahap berikutnya tetap akurat.

### 4. Fleksibilitas Metode Pembayaran (Transfer Bank & COD)
Mendukung dua metode pembayaran utama yang paling sering digunakan di Indonesia, yaitu **Transfer Bank Manual** dan **Cash on Delivery (COD)**. Pilihan metode pembayaran terikat secara instan dengan sistem formulir checkout menggunakan Alpine.js untuk memastikan proses data aman.

### 5. Sistem Unggah Bukti Transfer & Verifikasi Status Order
Bagi pengguna yang memilih metode Transfer Bank, sistem menyediakan fitur khusus untuk mengunggah gambar bukti transfer (maksimal 2MB, format JPEG/PNG) langsung di halaman detail pesanan. Setelah bukti diunggah, status pembayaran otomatis berubah menjadi `pending` untuk menunggu proses verifikasi dan validasi oleh admin toko.

### 6. Alur Checkout Pintar & Proteksi Alamat
Sistem checkout dirancang responsif dan aman. Jika pengguna belum memiliki alamat pengiriman yang terdaftar, sistem akan memblokir tombol order dan memunculkan peringatan untuk menambahkan alamat terlebih dahulu. Dilengkapi juga dengan parameter memori pengalihan otomatis (`?redirect=checkout`) agar pengguna langsung diarahkan kembali ke halaman checkout setelah selesai membuat alamat baru.

---

## 🛠️ Tech Stack & Dependensi

- **Backend Framework:** Laravel 13 (PHP 8.2+)
- **Frontend Interactivity:** Alpine.js (Two-Way Data Binding)
- **Styling Architecture:** Tailwind CSS (Custom Vault Theme)
- **Database Engine:** MySQL / MariaDB
- **Logistics Engine:** Komerce / RajaOngkir API Mirror

---
