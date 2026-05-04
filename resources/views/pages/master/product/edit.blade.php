@extends('layouts.app')

@section('title', 'Edit Produk: ' . $product->name)

@section('breadcrumb')
    <li class="breadcrumb-item"><span class="bullet bg-gray-500 w-5px h-2px"></span></li>
    <li class="breadcrumb-item text-muted">Master Data</li>
    <li class="breadcrumb-item"><span class="bullet bg-gray-500 w-5px h-2px"></span></li>
    <li class="breadcrumb-item text-muted">Produk</li>
    <li class="breadcrumb-item"><span class="bullet bg-gray-500 w-5px h-2px"></span></li>
    <li class="breadcrumb-item text-muted">Edit</li>
@endsection

@section('content')
<form id="kt_ecommerce_add_product_form" class="form" action="{{ route('master.product.update', $product->id) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')
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
                                <input type="text" name="name" class="form-control mb-2" placeholder="Masukkan nama produk" value="{{ old('name', $product->name) }}" required />
                            </div>
                            <div class="col-md-4 fv-row">
                                <label class="form-label">SKU</label>
                                <input type="text" name="sku" class="form-control mb-2" placeholder="Nomor SKU (Opsional)" value="{{ old('sku', $product->sku) }}" />
                            </div>
                        </div>
                        <div>
                            <label class="form-label">Deskripsi</label>
                            <div id="kt_ecommerce_add_product_description" class="min-h-200px mb-2">{!! $product->description !!}</div>
                            <input type="hidden" name="description" id="kt_ecommerce_add_product_description_input" value="{{ $product->description }}" />
                            <div class="text-muted fs-7">Berikan deskripsi detail produk untuk mempermudah identifikasi.</div>
                        </div>
                    </div>
                </div>
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
                                @if($product->image_path)
                                    <div class="position-relative w-125px h-125px">
                                        <img src="{{ asset('storage/' . $product->image_path) }}" class="w-100 h-100 object-fit-cover rounded border" alt="Product Image" />
                                        <div class="position-absolute top-0 end-0 m-n2">
                                            <button type="button" class="btn btn-icon btn-circle btn-danger w-20px h-20px shadow" title="Hapus" onclick="$(this).parent().parent().remove()">
                                                <i class="ki-duotone ki-cross fs-9"><span class="path1"></span><span class="path2"></span></i>
                                            </button>
                                        </div>
                                    </div>
                                @endif
                                <label class="btn btn-outline btn-outline-dashed btn-outline-default d-flex flex-column flex-center w-125px h-125px" style="cursor:pointer">
                                    <i class="ki-duotone ki-plus fs-2tx text-primary mb-2"></i>
                                    <span class="fs-7 fw-bold text-gray-400">Tambah Foto</span>
                                    <input type="file" name="product_image" accept=".png, .jpg, .jpeg" class="d-none" id="kt_ecommerce_add_product_media_input" />
                                </label>
                            </div>
                        </div>
                        <div class="text-muted fs-7 mt-2">Format file: .png, .jpg, .jpeg. Maksimal 2MB.</div>
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
                                    <option value="m" {{ $product->base_unit == 'm' ? 'selected' : '' }}>Meter (m)</option>
                                    <option value="roll" {{ $product->base_unit == 'roll' ? 'selected' : '' }}>Roll</option>
                                    <option value="kg" {{ $product->base_unit == 'kg' ? 'selected' : '' }}>Kg</option>
                                    <option value="pcs" {{ $product->base_unit == 'pcs' ? 'selected' : '' }}>Pcs</option>
                                </select>
                            </div>
                            <div class="col-md-4 fv-row">
                                <label class="required form-label">Ambang Batas (ROP)</label>
                                <input type="number" name="minimum_stock_level" class="form-control" placeholder="0" step="0.01" value="{{ old('minimum_stock_level', $product->minimum_stock_level) }}" required />
                                <div class="text-muted fs-7 mt-2">Stok minimum untuk peringatan.</div>
                            </div>
                            <div class="col-md-4 fv-row">
                                <label class="form-label">Lokasi Rak/Gudang</label>
                                <input type="text" name="warehouse_location" class="form-control" placeholder="Contoh: Rak A1" value="{{ old('warehouse_location', $product->warehouse_location) }}" />
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
                                <input type="text" name="spec_width" class="form-control mb-2" placeholder="Contoh: 150cm" value="{{ data_get($product->specifications, 'width') }}" />
                            </div>
                            <div class="col-md-4 fv-row">
                                <label class="form-label">Gramasi (Grammage)</label>
                                <input type="text" name="spec_grammage" class="form-control mb-2" placeholder="Contoh: 160-170" value="{{ data_get($product->specifications, 'grammage') }}" />
                            </div>
                            <div class="col-md-4 fv-row">
                                <label class="form-label">Komposisi</label>
                                <input type="text" name="spec_composition" class="form-control mb-2" placeholder="Contoh: 100% Cotton" value="{{ data_get($product->specifications, 'composition') }}" />
                            </div>
                        </div>
                        <div class="row g-9">
                            <div class="col-md-6 fv-row">
                                <label class="form-label">Warna</label>
                                <select name="spec_color" class="form-select mb-2" data-placeholder="Pilih Warna" id="kt_ecommerce_add_product_color">
                                    <option></option>
                                    @foreach($colors as $color)
                                        <option value="{{ $color->name }}" data-kt-color="{{ $color->hex_code }}" {{ data_get($product->specifications, 'color') == $color->name ? 'selected' : '' }}>{{ $color->name }}</option>
                                    @endforeach
                                    <option value="ADD_NEW_COLOR" class="fw-bold text-primary">-- Tambah Warna Baru --</option>
                                </select>
                            </div>
                            <div class="col-md-6 fv-row">
                                <label class="form-label">Motif</label>
                                <input type="text" name="spec_motif" class="form-control mb-2" placeholder="Contoh: Polos, Salur" value="{{ data_get($product->specifications, 'motif') }}" />
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
                                <option value="{{ $category->id }}" {{ $product->category_id == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                            @endforeach
                            <option value="ADD_NEW_CATEGORY" class="fw-bold text-primary">-- Tambah Kategori Baru --</option>
                        </select>
                        
                        <label class="form-label text-muted d-block">Label (Tags)</label>
                        @php
                            $tags = data_get($product->specifications, 'tags', []);
                            $tagsString = is_array($tags) ? implode(',', $tags) : '';
                        @endphp
                        <input id="kt_ecommerce_add_product_tags" name="tags" class="form-control mb-2" value="{{ $tagsString }}" />
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
                            <span class="indicator-label">Perbarui</span>
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
@endsection

@push('scripts')
<script src="{{ asset('assets/custom/js/pages/master/product/form.js') }}?v={{ time() }}"></script>
@endpush
