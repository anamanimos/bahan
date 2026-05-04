# Fitur: Mobile Camera Companion untuk Aplikasi Nota Laravel

## Ringkasan

Menghubungkan kamera HP ke aplikasi nota berbasis web di PC, tanpa perlu scan ulang setiap ganti nota. HP bertindak sebagai kamera companion yang selalu terhubung selama sesi kerja.

---

## Arsitektur

```
[PC: Browser]  <──WebSocket──>  [Laravel Server]  <──WebSocket──>  [HP: Browser]
     │                                  │
     │  broadcast "nota aktif"          │  terima upload foto
     │                                  │
     └──────── POST /api/photo ─────────┘
```

---

## Alur Kerja

### 1. Pertama Kali (Pairing)
1. User login di PC → server buat **companion token** unik untuk sesi ini
2. PC tampilkan QR Code berisi URL: `https://app.anda/cam/{companion_token}`
3. User scan QR dengan HP satu kali
4. HP membuka halaman kamera → terhubung ke WebSocket channel `companion.{token}`
5. PC dan HP kini terhubung selama sesi

### 2. Saat User Buka/Pindah Nota
1. PC broadcast event `NotaAktif` ke channel `companion.{token}`
2. Event berisi: `{ nota_id, nomor_nota, nama_klien }`
3. HP menerima event → update tampilan "Nota Aktif" secara otomatis
4. Tombol kamera di HP siap digunakan untuk nota tersebut

### 3. Saat User Foto di HP
1. HP capture foto via `getUserMedia` API
2. Upload ke `POST /api/companion/upload` dengan payload: `{ token, nota_id, foto }`
3. Server simpan foto, broadcast event `FotoDiterima` ke PC
4. PC menerima event → tampilkan preview foto tanpa reload halaman

---

## Edge Cases

### User tidak di halaman nota
- PC broadcast event `SessionIdle` ke HP
- HP: tombol kamera **disabled**, tampilkan pesan _"Buka nota di PC terlebih dahulu"_

### User buka 2 tab nota sekaligus
- Gunakan **Last Active Wins**: tab yang terakhir difokus/diklik dianggap aktif
- Implementasi: gunakan event `visibilitychange` + `focus` di browser PC
- Tab lain otomatis jadi idle, tidak mengirim broadcast

### HP menutup browser / koneksi terputus
- PC tampilkan indikator "HP Offline" di area kamera
- Companion token tetap valid, HP bisa reconnect dengan membuka URL yang sama (tidak perlu scan ulang)

### Sesi berakhir / logout
- Companion token di-invalidate di server
- HP redirect ke halaman "Sesi telah berakhir"

---

## Komponen yang Perlu Dibuat

### Backend (Laravel)

```
app/
├── Models/
│   └── CompanionSession.php        # Model untuk menyimpan token sesi
├── Http/Controllers/
│   ├── CompanionController.php     # Handle pairing & halaman kamera HP
│   └── CompanionUploadController.php  # Handle upload foto dari HP
├── Events/
│   ├── NotaAktifChanged.php        # Broadcast saat nota aktif berubah
│   ├── SessionIdle.php             # Broadcast saat PC tidak di halaman nota
│   └── FotoUploaded.php            # Broadcast saat foto berhasil diupload
└── Http/Middleware/
    └── ValidateCompanionToken.php  # Validasi token HP
```

### Frontend (PC)

```
resources/js/
└── companion/
    ├── qr-generator.js             # Generate & tampilkan QR Code
    ├── tab-focus-tracker.js        # Detect tab aktif (visibilitychange + focus)
    └── photo-receiver.js           # Terima event foto, update UI

resources/views/
└── nota/
    └── _companion-panel.blade.php  # Panel kamera di sidebar/halaman nota
```

### Frontend (HP)

```
resources/views/
└── companion/
    └── camera.blade.php            # Halaman kamera untuk HP

resources/js/
└── companion/
    └── mobile-camera.js            # getUserMedia, capture, upload
```

---

## Database

```sql
CREATE TABLE companion_sessions (
    id              BIGINT PRIMARY KEY AUTO_INCREMENT,
    user_id         BIGINT NOT NULL,
    token           VARCHAR(64) UNIQUE NOT NULL,  -- token untuk URL QR
    nota_id_aktif   BIGINT NULL,                  -- nota yang sedang aktif di PC
    last_seen_at    TIMESTAMP NULL,               -- kapan HP terakhir aktif
    expires_at      TIMESTAMP NOT NULL,           -- token expired (misal: akhir hari)
    created_at      TIMESTAMP,
    updated_at      TIMESTAMP,

    FOREIGN KEY (user_id) REFERENCES users(id)
);
```

---

## Routes

```php
// routes/web.php

// Halaman kamera untuk HP (tidak perlu auth, cukup token valid)
Route::get('/cam/{token}', [CompanionController::class, 'show'])
    ->name('companion.camera')
    ->middleware('companion.token');

// routes/api.php

// Upload foto dari HP
Route::post('/companion/upload', [CompanionUploadController::class, 'store'])
    ->middleware('companion.token');

// Generate/refresh token (dipanggil dari PC saat buka halaman nota)
Route::post('/companion/session', [CompanionController::class, 'createSession'])
    ->middleware('auth');

// Update nota aktif (dipanggil dari PC saat buka/pindah nota)
Route::patch('/companion/nota-aktif', [CompanionController::class, 'updateNotaAktif'])
    ->middleware('auth');
```

---

## WebSocket Events

Menggunakan **Laravel Reverb** (self-hosted, gratis) atau **Pusher** (ada free tier).

### Channel
```
companion.{companion_token}
```
Channel ini bersifat private, hanya bisa diakses oleh PC dan HP yang memiliki token yang sama.

### Events

| Event | Dari | Ke | Payload |
|---|---|---|---|
| `NotaAktifChanged` | PC | HP | `{ nota_id, nomor_nota, nama_klien }` |
| `SessionIdle` | PC | HP | `{ reason: 'navigated_away' }` |
| `FotoUploaded` | Server | PC | `{ nota_id, foto_url, foto_id }` |

---

## UI: Halaman Kamera HP

```
┌─────────────────────────┐
│  🔗 Terhubung           │  ← status koneksi
├─────────────────────────┤
│  📋 Nota Aktif:         │
│  #1042 - PT. Maju Jaya  │  ← update otomatis via WebSocket
├─────────────────────────┤
│                         │
│   [ preview kamera ]    │
│                         │
├─────────────────────────┤
│    [📷  Ambil Foto]     │
├─────────────────────────┤
│  ✅ 2 foto terupload    │  ← log foto yang sudah dikirim
└─────────────────────────┘

── Saat idle (PC tidak di halaman nota) ──
┌─────────────────────────┐
│  ⏸️  Tidak Ada Nota     │
│     yang Aktif          │
│                         │
│  Buka nota di PC        │
│  untuk mengambil foto   │
│                         │
│    [📷  Ambil Foto]     │  ← tombol disabled
└─────────────────────────┘
```

---

## UI: Panel Companion di PC (Halaman Nota)

```
┌──────────────────────────────┐
│  📷 Kamera HP                │
├──────────────────────────────┤
│  ● HP Terhubung              │  ← hijau jika HP aktif, merah jika offline
│                              │
│  [QR Code jika belum pair]   │  ← hilang setelah HP terhubung
│                              │
│  Foto (3):                   │
│  [ foto1 ] [ foto2 ] [ + ]   │
└──────────────────────────────┘
```

---

## Tech Stack

| Kebutuhan | Package |
|---|---|
| QR Code generator (PC) | `simplesoftwareio/simple-qrcode` |
| WebSocket server | **Laravel Reverb** (rekomendasi, self-hosted) atau Pusher |
| WebSocket client (JS) | Laravel Echo + `pusher-js` |
| Kamera di HP | Native browser `getUserMedia` API (tidak perlu library) |
| Upload foto | `fetch` / `axios` |

---

## Urutan Implementasi (Step by Step)

1. **Install & setup Laravel Reverb**
2. **Buat tabel `companion_sessions`** + model + migration
3. **Buat route & controller** untuk generate token dan halaman HP
4. **Buat 3 broadcast events** (NotaAktifChanged, SessionIdle, FotoUploaded)
5. **Buat halaman kamera HP** (`camera.blade.php` + `mobile-camera.js`)
6. **Integrasi di halaman nota PC**: tab focus tracker + broadcast nota aktif + panel companion
7. **Handle upload foto** di server + broadcast balik ke PC
8. **Test edge cases**: idle, 2 tab, HP disconnect, token expired

---

## Catatan

- Companion token sebaiknya **expired setiap akhir hari kerja** atau saat user logout, bukan per nota
- Untuk keamanan, validasi bahwa `nota_id` yang dikirim HP benar-benar milik user yang memiliki token
- `getUserMedia` di HP **wajib HTTPS** — pastikan aplikasi sudah menggunakan SSL
- Pertimbangkan kompresi foto sebelum upload (canvas resize) agar upload lebih cepat di koneksi HP
