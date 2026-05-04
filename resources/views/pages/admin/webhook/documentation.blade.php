@extends('layouts.app')

@section('title', 'Dokumentasi Webhook')

@section('breadcrumb')
    <li class="breadcrumb-item"><span class="bullet bg-gray-500 w-5px h-2px"></span></li>
    <li class="breadcrumb-item text-muted">Super Admin</li>
    <li class="breadcrumb-item"><span class="bullet bg-gray-500 w-5px h-2px"></span></li>
    <li class="breadcrumb-item text-muted">Webhook</li>
    <li class="breadcrumb-item"><span class="bullet bg-gray-500 w-5px h-2px"></span></li>
    <li class="breadcrumb-item text-muted">Dokumentasi</li>
@endsection

@section('content')
<div class="card card-flush">
    <div class="card-header pt-7">
        <div class="card-title d-flex flex-column">
            <span class="fs-2hx fw-bold text-gray-900 me-2 lh-1 ls-n2">Panduan Integrasi Webhook</span>
            <span class="text-gray-400 pt-1 fw-semibold fs-6">Cara menerima dan memproses data dari aplikasi Bahan</span>
        </div>
        <div class="card-toolbar">
            <a href="{{ route('admin.webhook.download-markdown') }}" class="btn btn-light-info btn-sm">
                <i class="ki-duotone ki-file-down fs-2"><span class="path1"></span><span class="path2"></span></i> Download Markdown (MD)
            </a>
        </div>
    </div>
    <div class="card-body">
        <div class="mb-10">
            <h3 class="fw-bold text-gray-800 mb-3">Pendahuluan</h3>
            <p class="fs-6 text-gray-600">
                Webhook memungkinkan aplikasi eksternal (seperti ERP atau Dashboard Monitoring) untuk menerima notifikasi secara real-time saat terjadi perubahan stok atau transaksi penjualan di sistem ini.
            </p>
        </div>

        <div class="separator separator-dashed mb-10"></div>

        <div class="mb-10">
            <h3 class="fw-bold text-gray-800 mb-3">Struktur Payload (JSON)</h3>
            <p class="fs-6 text-gray-600 mb-5">Setiap request dikirim menggunakan metode <code>POST</code> dengan payload JSON berikut:</p>
            
            <div class="mb-5">
                <h4 class="fw-bold fs-6 text-gray-700 mb-2">1. Penjualan (Create Sale)</h4>
                <div class="bg-light p-5 rounded">
                    <pre class="mb-0 text-dark">
{
  "timestamp": "2026-05-04T12:00:00Z",
  "action": "create",
  "source": "sale",
  "data": {
    "sale_id": 123,
    "invoice_number": "INV-20260504-0001",
    "order_number": "ERP-ORDER-999",
    "customer": {
      "name": "Budi Santoso",
      "type": "retail"
    },
    "items": [
      {
        "sku": "PROD-001",
        "name": "Bahan Kain Katun",
        "lot": "LOT-20260504-001-1",
        "order_reference": "ERP-ORDER-999",
        "quantity": 5.5,
        "unit": "meter",
        "price": 55000,
        "subtotal": 302500
      }
    ]
  }
}
                    </pre>
                </div>
            </div>

            <div class="mb-5">
                <h4 class="fw-bold fs-6 text-gray-700 mb-2">2. Pembatalan Penjualan (Delete Sale)</h4>
                <div class="bg-light p-5 rounded">
                    <pre class="mb-0 text-dark">
{
  "timestamp": "2026-05-04T12:05:00Z",
  "action": "delete",
  "source": "sale",
  "data": {
    "sale_id": 123,
    "invoice_number": "INV-20260504-0001",
    "deleted_at": "2026-05-04T12:05:00Z"
  }
}
                    </pre>
                </div>
            </div>

            <div class="mb-5">
                <h4 class="fw-bold fs-6 text-gray-700 mb-2">3. Penerimaan Barang (Create Purchase)</h4>
                <p class="fs-7 text-gray-500 mb-2 italic">Data dikirimkan per lot (jika satu item memiliki 2 lot, maka akan muncul 2 baris data dalam array items).</p>
                <div class="bg-light p-5 rounded">
                    <pre class="mb-0 text-dark">
{
  "timestamp": "2026-05-04T12:10:00Z",
  "action": "create",
  "source": "purchase",
  "data": {
    "goods_receipt_id": 45,
    "identifier": "GR-20260504-001",
    "order_number": "ERP-PO-888",
    "supplier": "Toko Bahan Sejahtera",
    "items": [
      {
        "sku": "PROD-002",
        "name": "Kain Silk Premium",
        "lot": "LOT-20260504-001-1",
        "order_reference": "ERP-PO-888",
        "quantity": 50,
        "unit": "meter",
        "price": 75000,
        "total": 3750000
      }
    ]
  }
}
                    </pre>
                </div>
            </div>

            <div class="mb-5">
                <h4 class="fw-bold fs-6 text-gray-700 mb-2">4. Penghapusan Produk (Delete Product)</h4>
                <div class="bg-light p-5 rounded">
                    <pre class="mb-0 text-dark">
{
  "timestamp": "2026-05-04T12:15:00Z",
  "action": "delete",
  "source": "product",
  "data": {
    "product_id": 10,
    "sku": "PROD-OLD-99",
    "name": "Bahan Lama",
    "deleted_at": "2026-05-04T12:15:00Z"
  }
}
                    </pre>
                </div>
            </div>

            <div class="mb-5">
                <h4 class="fw-bold fs-6 text-gray-700 mb-2">5. Penggabungan Produk (Merge Product)</h4>
                <div class="bg-light p-5 rounded">
                    <pre class="mb-0 text-dark">
{
  "timestamp": "2026-05-04T12:20:00Z",
  "action": "merge",
  "source": "product",
  "data": {
    "target_id": 10,
    "source_ids": [12, 15, 18],
    "timestamp": "2026-05-04T12:20:00Z"
  }
}
                    </pre>
                </div>
            </div>
        </div>

        <div class="mb-10">
            <h3 class="fw-bold text-gray-800 mb-3">Headers</h3>
            <ul class="fs-6 text-gray-600">
                <li><code>Content-Type: application/json</code></li>
                <li><code>X-Webhook-Secret: (Token yang Anda tentukan di pengaturan)</code></li>
            </ul>
        </div>

        <div class="mb-10">
            <h3 class="fw-bold text-gray-800 mb-3">Event Triggers</h3>
            <div class="table-responsive">
                <table class="table table-row-bordered table-row-gray-300 align-middle">
                    <thead>
                        <tr class="fw-bold fs-6 text-gray-800">
                            <th>Event (action + source)</th>
                            <th>Deskripsi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><code>create sale</code></td>
                            <td>Terjadi saat transaksi penjualan (POS) selesai disimpan.</td>
                        </tr>
                        <tr>
                            <td><code>delete sale</code></td>
                            <td>Terjadi saat data penjualan dihapus. Stok otomatis dikembalikan ke lot asal.</td>
                        </tr>
                        <tr>
                            <td><code>create purchase</code></td>
                            <td>Terjadi saat penerimaan barang (Goods Receipt) dikonfirmasi.</td>
                        </tr>
                        <tr>
                            <td><code>delete product</code></td>
                            <td>Terjadi saat master data produk dihapus.</td>
                        </tr>
                        <tr>
                            <td><code>merge product</code></td>
                            <td>Terjadi saat beberapa produk digabung menjadi satu. Field <code>target_id</code> adalah produk yang dipertahankan.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="text-end mt-10">
            <a href="{{ route('admin.webhook.index') }}" class="btn btn-primary">Kembali ke Daftar Webhook</a>
        </div>
    </div>
</div>
@endsection
