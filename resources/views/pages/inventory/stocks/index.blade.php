@extends('layouts.app')

@section('title', 'Manajemen Stok')

@section('breadcrumb')
    <li class="breadcrumb-item"><span class="bullet bg-gray-500 w-5px h-2px"></span></li>
    <li class="breadcrumb-item text-muted">Inventori</li>
    <li class="breadcrumb-item"><span class="bullet bg-gray-500 w-5px h-2px"></span></li>
    <li class="breadcrumb-item text-muted">Stok</li>
@endsection

@section('content')
<div class="row g-5 g-xl-10 mb-5 mb-xl-10">
    <!-- Stats -->
    <div class="col-md-4">
        <div class="card card-flush h-md-100">
            <div class="card-header pt-5">
                <div class="card-title d-flex flex-column">
                    <span class="fs-2hx fw-bold text-gray-900 me-2 lh-1 ls-n2">{{ number_format($stats['total_items'], 0, ',', '.') }}</span>
                    <span class="text-gray-500 pt-1 fw-semibold fs-6">Total Unit Tersedia</span>
                </div>
            </div>
            <div class="card-body d-flex flex-column justify-content-end pe-0">
                <span class="fs-6 fw-bolder text-gray-800 d-block mb-2">Semua Produk Terdaftar</span>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card card-flush h-md-100">
            <div class="card-header pt-5">
                <div class="card-title d-flex flex-column">
                    <span class="fs-2hx fw-bold text-gray-900 me-2 lh-1 ls-n2">{{ $stats['total_products'] }}</span>
                    <span class="text-gray-500 pt-1 fw-semibold fs-6">Produk Memiliki Stok</span>
                </div>
            </div>
            <div class="card-body d-flex flex-column justify-content-end pe-0">
                <span class="fs-6 fw-bolder text-gray-800 d-block mb-2">Dari total {{ $stats['total_sku_count'] }} jenis produk</span>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card card-flush h-md-100 bg-light-danger bg-opacity-50">
            <div class="card-header pt-5">
                <div class="card-title d-flex flex-column">
                    <span class="fs-2hx fw-bold text-danger me-2 lh-1 ls-n2">{{ $stats['low_stock'] }}</span>
                    <span class="text-danger pt-1 fw-semibold fs-6">Produk Stok Menipis</span>
                </div>
            </div>
            <div class="card-body d-flex flex-column justify-content-end pe-0">
                <span class="fs-6 fw-bolder text-danger d-block mb-2">Perlu segera dipesan ulang</span>
            </div>
        </div>
    </div>
</div>

<div class="card card-flush">
    <div class="card-header align-items-center py-5 gap-2 gap-md-5">
        <div class="card-title">
            <!-- Search -->
            <div class="d-flex align-items-center position-relative my-1">
                <i class="ki-duotone ki-magnifier fs-3 position-absolute ms-4"><span class="path1"></span><span class="path2"></span></i>
                <input type="text" data-kt-stock-filter="search" class="form-control form-control-solid w-250px ps-12" placeholder="Cari Nama Bahan..." />
            </div>
        </div>
        <div class="card-toolbar gap-3">
            <!-- Filter -->
            <button type="button" class="btn btn-light-primary btn-sm" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                <i class="ki-duotone ki-filter fs-2"><span class="path1"></span><span class="path2"></span></i> Filter
            </button>
            <div class="menu menu-sub menu-sub-dropdown w-250px w-md-300px" data-kt-menu="true">
                <div class="px-7 py-5"><div class="fs-5 text-gray-900 fw-bold">Opsi Filter</div></div>
                <div class="separator border-gray-200"></div>
                <div class="px-7 py-5">
                    <div class="mb-5">
                        <label class="form-label fw-semibold">Status Stok:</label>
                        <select class="form-select form-select-solid" data-control="select2" data-placeholder="Pilih Status" data-allow-clear="true" id="filter_stock_status">
                            <option></option>
                            <option value="available">Tersedia</option>
                            <option value="low">Menipis</option>
                            <option value="out">Habis</option>
                        </select>
                    </div>
                    <div class="mb-5">
                        <label class="form-label fw-semibold">Kategori:</label>
                        <select class="form-select form-select-solid" data-control="select2" data-placeholder="Pilih Kategori" data-allow-clear="true" multiple="multiple" id="filter_categories">
                            @foreach(\App\Models\Category::all() as $category)
                                <option value="{{ $category->name }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="d-flex justify-content-end">
                        <button type="reset" class="btn btn-sm btn-light btn-active-light-primary" data-kt-menu-dismiss="true" id="filter_reset">Reset</button>
                    </div>
                </div>
            </div>

            <a href="{{ route('inventory.goods-receipt.create') }}" class="btn btn-primary btn-sm">
                <i class="ki-duotone ki-plus fs-2"></i> Input Stok Baru
            </a>
        </div>
    </div>

    <div class="px-10 pb-5 d-flex flex-wrap gap-2 d-none" id="active-filters-container"></div>

    <div class="card-body pt-0">
        <div class="table-responsive">
            <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_stocks_table">
                <thead>
                    <tr class="text-start text-gray-400 fw-bold fs-7 text-uppercase gs-0">
                        <th class="min-w-200px">Produk</th>
                        <th class="min-w-100px text-center">Kategori</th>
                        <th class="min-w-100px text-center">Unit Lot</th>
                        <th class="min-w-100px text-end">Total Stok</th>
                        <th class="min-w-100px text-center">Status</th>
                        <th class="text-end min-w-70px">Detail</th>
                    </tr>
                </thead>
                <tbody class="fw-semibold text-gray-600"></tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('styles')
    <link href="{{ asset('assets/vendors/plugins/custom/datatables/datatables.bundle.css') }}" rel="stylesheet" type="text/css" />
@endpush

@push('scripts')
    <script src="{{ asset('assets/vendors/plugins/custom/datatables/datatables.bundle.js') }}"></script>
    <script src="{{ asset('assets/custom/js/pages/inventory/stock/list.js') }}?v={{ time() }}"></script>
@endpush
