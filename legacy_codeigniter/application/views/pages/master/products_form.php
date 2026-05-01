<form id="kt_ecommerce_add_product_form" class="form" action="<?= isset($product) ? base_url('inventory/products/update/'.$product['id']) : base_url('inventory/products/store') ?>" method="POST" enctype="multipart/form-data">
    <div class="row g-7 g-lg-10">
    <!--begin::Main column-->
    <div class="col-lg-8">
        <div class="d-flex flex-column gap-7 gap-lg-10">
        <!--begin::General options-->
        <div class="card card-flush py-4">
            <!--begin::Card header-->
            <div class="card-header">
                <div class="card-title">
                    <h2>Informasi Umum</h2>
                </div>
            </div>
            <!--end::Card header-->
            <!--begin::Card body-->
            <div class="card-body pt-0">
                <div class="row g-9 mb-10">
                    <div class="col-md-8 fv-row">
                        <label class="required form-label">Nama Produk</label>
                        <input type="text" name="name" class="form-control mb-2" placeholder="Masukkan nama produk" value="<?= isset($product['name']) ? $product['name'] : '' ?>" />
                    </div>
                    <div class="col-md-4 fv-row">
                        <label class="form-label">SKU</label>
                        <input type="text" name="sku" class="form-control mb-2" placeholder="Nomor SKU (Opsional)" value="<?= isset($product['sku']) ? $product['sku'] : '' ?>" />
                    </div>
                </div>
                <!--begin::Input group-->
                <div>
                    <!--begin::Label-->
                    <label class="form-label">Deskripsi</label>
                    <!--end::Label-->
                    <!--begin::Editor-->
                    <div id="kt_ecommerce_add_product_description" class="min-h-200px mb-2"><?= isset($product['description']) ? $product['description'] : '' ?></div>
                    <input type="hidden" name="description" id="kt_ecommerce_add_product_description_input" value="<?= isset($product['description']) ? htmlspecialchars($product['description']) : '' ?>" />
                    <!--end::Editor-->
                    <div class="text-muted fs-7">Berikan deskripsi detail produk untuk mempermudah identifikasi.</div>
                </div>
                <!--end::Input group-->
            </div>
            <!--end::Card body-->
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
                    <!--begin::Gallery Container-->
                    <div class="d-flex flex-wrap gap-5 border-0 p-0 bg-transparent" id="kt_ecommerce_add_product_media">
                        <!--begin::Items-->
                        <div class="d-flex flex-wrap gap-5" id="kt_ecommerce_add_product_media_items">
                            <?php if(isset($product['photos'])): ?>
                                <?php foreach($product['photos'] as $photo): ?>
                                    <div class="image-input image-input-outline border border-gray-300 rounded p-2">
                                        <div class="image-input-wrapper w-125px h-125px" style="background-image: url(<?= base_url('assets/media/products/'.$photo) ?>)"></div>
                                        <span class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow position-absolute translate-middle top-0 start-100" data-kt-image-input-action="remove">
                                            <i class="ki-duotone ki-cross fs-2"><span class="path1"></span><span class="path2"></span></i>
                                        </span>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                            
                            <!--begin::Add new item-->
                            <label class="btn btn-outline btn-outline-dashed btn-outline-default d-flex flex-column flex-center w-125px h-125px" style="cursor:pointer">
                                <i class="ki-duotone ki-plus fs-2tx text-primary mb-2"></i>
                                <span class="fs-7 fw-bold text-gray-400">Tambah Foto</span>
                                <input type="file" name="product_photos[]" multiple accept=".png, .jpg, .jpeg" class="d-none" id="kt_ecommerce_add_product_media_input" />
                            </label>
                            <!--end::Add new item-->
                        </div>
                        <!--end::Items-->
                    </div>
                </div>
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
                        <select class="form-select" data-control="select2" name="base_unit">
                            <option value="m" <?= (isset($product['base_unit']) && $product['base_unit'] == 'm') ? 'selected' : '' ?>>Meter (m)</option>
                            <option value="roll" <?= (isset($product['base_unit']) && $product['base_unit'] == 'roll') ? 'selected' : '' ?>>Roll</option>
                            <option value="kg" <?= (isset($product['base_unit']) && $product['base_unit'] == 'kg') ? 'selected' : '' ?>>Kg</option>
                        </select>
                        <div class="text-muted fs-7 mt-2">Satuan dasar untuk stok.</div>
                    </div>
                    <div class="col-md-4 fv-row">
                        <label class="required form-label">Ambang Batas (ROP)</label>
                        <input type="text" name="rop" class="form-control" placeholder="0" value="<?= isset($product['rop']) ? $product['rop'] : '' ?>" />
                        <div class="text-muted fs-7 mt-2">Stok minimum untuk peringatan.</div>
                    </div>
                    <div class="col-md-4 fv-row">
                        <label class="form-label">Lokasi Rak/Gudang</label>
                        <input type="text" name="location" class="form-control" placeholder="Contoh: Rak A1" value="<?= isset($product['location']) ? $product['location'] : '' ?>" />
                        <div class="text-muted fs-7 mt-2">Titik penyimpanan fisik.</div>
                    </div>
                </div>
            </div>
        </div>
        <!--end::Inventory Info-->
        <!--begin::Specifications-->
        <div class="card card-flush py-4">
            <!--begin::Card header-->
            <div class="card-header">
                <div class="card-title">
                    <h2>Spesifikasi Teknis</h2>
                </div>
            </div>
            <!--end::Card header-->
            <!--begin::Card body-->
            <div class="card-body pt-0">
                <div class="row g-9 mb-5">
                    <div class="col-md-4 fv-row">
                        <label class="form-label">Lebar (Width)</label>
                        <input type="text" name="spec_width" class="form-control mb-2" placeholder="Contoh: 150cm" value="<?= isset($product['specs']['width']) ? $product['specs']['width'] : '' ?>" />
                    </div>
                    <div class="col-md-4 fv-row">
                        <label class="form-label">Gramasi (Grammage)</label>
                        <input type="text" name="spec_grammage" class="form-control mb-2" placeholder="Contoh: 160-170" value="<?= isset($product['specs']['grammage']) ? $product['specs']['grammage'] : '' ?>" />
                    </div>
                    <div class="col-md-4 fv-row">
                        <label class="form-label">Komposisi</label>
                        <input type="text" name="spec_composition" class="form-control mb-2" placeholder="Contoh: 100% Cotton" value="<?= isset($product['specs']['composition']) ? $product['specs']['composition'] : '' ?>" />
                    </div>
                </div>
                <div class="row g-9">
                    <div class="col-md-6 fv-row">
                        <label class="form-label">Warna</label>
                        <select name="spec_color" class="form-select mb-2" data-placeholder="Pilih Warna" id="kt_ecommerce_add_product_color">
                            <option></option>
                            <option value="Hitam" data-kt-color="#000000" <?= (isset($product['specs']['color']) && $product['specs']['color'] == 'Hitam') ? 'selected' : '' ?>>Hitam</option>
                            <option value="Putih" data-kt-color="#FFFFFF" <?= (isset($product['specs']['color']) && $product['specs']['color'] == 'Putih') ? 'selected' : '' ?>>Putih</option>
                            <option value="Merah" data-kt-color="#FF0000" <?= (isset($product['specs']['color']) && $product['specs']['color'] == 'Merah') ? 'selected' : '' ?>>Merah</option>
                            <option value="Biru" data-kt-color="#0000FF" <?= (isset($product['specs']['color']) && $product['specs']['color'] == 'Biru') ? 'selected' : '' ?>>Biru</option>
                            <option value="ADD_NEW_COLOR" class="fw-bold text-primary">-- Tambah Warna Baru --</option>
                            <?php if(isset($product['specs']['color']) && !in_array($product['specs']['color'], ['Hitam', 'Putih', 'Merah', 'Biru'])): ?>
                                <option value="<?= $product['specs']['color'] ?>" data-kt-color="#CCCCCC" selected><?= $product['specs']['color'] ?></option>
                            <?php endif; ?>
                        </select>
                    </div>
                    <div class="col-md-6 fv-row">
                        <label class="form-label">Motif</label>
                        <input type="text" name="spec_motif" class="form-control mb-2" placeholder="Contoh: Polos, Salur" value="<?= isset($product['specs']['motif']) ? $product['specs']['motif'] : '' ?>" />
                    </div>
                </div>
            </div>
            <!--end::Card body-->
        </div>
        <!--end::Specifications-->
        </div>
    </div>
    <!--end::Main column-->

    <!--begin::Aside column-->
    <div class="col-lg-4">
        <div class="d-flex flex-column gap-7 gap-lg-10 sticky-lg-top" style="top: 100px">
        <!--begin::Category & tags-->
        <div class="card card-flush py-4 h-100">
            <!--begin::Card header-->
            <div class="card-header">
                <!--begin::Card title-->
                <div class="card-title">
                    <h2>Kategori & Label</h2>
                </div>
                <!--end::Card title-->
            </div>
            <!--end::Card header-->
            <!--begin::Card body-->
            <div class="card-body pt-0">
                <!--begin::Input group-->
                <label class="form-label text-muted">Kategori</label>
                <select class="form-select mb-5" data-control="select2" data-placeholder="Pilih Kategori" data-allow-clear="true" name="category_id" id="kt_ecommerce_add_product_category">
                    <option></option>
                    <option value="1" <?= (isset($product['category_id']) && $product['category_id'] == 1) ? 'selected' : '' ?>>Kain Rajut</option>
                    <option value="1.1">-- Cotton Combed</option>
                    <option value="1.2">-- Cotton Carded</option>
                    <option value="2">Kain Tenun</option>
                    <option value="2.1">-- Drill</option>
                    <option value="2.2">-- Oxford</option>
                    <option value="ADD_NEW_CATEGORY" class="fw-bold text-primary">-- Tambah Kategori Baru --</option>
                </select>
                <!--end::Input group-->
                
                <!--begin::Input group-->
                <label class="form-label text-muted d-block">Label (Tags)</label>
                <input id="kt_ecommerce_add_product_tags" name="tags" class="form-control mb-2" value="<?= isset($product['tags']) ? implode(',', $product['tags']) : '' ?>" />
                <div class="text-muted fs-7">Gunakan label untuk mempermudah pencarian (Pisahkan dengan koma).</div>
                <!--end::Input group-->
            </div>
            <!--end::Card body-->
        </div>
        <!--end::Category & tags-->

        <div class="row g-3 mt-5">
            <div class="col-6">
                <a href="<?= base_url('inventory/products') ?>" id="kt_ecommerce_add_product_cancel" class="btn btn-light w-100">Batal</a>
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
<!--end::Form-->

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
                <div class="scroll-y me-n7 pe-7" data-kt-scroll="true" data-kt-scroll-activate="{default: false, lg: true}" data-kt-scroll-max-height="auto" data-kt-scroll-offset="300px">
                    <div class="fv-row mb-7">
                        <label class="required fs-6 fw-semibold mb-2">Nama Kategori</label>
                        <input type="text" class="form-control form-control-solid" placeholder="Contoh: Kain Denim" id="kt_modal_add_category_name" />
                    </div>
                    <div class="fv-row mb-7">
                        <label class="fs-6 fw-semibold mb-2">Induk Kategori (Parent)</label>
                        <select class="form-select form-select-solid" data-control="select2" data-placeholder="Pilih Induk (Jika ada)" id="kt_modal_add_category_parent" data-dropdown-parent="#kt_modal_add_category">
                            <option></option>
                            <option value="1">Kain Rajut</option>
                            <option value="2">Kain Tenun</option>
                        </select>
                        <div class="text-muted fs-7 mt-2">Kosongkan jika ingin membuat kategori level utama.</div>
                    </div>
                </div>
            </div>
            <div class="modal-footer flex-center">
                <button type="reset" data-bs-dismiss="modal" class="btn btn-light me-3">Batal</button>
                <button type="button" id="kt_modal_add_category_submit" class="btn btn-primary">
                    <span class="indicator-label">Simpan Kategori</span>
                </button>
            </div>
        </div>
    </div>
</div>
<!--end::Modal - Add Category-->
