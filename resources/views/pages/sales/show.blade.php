@extends('layouts.app')

@section('title', 'Detail Penjualan - ' . $sale->invoice_number)

@section('breadcrumb')
    <li class="breadcrumb-item"><span class="bullet bg-gray-500 w-5px h-2px"></span></li>
    <li class="breadcrumb-item text-muted"><a href="{{ route('sales.index') }}" class="text-muted text-hover-primary">Penjualan</a></li>
    <li class="breadcrumb-item"><span class="bullet bg-gray-500 w-5px h-2px"></span></li>
    <li class="breadcrumb-item text-muted">Detail</li>
@endsection

@section('content')
<div class="d-flex flex-column gap-7 gap-lg-10">
    <div class="d-flex flex-wrap flex-stack gap-5 gap-lg-10">
        <ul class="nav nav-custom nav-tabs nav-line-tabs nav-line-tabs-2x border-0 fs-4 fw-semibold mb-lg-n2 me-auto">
            <li class="nav-item">
                <a class="nav-link text-active-primary pb-4 active" data-bs-toggle="tab" href="#kt_sale_details_general">Informasi Umum</a>
            </li>
        </ul>
        <div class="d-flex gap-2 ms-auto">
            <a href="{{ route('sales.index') }}" class="btn btn-icon btn-light btn-sm">
                <i class="ki-duotone ki-left fs-2"></i>
            </a>
            <a href="{{ route('sales.print', $sale->uuid) }}" target="_blank" class="btn btn-success btn-sm d-flex align-items-center">
                <i class="ki-duotone ki-printer fs-2 me-1"></i> <span class="d-none d-sm-inline">Cetak Nota</span>
            </a>
        </div>
    </div>

    <div class="tab-content">
        <div class="tab-pane fade show active" id="kt_sale_details_general" role="tab-panel">
            <div class="d-flex flex-column gap-7 gap-lg-10">
                <div class="row g-5 g-xl-10">
                    <div class="col-md-6 col-xl-4">
                        <div class="card card-flush py-4 h-100">
                            <div class="card-header border-0">
                                <div class="card-title">
                                    <h2>Detail Penjualan</h2>
                                </div>
                            </div>
                            <div class="card-body pt-0">
                                <div class="d-flex flex-column gap-3">
                                    <div class="d-flex justify-content-between align-items-center border-bottom border-gray-200 pb-2">
                                        <span class="text-gray-400 fw-bold">Invoice</span>
                                        <span class="text-gray-800 fw-bold">{{ $sale->invoice_number }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center border-bottom border-gray-200 pb-2">
                                        <span class="text-gray-400 fw-bold">Tanggal</span>
                                        <span class="text-gray-800 fw-bold">{{ $sale->sale_date->format('d/m/Y') }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center border-bottom border-gray-200 pb-2">
                                        <span class="text-gray-400 fw-bold">Kasir</span>
                                        <span class="text-gray-800 fw-bold">{{ $sale->creator->name ?? 'System' }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center border-bottom border-gray-200 pb-2">
                                        <span class="text-gray-400 fw-bold">Status</span>
                                        <span class="badge badge-light-success fw-bold">{{ $sale->status }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="text-gray-400 fw-bold">Pembayaran</span>
                                        <span class="text-gray-800 fw-bold">{{ $sale->payment_method }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-xl-4">
                        <div class="card card-flush py-4 h-100">
                            <div class="card-header border-0">
                                <div class="card-title">
                                    <h2>Pelanggan</h2>
                                </div>
                            </div>
                            <div class="card-body pt-0">
                                <div class="d-flex flex-column gap-3">
                                    <div class="d-flex justify-content-between align-items-center border-bottom border-gray-200 pb-2">
                                        <span class="text-gray-400 fw-bold">Nama</span>
                                        <span class="text-gray-800 fw-bold">{{ $sale->customer->name }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center border-bottom border-gray-200 pb-2">
                                        <span class="text-gray-400 fw-bold">Tipe</span>
                                        <span class="badge badge-light-primary fw-bold">{{ ucfirst($sale->customer->type) }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center border-bottom border-gray-200 pb-2">
                                        <span class="text-gray-400 fw-bold">Email</span>
                                        <span class="text-gray-800 fw-bold text-truncate ms-2" style="max-width: 150px;">{{ $sale->customer->email ?? '-' }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="text-gray-400 fw-bold">Telepon</span>
                                        <span class="text-gray-800 fw-bold">{{ $sale->customer->phone ?? '-' }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card card-flush py-4">
                    <div class="card-header">
                        <div class="card-title">
                            <h2>Daftar Barang</h2>
                        </div>
                    </div>
                    <div class="card-body pt-0">
                        <div class="table-responsive">
                            <table class="table align-middle table-row-dashed fs-6 gy-5">
                                <thead>
                                    <tr class="text-start text-gray-400 fw-bold fs-7 text-uppercase gs-0">
                                        <th class="min-w-150px">Produk</th>
                                        <th class="min-w-100px d-none d-md-table-cell">Lot</th>
                                        <th class="min-w-100px d-none d-sm-table-cell">Ref. Order</th>
                                        <th class="min-w-70px text-end">Jumlah</th>
                                        <th class="min-w-100px text-end">Total</th>
                                    </tr>
                                </thead>
                                <tbody class="fw-semibold text-gray-600">
                                    @foreach($sale->items as $item)
                                    <tr>
                                        <td>
                                            <div class="d-flex flex-column">
                                                <span class="text-gray-800 fs-6 fw-bold">{{ $item->product->name }}</span>
                                                <span class="text-muted fs-8 d-md-none">Lot: {{ $item->lot->identifier ?? 'N/A' }}</span>
                                                @if($item->order_reference)
                                                    <span class="text-primary fs-8 d-sm-none">Ref: {{ $item->order_reference }}</span>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="d-none d-md-table-cell">
                                            <span class="badge badge-light fw-bold">{{ $item->lot->identifier ?? 'N/A' }}</span>
                                        </td>
                                        <td class="d-none d-sm-table-cell">
                                            @if($item->order_reference)
                                                <span class="badge badge-light-primary fw-bold">{{ $item->order_reference }}</span>
                                            @else
                                                <span class="text-muted fs-7 italic">-</span>
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            <div class="d-flex flex-column align-items-end">
                                                <span class="text-gray-800">{{ number_format($item->quantity, 2) }} {{ $item->product->base_unit }}</span>
                                                <span class="text-muted fs-8">@ {{ number_format($item->unit_price, 0, ',', '.') }}</span>
                                            </div>
                                        </td>
                                        <td class="text-end fw-bold text-gray-900">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="4" class="text-end fs-4 text-gray-800 d-none d-sm-table-cell">TOTAL</th>
                                        <th colspan="3" class="text-end fs-4 text-gray-800 d-sm-none">TOTAL</th>
                                        <th class="text-end fs-4 fw-bold text-gray-900">Rp {{ number_format($sale->total_amount, 0, ',', '.') }}</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
