# PRD — CRM Layanan Akademik Mahasiswa
## Prodi Sistem Informasi

**Versi:** 1.0  
**Teknologi:** PHP · Laravel · JavaScript · HTML · CSS  
**Scope Dokumen:** Frontend Design, UI Requirements, Code Features, User Flow

---

## 1. Design System & Frontend

### 1.1 Palet Warna

| Token | Hex | Penggunaan |
|---|---|---|
| `--primary` | `#1E3A5F` | Sidebar, header, tombol utama |
| `--primary-light` | `#2E5B8A` | Hover state, aksen |
| `--accent` | `#25D366` | Tombol WhatsApp, notifikasi aktif |
| `--accent-warning` | `#F59E0B` | Badge cuti, pengingat |
| `--accent-danger` | `#EF4444` | Badge DO, error |
| `--accent-success` | `#10B981` | Mahasiswa aktif, sukses |
| `--bg-base` | `#F1F5F9` | Background halaman |
| `--bg-card` | `#FFFFFF` | Card, modal, tabel |
| `--text-primary` | `#1E293B` | Teks utama |
| `--text-muted` | `#64748B` | Label, placeholder |

### 1.2 Tipografi

```
Font Utama : Inter (Google Fonts)
Font Mono  : JetBrains Mono (tabel data NIM, kode)

H1  → 28px / 700 / line-height 1.3
H2  → 22px / 600
H3  → 18px / 600
Body → 14px / 400
Small → 12px / 400 / color: --text-muted
```

### 1.3 Layout Global

```
┌──────────────────────────────────────────────────────┐
│  TOPBAR  (64px)  Logo | Breadcrumb | Notif | Avatar  │
├───────────┬──────────────────────────────────────────┤
│           │                                          │
│  SIDEBAR  │         CONTENT AREA                     │
│  (240px)  │         (fluid, padding 24px)            │
│           │                                          │
│  collapsed│                                          │
│  → 64px   │                                          │
└───────────┴──────────────────────────────────────────┘
```

- Sidebar collapsible dengan smooth transition 300ms
- Responsive breakpoint: ≤768px sidebar jadi drawer overlay
- Content area max-width: 1280px, auto center

### 1.4 Komponen UI

**Card**
```css
border-radius: 12px;
box-shadow: 0 1px 3px rgba(0,0,0,0.08), 0 1px 2px rgba(0,0,0,0.06);
padding: 20px 24px;
background: var(--bg-card);
```

**Tombol Primer**
```css
background: var(--primary);
color: white;
border-radius: 8px;
padding: 10px 20px;
font-weight: 600;
transition: background 200ms;
```

**Badge Status Mahasiswa**
```
Aktif          → bg #D1FAE5  text #065F46  border #6EE7B7
Cuti           → bg #FEF3C7  text #92400E  border #FCD34D
Drop Out       → bg #FEE2E2  text #991B1B  border #FCA5A5
Tanpa Keterangan → bg #F1F5F9 text #475569 border #CBD5E1
```

**Input Field**
```css
border: 1.5px solid #CBD5E1;
border-radius: 8px;
padding: 10px 14px;
font-size: 14px;
/* focus → border-color: var(--primary) + ring shadow */
```

---

## 2. Halaman & Requirements

### 2.1 Halaman Login

**Layout:** Full-screen split — sisi kiri ilustrasi/branding, sisi kanan form.

**Elemen UI:**
- Logo prodi + nama institusi (atas)
- Judul: "Sistem Informasi Akademik" (H1)
- Field **Nama Pengguna** (text input, required)
- Field **Password** (password input + toggle show/hide)
- Tombol **Masuk** (lebar penuh)
- Teks versi kecil di bawah: "CRM Akademik © 2024 — Prodi Sistem Informasi"

**Validasi Frontend:**
- Keduanya wajib diisi — tampilkan pesan error inline di bawah field
- Disable tombol saat request sedang diproses (loading spinner)
- Shake animation pada form jika login gagal

**Requirement Teknis (Laravel):**
```php
// Route
POST /login → Auth\LoginController@login

// Validasi
$request->validate([
    'name'     => 'required|string',
    'password' => 'required|min:6',
]);

// Session
Auth::attempt(['name' => $name, 'password' => $password])
```

---

### 2.2 Dashboard

**Layout:** Grid statistik di atas, kemudian grafik + tabel ringkasan.

**Komponen:**

**Stat Cards (row 4 kolom)**
```
┌──────────┐ ┌──────────┐ ┌──────────┐ ┌──────────┐
│  Aktif   │ │   Cuti   │ │    DO    │ │ Tanpa Ket│
│   icon   │ │   icon   │ │   icon   │ │   icon   │
│  [angka] │ │  [angka] │ │  [angka] │ │  [angka] │
│  +2.1%   │ │  +0.5%   │ │   ±0%   │ │  -1.2%   │
└──────────┘ └──────────┘ └──────────┘ └──────────┘
```

**Grafik Tren (row bawah):**
- Kiri: Line chart — jumlah mahasiswa per status per semester (Chart.js)
- Kanan: Doughnut chart — proporsi status saat ini

**Tabel Pengajuan Terbaru:**
- Kolom: No | Nama | NIM | Jenis Layanan | Status | Tanggal | Aksi
- Pagination 10 rows per page
- Status badge warna-warni

**Pengingat Aktif (sidebar widget):**
- List mahasiswa yang masa cutinya akan berakhir ≤30 hari
- Tombol "Kirim Notif WA" di tiap baris

---

### 2.3 Data Mahasiswa

**Tab navigasi horizontal:**
```
[ Semua ] [ Aktif ] [ Cuti ] [ Drop Out ] [ Tanpa Keterangan ]
```

**Toolbar:**
- Search bar (cari nama / NIM)
- Filter dropdown: Angkatan, Semester
- Tombol **+ Tambah Mahasiswa** (kanan)
- Tombol **Export Excel**

**Tabel:**

| # | Foto | Nama | NIM | Angkatan | Semester | Status | Aksi |
|---|------|------|-----|----------|----------|--------|------|

- Kolom status menggunakan badge komponen
- Kolom aksi: ikon Edit (pensil) · Detail (mata) · Hapus (trash) dengan tooltip
- Klik baris → buka side drawer detail mahasiswa

**Side Drawer Detail Mahasiswa:**
```
┌─────────────────────────────────┐
│ ← Kembali          [Edit] [X]   │
│                                 │
│  [Foto Avatar]                  │
│  Nama Lengkap                   │
│  NIM · Angkatan                 │
│  Badge Status                   │
│                                 │
│  ── Info Akademik ──            │
│  Semester   : 6                 │
│  SKS Tempuh : 110               │
│  IPK        : 3.45              │
│                                 │
│  ── Riwayat Status ──           │
│  Timeline vertikal              │
│                                 │
│  ── Kontak ──                   │
│  No. WA: 0812-xxxx              │
│  [Hubungi via WhatsApp]         │
└─────────────────────────────────┘
```

**Form Tambah/Edit Mahasiswa (Modal):**
- Nama Lengkap, NIM, Angkatan, Semester, Status, No. WhatsApp, Email, Alamat, Foto

---

### 2.4 Pengajuan Layanan

**Jenis Layanan yang Didukung:**
1. Surat Keterangan Aktif
2. Pengajuan Cuti Akademik
3. Permohonan Aktif Kembali
4. Surat Pengantar Penelitian
5. Legalisasi Dokumen
6. Permohonan Bebas Pustaka

**Tampilan:**
- Kartu layanan bergrid (2 kolom) dengan ikon, nama, dan deskripsi singkat
- Klik kartu → buka modal form pengajuan

**Form Pengajuan:**
```
Mahasiswa    : [Search autocomplete by NIM/Nama]
Jenis Layanan: [Dropdown]
Keterangan   : [Textarea]
Lampiran     : [File upload, maks 5MB, .pdf/.jpg]
              [Tombol Ajukan]
```

**Tabel Riwayat Pengajuan:**
- Filter: Status (Menunggu / Diproses / Selesai / Ditolak), Tanggal
- Badge status + tombol aksi (Detail, Update Status, Unduh Surat)

---

### 2.5 Pengingat Cuti

**Fungsi:** Sistem otomatis menandai mahasiswa yang masa cutinya akan/sudah habis.

**Tampilan Halaman:**
- Header: "Pengingat Masa Cuti" dengan jumlah aktif
- Tabel mahasiswa cuti + kolom "Sisa Hari"
- Highlight merah jika sisa ≤ 7 hari, kuning jika ≤ 30 hari

**Kolom Tabel:**
| Nama | NIM | Mulai Cuti | Akhir Cuti | Sisa Hari | Status | Aksi WA |

**Aksi per baris:**
- Tombol "Kirim Pengingat WA" → generate link WhatsApp dengan pesan template
- Tombol "Perpanjang Cuti"
- Tombol "Aktifkan Kembali"

**Scheduled Job (Laravel):**
```php
// app/Console/Kernel.php
$schedule->command('cuti:check-reminder')->daily();
// Kirim notif H-30, H-7, H-1
```

---

### 2.6 Kontak Layanan (Management Contact)

**Fungsi:** Kelola daftar kontak staf/dosen yang bisa dihubungi mahasiswa via WhatsApp.

**Fitur Utama:**

**Auto Play / Live Preview:**
- Saat admin mengetik pesan template → preview bubble chat WhatsApp muncul real-time di sisi kanan
- Simulasi tampilan pesan WhatsApp (warna hijau bubble, font, timestamp)

**Daftar Kontak:**
```
┌──────────────────────────────────────────────┐
│ [+] Tambah Kontak           [Search kontak]  │
├──────────────────────────────────────────────┤
│ 📞 Admin Akademik                            │
│    0812-xxxx-xxxx  · Aktif                  │
│    [Edit] [Preview WA] [Nonaktifkan]         │
│                                              │
│ 📞 Koordinator Prodi                        │
│    0813-xxxx-xxxx  · Aktif                  │
│    [Edit] [Preview WA] [Nonaktifkan]         │
└──────────────────────────────────────────────┘
```

**Form Tambah Kontak:**
- Nama Kontak
- Jabatan/Peran
- Nomor WhatsApp (format: 628xxxxxxxxxx)
- Pesan Template (textarea dengan variabel: `{nama_mahasiswa}`, `{nim}`, `{keperluan}`)
- Toggle: Aktif / Nonaktif

**Auto Play — Template Preview:**
```javascript
// Real-time preview saat admin mengetik template
messageInput.addEventListener('input', () => {
  const previewText = template
    .replace('{nama_mahasiswa}', 'Budi Santoso')
    .replace('{nim}', '2021001001')
    .replace('{keperluan}', '[Keperluan]');
  previewBubble.textContent = previewText;
});
```

**Generate WhatsApp Link:**
```javascript
// format: https://wa.me/{nomor}?text={pesan_encode}
function generateWALink(phone, template, studentData) {
  const message = template
    .replace('{nama_mahasiswa}', studentData.name)
    .replace('{nim}', studentData.nim)
    .replace('{keperluan}', studentData.need);
  return `https://wa.me/${phone}?text=${encodeURIComponent(message)}`;
}
```

---

## 3. Code Features

### 3.1 Struktur Direktori Laravel

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Auth/LoginController.php
│   │   ├── DashboardController.php
│   │   ├── MahasiswaController.php
│   │   ├── PengajuanController.php
│   │   ├── CutiReminderController.php
│   │   └── KontakController.php
│   └── Middleware/
│       └── CheckRole.php
├── Models/
│   ├── User.php
│   ├── Mahasiswa.php
│   ├── Pengajuan.php
│   ├── CutiRecord.php
│   └── KontakLayanan.php
├── Console/Commands/
│   └── CutiReminderCheck.php
resources/
├── views/
│   ├── layouts/app.blade.php
│   ├── auth/login.blade.php
│   ├── dashboard/index.blade.php
│   ├── mahasiswa/index.blade.php
│   ├── pengajuan/index.blade.php
│   ├── cuti/reminder.blade.php
│   └── kontak/index.blade.php
└── js/
    ├── app.js
    ├── dashboard-chart.js
    ├── mahasiswa-table.js
    └── wa-generator.js
```

### 3.2 Database Schema (Migrasi)

```php
// mahasiswas
Schema::create('mahasiswas', function (Blueprint $table) {
    $table->id();
    $table->string('nim', 20)->unique();
    $table->string('nama');
    $table->string('angkatan', 4);
    $table->integer('semester');
    $table->enum('status', ['aktif','cuti','drop_out','tanpa_keterangan'])->default('aktif');
    $table->string('no_whatsapp', 20)->nullable();
    $table->string('email')->nullable();
    $table->string('foto')->nullable();
    $table->decimal('ipk', 3, 2)->nullable();
    $table->integer('sks_tempuh')->default(0);
    $table->timestamps();
});

// cuti_records
Schema::create('cuti_records', function (Blueprint $table) {
    $table->id();
    $table->foreignId('mahasiswa_id')->constrained();
    $table->date('tanggal_mulai');
    $table->date('tanggal_selesai');
    $table->text('alasan')->nullable();
    $table->enum('status_notif', ['belum','terkirim'])->default('belum');
    $table->timestamps();
});

// pengajuans
Schema::create('pengajuans', function (Blueprint $table) {
    $table->id();
    $table->foreignId('mahasiswa_id')->constrained();
    $table->string('jenis_layanan');
    $table->text('keterangan')->nullable();
    $table->string('lampiran')->nullable();
    $table->enum('status', ['menunggu','diproses','selesai','ditolak'])->default('menunggu');
    $table->timestamps();
});

// kontak_layanans
Schema::create('kontak_layanans', function (Blueprint $table) {
    $table->id();
    $table->string('nama_kontak');
    $table->string('jabatan');
    $table->string('no_whatsapp', 20);
    $table->text('pesan_template');
    $table->boolean('is_active')->default(true);
    $table->timestamps();
});
```

### 3.3 JavaScript Features

**dashboard-chart.js — Inisialisasi Chart.js**
```javascript
// Line chart tren mahasiswa
const ctx = document.getElementById('trendChart').getContext('2d');
new Chart(ctx, {
  type: 'line',
  data: {
    labels: semesterLabels,
    datasets: [
      { label: 'Aktif',   data: aktifData,   borderColor: '#10B981' },
      { label: 'Cuti',    data: cutiData,    borderColor: '#F59E0B' },
      { label: 'Drop Out',data: doData,      borderColor: '#EF4444' },
    ]
  },
  options: { responsive: true, maintainAspectRatio: false }
});
```

**mahasiswa-table.js — Search & Filter real-time**
```javascript
document.getElementById('searchInput').addEventListener('input', function() {
  const query = this.value.toLowerCase();
  document.querySelectorAll('tbody tr').forEach(row => {
    const text = row.textContent.toLowerCase();
    row.style.display = text.includes(query) ? '' : 'none';
  });
});
```

**wa-generator.js — Generate & Auto Play**
```javascript
// Auto Play preview template
document.getElementById('templateInput').addEventListener('input', updatePreview);

function updatePreview() {
  const template = document.getElementById('templateInput').value;
  const preview  = template
    .replace(/\{nama_mahasiswa\}/g, 'Contoh Mahasiswa')
    .replace(/\{nim\}/g, '2021001001')
    .replace(/\{keperluan\}/g, 'Surat Keterangan Aktif');
  document.getElementById('waBubblePreview').textContent = preview;
}

// Buka WhatsApp
function openWhatsApp(phone, template, data) {
  const msg  = template
    .replace('{nama_mahasiswa}', data.nama)
    .replace('{nim}', data.nim)
    .replace('{keperluan}', data.keperluan);
  const url  = `https://wa.me/${phone}?text=${encodeURIComponent(msg)}`;
  window.open(url, '_blank');
}
```

### 3.4 Blade Layout Utama

```html
<!-- resources/views/layouts/app.blade.php -->
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>CRM Akademik — @yield('title')</title>
  @vite(['resources/css/app.css','resources/js/app.js'])
</head>
<body class="bg-base">

  <!-- Topbar -->
  <header id="topbar">
    <button id="sidebar-toggle">☰</button>
    <span class="logo">CRM Akademik</span>
    <nav class="topbar-right">
      <div class="notif-bell" id="notifBell">🔔 <span class="badge">3</span></div>
      <div class="avatar-menu">{{ Auth::user()->name }}</div>
    </nav>
  </header>

  <!-- Sidebar -->
  <aside id="sidebar">
    <nav>
      <a href="/dashboard"  class="{{ request()->is('dashboard')  ? 'active' : '' }}">📊 Dashboard</a>
      <a href="/mahasiswa"  class="{{ request()->is('mahasiswa*') ? 'active' : '' }}">🎓 Data Mahasiswa</a>
      <a href="/pengajuan"  class="{{ request()->is('pengajuan*') ? 'active' : '' }}">📋 Pengajuan Layanan</a>
      <a href="/cuti"       class="{{ request()->is('cuti*')      ? 'active' : '' }}">⏰ Pengingat Cuti</a>
      <a href="/kontak"     class="{{ request()->is('kontak*')    ? 'active' : '' }}">📞 Kontak Layanan</a>
    </nav>
    <div class="sidebar-footer">
      <form method="POST" action="/logout">@csrf
        <button type="submit">🚪 Keluar</button>
      </form>
    </div>
  </aside>

  <!-- Content -->
  <main id="content">
    @yield('content')
  </main>

  @stack('scripts')
</body>
</html>
```

---

## 4. User Flow

### 4.1 Flow Login

```
[Buka Website]
      │
      ▼
[Halaman Login]
      │
      ├─ Input Nama + Password
      │
      ▼
[Validasi Frontend] ──── gagal ──→ [Tampilkan Error Inline + Shake]
      │                                        │
      │ sukses                                 ▼
      ▼                               [Tetap di Login]
[POST /login]
      │
      ├─ Auth gagal ──→ [Flash Error: "Nama atau password salah"]
      │
      │ Auth sukses
      ▼
[Redirect → /dashboard]
```

---

### 4.2 Flow Data Mahasiswa

```
[Menu: Data Mahasiswa]
      │
      ▼
[Tabel + Tab Status]
      │
      ├─ [Tab Filter] → Filter data sesuai status
      │
      ├─ [Search] → Filter real-time via JS
      │
      ├─ [+ Tambah] → [Modal Form] → Submit → [Refresh Tabel]
      │
      ├─ [Edit] → [Modal Edit Prefill] → Submit → [Update Row]
      │
      ├─ [Detail] → [Side Drawer] → Tampilkan info lengkap + Riwayat
      │                 │
      │                 └─ [Hubungi WA] → Buka wa.me link baru
      │
      └─ [Hapus] → [Konfirmasi Modal] → Delete → [Refresh]
```

---

### 4.3 Flow Pengajuan Layanan

```
[Menu: Pengajuan Layanan]
      │
      ▼
[Grid Kartu Layanan]
      │
      ├─ Klik Kartu Layanan
      │         │
      │         ▼
      │   [Modal Form Pengajuan]
      │         │
      │         ├─ Search Mahasiswa (autocomplete)
      │         ├─ Isi Keterangan + Upload Lampiran
      │         └─ [Ajukan]
      │                 │
      │                 ▼
      │         [Simpan ke DB → status: menunggu]
      │         [Notif muncul di bell topbar]
      │
      └─ [Tabel Riwayat]
              │
              ├─ [Update Status] → Modal pilih status baru
              └─ [Unduh Surat] → Generate PDF / redirect
```

---

### 4.4 Flow Pengingat Cuti

```
[CRON: daily 07.00]
      │
      ▼
[Command: cuti:check-reminder]
      │
      ├─ Query mahasiswa cuti dengan sisa hari ≤ 30
      │
      └─ Tandai di DB → tampil di halaman Pengingat Cuti

[Admin buka: /cuti]
      │
      ▼
[Tabel Mahasiswa Cuti + Highlight Warna]
      │
      ├─ [Kirim Pengingat WA]
      │         │
      │         ▼
      │   Generate wa.me link dengan template pesan
      │   Buka tab baru → Admin kirim manual
      │   Update kolom status_notif → 'terkirim'
      │
      ├─ [Perpanjang Cuti] → Modal form tanggal baru
      │
      └─ [Aktifkan Kembali] → Ubah status mahasiswa → aktif
```

---

### 4.5 Flow Kontak Layanan + Auto Play

```
[Menu: Kontak Layanan]
      │
      ▼
[Daftar Kartu Kontak]
      │
      ├─ [+ Tambah Kontak]
      │         │
      │         ▼
      │   ┌──────────────────┬───────────────────────┐
      │   │  FORM KIRI       │  PREVIEW KANAN        │
      │   │                  │                       │
      │   │  Nama, Jabatan   │  [Bubble WA]          │
      │   │  No. WA          │  "Halo Contoh         │
      │   │  Template Pesan  │   Mahasiswa..."       │
      │   │  [ketik...]      │  (auto update)        │
      │   └──────────────────┴───────────────────────┘
      │         │
      │    [Simpan] → Tambah ke daftar
      │
      ├─ [Edit] → Sama seperti tambah, prefill data
      │
      ├─ [Preview WA] → Tampilkan dialog bubble chat
      │
      └─ [Kirim WA dari tabel mahasiswa]
                │
                ▼
          Pilih kontak dari dropdown
          Generate link dengan data mahasiswa
          Buka wa.me di tab baru
```

---

## 5. Routing Laravel

```php
// routes/web.php

// Auth
Route::get('/login',  [LoginController::class, 'showForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout',[LoginController::class, 'logout'])->name('logout');

// Protected
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index']);

    Route::resource('/mahasiswa', MahasiswaController::class);
    Route::get('/mahasiswa/status/{status}', [MahasiswaController::class, 'byStatus']);

    Route::resource('/pengajuan', PengajuanController::class);
    Route::patch('/pengajuan/{id}/status', [PengajuanController::class, 'updateStatus']);

    Route::get('/cuti',           [CutiReminderController::class, 'index']);
    Route::post('/cuti/{id}/notif', [CutiReminderController::class, 'kirimNotif']);
    Route::patch('/cuti/{id}/aktif',[CutiReminderController::class, 'aktifkanKembali']);

    Route::resource('/kontak', KontakController::class);
    Route::get('/kontak/{id}/wa-link', [KontakController::class, 'generateLink']);
});
```

---

## 6. Notifikasi WhatsApp

Semua notifikasi WhatsApp menggunakan pendekatan **deep link wa.me** (tidak memerlukan API berbayar):

```
Format URL:
https://wa.me/{nomor_internasional}?text={pesan_encode_url}

Contoh nomor: 6281234567890 (62 = kode Indonesia, hilangkan angka 0 di depan)
```

**Template Pesan Default:**
```
Halo {nama_mahasiswa} (NIM: {nim}),

Kami ingin menginformasikan bahwa masa cuti akademik Anda
akan berakhir dalam {sisa_hari} hari lagi.

Silakan segera mengurus perpanjangan cuti atau aktivasi kembali
ke bagian akademik Prodi Sistem Informasi.

Terima kasih.
— Admin Akademik Prodi SI
```
*Dokumen ini mencakup frontend design, UI requirements, code features, dan user flow.*
*Implementasi backend detail (auth guard, policy, test) mengikuti konvensi standar Laravel.*