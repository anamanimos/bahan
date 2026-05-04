@extends('layouts.app')

@section('title', 'Buat Pengajuan Beli (PR)')

@section('breadcrumb')
    <li class="breadcrumb-item"><span class="bullet bg-gray-500 w-5px h-2px"></span></li>
    <li class="breadcrumb-item text-muted"><a href="{{ route('inventory.purchase-requisition.index') }}" class="text-muted text-hover-primary">Inventori</a></li>
    <li class="breadcrumb-item"><span class="bullet bg-gray-500 w-5px h-2px"></span></li>
    <li class="breadcrumb-item text-muted"><a href="{{ route('inventory.purchase-requisition.index') }}" class="text-muted text-hover-primary">Daftar PR</a></li>
    <li class="breadcrumb-item"><span class="bullet bg-gray-500 w-5px h-2px"></span></li>
    <li class="breadcrumb-item text-muted">Buat Baru</li>
@endsection

@section('content')
<div class="d-flex flex-column">
    <!--begin::Form-->
    <form id="kt_pr_form" class="form" method="POST" action="{{ route('inventory.purchase-requisition.store') }}">
        @csrf
        <!--begin::Main Content-->
        <div class="card card-flush mb-5">
            <!--begin::Card header-->
            <div class="card-header pt-5">
                <div class="card-title flex-column">
                    <h2 class="fw-bold">Pengajuan Beli (PR)</h2>
                    <div class="text-muted fw-semibold fs-7">{{ \Carbon\Carbon::now()->translatedFormat('l, d M Y') }}</div>
                </div>
                <div class="card-toolbar">
                    <span class="badge badge-light-primary fs-7 fw-bold">Otomatis (Draft)</span>
                </div>
            </div>
            <!--end::Card header-->
            <!--begin::Card body-->
            <div class="card-body pt-0">
                <div class="mb-5">
                    <label class="form-label fw-bold fs-7 text-gray-700">Catatan / Keterangan (Opsional)</label>
                    <textarea class="form-control form-control-solid" name="notes" rows="2" placeholder="Tambahkan catatan untuk PR ini..."></textarea>
                </div>
            
                <div class="separator separator-dashed my-5"></div>
                
                <!--begin::Items Container-->
                <div id="kt_pr_items_container">
                    <!--begin::Item Template-->
                    <div class="pr-item mb-4 border border-dashed border-gray-300 rounded position-relative" data-kt-element="item">
                        <!--begin::Item Header (Visible on mobile as summary)-->
                        <div class="d-flex align-items-center justify-content-between p-4 cursor-pointer d-md-none bg-light-primary bg-opacity-10 rounded-top" data-bs-toggle="collapse" data-bs-target="#kt_pr_item_collapse_0">
                            <div class="d-flex align-items-center">
                                <span class="badge badge-circle badge-outline badge-primary me-3 fw-bold fs-8" data-kt-element="item-index">1</span>
                                <span class="fw-bold text-gray-800 fs-6" data-kt-element="item-summary">Pilih Bahan...</span>
                            </div>
                            <div class="d-flex align-items-center">
                                <span class="text-primary fw-bold fs-7 me-3">Rp <span data-kt-element="total">0</span></span>
                                <i class="ki-duotone ki-down fs-3 transition-all"></i>
                            </div>
                        </div>
                        <!--end::Item Header-->

                        <!--begin::Item Body (Collapsible on mobile)-->
                        <div id="kt_pr_item_collapse_0" class="collapse show d-md-block">
                            <div class="p-5 pt-0 pt-md-5">
                                <!--begin::Delete Button (Desktop)-->
                                <button type="button" class="btn btn-icon btn-sm btn-light-danger position-absolute top-0 end-0 m-2 rounded-circle d-none d-md-flex" data-kt-element="remove-item">
                                    <i class="ki-duotone ki-cross fs-2"><span class="path1"></span><span class="path2"></span></i>
                                </button>
                                <!--end::Delete Button-->

                                <div class="row g-5">
                                    <!--begin::Col-->
                                    <div class="col-12 col-md-4">
                                        <label class="required form-label fw-bold fs-7 text-gray-700">Bahan / Produk</label>
                                        <select class="form-select form-select-solid" name="product_id[]" data-control="select2" data-placeholder="Cari Bahan..." data-kt-element="item-name" required>
                                            <option></option>
                                        </select>
                                        <div class="mt-2 row g-2">
                                            <div class="col-4">
                                                <select class="form-select form-select-solid fs-7" name="context_type[]" data-kt-element="context-type" data-control="select2" data-hide-search="true">
                                                    <option value="Stock">Stok</option>
                                                    <option value="Order">Order</option>
                                                </select>
                                            </div>
                                            <div class="col-8 d-none" data-kt-element="order-container">
                                                <select class="form-select form-select-solid fs-7" name="order_reference[]" data-placeholder="Pilih Order..." data-kt-element="order-ref" data-ajax-url="{{ route('inventory.ajax.purchase-requisition.search-orders') }}">
                                                    <option></option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <!--end::Col-->
                                    <!--begin::Col-->
                                    <div class="col-12 col-md-3">
                                        <label class="required form-label fw-bold fs-7 text-gray-700">Toko / Supplier</label>
                                        <select class="form-select form-select-solid" name="supplier_id[]" data-control="select2" data-placeholder="Cari Toko..." data-kt-element="item-supplier" required>
                                            <option></option>
                                        </select>
                                    </div>
                                    <!--end::Col-->
                                    <!--begin::Col-->
                                    <div class="col-4 col-md-2">
                                        <label class="required form-label fw-bold fs-7 text-gray-700">Jumlah</label>
                                        <div class="input-group input-group-solid">
                                            <input type="number" class="form-control form-control-solid" name="quantity[]" placeholder="0" data-kt-element="quantity" step="0.01" min="0.01" required />
                                            <span class="input-group-text fs-8" data-kt-element="unit-label">Pcs</span>
                                        </div>
                                    </div>
                                    <!--end::Col-->
                                    <!--begin::Col-->
                                    <div class="col-8 col-md-3">
                                        <label class="form-label fw-bold fs-7 text-gray-700">Est. Harga Satuan</label>
                                        <div class="input-group input-group-solid">
                                            <span class="input-group-text fs-8">Rp</span>
                                            <input type="text" class="form-control form-control-solid text-end" name="price[]" placeholder="0" data-kt-element="price" />
                                        </div>
                                    </div>
                                    <!--end::Col-->
                                </div>
                                
                                <!--begin::Remove Button (Mobile)-->
                                <div class="d-flex justify-content-between align-items-center mt-5 d-md-none">
                                    <button type="button" class="btn btn-sm btn-light-danger" data-kt-element="remove-item">
                                        <i class="ki-duotone ki-trash fs-4"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span></i>
                                        Hapus Item
                                    </button>
                                    <div class="fw-bold text-gray-700 d-none d-md-block">Subtotal: <span class="text-primary">Rp <span data-kt-element="total-desktop">0</span></span></div>
                                </div>
                                <!--end::Remove Button-->
                            </div>
                        </div>
                        <!--end::Item Body-->
                    </div>
                    <!--end::Item Template-->
                </div>
                <!--end::Items Container-->

                <!--begin::Actions-->
                <button type="button" class="btn btn-sm btn-light-primary w-100 mb-10" id="kt_pr_add_item">
                    <i class="ki-duotone ki-plus fs-3"></i> Tambah Bahan Lain
                </button>
                <!--end::Actions-->
            </div>
            <!--end::Card body-->
        </div>
        <!--end::Main Content-->

        <!--begin::Sticky Footer-->
        <div class="card card-flush shadow-sm sticky-bottom" style="bottom: 0; z-index: 100; border-top: 1px solid #eee;">
            <div class="card-body p-5">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="d-flex flex-column">
                        <span class="text-gray-400 fw-bold fs-8 text-uppercase">Total Estimasi</span>
                        <span class="fs-3 fw-bold text-gray-800">Rp <span id="kt_pr_grand_total">0</span></span>
                    </div>
                    <div class="d-flex gap-3">
                        <a href="{{ route('inventory.purchase-requisition.index') }}" class="btn btn-light px-8">Batal</a>
                        <button type="submit" class="btn btn-primary px-8" id="kt_pr_submit">
                            <span class="indicator-label">Ajukan PR</span>
                            <span class="indicator-progress">
                                Mohon Tunggu... <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <!--end::Sticky Footer-->
    </form>
    <!--end::Form-->
</div>

<!--begin::Modal - Add Product Quick-->
<div class="modal fade" id="kt_modal_add_product_quick" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-400px">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="fw-bold">Tambah Produk Baru</h2>
                <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
                    <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
                </div>
            </div>
            <div class="modal-body py-10 px-lg-17">
                <form id="kt_modal_add_product_quick_form" action="{{ route('master.product.store') }}">
                    <div class="mb-5">
                        <label class="required form-label">Nama Produk</label>
                        <input type="text" class="form-control form-control-solid" name="name" placeholder="Contoh: Cotton Combed 30s Merah" required />
                    </div>
                    <div class="mb-5">
                        <label class="required form-label">Kategori</label>
                        <select class="form-select form-select-solid" name="category_id" required>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-5">
                        <label class="required form-label">Satuan Dasar</label>
                        <select class="form-select form-select-solid" name="base_unit" required>
                            <option value="Kg">Kg</option>
                            <option value="Pcs">Pcs</option>
                            <option value="Roll">Roll</option>
                            <option value="Meter">Meter</option>
                            <option value="Pack">Pack</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer flex-center">
                <button type="button" class="btn btn-light me-3" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="kt_modal_add_product_quick_submit">
                    <span class="indicator-label">Simpan Produk</span>
                    <span class="indicator-progress">Menyimpan... <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                </button>
            </div>
        </div>
    </div>
</div>
<!--end::Modal - Add Product Quick-->
@endsection

@push('styles')
<style>
/* Custom style for mobile focus */
@media (max-width: 767.98px) {
    .sticky-bottom {
        position: sticky !important;
        margin-left: -1.25rem;
        margin-right: -1.25rem;
        margin-bottom: -1.25rem;
        border-radius: 0;
    }
    [data-bs-toggle="collapse"][aria-expanded="true"] i {
        transform: rotate(180deg);
    }
}
</style>
@endpush

@push('scripts')
    <script src="{{ asset('assets/custom/js/pages/inventory/purchase-requisition/create.js') }}?v={{ time() }}"></script>
@endpush
