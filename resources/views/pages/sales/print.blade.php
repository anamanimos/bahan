<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nota Penjualan - {{ $sale->invoice_number }}</title>
    <style>
        @page {
            size: 80mm auto;
            margin: 0;
        }
        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 12px;
            line-height: 1.4;
            width: 80mm;
            margin: 0;
            padding: 10mm;
            color: #000;
        }
        .header {
            text-align: center;
            margin-bottom: 5mm;
        }
        .header h1 {
            font-size: 16px;
            margin: 0;
            text-transform: uppercase;
        }
        .separator {
            border-top: 1px dashed #000;
            margin: 2mm 0;
        }
        .info-table {
            width: 100%;
            margin-bottom: 3mm;
        }
        .info-table td {
            vertical-align: top;
        }
        .item-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 3mm;
        }
        .item-table th {
            text-align: left;
            border-bottom: 1px dashed #000;
            padding-bottom: 1mm;
        }
        .item-table td {
            padding: 1mm 0;
        }
        .text-right {
            text-align: right;
        }
        .total-section {
            margin-top: 3mm;
        }
        .footer {
            text-align: center;
            margin-top: 5mm;
            font-size: 10px;
        }
        @media print {
            .no-print {
                display: none;
            }
        }
        .btn-print {
            background: #000;
            color: #fff;
            padding: 5px 15px;
            border: none;
            cursor: pointer;
            margin-bottom: 5mm;
        }
    </style>
</head>
<body>
    <div class="no-print" style="padding: 10px; text-align: center;">
        <button class="btn-print" onclick="window.print()">Cetak Nota</button>
        <button class="btn-print" style="background: #666;" onclick="window.close()">Tutup</button>
    </div>

    <div class="header">
        <h1>DAMAI JAYA</h1>
        <div>Jl. Raya No. 123, Kota</div>
        <div>Telp: 0812-3456-7890</div>
    </div>

    <div class="separator"></div>

    <table class="info-table">
        <tr>
            <td>No. Nota</td>
            <td class="text-right">{{ $sale->invoice_number }}</td>
        </tr>
        <tr>
            <td>Tanggal</td>
            <td class="text-right">{{ $sale->sale_date->format('d/m/Y') }}</td>
        </tr>
        <tr>
            <td>Kasir</td>
            <td class="text-right">{{ $sale->creator->name ?? 'System' }}</td>
        </tr>
        <tr>
            <td>Pelanggan</td>
            <td class="text-right">{{ $sale->customer->name }}</td>
        </tr>
    </table>

    <div class="separator"></div>

    <table class="item-table">
        <thead>
            <tr>
                <th>Item</th>
                <th class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($sale->items as $item)
            <tr>
                <td>
                    {{ $item->product->name }}<br>
                    <small>{{ number_format($item->quantity, 2) }} {{ $item->product->base_unit }} x {{ number_format($item->unit_price, 0, ',', '.') }}</small>
                    @if($item->order_reference)
                        <br><small>Ref: {{ $item->order_reference }}</small>
                    @endif
                </td>
                <td class="text-right" style="vertical-align: bottom;">
                    {{ number_format($item->subtotal, 0, ',', '.') }}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="separator"></div>

    <table class="info-table total-section">
        <tr style="font-weight: bold; font-size: 14px;">
            <td>TOTAL</td>
            <td class="text-right">Rp {{ number_format($sale->total_amount, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td>Metode</td>
            <td class="text-right">{{ $sale->payment_method }}</td>
        </tr>
    </table>

    <div class="separator"></div>

    <div class="footer">
        Terima Kasih Atas Kunjungan Anda<br>
        Barang yang sudah dibeli tidak dapat ditukar/dikembalikan
    </div>

    <script>
        // Auto print on load
        window.onload = function() {
            // window.print();
        }
    </script>
</body>
</html>
