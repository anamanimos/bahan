@extends('layouts.app')

@section('title', 'Detail Penerimaan Barang (Goods Receipt)')

@section('breadcrumb')
    <li class="breadcrumb-item"><span class="bullet bg-gray-500 w-5px h-2px"></span></li>
    <li class="breadcrumb-item text-muted">Inventori</li>
    <li class="breadcrumb-item"><span class="bullet bg-gray-500 w-5px h-2px"></span></li>
    <li class="breadcrumb-item text-muted">Detail Penerimaan</li>
@endsection

@section('content')
<div class="d-flex flex-column gap-7 gap-lg-10">
    <!--begin::Main column-->
    <div class="d-flex flex-column flex-row-fluid gap-7 gap-lg-10">
        <!--begin::Order summary-->
        <div class="card card-flush py-4">
            <div class="card-header">
                <div class="card-title">
                    <h2>#{{ $receipt->identifier }}</h2>
                </div>
                <div class="card-toolbar gap-3">
                    <a href="{{ route('inventory.goods-receipt.index') }}" class="btn btn-sm btn-light">
                        <i class="ki-duotone ki-arrow-left fs-3"><span class="path1"></span><span class="path2"></span></i> Kembali
                    </a>
                    <a href="{{ route('inventory.goods-receipt.edit', $receipt->id) }}" class="btn btn-sm btn-light-primary">
                        <i class="ki-duotone ki-pencil fs-3"><span class="path1"></span><span class="path2"></span></i> Edit Nota
                    </a>
                </div>
            </div>
            <div class="card-body pt-0">
                <div class="d-flex flex-wrap flex-stack">
                    <div class="d-flex flex-column flex-grow-1 pe-8">
                        <div class="d-flex flex-wrap">
                            <div class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6 mb-3">
                                <div class="d-flex align-items-center">
                                    <div class="fs-2 fw-bold">{{ $receipt->received_date->format('d M Y') }}</div>
                                </div>
                                <div class="fw-semibold fs-6 text-gray-400">Tanggal Terima</div>
                            </div>
                            <div class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6 mb-3">
                                <div class="d-flex align-items-center">
                                    <div class="fs-2 fw-bold text-success">Rp {{ number_format($receipt->items->sum(fn($i) => $i->received_quantity * $i->unit_price), 0, ',', '.') }}</div>
                                </div>
                                <div class="fw-semibold fs-6 text-gray-400">Total Estimasi Nilai</div>
                            </div>
                            <div class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6 mb-3">
                                <div class="d-flex align-items-center">
                                    <div class="fs-2 fw-bold">{{ $receipt->items->count() }}</div>
                                </div>
                                <div class="fw-semibold fs-6 text-gray-400">Jumlah Item</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--end::Order summary-->

        <div class="row g-7 g-lg-10">
            <!--begin::Left side-->
            <div class="col-xl-8">
                <!--begin::Product List-->
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
                                        <th class="min-w-175px">Produk</th>
                                        <th class="min-w-100px text-end">SKU</th>
                                        <th class="min-w-70px text-end">Jumlah</th>
                                        <th class="min-w-100px text-end">Harga Satuan</th>
                                        <th class="min-w-100px text-end">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody class="fw-semibold text-gray-600">
                                    @foreach($receipt->items as $item)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="ms-0">
                                                    <a href="#" class="text-gray-800 text-hover-primary fs-5 fw-bold">{{ $item->product->name }}</a>
                                                    @if($item->order_reference)
                                                        <div class="text-muted fs-7">Ref: {{ $item->order_reference }}</div>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-end">{{ $item->product->sku }}</td>
                                        <td class="text-end">{{ number_format($item->received_quantity, 2) }} {{ $item->unit }}</td>
                                        <td class="text-end">Rp {{ number_format($item->unit_price, 0, ',', '.') }}</td>
                                        <td class="text-end">Rp {{ number_format($item->received_quantity * $item->unit_price, 0, ',', '.') }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr class="text-gray-800 fw-bold fs-6">
                                        <td colspan="4" class="text-end">Total Akhir</td>
                                        <td class="text-end text-success">Rp {{ number_format($receipt->items->sum(fn($i) => $i->received_quantity * $i->unit_price), 0, ',', '.') }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
                <!--end::Product List-->
            </div>
            <!--end::Left side-->

            <!--begin::Right side-->
            <div class="col-xl-4">
                <!--begin::Supplier details-->
                <div class="card card-flush py-4">
                    <div class="card-header">
                        <div class="card-title">
                            <h2>Supplier / Pengirim</h2>
                        </div>
                    </div>
                    <div class="card-body pt-0">
                        <div class="d-flex align-items-center mb-7">
                            <div class="symbol symbol-50px me-5">
                                <div class="symbol-label fs-2 fw-bold bg-light-primary text-primary">{{ substr($receipt->supplier->name, 0, 1) }}</div>
                            </div>
                            <div class="d-flex flex-column">
                                <a href="#" class="fs-6 text-gray-800 text-hover-primary fw-bold">{{ $receipt->supplier->name }}</a>
                                <div class="text-muted fw-semibold fs-7">{{ $receipt->supplier->phone_number ?: '-' }}</div>
                            </div>
                        </div>
                        <div class="separator separator-dashed mb-7"></div>
                        <div class="fs-6">
                            <div class="fw-bold mb-1">Alamat:</div>
                            <div class="text-gray-600">{{ $receipt->supplier->address ?: 'Tidak ada alamat' }}</div>
                        </div>
                    </div>
                </div>
                <!--end::Supplier details-->

                <!--begin::Invoice Photo-->
                @if($receipt->invoice_photo_path)
                <div class="card card-flush py-4 mt-7">
                    <div class="card-header">
                        <div class="card-title">
                            <h2>Foto Nota / Surat Jalan</h2>
                        </div>
                    </div>
                    <div class="card-body pt-0">
                        @php $photoUrl = \App\Services\MediaSyncService::url($receipt->invoice_photo_path); @endphp
                        <a href="{{ $photoUrl }}" target="_blank">
                            <img src="{{ $photoUrl }}" class="img-fluid rounded shadow-sm w-100" />
                        </a>
                        <div class="text-center mt-3">
                            <a href="{{ $photoUrl }}" target="_blank" class="btn btn-sm btn-light">
                                <i class="ki-duotone ki-magnifier fs-4 me-1"><span class="path1"></span><span class="path2"></span></i> Lihat Ukuran Penuh
                            </a>
                        </div>
                    </div>
                </div>
                @endif
                <!--end::Invoice Photo-->
            </div>
            <!--end::Right side-->
        </div>
    </div>
    <!--end::Main column-->
</div>
@endsection
