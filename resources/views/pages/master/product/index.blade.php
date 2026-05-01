@extends('layouts.app')

@section('title', 'Master Data Produk')

@section('breadcrumb')
    <li class="breadcrumb-item"><span class="bullet bg-gray-500 w-5px h-2px"></span></li>
    <li class="breadcrumb-item text-muted">Master Data</li>
    <li class="breadcrumb-item"><span class="bullet bg-gray-500 w-5px h-2px"></span></li>
    <li class="breadcrumb-item text-muted">Produk</li>
@endsection

@section('toolbar_actions')
    <a href="{{ route('master.category.index') }}" class="btn btn-sm btn-light-primary">
        <i class="ki-duotone ki-category fs-2"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span></i> Kelola Kategori
    </a>
@endsection

@section('content')
<div class="card card-flush">
    <div class="card-header align-items-center py-5 gap-2 gap-md-5">
        <div class="card-title">
            <div class="d-flex align-items-center position-relative my-1">
                <i class="ki-duotone ki-magnifier fs-3 position-absolute ms-4"><span class="path1"></span><span class="path2"></span></i>
                <input type="text" data-kt-product-filter="search" class="form-control form-control-solid w-250px ps-12" placeholder="Cari Produk..." />
            </div>
        </div>
        
        <div class="card-toolbar flex-row-fluid justify-content-end gap-5">
            <div class="d-flex gap-3">
                <button type="button" class="btn btn-sm btn-light-primary btn-flex" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                    <i class="ki-duotone ki-filter fs-2"><span class="path1"></span><span class="path2"></span></i>
                    Filter
                </button>
                <div class="menu menu-sub menu-sub-dropdown w-250px w-md-300px" data-kt-menu="true" id="kt_menu_filter">
                    <div class="px-7 py-5"><div class="fs-5 text-gray-900 fw-bold">Opsi Filter</div></div>
                    <div class="separator border-gray-200"></div>
                    <div class="px-7 py-5">
                        <div class="mb-5">
                            <label class="form-label fw-semibold">Kategori:</label>
                            <select class="form-select form-select-solid" data-control="select2" data-placeholder="Pilih Kategori" data-allow-clear="true" multiple="multiple" id="filter_categories">
                                @foreach($categories as $category)
                                    <option value="{{ $category->name }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="d-flex justify-content-end">
                            <button type="reset" class="btn btn-sm btn-light btn-active-light-primary" data-kt-menu-dismiss="true" data-kt_menu_filter="reset">Reset</button>
                        </div>
                    </div>
                </div>
            </div>

            <a href="{{ route('master.product.create') }}" class="btn btn-sm btn-primary">Tambah Produk</a>
        </div>
    </div>

    <div class="px-10 pb-5 d-flex flex-wrap gap-2 d-none" id="active-filters-container"></div>

    <div class="card-body pt-0">
        <div class="table-responsive">
            <table class="table align-middle table-row-dashed fs-6 gy-2 w-100" id="kt_products_table">
                <thead>
                    <tr class="text-start text-gray-500 fw-bold fs-7 text-uppercase gs-0">
                        <th class="w-10px pe-2"><div class="form-check form-check-sm form-check-custom form-check-solid me-3"><input class="form-check-input" type="checkbox" data-kt-check="true" data-kt-check-target="#kt_products_table .form-check-input" value="1" /></div></th>
                        <th class="min-w-200px">Produk</th>
                        <th class="min-w-100px">Satuan</th>
                        <th class="min-w-100px">SKU</th>
                        <th class="min-w-70px">Stok</th>
                        <th class="min-w-100px">Kategori</th>
                        <th class="min-w-100px">Warna</th>
                        <th class="text-end min-w-70px">Aksi</th>
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
<style>
.resize-handle { position: absolute; top: 0; right: 0; width: 5px; cursor: col-resize; user-select: none; height: 100%; z-index: 10; }
.resize-handle:hover, .resize-handle.resizing { background: rgba(0, 158, 247, 0.5); }
body.resizing { cursor: col-resize !important; }
</style>
@endpush

@push('scripts')
    <script src="{{ asset('assets/vendors/plugins/custom/datatables/datatables.bundle.js') }}"></script>
    <script src="{{ asset('assets/custom/js/pages/master/product/list.js') }}?v={{ time() }}"></script>
@endpush
