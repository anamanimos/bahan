<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Webhook;
use Illuminate\Http\Request;

class WebhookController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $webhooks = Webhook::latest()->get();
        return view('pages.admin.webhook.index', compact('webhooks'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('pages.admin.webhook.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'url' => 'required|url|max:255',
            'secret' => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ]);

        Webhook::create($request->all());

        return redirect()->route('admin.webhook.index')
            ->with('success', 'Webhook berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Webhook $webhook)
    {
        return view('pages.admin.webhook.edit', compact('webhook'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Webhook $webhook)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'url' => 'required|url|max:255',
            'secret' => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ]);

        $webhook->update($request->all());

        return redirect()->route('admin.webhook.index')
            ->with('success', 'Webhook berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Webhook $webhook)
    {
        $webhook->delete();

        return response()->json([
            'success' => true,
            'message' => 'Webhook berhasil dihapus.'
        ]);
    }

    /**
     * Display the webhook documentation.
     */
    public function documentation()
    {
        return view('pages.admin.webhook.documentation');
    }

    /**
     * Download the webhook documentation as Markdown.
     */
    public function downloadMarkdown()
    {
        $content = "# Panduan Integrasi Webhook Bahan\n\n" .
            "Webhook memungkinkan aplikasi eksternal untuk menerima notifikasi secara real-time saat terjadi perubahan stok atau transaksi di sistem ini.\n\n" .
            "## Struktur Payload (JSON)\n\n" .
            "Setiap request dikirim menggunakan metode `POST` dengan payload JSON.\n\n" .
            "### 1. Penjualan (Create Sale)\n" .
            "```json\n" .
            "{\n" .
            "  \"timestamp\": \"2026-05-04T12:00:00Z\",\n" .
            "  \"action\": \"create\",\n" .
            "  \"source\": \"sale\",\n" .
            "  \"data\": {\n" .
            "    \"sale_id\": 123,\n" .
            "    \"invoice_number\": \"INV-20260504-0001\",\n" .
            "    \"order_number\": \"ERP-ORDER-999\",\n" .
            "    \"customer\": {\n" .
            "      \"name\": \"Budi Santoso\",\n" .
            "      \"type\": \"retail\"\n" .
            "    },\n" .
            "    \"items\": [\n" .
            "      {\n" .
            "        \"sku\": \"PROD-001\",\n" .
            "        \"name\": \"Bahan Kain Katun\",\n" .
            "        \"lot\": \"LOT-20260504-001-1\",\n" .
            "        \"order_reference\": \"ERP-ORDER-999\",\n" .
            "        \"quantity\": 5.5,\n" .
            "        \"unit\": \"meter\",\n" .
            "        \"price\": 55000,\n" .
            "        \"subtotal\": 302500\n" .
            "      }\n" .
            "    ]\n" .
            "  }\n" .
            "}\n" .
            "```\n\n" .
            "### 2. Pembatalan Penjualan (Delete Sale)\n" .
            "```json\n" .
            "{\n" .
            "  \"timestamp\": \"2026-05-04T12:05:00Z\",\n" .
            "  \"action\": \"delete\",\n" .
            "  \"source\": \"sale\",\n" .
            "  \"data\": {\n" .
            "    \"sale_id\": 123,\n" .
            "    \"invoice_number\": \"INV-20260504-0001\",\n" .
            "    \"deleted_at\": \"2026-05-04T12:05:00Z\"\n" .
            "  }\n" .
            "}\n" .
            "```\n\n" .
            "### 3. Penerimaan Barang (Create Purchase)\n" .
            "```json\n" .
            "{\n" .
            "  \"timestamp\": \"2026-05-04T12:10:00Z\",\n" .
            "  \"action\": \"create\",\n" .
            "  \"source\": \"purchase\",\n" .
            "  \"data\": {\n" .
            "    \"goods_receipt_id\": 45,\n" .
            "    \"identifier\": \"GR-20260504-001\",\n" .
            "    \"order_number\": \"ERP-PO-888\",\n" .
            "    \"supplier\": \"Toko Bahan Sejahtera\",\n" .
            "    \"items\": [\n" .
            "      {\n" .
            "        \"sku\": \"PROD-002\",\n" .
            "        \"name\": \"Kain Silk Premium\",\n" .
            "        \"lot\": \"LOT-20260504-001-1\",\n" .
            "        \"order_reference\": \"ERP-PO-888\",\n" .
            "        \"quantity\": 50,\n" .
            "        \"unit\": \"meter\",\n" .
            "        \"price\": 75000,\n" .
            "        \"total\": 3750000\n" .
            "      }\n" .
            "    ]\n" .
            "  }\n" .
            "}\n" .
            "```\n\n" .
            "### 4. Penghapusan Produk (Delete Product)\n" .
            "```json\n" .
            "{\n" .
            "  \"timestamp\": \"2026-05-04T12:15:00Z\",\n" .
            "  \"action\": \"delete\",\n" .
            "  \"source\": \"product\",\n" .
            "  \"data\": {\n" .
            "    \"product_id\": 10,\n" .
            "    \"sku\": \"PROD-OLD-99\",\n" .
            "    \"name\": \"Bahan Lama\",\n" .
            "    \"deleted_at\": \"2026-05-04T12:15:00Z\"\n" .
            "  }\n" .
            "}\n" .
            "```\n\n" .
            "### 5. Penggabungan Produk (Merge Product)\n" .
            "```json\n" .
            "{\n" .
            "  \"timestamp\": \"2026-05-04T12:20:00Z\",\n" .
            "  \"action\": \"merge\",\n" .
            "  \"source\": \"product\",\n" .
            "  \"data\": {\n" .
            "    \"target_id\": 10,\n" .
            "    \"source_ids\": [12, 15, 18],\n" .
            "    \"timestamp\": \"2026-05-04T12:20:00Z\"\n" .
            "  }\n" .
            "}\n" .
            "```\n\n" .
            "## Event Triggers\n\n" .
            "| Event (action + source) | Deskripsi |\n" .
            "|---|---|\n" .
            "| `create sale` | Terjadi saat transaksi penjualan (POS) selesai disimpan. |\n" .
            "| `delete sale` | Terjadi saat data penjualan dihapus. |\n" .
            "| `create purchase` | Terjadi saat penerimaan barang (Goods Receipt) dikonfirmasi. |\n" .
            "| `delete product` | Terjadi saat master data produk dihapus. |\n" .
            "| `merge product` | Terjadi saat beberapa produk digabung menjadi satu. |\n\n" .
            "## Headers\n" .
            "- `Content-Type: application/json`\n" .
            "- `X-Webhook-Secret`: Token rahasia Anda.\n";

        return response($content)
            ->header('Content-Type', 'text/markdown')
            ->header('Content-Disposition', 'attachment; filename="webhook-bahan.md"');
    }
}
