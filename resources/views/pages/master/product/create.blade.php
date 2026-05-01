@extends('layouts.app')

@section('title', 'Tambah Produk Baru')

@section('breadcrumb')
    <li class="breadcrumb-item"><span class="bullet bg-gray-500 w-5px h-2px"></span></li>
    <li class="breadcrumb-item text-muted">Master Data</li>
    <li class="breadcrumb-item"><span class="bullet bg-gray-500 w-5px h-2px"></span></li>
    <li class="breadcrumb-item text-muted">Produk</li>
    <li class="breadcrumb-item"><span class="bullet bg-gray-500 w-5px h-2px"></span></li>
    <li class="breadcrumb-item text-muted">Tambah</li>
@endsection

@section('content')
<form id="kt_ecommerce_add_product_form" class="form" action="{{ route('master.product.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="row g-7 g-lg-10">
        <!--begin::Main column-->
        <div class="col-lg-8">
            <div class="d-flex flex-column gap-7 gap-lg-10">
                <!--begin::General options-->
                <div class="card card-flush py-4">
                    <div class="card-header">
                        <div class="card-title">
                            <h2>Informasi Umum</h2>
                        </div>
                    </div>
                    <div class="card-body pt-0">
                        <div class="row g-9 mb-10">
                            <div class="col-md-8 fv-row">
                                <label class="required form-label">Nama Produk</label>
                                <input type="text" name="name" class="form-control mb-2" placeholder="Masukkan nama produk" required />
                            </div>
                            <div class="col-md-4 fv-row">
                                <label class="form-label">SKU</label>
                                <input type="text" name="sku" class="form-control mb-2" placeholder="Nomor SKU (Opsional)" />
                            </div>
                        </div>
                        <div>
                            <label class="form-label">Deskripsi</label>
                            <div id="kt_ecommerce_add_product_description" class="min-h-200px mb-2"></div>
                            <input type="hidden" name="description" id="kt_ecommerce_add_product_description_input" />
                            <div class="text-muted fs-7">Berikan deskripsi detail produk untuk mempermudah identifikasi.</div>
                        </div>
                    </div>
                </div>
                <!--end::General options-->

                <!--begin::Media-->
                <div class="card card-flush py-4">
                    <div class="card-header">
                        <div class="card-title">
                            <h2>Galeri Foto Produk</h2>
                        </div>
                    </div>
                    <div class="card-body pt-0">
                        <div class="fv-row mb-2">
                            <div class="d-flex flex-wrap gap-5" id="kt_ecommerce_add_product_media_items">
                                <label class="btn btn-outline btn-outline-dashed btn-outline-default d-flex flex-column flex-center w-125px h-125px" style="cursor:pointer">
                                    <i class="ki-duotone ki-plus fs-2tx text-primary mb-2"></i>
                                    <span class="fs-7 fw-bold text-gray-400">Tambah Foto</span>
                                    <input type="file" name="product_photos[]" multiple accept=".png, .jpg, .jpeg" class="d-none" id="kt_ecommerce_add_product_media_input" />
                                </label>
                            </div>
                        </div>
                        <div class="text-muted fs-7 mt-2">Format file: .png, .jpg, .jpeg. Maksimal 5 file.</div>
                    </div>
                </div>
                <!--end::Media-->

                <!--begin::Inventory Info-->
                <div class="card card-flush py-4">
                    <div class="card-header">
                        <div class="card-title">
                            <h2>Stok & Inventori</h2>
                        </div>
                    </div>
                    <div class="card-body pt-0">
                        <div class="row g-9">
                            <div class="col-md-4 fv-row">
                                <label class="required form-label">Satuan Utama</label>
                                <select class="form-select" data-control="select2" name="base_unit" required>
                                    <option value="m">Meter (m)</option>
                                    <option value="roll">Roll</option>
                                    <option value="kg">Kg</option>
                                    <option value="pcs">Pcs</option>
                                </select>
                            </div>
                            <div class="col-md-4 fv-row">
                                <label class="required form-label">Ambang Batas (ROP)</label>
                                <input type="number" name="minimum_stock_level" class="form-control" placeholder="0" step="0.01" value="0" required />
                                <div class="text-muted fs-7 mt-2">Stok minimum untuk peringatan.</div>
                            </div>
                            <div class="col-md-4 fv-row">
                                <label class="form-label">Lokasi Rak/Gudang</label>
                                <input type="text" name="warehouse_location" class="form-control" placeholder="Contoh: Rak A1" />
                            </div>
                        </div>
                    </div>
                </div>
                <!--end::Inventory Info-->

                <!--begin::Specifications-->
                <div class="card card-flush py-4">
                    <div class="card-header">
                        <div class="card-title">
                            <h2>Spesifikasi Teknis</h2>
                        </div>
                    </div>
                    <div class="card-body pt-0">
                        <div class="row g-9 mb-5">
                            <div class="col-md-4 fv-row">
                                <label class="form-label">Lebar (Width)</label>
                                <input type="text" name="spec_width" class="form-control mb-2" placeholder="Contoh: 150cm" />
                            </div>
                            <div class="col-md-4 fv-row">
                                <label class="form-label">Gramasi (Grammage)</label>
                                <input type="text" name="spec_grammage" class="form-control mb-2" placeholder="Contoh: 160-170" />
                            </div>
                            <div class="col-md-4 fv-row">
                                <label class="form-label">Komposisi</label>
                                <input type="text" name="spec_composition" class="form-control mb-2" placeholder="Contoh: 100% Cotton" />
                            </div>
                        </div>
                        <div class="row g-9">
                            <div class="col-md-6 fv-row">
                                <label class="form-label">Warna</label>
                                <select name="spec_color" class="form-select mb-2" data-placeholder="Pilih Warna" id="kt_ecommerce_add_product_color">
                                    <option></option>
                                    @foreach($colors as $color)
                                        <option value="{{ $color->name }}" data-kt-color="{{ $color->hex_code }}">{{ $color->name }}</option>
                                    @endforeach
                                    <option value="ADD_NEW_COLOR" class="fw-bold text-primary">-- Tambah Warna Baru --</option>
                                </select>
                            </div>
                            <div class="col-md-6 fv-row">
                                <label class="form-label">Motif</label>
                                <input type="text" name="spec_motif" class="form-control mb-2" placeholder="Contoh: Polos, Salur" />
                            </div>
                        </div>
                    </div>
                </div>
                <!--end::Specifications-->
            </div>
        </div>
        <!--end::Main column-->

        <!--begin::Aside column-->
        <div class="col-lg-4">
            <div class="d-flex flex-column gap-7 gap-lg-10 sticky-lg-top" style="top: 100px">
                <!--begin::Category & tags-->
                <div class="card card-flush py-4">
                    <div class="card-header">
                        <div class="card-title">
                            <h2>Kategori & Label</h2>
                        </div>
                    </div>
                    <div class="card-body pt-0">
                        <label class="form-label text-muted">Kategori</label>
                        <select class="form-select mb-5" data-control="select2" data-placeholder="Pilih Kategori" data-allow-clear="true" name="category_id" id="kt_ecommerce_add_product_category" required>
                            <option></option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                            <option value="ADD_NEW_CATEGORY" class="fw-bold text-primary">-- Tambah Kategori Baru --</option>
                        </select>
                        
                        <label class="form-label text-muted d-block">Label (Tags)</label>
                        <input id="kt_ecommerce_add_product_tags" name="tags" class="form-control mb-2" value="" />
                        <div class="text-muted fs-7">Gunakan label untuk mempermudah pencarian.</div>
                    </div>
                </div>
                <!--end::Category & tags-->

                <div class="row g-3 mt-5">
                    <div class="col-6">
                        <a href="{{ route('master.product.index') }}" class="btn btn-light w-100">Batal</a>
                    </div>
                    <div class="col-6">
                        <button type="submit" id="kt_ecommerce_add_product_submit" class="btn btn-primary w-100">
                            <span class="indicator-label">Simpan</span>
                            <span class="indicator-progress">
                                <span class="spinner-border spinner-border-sm align-middle"></span>
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <!--end::Aside column-->
    </div>
</form>

<!--begin::Modal - Add Category-->
<div class="modal fade" id="kt_modal_add_category" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-450px">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="fw-bold">Tambah Kategori Baru</h2>
                <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
                    <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
                </div>
            </div>
            <div class="modal-body py-10 px-lg-17">
                <div class="fv-row mb-7">
                    <label class="required fs-6 fw-semibold mb-2">Nama Kategori</label>
                    <input type="text" class="form-control form-control-solid" placeholder="Contoh: Kain Denim" id="kt_modal_add_category_name" />
                </div>
                <div class="fv-row mb-7">
                    <label class="fs-6 fw-semibold mb-2">Induk Kategori (Parent)</label>
                    <select class="form-select form-select-solid" data-control="select2" data-placeholder="Pilih Induk (Jika ada)" id="kt_modal_add_category_parent" data-dropdown-parent="#kt_modal_add_category">
                        <option></option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="modal-footer flex-center">
                <button type="button" data-bs-dismiss="modal" class="btn btn-light me-3">Batal</button>
                <button type="button" id="kt_modal_add_category_submit" class="btn btn-primary">Simpan Kategori</button>
            </div>
        </div>
    </div>
</div>

<!--begin::Modal - Add Color-->
<div class="modal fade" id="kt_modal_add_color" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-450px">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="fw-bold">Tambah Warna Baru</h2>
                <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
                    <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
                </div>
            </div>
            <div class="modal-body py-10 px-lg-17">
                <div class="fv-row mb-7">
                    <label class="required fs-6 fw-semibold mb-2">Nama Warna</label>
                    <input type="text" class="form-control form-control-solid" placeholder="Contoh: Navy Blue" id="kt_modal_add_color_name" />
                </div>
                <div class="fv-row mb-7">
                    <label class="required fs-6 fw-semibold mb-2">Pilih Warna</label>
                    <div class="d-flex align-items-center">
                        <input type="color" class="form-control form-control-color w-100px me-3" id="kt_modal_add_color_picker" value="#009ef7" />
                        <span class="fs-7 fw-bold text-gray-700" id="kt_modal_add_color_hex">#009EF7</span>
                    </div>
                </div>
            </div>
            <div class="modal-footer flex-center">
                <button type="button" data-bs-dismiss="modal" class="btn btn-light me-3">Batal</button>
                <button type="button" id="kt_modal_add_color_submit" class="btn btn-primary">Simpan Warna</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('assets/custom/js/pages/master/product/form.js') }}?v={{ time() }}"></script>
@endpush
