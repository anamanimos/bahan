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
        
            <div class="d-flex justify-content-end align-items-center d-none" data-kt-product-table-toolbar="selected">
                <div class="fw-bold me-5">
                    <span class="me-2" data-kt-product-table-select="selected_count">0</span> Terpilih
                </div>
                <button type="button" class="btn btn-sm btn-light-success me-3" data-kt-product-table-select="export_selected">
                    <i class="ki-duotone ki-file-down fs-2"><span class="path1"></span><span class="path2"></span></i> Export Terpilih
                </button>
                <button type="button" class="btn btn-sm btn-light-primary me-3" data-kt-product-table-select="merge_selected">
                    <i class="ki-duotone ki-copy fs-2"><span class="path1"></span><span class="path2"></span></i> Merge Produk
                </button>
            </div>

            <div class="d-flex justify-content-end align-items-center" data-kt-product-table-toolbar="base">
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
                            <div class="mb-10">
                                <label class="form-label fw-semibold">Warna:</label>
                                <select class="form-select form-select-solid" data-control="select2" data-placeholder="Pilih Warna" data-allow-clear="true" multiple="multiple" id="filter_colors">
                                    @foreach($colors as $color)
                                        <option value="{{ $color->name }}" data-kt-color="{{ $color->hex_code }}">{{ $color->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="d-flex justify-content-end">
                                <button type="reset" class="btn btn-sm btn-light btn-active-light-primary" data-kt-menu-dismiss="true" data-kt_menu_filter="reset">Reset</button>
                            </div>
                        </div>
                    </div>
                </div>

                <button type="button" class="btn btn-sm btn-light-success ms-3" data-kt-product-action="export_all">
                    <i class="ki-duotone ki-file-down fs-2"><span class="path1"></span><span class="path2"></span></i> Export Masal
                </button>
                <button type="button" class="btn btn-sm btn-light-info ms-3" data-bs-toggle="modal" data-bs-target="#kt_modal_import_products">
                    <i class="ki-duotone ki-file-up fs-2"><span class="path1"></span><span class="path2"></span></i> Import
                </button>
                <a href="{{ route('master.product.create') }}" class="btn btn-sm btn-primary ms-3">Tambah Produk</a>
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

<!--begin::Modal - Merge Products-->
<div class="modal fade" id="kt_modal_merge_products" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-650px">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="fw-bold">Merge Produk</h2>
                <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
                    <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
                </div>
            </div>
            <form id="kt_modal_merge_products_form">
                <div class="modal-body py-10 px-lg-17">
                    <div class="notice d-flex bg-light-warning rounded border-warning border border-dashed mb-9 p-6">
                        <i class="ki-duotone ki-information-5 fs-2tx text-warning me-4"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>
                        <div class="d-flex flex-stack flex-grow-1">
                            <div class="fw-semibold">
                                <h4 class="text-gray-900 fw-bold">Peringatan Penting</h4>
                                <div class="fs-6 text-gray-700">Proses merge akan memindahkan semua data stok, riwayat pembelian, dan penerimaan barang ke satu produk utama. Produk lainnya akan <strong>dihapus</strong>. Tindakan ini tidak dapat dibatalkan.</div>
                            </div>
                        </div>
                    </div>

                    <div class="fv-row mb-10">
                        <label class="required fs-6 fw-semibold mb-2">Pilih Produk Utama (Target)</label>
                        <select class="form-select form-select-solid" data-control="select2" data-placeholder="Pilih produk yang akan dipertahankan" id="merge_target_product" name="target_id">
                            <option></option>
                        </select>
                        <div class="text-muted fs-7 mt-2">Semua data dari produk lain akan dipindahkan ke produk ini.</div>
                    </div>

                    <div class="fv-row">
                        <label class="fs-6 fw-semibold mb-2">Produk yang akan digabung (Source):</label>
                        <div id="merge_source_products_list" class="d-flex flex-column gap-2 bg-light rounded p-4 border border-dashed border-gray-300">
                            <!-- List will be populated via JS -->
                        </div>
                    </div>
                </div>
                <div class="modal-footer flex-center">
                    <button type="button" data-bs-dismiss="modal" class="btn btn-light me-3">Batal</button>
                    <button type="submit" id="kt_modal_merge_products_submit" class="btn btn-primary">
                        <span class="indicator-label">Proses Merge</span>
                        <span class="indicator-progress">Mohon tunggu... <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!--begin::Modal - Import Products-->
<div class="modal fade" id="kt_modal_import_products" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-650px">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="fw-bold">Import Produk (CSV)</h2>
                <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
                    <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
                </div>
            </div>
            <form id="kt_modal_import_products_form">
                <div class="modal-body py-10 px-lg-17">
                    <div class="notice d-flex bg-light-primary rounded border-primary border border-dashed mb-9 p-6">
                        <i class="ki-duotone ki-information fs-2tx text-primary me-4"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>
                        <div class="d-flex flex-stack flex-grow-1">
                            <div class="fw-semibold">
                                <h4 class="text-gray-900 fw-bold">Template Import</h4>
                                <div class="fs-6 text-gray-700">Untuk menghindari error, gunakan template CSV standar. <a href="{{ route('master.product.template') }}" class="fw-bold" target="_blank">Download Template</a></div>
                            </div>
                        </div>
                    </div>

                    <div class="fv-row mb-10">
                        <label class="required fs-6 fw-semibold mb-2">Pilih File CSV</label>
                        <input type="file" name="file" class="form-control form-control-solid" accept=".csv" required />
                        <div class="text-muted fs-7 mt-2">Hanya file berekstensi .csv yang diizinkan. Jika SKU sudah ada, data produk akan diperbarui.</div>
                    </div>
                </div>
                <div class="modal-footer flex-center">
                    <button type="button" data-bs-dismiss="modal" class="btn btn-light me-3">Batal</button>
                    <button type="submit" id="kt_modal_import_products_submit" class="btn btn-primary">
                        <span class="indicator-label">Import File</span>
                        <span class="indicator-progress">Mohon tunggu... <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                    </button>
                </div>
            </form>
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
