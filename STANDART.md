# Standarisasi Pengembangan Aplikasi Toko Kain & Bahan Pelengkap Garment

Dokumen ini mendefinisikan standar teknis, struktur, dan arsitektur untuk pengembangan aplikasi Toko Kain yang terintegrasi dengan ERP Konveksi. Standar ini merujuk pada **PRD v1.1** dan menggunakan **Metronic v8.3.2 Demo 1** sebagai basis UI.

---

## 1. Arsitektur & Stack Teknologi
- **Framework:** CodeIgniter 3.1.13 (Optimized for PHP 8.3)
- **Database:** MariaDB (Engine: InnoDB)
- **UI Template:** Metronic v8.3.2 - Demo 1
- **Metode Inventori:** FIFO (First-In First-Out) dengan Tabel Lot.
- **Autentikasi:** SSO Terintegrasi ERP (No local password storage).

---

## 2. Standar Struktur Direktori (Clean Code MCP)
Untuk menjaga keteraturan dan kemudahan audit, struktur folder harus mengikuti pola berikut:

```text
application/
├── controllers/
│   ├── api/             # Semua REST API endpoints (Material Request, Usage, etc)
│   ├── auth/            # Logika SSO & Session Management
│   ├── inventory/       # Modul Produk, PO, Penerimaan, Penyesuaian
│   └── sales/           # Modul POS, Retail Order, Internal Request
├── models/
│   ├── inventory/       # Logic database stok & perhitungan FIFO
│   └── sales/           # Logic database transaksi penjualan
├── views/
│   ├── layouts/         # Master template (header, sidebar, footer)
│   ├── components/      # Reusable UI fragments (modals, cards, forms)
│   └── pages/           # Konten spesifik per modul (Product List, POS UI)
└── third_party/         # Library eksternal (Telegram Bot API, docx-reader)
assets/
├── custom/              # File buatan sendiri (WAJIB EXTERNAL)
│   ├── css/             # style.css tambahan (jika sangat diperlukan)
│   └── js/              # Logika frontend per halaman (Clean Code)
└── vendors/             # Asset asli dari Metronic v8.3.2 (Dilarang modifikasi langsung)
```

---

## 3. Standar UI/UX & Asset Management
Berdasarkan instruksi, penggunaan CSS/JS harus mematuhi aturan berikut:

### 3.1 Penggunaan Template Metronic
- **Master Layout:** Gunakan `layout/demo1/dark-sidebar.html` sebagai basis standar.
- **Tanpa CSS Baru:** Optimalkan class-class utility dari Bootstrap 5 dan Metronic (misal: `bg-light-primary`, `text-gray-800`, `fw-bold`, `card-flush`) untuk menghindari pembuatan file CSS baru.
- **External Only:** DILARANG keras menggunakan tag `<style>` atau atribut `style="..."` (inline) di dalam HTML. Semua styling tambahan harus diletakkan di `assets/custom/css/`.

### 3.2 JavaScript Modular (MCP Standards)
- Setiap halaman wajib memiliki satu file JS eksternal di `assets/custom/js/pages/[module_name].js`.
- Gunakan **Class Definition** atau **Object Literal** (Pola Metronic) untuk merangkum fungsionalitas.
- Contoh Implementasi JS:
  ```javascript
  "use strict";
  var TKAppInventory = function() {
      // Private variables
      var table;
      // Private functions
      var initDataTable = function() { ... };
      // Public methods
      return {
          init: function() {
              initDataTable();
          }
      };
  }();
  // On document ready
  KTUtil.onDOMContentLoaded(function() {
      TKAppInventory.init();
  });
  ```

---

## 4. Standar Database & Penamaan
- **Naming Convention:** `snake_case` untuk nama tabel dan kolom (misal: `m_products`, `t_sales_detail`).
- **Audit Fields:** Setiap tabel transaksi/master wajib memiliki:
  - `created_at`, `updated_at` (timestamp)
  - `created_by`, `updated_by` (email user dari SSO ERP)
- **Primary Key:** `id` (BigInt, Auto Increment).

---

## 5. Integrasi & Keamanan
- **API Security:** Wajib menggunakan Header `X-API-Key` dan validasi TLS 1.2+.
- **SSO Flow:**
  1. ERP redirect ke Toko dengan Token & Email.
  2. Toko melakukan verifikasi Token via Backend (POST) ke API ERP.
  3. Session lokal dibuat hanya setelah verifikasi sukses.
- **Error Handling:** Gunakan response JSON standar untuk semua request AJAX/API: `{ "status": "success/error", "message": "...", "data": [...] }`.

---

## 6. Mapping Komponen Metronic ke Fitur PRD
| Fitur PRD | Komponen Metronic Demo 1 |
| :--- | :--- |
| Dashboard Utama | `dashboards/ecommerce.html` |
| Katalog Produk | `apps/ecommerce/catalog/products.html` |
| POS / Kasir Retail | `dashboards/pos.html` |
| User Management | `apps/user-management/users/list.html` |
| Material Request | `apps/ecommerce/sales/listing.html` |
| Manajemen Harga | `apps/ecommerce/catalog/edit-product.html` |
| Notifikasi Alert | `widgets/lists.html` |

---

## 7. Backup & Maintenance
- **Telegram Backup:** Script `mysqldump` dijalankan via Linux Crontab harian pukul 02:00 WIB.
- **Log Audit:** Semua perubahan harga dan stok wajib dicatat dalam tabel `t_logs`.
