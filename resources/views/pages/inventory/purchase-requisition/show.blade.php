@extends('layouts.app')

@section('title', 'Detail Pengajuan Beli - ' . $requisition->identifier)

@section('breadcrumb')
    <li class="breadcrumb-item"><span class="bullet bg-gray-500 w-5px h-2px"></span></li>
    <li class="breadcrumb-item text-muted">Inventori</li>
    <li class="breadcrumb-item"><span class="bullet bg-gray-500 w-5px h-2px"></span></li>
    <li class="breadcrumb-item text-muted"><a href="{{ route('inventory.purchase-requisition.index') }}" class="text-muted text-hover-primary">Daftar PR</a></li>
    <li class="breadcrumb-item"><span class="bullet bg-gray-500 w-5px h-2px"></span></li>
    <li class="breadcrumb-item text-muted">Detail PR</li>
@endsection

@section('content')
<div class="d-flex flex-column gap-7 gap-lg-10">
    <!--begin::Order summary-->
    <div class="d-flex flex-column flex-xl-row gap-7 gap-lg-10">
        <!--begin::Order details-->
        <div class="card card-flush py-4 flex-row-fluid">
            <!--begin::Card header-->
            <div class="card-header">
                <div class="card-title">
                    <h2>Rincian Pengajuan (#{{ $requisition->identifier }})</h2>
                </div>
            </div>
            <!--end::Card header-->
            <!--begin::Card body-->
            <div class="card-body pt-0">
                <div class="table-responsive">
                    <!--begin::Table-->
                    <table class="table align-middle table-row-bordered mb-0 fs-6 gy-5 min-w-300px">
                        <tbody class="fw-semibold text-gray-600">
                            <tr>
                                <td class="text-muted">
                                    <div class="d-flex align-items-center">
                                        <i class="ki-duotone ki-calendar fs-2 me-2"><span class="path1"></span><span class="path2"></span></i> Tanggal Pengajuan
                                    </div>
                                </td>
                                <td class="fw-bold text-end text-gray-800">{{ $requisition->created_at->translatedFormat('d F Y, H:i') }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">
                                    <div class="d-flex align-items-center">
                                        <i class="ki-duotone ki-user fs-2 me-2"><span class="path1"></span><span class="path2"></span></i> Diajukan Oleh
                                    </div>
                                </td>
                                <td class="fw-bold text-end text-gray-800">Admin Gudang</td>
                            </tr>
                            <tr>
                                <td class="text-muted">
                                    <div class="d-flex align-items-center">
                                        <i class="ki-duotone ki-status fs-2 me-2"><span class="path1"></span><span class="path2"></span></i> Status
                                    </div>
                                </td>
                                <td class="text-end">
                                    @php
                                        $statusClass = [
                                            'Draft' => 'badge-light-dark',
                                            'Submitted' => 'badge-light-warning',
                                            'Approved' => 'badge-light-success',
                                            'Rejected' => 'badge-light-danger',
                                            'Partially Approved' => 'badge-light-info',
                                            'Completed' => 'badge-light-success',
                                        ][$requisition->status] ?? 'badge-light-primary';
                                    @endphp
                                    <span class="badge {{ $statusClass }} fw-bold">{{ $requisition->status }}</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <!--end::Table-->
                </div>
            </div>
            <!--end::Card body-->
        </div>
        <!--end::Order details-->

        <!--begin::Notes-->
        <div class="card card-flush py-4 flex-row-fluid">
            <!--begin::Card header-->
            <div class="card-header">
                <div class="card-title">
                    <h2>Catatan Tambahan</h2>
                </div>
            </div>
            <!--end::Card header-->
            <!--begin::Card body-->
            <div class="card-body pt-0">
                <p class="text-gray-600 fs-6">
                    {{ $requisition->notes ?: 'Tidak ada catatan tambahan untuk pengajuan ini.' }}
                </p>
            </div>
            <!--end::Card body-->
        </div>
        <!--end::Notes-->
    </div>
    <!--end::Order summary-->

    <!--begin::Product List-->
    <div class="card card-flush py-4 flex-row-fluid overflow-hidden d-none d-md-block">
        <!--begin::Card header-->
        <div class="card-header">
            <div class="card-title">
                <h2>Daftar Barang yang Diajukan</h2>
            </div>
        </div>
        <!--end::Card header-->
        <!--begin::Card body-->
        <div class="card-body pt-0">
            <div class="table-responsive">
                <!--begin::Table-->
                <table class="table align-middle table-row-dashed fs-6 gy-5 mb-0">
                    <thead>
                        <tr class="text-start text-gray-400 fw-bold fs-7 text-uppercase gs-0">
                            <th class="min-w-175px">Produk / Bahan</th>
                            <th class="min-w-100px">Status Item</th>
                            <th class="min-w-125px">Tujuan (Context)</th>
                            <th class="min-w-100px text-end">Jumlah</th>
                            <th class="min-w-125px text-end">Est. Harga</th>
                            <th class="min-w-125px text-end">Total Estimasi</th>
                        </tr>
                    </thead>
                    <tbody class="fw-semibold text-gray-800">
                        @php $grandTotal = 0; @endphp
                        @foreach($requisition->items as $item)
                            @php 
                                $subtotal = $item->requested_quantity * $item->estimated_unit_price; 
                                $grandTotal += $subtotal;
                                $itemStatusColor = [
                                    'Pending' => 'warning',
                                    'Approved' => 'success',
                                    'Rejected' => 'danger'
                                ][$item->status] ?? 'primary';
                            @endphp
                            <tr class="{{ $item->status == 'Rejected' ? 'bg-light-danger bg-opacity-10' : '' }}">
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="ms-0">
                                            <div class="text-gray-800 text-hover-primary fs-6 fw-bold">{{ $item->product->name }}</div>
                                            <div class="text-muted fs-7">SKU: {{ $item->product->sku ?: '-' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex flex-column">
                                        <span class="badge badge-light-{{ $itemStatusColor }} fw-bold fs-8 w-fit">{{ $item->status }}</span>
                                        @if($item->notes)
                                            <span class="text-muted fs-9 mt-1 italic">Note: {{ $item->notes }}</span>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex flex-column">
                                        <span class="badge badge-light-{{ $item->context == 'Order' ? 'success' : 'info' }} fw-bold px-2 py-1 fs-8 mb-1 w-fit">{{ $item->context }}</span>
                                        @if($item->erp_order_reference)
                                            <span class="text-muted fs-8">Ref: {{ $item->erp_order_reference }}</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="text-end">
                                    {{ number_format($item->requested_quantity, 2, ',', '.') }} {{ $item->unit }}
                                </td>
                                <td class="text-end">
                                    Rp {{ number_format($item->estimated_unit_price, 0, ',', '.') }}
                                </td>
                                <td class="text-end text-primary fw-bold">
                                    Rp {{ number_format($subtotal, 0, ',', '.') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="4" class="text-end fs-4 fw-bold">Total Estimasi Keseluruhan:</td>
                            <td class="text-end fs-4 fw-bolder text-success">Rp {{ number_format($grandTotal, 0, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                </table>
                <!--end::Table-->
            </div>
        </div>
        <!--end::Card body-->
    </div>
    <!--end::Product List-->

    <!--begin::Mobile Product List-->
    <div class="d-md-none">
        <h2 class="fw-bold mb-5 px-1">Daftar Barang yang Diajukan</h2>
        @php $grandTotal = 0; @endphp
        @foreach($requisition->items as $item)
            @php 
                $subtotal = $item->requested_quantity * $item->estimated_unit_price; 
                $grandTotal += $subtotal;
            @endphp
            <div class="bg-white rounded shadow-sm mb-4 p-5 border border-gray-200">
                <div class="d-flex flex-stack mb-3">
                    <div class="d-flex flex-column">
                        <span class="fw-bold text-gray-800 fs-6">{{ $item->product->name }}</span>
                        <span class="text-muted fs-8">SKU: {{ $item->product->sku ?: '-' }}</span>
                    </div>
                    @php
                        $itemStatusColor = [
                            'Pending' => 'warning',
                            'Approved' => 'success',
                            'Rejected' => 'danger'
                        ][$item->status] ?? 'primary';
                    @endphp
                    <span class="badge badge-light-{{ $itemStatusColor }} fw-bold fs-9">{{ $item->status }}</span>
                </div>
                
                <div class="d-flex flex-stack mb-3">
                    <span class="text-muted fs-7">Jumlah:</span>
                    <span class="fw-bold text-gray-800 fs-7">{{ number_format($item->requested_quantity, 2, ',', '.') }} {{ $item->unit }}</span>
                </div>
                
                <div class="d-flex flex-stack mb-3">
                    <span class="text-muted fs-7">Tujuan:</span>
                    <span class="badge badge-light-{{ $item->context == 'Order' ? 'success' : 'info' }} fw-bold fs-9">{{ $item->context }} {{ $item->erp_order_reference ? '('.$item->erp_order_reference.')' : '' }}</span>
                </div>

                <div class="separator separator-dashed my-3"></div>

                <div class="d-flex flex-stack">
                    <span class="text-muted fs-7">Subtotal Estimasi:</span>
                    <span class="fw-bolder text-primary fs-6">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                </div>
            </div>
        @endforeach

        <div class="bg-light-success bg-opacity-50 rounded p-5 mb-5 border border-success border-dashed">
            <div class="d-flex flex-stack">
                <span class="fw-bold text-gray-700">Total Estimasi Keseluruhan:</span>
                <span class="fs-3 fw-bolder text-success">Rp {{ number_format($grandTotal, 0, ',', '.') }}</span>
            </div>
        </div>
    </div>
    <!--end::Mobile Product List-->

    <div class="d-flex flex-column flex-md-row justify-content-end gap-3 mb-10 mt-5 sticky-bottom-mobile">
        <a href="{{ route('inventory.purchase-requisition.index') }}" class="btn btn-light px-10 order-2 order-md-1">Kembali</a>
        @if($requisition->status == 'Submitted' && auth()->user()->hasRole('admin'))
            <a href="{{ route('inventory.purchase-requisition.verify', $requisition->identifier) }}" class="btn btn-primary px-10 order-1 order-md-2">
                Verifikasi Sekarang
            </a>
        @endif
    </div>
</div>
@endsection

@push('styles')
<style>
    @media (max-width: 767.98px) {
        .sticky-bottom-mobile {
            position: sticky !important;
            bottom: 0;
            z-index: 100;
            background: #fff;
            margin-left: -1.25rem !important;
            margin-right: -1.25rem !important;
            width: calc(100% + 2.5rem) !important;
            padding: 1.25rem !important;
            box-shadow: 0 -10px 30px rgba(0,0,0,0.05) !important;
            border-top: 1px solid #eee;
        }
    }
</style>
@endpush
