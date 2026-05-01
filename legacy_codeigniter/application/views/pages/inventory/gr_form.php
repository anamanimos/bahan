<div class="d-flex flex-column">
    <!--begin::Form-->
    <form id="kt_gr_form" class="form">
        <div class="row g-5 g-xl-8">
            <!--begin::Left Column (Header Info)-->
            <div class="col-xl-4">
                <div class="card card-flush h-lg-100">
                    <div class="card-header pt-7">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="card-label fw-bold text-gray-800">Informasi Nota</span>
                            <span class="text-gray-400 mt-1 fw-semibold fs-7">Lengkapi data surat jalan</span>
                        </h3>
                    </div>
                    <div class="card-body">
                        <!-- Tanggal -->
                        <div class="mb-8">
                            <label class="required form-label fw-bold">Tanggal Terima</label>
                            <div class="position-relative d-flex align-items-center">
                                <i class="ki-duotone ki-calendar fs-2 position-absolute mx-4"><span class="path1"></span><span class="path2"></span></i>
                                <input type="text" class="form-control form-control-solid ps-12" name="date" id="kt_gr_date" value="<?= date('Y-m-d') ?>" />
                            </div>
                        </div>

                        <!-- Supplier -->
                        <div class="mb-8">
                            <label class="required form-label fw-bold">Supplier / Pengirim</label>
                            <select class="form-select form-select-solid" name="supplier_id" id="gr_supplier_id" data-control="select2" data-placeholder="Pilih Supplier...">
                                <option></option>
                                <option value="1">Toko Subur Makmur</option>
                                <option value="2">CV. Tekstil Jaya</option>
                                <option value="3">PT. Bahan Utama</option>
                            </select>
                        </div>

                        <!-- Foto Nota -->
                        <div class="mb-0">
                            <label class="required form-label fw-bold">Foto Nota / Surat Jalan</label>
                            <div class="fv-row">
                                <div class="dropzone d-flex align-items-center justify-content-center border-dashed border-primary bg-light-primary rounded p-5 cursor-pointer position-relative" id="kt_gr_invoice_upload" style="min-height: 200px;">
                                    <input type="file" name="invoice_photo" class="position-absolute opacity-0 w-100 h-100 cursor-pointer" accept="image/*" id="kt_gr_invoice_input" />
                                    <div class="text-center" id="kt_gr_invoice_placeholder">
                                        <i class="ki-duotone ki-camera fs-3hx text-primary mb-2"><span class="path1"></span><span class="path2"></span></i>
                                        <div class="fs-6 fw-bold text-gray-800">Ambil Foto</div>
                                        <div class="text-muted fs-8">Klik di sini</div>
                                    </div>
                                    <div class="d-none w-100" id="kt_gr_invoice_preview_container">
                                        <img src="" class="img-fluid rounded shadow-sm w-100 mb-2" id="kt_gr_invoice_preview" />
                                        <div class="text-primary fw-bold fs-8 text-center border border-primary border-dashed rounded p-2">Ganti Foto</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--end::Left Column-->

            <!--begin::Right Column (Items)-->
            <div class="col-xl-8">
                <div class="card card-flush h-lg-100">
                    <div class="card-header pt-7 align-items-center">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="card-label fw-bold text-gray-800">Daftar Barang</span>
                            <span class="text-gray-400 mt-1 fw-semibold fs-7" id="kt_gr_total_items_label">0 Item terdaftar</span>
                        </h3>
                        <div class="card-toolbar gap-3">
                            <div class="w-200px d-none d-md-block">
                                <select class="form-select form-select-solid form-select-sm" id="kt_gr_add_by_pr" data-control="select2" data-placeholder="Tarik dari PR...">
                                    <option></option>
                                    <option value="PR-20260501-001">PR-20260501-001</option>
                                    <option value="PR-20260430-008">PR-20260430-008</option>
                                </select>
                            </div>
                            <button type="button" class="btn btn-light-primary btn-sm" id="kt_gr_add_manual">
                                <i class="ki-duotone ki-plus fs-3"></i> Manual
                            </button>
                        </div>
                    </div>
                    <div class="card-body pt-5">
                        <!-- Mobile PR Search -->
                        <div class="d-md-none mb-5">
                            <select class="form-select form-select-solid" id="kt_gr_add_by_pr_mobile" data-control="select2" data-placeholder="Tarik dari PR...">
                                <option></option>
                                <option value="PR-20260501-001">PR-20260501-001</option>
                                <option value="PR-20260430-008">PR-20260430-008</option>
                            </select>
                        </div>

                        <!--begin::Items Container-->
                        <div id="kt_gr_items_container">
                            <div class="text-center py-20 bg-light-primary bg-opacity-30 border border-dashed border-primary rounded mb-5" id="gr_items_empty">
                                <i class="ki-duotone ki-delivery-2 fs-3x text-primary mb-3"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span><span class="path6"></span><span class="path7"></span><span class="path8"></span><span class="path9"></span></i>
                                <div class="text-gray-600 fw-semibold">Gunakan tombol di atas untuk menambah item</div>
                            </div>
                            
                            <!-- Item template -->
                            <div class="gr-item d-none mb-4 border border-dashed border-gray-300 rounded shadow-sm" data-kt-element="item">
                                <!-- Header: Click to collapse -->
                                <div class="p-3 bg-light-primary bg-opacity-10 d-flex justify-content-between align-items-center rounded-top border-bottom border-gray-200 cursor-pointer collapsible" 
                                     data-bs-toggle="collapse" data-kt-element="collapse-trigger">
                                    <div class="d-flex align-items-center">
                                        <i class="ki-duotone ki-down fs-4 me-2 collapsible-active-rotate-180"></i>
                                        <span class="fw-bold fs-7 text-gray-800" data-kt-element="item-source">Item Baru</span>
                                    </div>
                                    <button type="button" class="btn btn-icon btn-sm btn-active-light-danger h-25px w-25px" data-kt-element="remove-item">
                                        <i class="ki-duotone ki-cross fs-3"><span class="path1"></span><span class="path2"></span></i>
                                    </button>
                                </div>
                                
                                <!-- Body: Collapsible content -->
                                <div class="collapse show" data-kt-element="collapse-content">
                                    <div class="p-5">
                                        <div class="row g-5 align-items-start">
                                            <!-- Col Barang -->
                                            <div class="col-12 col-md-3" data-kt-element="product-col">
                                                <label class="form-label fw-bold fs-8 text-gray-700 mb-1">Nama Barang</label>
                                                <div class="d-flex flex-column" data-kt-element="product-display">
                                                    <div class="fw-bold text-gray-800 fs-7 lh-1 mb-1" data-kt-element="item-name">Cotton Combed 30s Putih</div>
                                                    <div class="text-muted fs-9">Ref: <span data-kt-element="order-ref">-</span></div>
                                                </div>
                                                <div class="d-none" data-kt-element="product-select-container">
                                                    <select class="form-select form-select-solid mb-2" name="product_id[]" data-placeholder="Cari Bahan...">
                                                        <option></option>
                                                    </select>
                                                    <div class="row g-2">
                                                        <div class="col-4">
                                                            <select class="form-select form-select-solid fs-9 px-3" name="context_type[]" data-kt-element="context-type">
                                                                <option value="Stok">Stok</option>
                                                                <option value="Order">Order</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-8 d-none" data-kt-element="order-container">
                                                            <select class="form-select form-select-solid fs-9" name="order_id[]" data-placeholder="Pilih Order..." data-kt-element="order-select">
                                                                <option></option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <!-- Col Qty -->
                                            <div class="col-6 col-md-2" data-kt-element="qty-pr-container">
                                                <label class="form-label fw-bold fs-8 text-gray-700 mb-1 text-center d-block">Sisa PR</label>
                                                <div class="fs-7 fw-bold text-gray-600 text-center"><span data-kt-element="qty-pr">10</span> <span class="fs-9" data-kt-element="unit">Mtr</span></div>
                                            </div>
                                            
                                            <div class="col-6 col-md-1" data-kt-element="qty-receive-col">
                                                <label class="form-label fw-bold fs-8 text-primary mb-1">Jml</label>
                                                <input type="number" class="form-control form-control-solid px-3" name="quantity[]" value="0" />
                                            </div>

                                            <!-- Col Price -->
                                            <div class="col-6 col-md-2" data-kt-element="price-col">
                                                <label class="form-label fw-bold fs-8 text-success mb-1">Harga Satuan</label>
                                                <div class="input-group input-group-solid">
                                                    <span class="input-group-text fs-9 px-2">Rp</span>
                                                    <input type="number" class="form-control form-control-solid ps-2" name="price[]" value="0" placeholder="0" />
                                                </div>
                                            </div>

                                            <!-- Col Notes -->
                                            <div class="col-12 col-md-4" data-kt-element="notes-col">
                                                <label class="form-label fw-bold fs-8 text-gray-700 mb-1">Catatan / Keterangan</label>
                                                <textarea class="form-control form-control-solid fs-8" name="notes[]" rows="1" placeholder="Tulis keterangan di sini..."></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--end::Items Container-->
                    </div>
                </div>
            </div>
            <!--end::Right Column-->
        </div>

        <!--begin::Sticky Footer-->
        <div class="card card-flush shadow-sm sticky-bottom mt-5" style="bottom: 0; z-index: 100; border-top: 1px solid #eee;">
            <div class="card-body p-5">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-5">
                    <div class="d-flex align-items-center gap-10">
                        <div class="d-flex flex-column">
                            <span class="text-gray-400 fw-bold fs-8 text-uppercase">Total Item</span>
                            <span class="fs-3 fw-bold text-gray-800"><span id="kt_gr_total_count">0</span> Item</span>
                        </div>
                        <div class="d-flex flex-column border-start ps-10">
                            <span class="text-gray-400 fw-bold fs-8 text-uppercase">Total Estimasi</span>
                            <span class="fs-3 fw-bold text-success">Rp <span id="kt_gr_total_estimation">0</span></span>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-success px-10" id="kt_gr_submit" disabled>
                        <span class="indicator-label">Simpan Nota GR</span>
                        <span class="indicator-progress">
                            <span class="spinner-border spinner-border-sm align-middle"></span>
                        </span>
                    </button>
                </div>
            </div>
        </div>
        <!--end::Sticky Footer-->
    </form>
    <!--end::Form-->
</div>

<style>
@media (max-width: 767.98px) {
    .sticky-bottom {
        position: sticky !important;
        margin-left: -1.25rem;
        margin-right: -1.25rem;
        margin-bottom: -1.25rem;
        border-radius: 0;
    }
}
.collapsible-active-rotate-180 { transition: transform 0.3s ease; }
.collapsed .collapsible-active-rotate-180 { transform: rotate(-90deg); }
</style>
