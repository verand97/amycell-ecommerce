# 📱 Amycell E-Commerce & HP Repair Service Platform

Amycell adalah platform e-commerce modern yang mengintegrasikan penjualan produk digital & fisik dengan sistem manajemen **Jasa Servis HP** (Smartphone Repair Service) secara terpadu, lengkap dengan dashboard admin yang komprehensif dan widget **Live Chat** real-time.

Platform ini dibangun menggunakan framework **Laravel 13** dan sistem styling termodern **Tailwind CSS v4**.

---

## ✨ Fitur Utama

### 🛒 1. E-Commerce (Belanja Online)
- **Katalog Produk**: Tampilan produk fisik dan digital yang responsif dengan filter kategori dan pencarian.
- **Keranjang Belanja (Shopping Cart)**: Manajemen kuantitas produk dinamis dan interaktif tanpa reload halaman.
- **Checkout & Transaksi**: Alur pemesanan produk fisik dan digital, lengkap dengan pengiriman bukti transfer pembayaran.

### 🔧 2. Jasa Servis HP (Smartphone Repair)
- **Pengajuan Servis (Customer)**: Form pengajuan servis online dengan detail tipe HP, keluhan, estimasi kerusakan, dan upload foto perangkat.
- **Timeline Pelacakan Real-Time**: Pelanggan dapat melacak status servis secara langsung (Pending -> Diterima -> Didiagnosa -> Dikerjakan -> Selesai -> Diambil/Dikirim).
- **Admin Service Dashboard**:
  - Konfirmasi penerimaan perangkat.
  - Update status pengerjaan, estimasi biaya, dan catatan internal admin.
  - Notifikasi progres pengerjaan servis.

### 💬 3. Sistem Live Chat Real-Time
- **Widget Chat Pelanggan**: Akses instan dari halaman customer untuk berkonsultasi langsung dengan admin.
- **Admin Chat Panel**: Ruang obrolan khusus admin dengan sistem antrean sesi obrolan (waiting/active) berbasis Laravel Events & Broadcasting.

### 📊 4. Panel Administrasi (Admin Dashboard)
- **Dasbor Utama**: Statistik penjualan, total pendapatan, grafik transaksi, dan antrean servis/chat.
- **Manajemen Produk**: CRUD produk fisik & digital (dengan upload file untuk produk digital).
- **Manajemen Kategori**: Pengaturan kategori dinamis dengan emoji sebagai ikon.
- **Verifikasi Transaksi**: Validasi bukti pembayaran bank transfer secara real-time untuk memproses pesanan otomatis.

---

## 🛠️ Tech Stack & Arsitektur

- **Backend**: Laravel 13.x (PHP 8.2+)
- **Database**: SQLite (default untuk efisiensi local development) atau MySQL/PostgreSQL
- **Frontend**: HTML5, Blade Templates, JavaScript (Vanilla ES6)
- **Styling**: Tailwind CSS v4 (Sistem utility-first modern berbasis CSS) & Custom Glassmorphism
- **Asset Bundler**: Vite (Rolldown engine)
- **Fonts**: Google Fonts (Inter / Outfit)

---

## 🚀 Panduan Instalasi Lokal

Ikuti langkah-langkah di bawah ini untuk menjalankan proyek Amycell di lingkungan lokal Anda:

### 1. Prasyarat (Prerequisites)
Pastikan Anda sudah menginstal:
- PHP >= 8.2 (dengan ekstensi `pdo_sqlite`, `sqlite3`, `mbstring`, `xml`, `openssl` aktif)
- Composer (Dependency Manager untuk PHP)
- Node.js & npm (untuk mengompilasi aset frontend)

### 2. Kloning Repositori
```bash
git clone https://github.com/verand97/amycell-ecommerce.git
cd amycell-ecommerce
```

### 3. Instal Dependensi Backend & Frontend
```bash
# Instal dependensi PHP
composer install

# Instal dependensi Node.js
npm install
```

### 4. Konfigurasi Lingkungan (`.env`)
Salin file `.env.example` menjadi `.env`:
```bash
cp .env.example .env
```
Secara default, konfigurasi database menggunakan **SQLite**. Buat file database kosong:
```bash
# Di Windows (PowerShell)
New-Item -ItemType File -Path database/database.sqlite -Force

# Di Linux / macOS / Git Bash
touch database/database.sqlite
```

Lalu sesuaikan baris berikut di `.env` Anda jika perlu:
```env
DB_CONNECTION=sqlite
DB_DATABASE=database/database.sqlite
```

### 5. Generate Application Key & Jalankan Migrasi
```bash
# Generate key aplikasi
php artisan key:generate

# Jalankan migrasi database beserta seeder awal
php artisan migrate --seed
```

### 6. Jalankan Server Development
Jalankan server backend Laravel:
```bash
php artisan serve
```
Dan jalankan dev server Vite untuk kompilasi aset CSS/JS real-time:
```bash
npm run dev
```

Buka browser Anda dan akses `http://127.0.0.1:8000`.

---

## 📂 Struktur Direktori Penting

```txt
amycell-ecommerce/
├── app/
│   ├── Http/Controllers/
│   │   ├── Admin/            # Controller panel manajemen admin (Produk, Servis, Transaksi)
│   │   └── Customer/         # Controller sisi pelanggan (Katalog, Keranjang, Servis)
│   └── Models/               # Model database Eloquent (Product, ServiceOrder, Transaction, ChatSession)
├── database/
│   ├── migrations/           # File migrasi database
│   └── seeders/              # Data awal untuk pengujian
├── resources/
│   ├── css/
│   │   └── app.css           # Konfigurasi Tailwind CSS v4 & custom animations
│   ├── js/
│   │   └── app.js            # Inisialisasi frontend JS
│   └── views/                # Template Blade
│       ├── admin/            # Tampilan panel admin
│       ├── auth/             # Halaman Login, Register, dll.
│       └── customer/         # Tampilan e-commerce dan pendaftaran servis
└── routes/
    └── web.php               # Routing utama web
```

---

## 🛡️ Keamanan & Lisensi
Proyek ini dibangun untuk tujuan e-commerce & portofolio perbaikan smartphone. Berlisensi di bawah [MIT License](LICENSE).
