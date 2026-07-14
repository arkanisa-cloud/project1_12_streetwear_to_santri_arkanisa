# Dokumentasi Alur & Logika Profil Admin

Halaman profil admin dirancang untuk memberikan kendali penuh kepada administrator atas informasi identitas dan keamanan akun mereka, serupa dengan sistem profil pelanggan namun disesuaikan dengan konteks administrasi.

## 1. Komponen Utama

### A. Controller: `Admin\ProfileController`
Bertanggung jawab untuk menangani permintaan tampilan dan pembaruan data.
- **`index()`**: Mengambil data pengguna yang sedang login (`auth()->user()`) dan mengirimkannya ke view.
- **`update()`**: 
    - Melakukan validasi input (Nama, Email, Avatar, dan Password).
    - Menangani upload file avatar (menyimpan di storage `public/avatars` dan menghapus file lama jika ada).
    - Memperbarui informasi dasar pengguna.
    - Melakukan pengecekan keamanan jika ingin mengubah password (memerlukan password saat ini).
    - Melakukan hashing password baru menggunakan `Hash::make()`.

### B. View: `admin.profile.index`
Menggunakan **Alpine.js** untuk manajemen state di sisi klien:
- **`isEditing`**: Mengatur tampilan antara mode "Read-Only" (Vault) dan "Interactive Mode" (Form Edit).
- **Transisi**: Menggunakan `x-transition` untuk memberikan efek visual yang halus saat berpindah mode.
- **Styling**: Menggunakan Tailwind CSS dengan pendekatan "Modern Identity Card" (Glassmorphism, dark theme, sharp borders).

### C. Route: `routes/web.php`
Ditempatkan di dalam middleware group `admin` untuk memastikan hanya pengguna dengan role admin yang dapat mengaksesnya.
- `GET /admin/profile`: Menampilkan halaman.
- `PUT /admin/profile`: Memproses data yang dikirim dari form.

## 2. Alur Kerja (Flow)

1. **Akses Halaman**: Admin mengklik logo profil di header atau menu "Admin Profile" di sidebar.
2. **Tampilan Awal**: Menampilkan informasi profil dalam kotak statis (Mode Vault).
3. **Trigger Edit**: Admin mengklik tombol "Edit" (ikon pensil). Alpine.js mengubah `isEditing` menjadi `true`.
4. **Pengisian Form**: Admin dapat mengubah nama, email, mengunggah foto baru, atau mengisi field password untuk keamanan.
5. **Validasi & Simpan**: Saat form dikirim, Laravel memvalidasi data. Jika sukses, data diupdate di database dan admin diarahkan kembali dengan notifikasi sukses (Toastr).
6. **Keamanan Password**: Jika field password diisi, sistem mewajibkan pengisian `current_password` yang akan divalidasi langsung oleh Laravel menggunakan rule `current_password`.

## 3. Fitur Navigasi (Animated Dropdown)

Navigasi profil di header layout admin diimplementasikan menggunakan Alpine.js:
- **Trigger**: Tombol profil dengan avatar/inisial admin.
- **State**: `profileOpen` (toggle on click).
- **Animasi**: Menggunakan kombinasi `opacity`, `scale`, dan `translate-y` untuk menciptakan efek menu yang muncul dari bawah tombol profil dengan gerakan yang elegan.
- **Opsi**: Menyediakan akses cepat ke halaman Profil dan tombol Logout.

---
*Dibuat untuk sistem STS (Seventy Seven Streetwear) - 2026 Archive*
