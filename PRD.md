# PRODUCT REQUIREMENTS DOCUMENT

## Aplikasi Toko Kain & Bahan Pelengkap Garment

**Terintegrasi ERP Konveksi | Dual Channel: Internal + Retail Umum**

| Atribut | Detail |
|---|---|
| **Versi Dokumen** | 1.2.0 |
| **Tanggal** | Mei 2026 |
| **Status** | Draft for Review |
| **Disusun oleh** | Tim Produk |
| **Ditujukan untuk** | Stakeholder Bisnis & Tim Pengembang |

---

## 1. Ringkasan Eksekutif

Aplikasi ini dirancang sebagai layer terpisah di atas sistem ERP konveksi yang sudah berjalan. ERP konveksi fokus pada proses produksi dan tidak mencatat sisa stok bahan; oleh karena itu dibutuhkan sebuah sistem toko independen yang mengelola pembelian, penyimpanan, pemakaian, dan penjualan bahan kain serta bahan pelengkap garment.

Sistem ini melayani dua segmen pengguna secara bersamaan:

- **Internal Konveksi** — pengambilan bahan untuk kebutuhan produksi order, terhubung langsung ke perhitungan HPP di ERP.
- **Penjualan Retail Umum** — penjualan bahan ke konsumen atau konveksi lain di luar perusahaan.

Integrasi dua arah melalui REST API memastikan setiap transaksi toko otomatis tercermin di ERP, sehingga perhitungan Harga Pokok Produksi (HPP) dan realisasi penggunaan bahan per order selalu akurat dan real-time.

---

## 2. Latar Belakang & Permasalahan

### 2.1 Kondisi Saat Ini

ERP konveksi yang sudah ada dirancang murni untuk manajemen order produksi. Modul persediaan di ERP hanya mencatat pemakaian bahan per order, bukan saldo stok fisik. Tidak ada fitur untuk:

- Mencatat pembelian bahan dari supplier dengan detail harga dan lot masuk.
- Menyimpan riwayat harga beli untuk keperluan penilaian persediaan (FIFO/Average).
- Melayani transaksi penjualan bahan ke pihak eksternal.
- Mengelola sisa bahan yang tidak terpakai dari satu order ke order berikutnya.

### 2.2 Akar Masalah

| No. | Masalah | Dampak |
|---|---|---|
| 1 | Tidak ada pencatatan stok sisa bahan di ERP | HPP tidak akurat; bahan terbuang tidak tercatat |
| 2 | Tidak ada channel penjualan bahan ke umum | Potensi pendapatan hilang |
| 3 | Harga beli bahan tidak terlacak per lot | Tidak bisa menghitung HPP dengan metode FIFO/Average |
| 4 | Tidak ada kategorisasi & label bahan yang fleksibel | Susah mencari & mengelompokkan item |
| 5 | Tidak ada minimum stok alert | Risiko kehabisan bahan saat produksi |

---

## 3. Tujuan & Sasaran

### 3.1 Tujuan Bisnis

- Menyediakan layer stok mandiri yang melengkapi ERP konveksi tanpa mengubah alur ERP yang sudah berjalan.
- Membuka channel pendapatan baru melalui penjualan bahan ke konsumen umum.
- Memastikan data pemakaian bahan per order tersedia di ERP untuk perhitungan HPP yang akurat.

### 3.2 Sasaran Terukur

| Sasaran | Indikator Keberhasilan | Target |
|---|---|---|
| Akurasi stok | Selisih stok fisik vs sistem | < 0,5% |
| Integrasi ERP | Waktu sinkronisasi data pemakaian ke ERP | < 5 menit (real-time) |
| Dual channel | Transaksi internal & retail dalam 1 sistem | 100% tercover |
| Penelusuran HPP | % order yang HPP-nya bisa dihitung dari data toko | > 98% |

---

## 4. Scope & Out-of-Scope

### 4.1 Dalam Scope

- Manajemen produk (kain & bahan pelengkap) dengan kategorisasi hierarkis, multi-label, foto produk, dan kolom lokasi peletakan gudang.
- Manajemen supplier dan pengadaan bahan: Purchase Requisition (PR) harian, approval per item, dan Goods Receipt dari nota fisik.
- Penerimaan barang, pencatatan lot masuk, dan penilaian persediaan metode FIFO.
- Manajemen satuan (UoM) dan konversi antar satuan (meter, yard, roll, pcs, lusin, kg, dll).
- Penjualan internal (permintaan bahan oleh tim produksi, terhubung ke nomor order ERP format YYMMNNN, contoh: 2604001).
- Penjualan retail ke konsumen umum (walk-in maupun remote order).
- Manajemen harga: harga jual retail, harga khusus internal (manual), harga tier/diskon volume; notifikasi dashboard ketika ada perubahan harga.
- Minimum stok alert dan reorder point per item.
- REST API untuk integrasi dua arah dengan ERP konveksi (ERP mendukung webhook).
- Laporan stok, pemakaian, penjualan, dan rekap HPP bahan per order.
- Manajemen pengguna berbasis SSO ERP — autentikasi eksklusif via ERP, role dikelola mandiri di aplikasi toko.
- Backup database otomatis harian: dump `.sql.gz` dikirim ke bot/grup Telegram.

### 4.2 Luar Scope

- Modul produksi/cutting/sewing (tetap di ERP konveksi).
- Akuntansi dan jurnal keuangan (tetap di software akuntansi yang ada).
- Pengiriman logistik ke konsumen (integrasi kurir bukan prioritas v1).
- E-commerce marketplace (Tokopedia, Shopee, dll) — dipertimbangkan di versi berikutnya.

---

## 5. Pengguna & Stakeholder

| Peran | Deskripsi | Akses Utama |
|---|---|---|
| Admin Toko | Mengelola seluruh sistem toko | Full access |
| Staff Gudang | Penerimaan barang & stock opname | Inventori, PO penerimaan |
| Staff Penjualan | Proses penjualan retail & internal | POS, order penjualan |
| Manajer Produksi (ERP) | Membuat permintaan bahan untuk order | Permintaan internal (via API/UI) |
| Konsumen Retail | Membeli bahan secara langsung/online | Frontend toko (view + order) |
| Manajemen / Owner | Melihat laporan dan dashboard | Read-only semua modul |
| Sistem ERP | Menerima & mengirim data via API | API integration only |

---

## 6. Fitur & Kebutuhan Fungsional

### 6.1 Katalog Produk & Manajemen Item

#### 6.1.1 Data Master Produk

Setiap item bahan memiliki atribut berikut:

| Atribut | Keterangan | Wajib? |
|---|---|---|
| Kode SKU | Kode unik otomatis atau manual | Ya |
| Nama Produk | Nama deskriptif item | Ya |
| Kategori | Hierarki kategori multilevel tak terbatas (adjacency list); produk ditempatkan di node mana saja | Ya |
| Label (Tags) | Multi-label bebas, bisa ditambah sewaktu-waktu | Tidak |
| Satuan Dasar | Satuan utama pencatatan stok (meter, pcs, kg, dll) | Ya |
| Satuan Jual | Bisa berbeda dari satuan dasar; ada faktor konversi | Ya |
| Spesifikasi | Lebar kain, gramasi, komposisi, warna, motif, dll | Tidak |
| Foto Produk | Gambar bahan — wajib ada untuk kemudahan identifikasi di POS | Ya |
| Lokasi Gudang | Teks bebas lokasi penyimpanan fisik, misal: 'Rak A3, Laci 2' | Tidak |
| Harga Pokok (HPP/FIFO) | Dihitung *on-the-fly* dari antrean lot FIFO saat transaksi — tidak disimpan di master produk | Otomatis |
| Stok Minimum (Reorder Point) | Ambang batas untuk notifikasi reorder | Ya |
| Status | Aktif / Tidak Aktif / Habis | Ya |
| Barcode/QR | Untuk scanning di gudang atau kasir | Tidak |

#### 6.1.2 Kategorisasi Multilevel

Sistem mendukung pohon kategori **multilevel tak terbatas** menggunakan struktur *adjacency list* (`id`, `parent_id`, `name`). Admin dapat membuat, memindahkan, dan menghapus node kategori kapan saja tanpa batas kedalaman. Produk dapat ditempatkan di level mana pun dalam pohon — tidak harus di node daun (leaf).

**Contoh struktur:**

```
Kain
├── Kain Rajut
│   ├── Cotton Combed
│   │   ├── 20s
│   │   └── 30s
│   └── Polyester
└── Kain Tenun
    ├── Drill
    └── Oxford

Bahan Pelengkap
├── Kancing
│   ├── Kancing Bungkus
│   └── Kancing Logam
├── Resleting
│   └── YKK
├── Benang
└── Label & Aksesoris
    ├── Label Woven
    └── Hangtag
```

**Aturan teknis:**

- Struktur disimpan sebagai *adjacency list*: setiap node menyimpan `parent_id` yang menunjuk ke node induknya; node root memiliki `parent_id = NULL`.
- Untuk query pohon penuh (breadcrumb, filter by ancestor), sistem menggunakan *recursive CTE* (didukung MariaDB 10.2+) atau *closure table* jika performa menjadi isu.
- Penghapusan kategori yang masih memiliki anak atau produk terkait akan diblokir — admin wajib memindahkan anak/produk terlebih dahulu.
- Setiap node kategori dapat dikonfigurasi atribut spesifikasinya sendiri yang diwariskan ke seluruh turunannya (misal: node `Kain` mendefinisikan field `Lebar Kain` dan `Gramasi` yang otomatis tersedia di semua sub-kategori kain).

#### 6.1.3 Multi-Label (Tags)

Label adalah penanda bebas yang bisa diterapkan ke satu produk sekaligus. Tujuannya melengkapi kategorisasi rigid dengan pengelompokan lintas-kategori. Contoh label:

- Musim/tren: `best-seller-2025`, `new-arrival`
- Keperluan spesifik: `untuk-seragam`, `ramah-anak`, `tahan-pudar`
- Status pengadaan: `indent`, `stok-terbatas`
- Supplier: `supplier-A`, `impor`

Fitur label: autocomplete saat mengetik, pembuatan label baru inline, filter produk berdasarkan kombinasi label (AND/OR).

---

### 6.2 Manajemen Pengadaan Bahan

Alur pengadaan mencerminkan praktik harian yang sesungguhnya: staff mengajukan kebutuhan beli setiap sore, buyer berbelanja keesokan harinya berdasarkan pengajuan yang disetujui, lalu admin menginput transaksi langsung dari nota fisik setelah beli. Tidak ada PO formal yang dikirim ke supplier — pembelian dilakukan langsung di toko/pasar bahan.

```
[Sore hari]   Staff input Purchase Requisition (PR)
                  → nama bahan, nama toko, jumlah, estimasi harga, konteks (order/stok)
                        ↓
[Review]      Admin/Manajer approve per item dalam PR
                  → item disetujui lanjut, item ditolak dibatalkan
                        ↓
[Besok pagi]  Buyer belanja mengacu daftar item yang disetujui
                        ↓
[Setelah beli] Admin input Goods Receipt dari nota fisik
                  → stok masuk, harga aktual tercatat, lot FIFO terbentuk
```

#### 6.2.1 Master Supplier

- Supplier **tidak perlu didaftarkan manual**. Setiap nama toko yang diinput di PR atau Goods Receipt otomatis terdaftar ke master supplier jika belum ada — menggunakan pencocokan nama (case-insensitive).
- Data supplier yang terbentuk otomatis: nama toko. Data tambahan (alamat, nomor telepon/WA, PIC) dapat dilengkapi belakangan oleh admin melalui halaman master supplier.
- Riwayat pembelian per supplier (total transaksi, frekuensi, rata-rata harga per item) terakumulasi otomatis dari Goods Receipt.
- Admin dapat menggabungkan (*merge*) dua entri supplier yang ternyata sama (misal: "Toko Sumber" dan "toko sumber" terbentuk sebagai dua entri terpisah).

#### 6.2.2 Purchase Requisition (PR)

Purchase Requisition adalah pengajuan kebutuhan beli harian yang dibuat oleh staff setiap sore sebagai acuan belanja keesokan harinya. Dalam satu hari boleh ada lebih dari satu PR — PR baru dapat dibuat kapan saja selama PR sebelumnya sudah melewati tahap pengajuan.

**Data wajib per baris item PR:**

| Field | Keterangan |
|---|---|
| Nama Bahan | Autocomplete dari master produk; jika belum ada di master, bisa input teks bebas dan produk baru terbentuk sebagai draft |
| Nama Toko | Autocomplete dari master supplier; jika belum ada, input bebas dan otomatis terdaftar |
| Jumlah | Kuantitas yang dibutuhkan beserta satuan |
| Estimasi Harga | Perkiraan harga beli per satuan — digunakan sebagai acuan budget, bukan harga final |
| Konteks | `Untuk Order [nomor order ERP]` atau `Untuk Stok Umum` |
| Keterangan | Opsional — catatan tambahan untuk buyer |

**Status alur PR:**

```
Draft → Diajukan → [per item: Disetujui | Ditolak] → Selesai Dibeli
```

- `Draft` — PR sedang diedit, belum diajukan.
- `Diajukan` — PR dikirim untuk di-review; staff tidak dapat mengedit lagi kecuali dikembalikan.
- `Disetujui / Ditolak` — status berlaku **per item**, bukan per PR secara keseluruhan. Admin atau Manajer dapat menyetujui sebagian item dan menolak sebagian lainnya dalam satu PR yang sama. Item yang ditolak wajib disertai alasan penolakan.
- `Selesai Dibeli` — semua item yang disetujui sudah terinput di Goods Receipt.

**Aturan tambahan:**
- Nomor PR digenerate otomatis dengan format `PR-YYYYMMDD-NNN` (misal: `PR-20260502-001`).
- PR yang sudah diajukan dapat dilihat oleh buyer dari halaman ringkasan PR hari ini — dikelompokkan per toko agar efisien saat belanja.
- Item PR yang ditolak tidak menghapus baris — tetap tercatat untuk keperluan histori dan audit.

#### 6.2.3 Goods Receipt (Input Nota)

Goods Receipt diinput oleh admin setelah buyer kembali dari belanja, berdasarkan nota fisik yang dibawa. Tidak diperlukan PO formal — nota dari toko/pasar menjadi sumber data utama.

**Data per Goods Receipt:**

| Field | Keterangan |
|---|---|
| Tanggal Beli | Tanggal transaksi di nota |
| Nama Toko / Supplier | Autocomplete dari master supplier; input bebas jika belum terdaftar |
| Nomor Nota | Opsional — nomor nota fisik dari toko |
| Referensi PR | Opsional — link ke PR yang menjadi dasar pembelian ini |

**Data per baris item:**

| Field | Keterangan |
|---|---|
| Nama Bahan | Pilih dari master produk |
| Qty Aktual | Kuantitas yang benar-benar dibeli (bisa berbeda dari PR) |
| Satuan | Satuan pembelian |
| Harga Beli per Satuan | Harga aktual dari nota — wajib diisi, tidak bisa disimpan jika kosong |
| Nomor Lot/Batch | Otomatis digenerate jika tidak ada nomor dari supplier |

- Setiap baris item yang disimpan otomatis membentuk satu lot baru dalam antrean FIFO stok.
- Satu Goods Receipt bisa berisi item dari beberapa PR sekaligus, atau tanpa PR sama sekali (pembelian spontan/tidak terencana).
- Setelah Goods Receipt disimpan, stok produk terkait bertambah secara real-time.
- Cetak label barcode/QR tersedia per lot untuk keperluan identifikasi fisik di gudang.

---

### 6.3 Manajemen Stok & Inventori

#### 6.3.1 Pencatatan Stok

- Saldo stok real-time per item, per satuan.
- Riwayat mutasi stok lengkap: masuk (PO), keluar (penjualan retail + pemakaian internal), penyesuaian.
- Satu lokasi gudang (single warehouse) untuk versi pertama.

#### 6.3.2 Penilaian Persediaan — Metode FIFO

- Sistem menggunakan metode FIFO (First In, First Out): lot yang paling pertama diterima akan dianggap keluar lebih dulu.
- Setiap lot penerimaan menyimpan: tanggal masuk, harga beli per unit, dan kuantitas sisa lot.
- Harga pokok setiap transaksi keluar dihitung otomatis dari antrean lot FIFO yang tersedia.
- Laporan nilai persediaan: sisa kuantitas per lot × harga beli lot = nilai buku stok total.

#### 6.3.3 Stok Opname

- Fitur penghitungan stok (stock count): generate sheet hitungan, input kuantitas aktual, dan posting selisih sebagai penyesuaian.
- Setiap penyesuaian membutuhkan keterangan alasan dan persetujuan supervisor.

#### 6.3.4 Minimum Stok & Notifikasi Reorder

- Setiap item memiliki Reorder Point (ROP) yang bisa diatur manual.
- Sistem otomatis mengirim notifikasi (in-app dan/atau email) ke admin/manajer gudang ketika stok menyentuh atau di bawah ROP.
- Dashboard alert: ringkasan item yang perlu di-reorder.

---

### 6.4 Penjualan Internal (Permintaan Bahan Konveksi)

#### 6.4.1 Alur Permintaan Bahan

- Tim produksi membuat Material Request (MR) di sistem toko atau ERP mengirim request via API.
- MR mereferensikan nomor order produksi (dari ERP) dan merinci item + kuantitas yang dibutuhkan.
- Staff gudang memverifikasi ketersediaan stok dan menyiapkan bahan.
- Pengeluaran bahan dicatat sebagai transaksi 'pemakaian internal' dengan harga khusus internal yang telah ditetapkan.
- Data pengeluaran dikirim ke ERP via API untuk dibebankan ke HPP order yang bersangkutan.

#### 6.4.2 Sisa Bahan (Return Material)

- Sisa bahan yang tidak dipakai dari sebuah order dapat dikembalikan ke gudang melalui transaksi Return Material.
- Return dilampirkan ke nomor order yang sama; stok bertambah kembali.
- ERP dinotifikasi via API agar HPP order dikoreksi.

---

### 6.5 Penjualan Retail (Konsumen Umum)

#### 6.5.1 Point of Sale (POS)

- Antarmuka kasir sederhana untuk penjualan walk-in: cari produk, tambah ke keranjang, tentukan kuantitas dan satuan, cetak struk.
- Input kuantitas desimal (misal: 2,5 meter kain).
- Metode pembayaran: tunai, transfer bank, QRIS.
- Cetak struk termal atau PDF.

#### 6.5.2 Order Retail (Non-Walk-in)

- Pembuatan order manual oleh staff untuk pelanggan yang memesan via telepon/WA.
- Status order: Draft > Dikonfirmasi > Diproses > Selesai/Dibatalkan.
- Catatan pelanggan (nama, nomor telepon) untuk riwayat pembelian.

#### 6.5.3 Manajemen Harga & Diskon

Harga **tidak disimpan di master produk** karena bersifat fluktuatif dan dapat berbeda antar channel atau toko. Harga dikelola di entitas terpisah (`product_prices`) sehingga master produk tetap stabil dan riwayat harga selalu tersimpan untuk keperluan audit dan analisis.

**Struktur tabel `product_prices`:**

| Kolom | Keterangan |
|---|---|
| `product_id` | FK ke master produk |
| `price_type` | `retail`, `internal`, `grosir_tier` |
| `store_id` | Identifikasi toko/channel (untuk mendukung multi-toko di masa depan) |
| `price` | Nilai harga |
| `min_qty` | Berlaku mulai kuantitas berapa (untuk harga tier; isi `1` jika tidak ada minimum) |
| `effective_date` | Tanggal harga mulai berlaku |
| `expired_date` | Tanggal harga berakhir — opsional, untuk harga promo |
| `created_by` / `updated_by` | Audit trail perubahan harga |

Karena harga menggunakan `effective_date`, harga lama tidak dihapus melainkan digantikan oleh entri baru — sehingga sistem otomatis menyimpan riwayat harga lengkap.

**Jenis harga yang didukung:**

| Jenis Harga | Deskripsi |
|---|---|
| Harga Retail Normal | Harga standar untuk konsumen umum per satuan jual |
| Harga Grosir / Tier | Harga lebih murah mulai kuantitas tertentu (misal: >= 5 meter dapat harga lebih rendah) |
| Harga Khusus Internal | Harga pemakaian internal konveksi, ditetapkan manual oleh admin — bukan turunan otomatis dari HPP |
| Diskon Manual | Diskon persentase atau nominal yang bisa diterapkan per transaksi oleh staff berwenang |
| Harga Jual Minimum | Batas bawah harga yang tidak bisa dilanggar tanpa persetujuan admin |

**Notifikasi Perubahan Harga:** setiap kali harga retail atau harga khusus internal diubah, sistem otomatis menampilkan notifikasi di dashboard untuk admin dan manajer. Notifikasi mencatat: item yang berubah, nilai harga lama, nilai harga baru, waktu perubahan, dan nama pengguna yang mengubah.

---

### 6.6 Integrasi API dengan ERP Konveksi

#### 6.6.1 Arsitektur Integrasi

Sistem toko menyediakan REST API (JSON) yang dapat dikonsumsi oleh ERP konveksi. Komunikasi bersifat dua arah:

- **Toko → ERP:** Push data pemakaian bahan, pengembalian bahan, dan nilai HPP bahan per order.
- **ERP → Toko:** Push nomor order baru, permintaan bahan (Material Request), dan konfirmasi penerimaan data.

#### 6.6.2 Endpoint API Utama

| Method | Endpoint | Fungsi |
|---|---|---|
| GET | `/api/v1/products` | Daftar produk dengan stok & harga |
| GET | `/api/v1/products/{sku}` | Detail produk spesifik |
| GET | `/api/v1/stock/{sku}` | Saldo stok real-time per item |
| POST | `/api/v1/material-request` | ERP membuat permintaan bahan baru |
| PATCH | `/api/v1/material-request/{id}` | Update status permintaan bahan |
| POST | `/api/v1/material-usage` | Mencatat pengeluaran bahan (push ke ERP) |
| POST | `/api/v1/material-return` | Mencatat pengembalian sisa bahan |
| GET | `/api/v1/usage-by-order/{order_id}` | Rincian pemakaian bahan per order produksi |
| GET | `/api/v1/hpp-by-order/{order_id}` | Nilai HPP bahan per order (qty × harga pokok) |
| POST | `/api/v1/webhook/erp` | ERP mengirim notifikasi ke sistem toko |

#### 6.6.3 Keamanan API

- Autentikasi menggunakan API Key (header: `X-API-Key`) atau OAuth 2.0 Client Credentials.
- Rate limiting: 500 request/menit per client.
- Semua komunikasi wajib HTTPS/TLS 1.2+.
- Log setiap request dan response untuk keperluan audit dan debugging.
- Versi API (v1, v2) untuk menjaga backward compatibility saat ada perubahan.

---

### 6.7 Manajemen Satuan (Unit of Measure)

| Satuan | Contoh Item | Konversi Umum |
|---|---|---|
| Meter (m) | Kain roll | 1 roll = 40–60 m (variabel per item) |
| Yard (yd) | Kain impor | 1 yard = 0,9144 m |
| Kilogram (kg) | Kain tenun berat, benang | Bergantung gramasi kain |
| Pieces (pcs) | Kancing, resleting per biji | — |
| Lusin (dz) | Kancing eceran | 1 lusin = 12 pcs |
| Gross | Kancing grosir | 1 gross = 144 pcs |
| Roll | Benang, pita | 1 roll = n meter (dikonfigurasi per item) |
| Pak/Bundle | Benang jahit per pak | Dikonfigurasi per item |

Setiap item memiliki Satuan Dasar (untuk stok internal) dan Satuan Jual (untuk transaksi penjualan), dengan faktor konversi yang dapat dikonfigurasi per item. Sistem mengonversi otomatis saat transaksi.

---

### 6.8 Autentikasi & Single Sign-On (SSO) via ERP

Aplikasi toko kain tidak memiliki sistem login mandiri. Seluruh autentikasi dilakukan melalui ERP menggunakan mekanisme SSO yang sudah tersedia. Role dan hak akses di dalam aplikasi toko dikelola secara terpisah — tidak semua pengguna ERP otomatis memiliki akses ke toko.

#### 6.8.1 Dua Cara Login

| Cara | Alur | Keterangan |
|---|---|---|
| 1. Shortcut di Sidebar ERP | User klik ikon/link toko kain di sidebar ERP → ERP generate token unik (berlaku 60 detik) → Browser di-redirect ke toko kain dengan `?token=xxx&email=xxx` → Toko kain POST ke `/api/auth/verify-sso` ERP → Jika valid, user otomatis login ke toko kain | Cara utama dan paling cepat. Tidak perlu input apapun. |
| 2. Tombol 'Login with ERP' di Halaman Login Toko | User membuka URL toko kain secara langsung → Halaman login menampilkan tombol 'Login with ERP' → Klik tombol → Browser di-redirect ke halaman login ERP → Setelah login ERP berhasil, ERP redirect balik ke toko kain dengan token SSO → Toko kain verifikasi token → User otomatis login | Untuk akses langsung tanpa melalui sidebar ERP. |

#### 6.8.2 Manajemen Akses Pengguna Toko

- Daftar pengguna yang boleh mengakses toko kain dikelola oleh Admin Toko — terpisah dari manajemen user di ERP.
- Saat user pertama kali SSO berhasil tetapi belum terdaftar di toko, sistem menampilkan pesan: *"Akun Anda belum memiliki akses ke Toko Kain. Hubungi administrator."*
- Admin Toko dapat menambahkan user baru berdasarkan email ERP, kemudian menetapkan role toko (Admin, Manajer, Staff Gudang, Staff Penjualan, dll).
- Menonaktifkan user di ERP tidak otomatis mencabut akses toko — Admin Toko harus menonaktifkan secara terpisah.

#### 6.8.3 Detail Teknis SSO

- Endpoint verifikasi: `POST https://app.damaijaya.my.id/api/auth/verify-sso` dengan body: `{ token, email }`.
- Token berlaku 60 detik sejak digenerate oleh ERP.
- Setelah verifikasi sukses, toko kain membuat session lokal (Laravel Session) dengan data user yang relevan.
- Jika verifikasi gagal (token expired, email tidak cocok, atau ERP menolak), user diarahkan kembali ke halaman login dengan pesan error yang informatif.
- Tidak ada penyimpanan password di sisi toko kain sama sekali.

---

### 6.9 Backup Database Otomatis via Telegram

Untuk menjamin ketersediaan data, sistem menjalankan backup database terjadwal setiap hari secara otomatis.

#### 6.9.1 Spesifikasi Backup

| Parameter | Detail |
|---|---|
| Format file | mysqldump terkompresi: `.sql.gz` |
| Jadwal | Setiap hari pukul 02.00 WIB (saat traffic terendah) |
| Penamaan file | `backup_tokokain_YYYYMMDD_HHMMSS.sql.gz` |
| Tujuan pengiriman | Bot/grup Telegram yang dikonfigurasi oleh admin |
| Ukuran maksimal | Telegram mendukung file hingga 2 GB; jika melebihi, backup dipecah per tabel besar |
| Enkripsi | File dienkripsi dengan password sebelum dikirim (opsional, dikonfigurasi admin) |
| Retensi di server | File backup lokal disimpan 7 hari sebelum dihapus otomatis |

#### 6.9.2 Notifikasi Status Backup di Telegram

- **Berhasil:** Bot mengirim file `.sql.gz` beserta pesan ringkasan — tanggal, ukuran file, jumlah tabel, dan durasi proses.
- **Gagal:** Bot mengirim pesan alert dengan detail error (misal: `mysqldump failed: Access denied`) agar admin dapat segera menangani.
- **Konfigurasi:** Telegram Bot Token dan Chat ID (bisa grup atau channel) diatur melalui halaman pengaturan sistem di admin panel.

---

### 6.10 Laporan & Dashboard

#### 6.10.1 Dashboard Utama

- Ringkasan stok: total item aktif, item di bawah ROP, item habis.
- Penjualan hari ini: retail vs internal.
- Nilai persediaan total.
- Grafik tren penjualan 30 hari terakhir.
- Notifikasi: PR yang menunggu approval, item perlu reorder, penyesuaian stok menunggu persetujuan, perubahan harga terbaru.

#### 6.10.2 Laporan Inventori

- Laporan stok per tanggal (snapshot).
- Kartu stok per item: riwayat semua mutasi masuk dan keluar dengan detail lot FIFO.
- Laporan nilai persediaan: sisa kuantitas per lot × harga beli lot = nilai buku stok.
- Laporan pergerakan stok (fast-moving vs slow-moving item).
- **Laporan pengadaan harian:** rekap PR per hari — item yang diajukan, disetujui, ditolak, dan realisasi harga aktual vs estimasi di PR.
- **Laporan pembelian per supplier:** total transaksi Goods Receipt per toko/supplier dalam periode tertentu.

#### 6.10.3 Laporan Penjualan

- Rekap penjualan retail harian/mingguan/bulanan.
- Penjualan per item, per kategori, per label.
- Rekap pemakaian internal per nomor order.

#### 6.10.4 Laporan HPP & Pemakaian Bahan untuk ERP

- Rincian pemakaian bahan per nomor order produksi (format YYMMNNN, contoh: `2604001`): item, kuantitas, satuan, harga pokok FIFO, total nilai.
- Perbandingan estimasi kebutuhan bahan (BOM dari ERP) vs realisasi pemakaian aktual.
- Rekap HPP bahan bulanan sebagai input laporan biaya produksi.
- Export ke CSV/Excel untuk rekonsiliasi manual jika diperlukan.

---

## 7. Kebutuhan Non-Fungsional

| Aspek | Kebutuhan | Target |
|---|---|---|
| Performa | Response time halaman & API | < 2 detik pada 50 concurrent users |
| Ketersediaan | Uptime sistem | > 99,5% (max downtime ~44 jam/tahun) |
| Keamanan | Autentikasi pengguna | Eksklusif via SSO ERP — tidak ada login lokal di aplikasi toko |
| Keamanan | Enkripsi komunikasi | HTTPS/TLS 1.2+ untuk semua request termasuk SSO |
| Skalabilitas | Kapasitas item produk | Min. 10.000 SKU aktif tanpa degradasi |
| Skalabilitas | Volume transaksi harian | Min. 500 transaksi/hari |
| Audit Trail | Log perubahan data sensitif | Semua CRUD pada stok & harga terekam dengan user & timestamp |
| Backup | Frekuensi & metode backup | Harian via Telegram Bot (file `.sql.gz`), retensi lokal 7 hari |
| Kompatibilitas | Browser | Chrome, Firefox, Edge (versi 2 tahun terakhir) |
| Responsif | Antarmuka kasir/POS | Bisa digunakan di tablet (min. 768px) |

---

## 8. Desain Arsitektur Sistem

### 8.1 Stack Teknologi

| Komponen | Teknologi | Keterangan |
|---|---|---|
| Backend Framework | Laravel 12 + PHP 8.3 | Full-stack MVC framework dengan Eloquent ORM, routing ekspresif, dan ekosistem package yang mature |
| Database | MariaDB | Kompatibel penuh dengan Laravel/Eloquent; penilaian FIFO menggunakan tabel lot terpisah |
| Frontend | Blade (Laravel) + Alpine.js atau vanilla JS | UI admin, kasir/POS, laporan — semua interaksi data via AJAX |
| Web Server | Nginx atau Apache + PHP-FPM 8.3 | Konfigurasi sesuai hosting yang ada |
| File Storage | Lokal server atau object storage (MinIO/S3) | Foto produk, lampiran PO |
| Cron Job | Linux crontab | Backup harian, notifikasi reorder terjadwal |
| Telegram Bot | Telegram Bot API (`sendDocument`) | Pengiriman file backup `.sql.gz` harian |
| SSO | Laravel Http Client (wrapper Guzzle) ke ERP verify-sso endpoint | Verifikasi token SSO dari ERP |
| Notifikasi In-App | Database polling atau SSE (Server-Sent Events) | Alert reorder, perubahan harga |

### 8.2 Alur Data Antar Sistem

- **Supplier** → [Penerimaan Barang + lot FIFO] → **Stok Toko** → Antrean FIFO diperbarui.
- **User ERP** (via SSO) → [Material Request via API/UI] → **Toko** → [Pengeluaran Bahan FIFO] → Stok berkurang → [Webhook Usage] → **ERP HPP Order**.
- **Konsumen Umum** → [POS / Order Retail] → **Toko** → Stok berkurang (FIFO) → Pendapatan tercatat.
- **Cron harian 02.00** → [mysqldump + gzip] → [Telegram Bot API sendDocument] → **Grup/channel Telegram**.
- **Admin ubah harga** → [Price Change Event] → Notifikasi in-app di dashboard.

### 8.3 Standarisasi Layer AJAX

Seluruh operasi data di aplikasi toko kain **wajib diproses melalui AJAX** — tidak ada form submit konvensional yang menyebabkan full page reload. Ini berlaku untuk semua modul: master produk, kategori, supplier, PO, penerimaan barang, transaksi penjualan, material request, manajemen harga, laporan, dan pengaturan sistem.

#### 8.3.1 Prinsip Dasar

- **No full page reload untuk operasi data.** Semua CRUD (create, read, update, delete), pencarian, filter, dan paginasi dilakukan via AJAX ke endpoint controller Laravel yang khusus menangani request JSON.
- **Halaman awal boleh di-render server-side (SSR).** Blade template merender HTML awal (layout, skeleton, tabel kosong); data kemudian di-fetch dan di-populate oleh JavaScript setelah halaman siap.
- **Satu response format.** Semua endpoint AJAX mengembalikan JSON dengan struktur seragam:

```json
{
  "status": "success" | "error",
  "message": "Pesan singkat untuk ditampilkan ke user",
  "data": { ... } | null,
  "errors": { "field": ["pesan validasi"] } | null
}
```

#### 8.3.2 Konvensi Endpoint AJAX di Laravel

Semua route yang melayani request AJAX dikelompokkan dalam prefix terpisah di `routes/web.php` atau `routes/api.php` agar mudah dibedakan dari route SSR biasa:

| Jenis | Prefix URL | Contoh |
|---|---|---|
| AJAX data | `/ajax/{modul}/{aksi}` | `POST /ajax/produk/simpan` |
| AJAX lookup / autocomplete | `/ajax/{modul}/cari` | `GET /ajax/kategori/cari?q=kain` |
| AJAX delete | `/ajax/{modul}/hapus` | `DELETE /ajax/supplier/hapus/5` |
| AJAX export | `/ajax/laporan/export` | `POST /ajax/laporan/export` |

Setiap controller AJAX wajib:
1. Memverifikasi bahwa request berasal dari AJAX (`$request->expectsJson()` atau `$request->ajax()`).
2. Memvalidasi request menggunakan Laravel Form Request dengan rules yang sesuai.
3. Memproteksi route dengan middleware `auth` dan CSRF token Laravel (`@csrf` di Blade atau header `X-CSRF-TOKEN` di fetch/axios).
4. Mengembalikan `response()->json(...)` dengan HTTP status code yang sesuai (`200`, `422`, `403`, `500`).

#### 8.3.3 Penanganan di Sisi Frontend

- **Loading state:** setiap tombol aksi menampilkan indikator loading (spinner atau disabled state) sejak AJAX dikirim hingga response diterima — mencegah double-submit.
- **Validasi dua lapis:** validasi ringan di frontend (field kosong, format angka) dilakukan sebelum AJAX dikirim; validasi penuh tetap dilakukan di backend dan error dikembalikan via field `errors` dalam response JSON.
- **Feedback ke user:** response `status: "success"` menampilkan notifikasi toast/alert hijau; `status: "error"` menampilkan pesan merah — tanpa reload halaman.
- **Tabel & list data:** menggunakan DataTables atau implementasi custom yang fetch data via AJAX (`serverSide: true`) untuk mendukung paginasi, sorting, dan filter tanpa reload.

#### 8.3.4 Pengecualian

| Kondisi | Penanganan |
|---|---|
| Upload file (foto produk, lampiran PO) | Tetap via AJAX menggunakan `FormData` + `XMLHttpRequest` / `fetch` dengan `multipart/form-data` |
| Download/export laporan (CSV, Excel, PDF) | Response berupa file binary — browser menangani download otomatis via link dinamis atau Blob URL; bukan full page reload |
| Redirect setelah login SSO | Satu-satunya pengecualian redirect server-side; terjadi hanya pada alur autentikasi SSO awal |

#### 8.3.5 Standarisasi Komponen Tabel

Setiap halaman yang menampilkan data dalam bentuk tabel di seluruh modul **wajib** mengikuti standar komponen berikut. Referensi tampilan dan interaksi mengacu pada implementasi yang sudah berjalan di halaman `/master/products`.

**Fitur wajib setiap tabel:**

**1. Search (Pencarian Global)**
- Tersedia input search di atas tabel yang mencari secara real-time atau on-submit ke backend via AJAX.
- Pencarian dilakukan di sisi server (`serverSide`) — bukan filter DOM — agar tetap akurat saat data melebihi satu halaman.
- Debounce minimal 300ms pada input search untuk mengurangi jumlah request ke server.
- Placeholder input menyebutkan kolom apa saja yang dicakup, misal: *"Cari nama, SKU, atau kategori..."*

**2. Paginasi & Jumlah Data Per Halaman**
- Setiap tabel memiliki kontrol paginasi (prev/next + nomor halaman) di bawah tabel.
- Tersedia dropdown pilihan jumlah baris per halaman dengan opsi: `10`, `25`, `50`, `100`.
- Pilihan jumlah per halaman disimpan di `localStorage` per modul sehingga preferensi user tidak hilang saat reload.
- Teks informasi jumlah data ditampilkan, misal: *"Menampilkan 1–25 dari 318 data"*.
- Semua paginasi diproses via AJAX — tidak ada reload halaman saat pindah halaman.

**3. Filter & Sorting bergaya Notion**
- Setiap header kolom memiliki tombol/ikon untuk memicu filter dan sorting — konsisten dengan pola yang sudah ada di `/master/products`.
- **Sorting:** klik header kolom untuk toggle `ASC` → `DESC` → *default*; status sorting aktif ditandai dengan ikon panah di header kolom.
- **Filter per kolom:** klik ikon filter di header kolom membuka dropdown/popover filter yang relevan dengan tipe data kolom tersebut:

| Tipe Data Kolom | Jenis Filter |
|---|---|
| Teks (nama, SKU, keterangan) | Input teks contains / not contains |
| Kategori / Status / Enum | Checklist multi-pilih |
| Angka (harga, stok, qty) | Range min–max |
| Tanggal | Date range picker (dari–sampai) |
| Boolean (aktif/nonaktif) | Toggle atau checklist |

- Filter aktif ditandai secara visual di header kolom (misal: ikon atau warna berbeda) sehingga user tahu kolom mana yang sedang difilter.
- Tersedia tombol **"Reset Filter"** untuk menghapus semua filter aktif sekaligus.
- Kombinasi filter (multi-kolom), sorting, search, dan paginasi dikirim sekaligus dalam satu AJAX request ke backend.

**4. Resize Kolom**
- Setiap kolom tabel dapat di-resize lebar-nya oleh user dengan cara drag pada batas antar header kolom.
- Lebar kolom hasil resize disimpan di `localStorage` per tabel sehingga persisten lintas sesi.
- Lebar minimum kolom adalah `60px` untuk mencegah kolom terlalu sempit hingga konten tidak terbaca.
- Kolom aksi (edit, hapus, detail) dikecualikan dari resize — lebar tetap.

**Ringkasan parameter AJAX request tabel:**

```json
{
  "draw": 1,
  "start": 0,
  "length": 25,
  "search": "cotton",
  "filters": {
    "status": ["aktif"],
    "kategori_id": [3, 7],
    "stok_min": 0,
    "stok_max": 500
  },
  "order_column": "nama_produk",
  "order_dir": "asc"
}
```

---

## 9. Manajemen Pengguna & Hak Akses (RBAC)

| Modul | Admin | Manajer | Staff Gudang | Staff Penjualan | API (ERP) |
|---|---|---|---|---|---|
| Master Produk | CRUD | R | R | R | R |
| Kategori & Label | CRUD | CRUD | R | R | R |
| Master Supplier | CRUD | CRUD | R | — | — |
| Purchase Requisition (PR) | CRUD | Approve | C/R | — | — |
| Goods Receipt (Nota) | CRUD | C/R | CRUD | — | — |
| Penyesuaian Stok | CRUD | Approve | C/R | — | — |
| Material Request | CRUD | R | R/U | — | C/R |
| Penjualan Retail | CRUD | R | — | CRUD | — |
| Harga & Diskon | CRUD | R | — | R | — |
| Laporan | Full | Full | Stok | Penjualan | HPP/Usage |
| Pengaturan Sistem | Full | — | — | — | — |

> **Keterangan:** C = Create, R = Read, U = Update, D = Delete. CRUD = full access.

---

## 10. Rencana Pengembangan (Roadmap)

| Fase | Durasi Estimasi | Deliverable Utama |
|---|---|---|
| Fase 1: Core Inventori | 6 minggu | Master produk, kategori, label, supplier (auto-register), Purchase Requisition, approval per item, Goods Receipt, manajemen stok, notifikasi reorder |
| Fase 2: Transaksi | 5 minggu | Penjualan internal (Material Request), penjualan retail/POS, manajemen harga & diskon |
| Fase 3: API Integrasi | 4 minggu | REST API lengkap: Material Request, Usage, Return, HPP endpoint; webhook; API docs |
| Fase 4: Laporan & Dashboard | 3 minggu | Dashboard, laporan inventori, laporan penjualan, laporan HPP, export Excel/CSV |
| Fase 5: Hardening & QA | 3 minggu | Pengujian end-to-end, uji beban, perbaikan bug, dokumentasi pengguna, training |
| Fase 6 (Future): v2 | TBD | Integrasi marketplace, manajemen multi-gudang, BOM otomatis, aplikasi mobile gudang |

---

## 11. Risiko & Mitigasi

| Risiko | Probabilitas | Dampak | Mitigasi |
|---|---|---|---|
| Inkonsistensi data antara toko dan ERP jika API gagal | Sedang | Tinggi | Retry mechanism, dead-letter queue, reconciliation report harian |
| Penolakan adopsi oleh staff (perubahan alur kerja) | Sedang | Sedang | Training intensif, antarmuka sederhana, pendampingan go-live |
| Harga beli bahan tidak diinput saat penerimaan | Tinggi | Tinggi | Field harga wajib (mandatory) saat goods receipt, tidak bisa disimpan jika kosong |
| Stok negatif akibat transaksi konkuren | Rendah | Tinggi | Optimistic/pessimistic locking di database untuk transaksi stok |
| Integrasi ERP membutuhkan customisasi ERP yang mahal | Sedang | Sedang | API dari sisi toko dirancang fleksibel; ERP bisa consume via polling jika webhook tidak tersedia |

---

## 12. Kriteria Keberhasilan (Definition of Done)

Sistem dianggap siap go-live apabila seluruh kriteria berikut terpenuhi:

- Semua transaksi pemakaian bahan internal yang terhubung ke nomor order produksi berhasil dikirim ke ERP dan muncul di kalkulasi HPP order di ERP.
- Selisih nilai stok antara sistem toko dan stok fisik (dari stock opname awal) < 1%.
- Staff gudang dan staff penjualan berhasil menyelesaikan 10 transaksi end-to-end (PO masuk, penjualan retail, material request internal) dalam uji simulasi tanpa bantuan.
- API endpoint HPP dan Usage berhasil diintegrasikan dan divalidasi oleh tim ERP.
- Tidak ada bug kritikal (P0/P1) yang belum tertangani pada saat go-live.
- Dokumentasi API (Swagger/OpenAPI) lengkap dan tersedia.
- Backup otomatis harian berjalan dan telah diuji restore-nya.

---

## 13. Keputusan Teknis yang Sudah Disepakati

| Topik | Keputusan |
|---|---|
| Metode penilaian persediaan | FIFO — lot terlama keluar lebih dulu |
| Standarisasi layer frontend | Semua operasi data wajib via AJAX — tidak ada full page reload untuk CRUD, filter, dan paginasi |
| Integrasi ERP | ERP mendukung webhook; toko push data via webhook ke ERP |
| Format nomor order | YYMMNNN, contoh: `2604001` (April 2026, order ke-1) |
| Faktur PPN retail | Tidak diperlukan untuk versi pertama |
| Manajemen gudang | Single warehouse — cukup 1 lokasi untuk versi pertama |
| Harga khusus internal | Ditetapkan manual oleh admin; bukan otomatis = HPP |
| Stack teknologi | Laravel 12 + PHP 8.3 + MariaDB |
| Backup | Dump harian `.sql.gz` dikirim langsung ke Telegram |

---

## 14. Catatan & Pertanyaan Terbuka

Hal-hal berikut masih membutuhkan konfirmasi lebih lanjut sebelum pengembangan dimulai:

1. Siapa yang berwenang mengelola daftar pengguna yang boleh akses toko kain (whitelist user ERP)? Apakah Admin Toko, atau ada proses persetujuan dari manajemen?
2. Apakah foto produk harus bisa di-upload langsung saat membuat item baru, atau bisa ditambahkan belakangan?
3. Apakah notifikasi perubahan harga cukup in-app (di dashboard), atau perlu juga dikirim ke Telegram?
4. Berapa ukuran rata-rata database saat ini (untuk estimasi ukuran file backup `.sql.gz` harian)?
5. Apakah perlu fitur impor data produk awal dari file Excel/CSV untuk migrasi data lama ke sistem baru?

---

*— Akhir Dokumen PRD v1.2 —*

> Dokumen ini bersifat living document dan akan diperbarui seiring iterasi produk.