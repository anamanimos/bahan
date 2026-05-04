@extends('layouts.app')

@section('title', 'Input Penerimaan Barang (Goods Receipt)')

@section('breadcrumb')
    <li class="breadcrumb-item"><span class="bullet bg-gray-500 w-5px h-2px"></span></li>
    <li class="breadcrumb-item text-muted">Inventori</li>
    <li class="breadcrumb-item"><span class="bullet bg-gray-500 w-5px h-2px"></span></li>
    <li class="breadcrumb-item text-muted">Input Penerimaan</li>
@endsection

@section('content')
<div class="d-flex flex-column">
    <!--begin::Form-->
    <form id="kt_goods_receipt_form" class="form">
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
                                <input type="text" class="form-control form-control-solid ps-12" name="date" id="kt_goods_receipt_date" value="{{ date('Y-m-d') }}" />
                            </div>
                        </div>

                        <!-- Supplier -->
                        <div class="mb-8">
                            <label class="required form-label fw-bold">Supplier / Pengirim</label>
                            <select class="form-select form-select-solid" name="supplier_id" id="goods_receipt_supplier_id" data-control="select2" data-placeholder="Pilih Supplier...">
                                <option></option>
                                <option value="add_new" data-kt-select2-template="add_new">+ Tambah Toko Baru</option>
                                @foreach(\App\Models\Supplier::orderBy('name', 'asc')->get() as $supplier)
                                    <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Foto Nota -->
                        <div class="mb-0">
                            <label class="required form-label fw-bold">Foto Nota / Surat Jalan</label>
                            <div class="fv-row">
                                <!-- Hidden file input for gallery pick -->
                                <input type="file" name="invoice_photo" class="d-none" accept="image/*" id="kt_goods_receipt_invoice_input" />

                                <!-- Option Selector (shown when no photo yet) -->
                                <div id="kt_goods_receipt_photo_options">
                                    <div class="d-flex flex-column gap-3">
                                        <!-- Option 1: Pick from gallery -->
                                        <div class="d-flex align-items-center gap-3 border border-dashed border-primary rounded p-4 cursor-pointer bg-hover-light-primary transition-all" id="kt_goods_receipt_option_gallery">
                                            <div class="d-flex align-items-center justify-content-center rounded-circle bg-light-primary" style="width:48px; height:48px; flex-shrink:0;">
                                                <i class="ki-duotone ki-picture fs-2 text-primary"><span class="path1"></span><span class="path2"></span></i>
                                            </div>
                                            <div>
                                                <div class="fw-bold text-gray-800 fs-6">Pilih Gambar</div>
                                                <div class="text-muted fs-8">Dari galeri atau file</div>
                                            </div>
                                        </div>

                                        <!-- Option 2: Capture from phone -->
                                        <div class="d-flex align-items-center gap-3 border border-dashed border-success rounded p-4 cursor-pointer bg-hover-light-success transition-all" id="kt_goods_receipt_option_companion">
                                            <div class="d-flex align-items-center justify-content-center rounded-circle bg-light-success" style="width:48px; height:48px; flex-shrink:0;">
                                                <i class="ki-duotone ki-phone fs-2 text-success"><span class="path1"></span><span class="path2"></span></i>
                                            </div>
                                            <div>
                                                <div class="fw-bold text-gray-800 fs-6">Ambil dari HP</div>
                                                <div class="text-muted fs-8">Scan QR, foto via kamera HP</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- QR Companion Panel (hidden by default) -->
                                <div class="d-none" id="kt_goods_receipt_companion_panel">
                                    <div class="text-center border border-dashed border-success rounded p-5 bg-light-success bg-opacity-25 position-relative">
                                        <!-- Loading state -->
                                        <div id="companion_qr_loading">
                                            <div class="spinner-border text-success mb-3" role="status">
                                                <span class="visually-hidden">Loading...</span>
                                            </div>
                                            <div class="text-gray-600 fw-semibold fs-7">Menyiapkan koneksi...</div>
                                        </div>

                                        <!-- QR Code display -->
                                        <div class="d-none" id="companion_qr_display">
                                            <div class="mb-3">
                                                <div class="d-inline-block bg-white rounded p-3 shadow-sm">
                                                    <div id="companion_qr_code" style="width:180px; height:180px;"></div>
                                                </div>
                                            </div>
                                            <div class="fw-bold text-gray-800 fs-7 mb-1">Scan QR Code dengan HP</div>
                                            <div class="text-muted fs-9 mb-3">Buka kamera HP → arahkan ke QR ini</div>
                                            <div class="d-flex align-items-center justify-content-center gap-2" id="companion_hp_status">
                                                <span class="badge badge-light-warning fs-9">
                                                    <i class="ki-duotone ki-time fs-8 me-1"><span class="path1"></span><span class="path2"></span></i>
                                                    Menunggu HP...
                                                </span>
                                            </div>
                                        </div>

                                        <!-- Cancel button -->
                                        <button type="button" class="btn btn-sm btn-light-danger mt-4" id="companion_cancel_btn">
                                            <i class="ki-duotone ki-cross fs-4 me-1"><span class="path1"></span><span class="path2"></span></i>
                                            Batal
                                        </button>
                                    </div>
                                </div>

                                <!-- Companion Connected Panel (shown when HP is already paired) -->
                                <div class="d-none" id="kt_goods_receipt_companion_connected">
                                    <div class="text-center border border-dashed border-success rounded p-4 bg-light-success bg-opacity-25">
                                        <div class="d-flex align-items-center justify-content-center gap-2 mb-3">
                                            <span class="bullet bullet-dot bg-success h-8px w-8px animation-blink"></span>
                                            <span class="fw-bold text-success fs-6">📱 HP Terhubung</span>
                                        </div>
                                        <div class="text-muted fs-8 mb-3">Ambil foto nota dari HP Anda.<br>Foto akan otomatis muncul di sini.</div>
                                        <div class="d-flex align-items-center justify-content-center gap-2">
                                            <div class="spinner-border spinner-border-sm text-success" role="status" id="companion_waiting_spinner">
                                                <span class="visually-hidden">Loading...</span>
                                            </div>
                                            <span class="text-gray-600 fs-8" id="companion_waiting_text">Menunggu foto dari HP...</span>
                                        </div>
                                        <div class="mt-3">
                                            <button type="button" class="btn btn-sm btn-light-primary me-2" id="companion_switch_gallery_btn">
                                                <i class="ki-duotone ki-picture fs-4 me-1"><span class="path1"></span><span class="path2"></span></i>
                                                Pilih Gambar
                                            </button>
                                            <button type="button" class="btn btn-sm btn-light-danger" id="companion_disconnect_btn">
                                                <i class="ki-duotone ki-cross fs-4 me-1"><span class="path1"></span><span class="path2"></span></i>
                                                Putuskan
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <!-- Photo Preview (hidden by default, shown after photo picked/received) -->
                                <div class="d-none" id="kt_goods_receipt_invoice_preview_container">
                                    <div class="position-relative">
                                        <img src="" class="img-fluid rounded shadow-sm w-100 mb-2" id="kt_goods_receipt_invoice_preview" />
                                        <div class="d-flex gap-2 mt-2">
                                            <button type="button" class="btn btn-sm btn-light-primary flex-fill" id="kt_goods_receipt_change_photo">
                                                <i class="ki-duotone ki-arrows-loop fs-4 me-1"><span class="path1"></span><span class="path2"></span></i>
                                                Ganti Foto
                                            </button>
                                        </div>
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
                            <span class="text-gray-400 mt-1 fw-semibold fs-7" id="kt_goods_receipt_total_items_label">0 Item terdaftar</span>
                        </h3>
                        <div class="card-toolbar gap-3">
                            <div class="w-200px d-none d-md-block">
                                <select class="form-select form-select-solid form-select-sm" id="kt_goods_receipt_add_by_purchase_requisition" data-control="select2" data-placeholder="Tarik dari PR...">
                                    <option></option>
                                    @foreach(\App\Models\PurchaseRequisition::where('status', 'Approved')->orderBy('identifier', 'desc')->get() as $pr)
                                        <option value="{{ $pr->identifier }}">{{ $pr->identifier }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <button type="button" class="btn btn-light-primary btn-sm" id="kt_goods_receipt_add_manual">
                                <i class="ki-duotone ki-plus fs-3"></i> Manual
                            </button>
                        </div>
                    </div>
                    <div class="card-body pt-5">
                        <!-- Mobile PR Search -->
                        <div class="d-md-none mb-5">
                            <select class="form-select form-select-solid" id="kt_goods_receipt_add_by_purchase_requisition_mobile" data-control="select2" data-placeholder="Tarik dari PR...">
                                <option></option>
                                @foreach(\App\Models\PurchaseRequisition::where('status', 'Approved')->orderBy('identifier', 'desc')->get() as $pr)
                                    <option value="{{ $pr->identifier }}">{{ $pr->identifier }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!--begin::Items Container-->
                        <div id="kt_goods_receipt_items_container">
                            <div class="text-center py-20 bg-light-primary bg-opacity-30 border border-dashed border-primary rounded mb-5" id="goods_receipt_items_empty">
                                <i class="ki-duotone ki-delivery-2 fs-3x text-primary mb-3"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span><span class="path6"></span><span class="path7"></span><span class="path8"></span><span class="path9"></span></i>
                                <div class="text-gray-600 fw-semibold">Gunakan tombol di atas untuk menambah item</div>
                            </div>
                            
                            <!-- Item template -->
                            <div class="goods-receipt-item d-none mb-4 border border-dashed border-gray-300 rounded shadow-sm" data-kt-element="item">
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
                                            <div class="col-6 col-md-2" data-kt-element="qty-purchase-requisition-container">
                                                <label class="form-label fw-bold fs-8 text-gray-700 mb-1 text-center d-block">Sisa PR</label>
                                                <div class="fs-7 fw-bold text-gray-600 text-center"><span data-kt-element="qty-purchase-requisition">10</span> <span class="fs-9" data-kt-element="unit">Mtr</span></div>
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
                            <span class="fs-3 fw-bold text-gray-800"><span id="kt_goods_receipt_total_count">0</span> Item</span>
                        </div>
                        <div class="d-flex flex-column border-start ps-10">
                            <span class="text-gray-400 fw-bold fs-8 text-uppercase">Total Estimasi</span>
                            <span class="fs-3 fw-bold text-success">Rp <span id="kt_goods_receipt_total_estimation">0</span></span>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-success px-10" id="kt_goods_receipt_submit" disabled>
                        <span class="indicator-label">Simpan Nota Goods Receipt</span>
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

<!-- Modal: Quick Add Supplier -->
<div class="modal fade" id="modal_quick_add_supplier" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-500px">
        <div class="modal-content">
            <form id="form_quick_add_supplier" class="form">
                <div class="modal-header">
                    <h2 class="fw-bold">Tambah Supplier Baru</h2>
                    <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
                        <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
                    </div>
                </div>
                <div class="modal-body py-10 px-lg-17">
                    <div class="fv-row mb-7">
                        <label class="required fs-6 fw-semibold mb-2">Nama Supplier</label>
                        <input type="text" class="form-control form-control-solid" name="name" placeholder="Masukkan nama supplier" required />
                    </div>
                    <div class="fv-row mb-7">
                        <label class="fs-6 fw-semibold mb-2">Nomor Telepon</label>
                        <input type="text" class="form-control form-control-solid" name="phone_number" placeholder="Contoh: 08123456789" />
                    </div>
                    <div class="fv-row mb-7">
                        <label class="fs-6 fw-semibold mb-2">Alamat</label>
                        <textarea class="form-control form-control-solid" name="address" rows="3" placeholder="Alamat lengkap supplier"></textarea>
                    </div>
                </div>
                <div class="modal-footer flex-center">
                    <button type="reset" class="btn btn-light me-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" id="btn_quick_add_supplier_submit" class="btn btn-primary">
                        <span class="indicator-label">Simpan Supplier</span>
                        <span class="indicator-progress">Mohon tunggu... <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('styles')
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
.animation-blink { animation: blink-animation 1.5s infinite; }
@keyframes blink-animation { 0%,100%{opacity:1;} 50%{opacity:0.3;} }
</style>
@endpush

@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <script src="{{ asset('assets/custom/js/pages/inventory/goods-receipt/form.js') }}?v={{ time() }}"></script>
@endpush
