@extends('layouts.app')

@section('title', 'Tambah Penjualan Baru')

@section('breadcrumb')
    <li class="breadcrumb-item"><span class="bullet bg-gray-500 w-5px h-2px"></span></li>
    <li class="breadcrumb-item text-muted"><a href="{{ route('sales.index') }}" class="text-muted text-hover-primary">Penjualan</a></li>
    <li class="breadcrumb-item"><span class="bullet bg-gray-500 w-5px h-2px"></span></li>
    <li class="breadcrumb-item text-muted">Tambah</li>
@endsection

@section('content')
<form id="kt_sales_create_form" class="form d-flex flex-column flex-lg-row">
    <!-- Sidebar -->
    <div class="d-flex flex-column gap-7 gap-lg-10 w-100 w-lg-300px mb-7 me-lg-10">
        <div class="card card-flush py-4">
            <div class="card-header">
                <div class="card-title"><h2>Informasi</h2></div>
            </div>
            <div class="card-body pt-0">
                <div class="fv-row mb-7">
                    <label class="required form-label">Pelanggan</label>
                    <select class="form-select mb-2" name="customer_id" id="customer_select" data-control="select2" data-placeholder="Pilih Pelanggan">
                        <option></option>
                        @foreach($customers as $customer)
                            <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                        @endforeach
                    </select>
                    <a href="#" class="btn btn-light-primary btn-sm w-100 mt-2" data-bs-toggle="modal" data-bs-target="#kt_modal_add_customer">
                        <i class="ki-duotone ki-plus fs-3"></i> Tambah Pelanggan
                    </a>
                </div>
                <div class="fv-row mb-7">
                    <label class="required form-label">Tanggal</label>
                    <input class="form-control" name="sale_date" placeholder="Pilih Tanggal" id="sale_date_picker" value="{{ date('Y-m-d') }}" />
                </div>
                <div class="fv-row">
                    <label class="form-label">Metode Bayar</label>
                    <select class="form-select" name="payment_method">
                        <option value="Cash">Tunai</option>
                        <option value="Transfer">Transfer</option>
                        <option value="Kredit">Kredit / Piutang</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="card card-flush py-4">
            <div class="card-header">
                <div class="card-title"><h2>Ringkasan</h2></div>
            </div>
            <div class="card-body pt-0">
                <div class="d-flex flex-stack mb-3">
                    <div class="fw-semibold text-gray-600 fs-7">Subtotal:</div>
                    <div class="fw-bold text-gray-800 fs-7">Rp <span id="summary_subtotal">0</span></div>
                </div>
                <div class="separator separator-dashed mb-3"></div>
                <div class="d-flex flex-stack">
                    <div class="fw-bolder text-gray-800 fs-4">Total:</div>
                    <div class="fw-bolder text-primary fs-4">Rp <span id="summary_total">0</span></div>
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" id="kt_sales_submit" class="btn btn-primary w-100">
                    <span class="indicator-label">Simpan Penjualan</span>
                    <span class="indicator-progress">Mohon tunggu... <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                </button>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="d-flex flex-column flex-row-fluid gap-7 gap-lg-10">
        <div class="card card-flush py-4">
            <div class="card-header">
                <div class="card-title"><h2>Daftar Barang</h2></div>
            </div>
            <div class="card-body pt-0">
                <div class="table-responsive">
                    <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_sales_items_table">
                        <thead>
                            <tr class="text-start text-gray-400 fw-bold fs-7 text-uppercase gs-0">
                                <th class="min-w-200px">Produk</th>
                                <th class="min-w-150px">Lot / Stok</th>
                                <th class="min-w-100px">Jumlah</th>
                                <th class="min-w-100px">Harga Satuan</th>
                                <th class="min-w-100px text-end">Total</th>
                                <th class="text-end min-w-50px"></th>
                            </tr>
                        </thead>
                        <tbody id="items_container">
                            <!-- Template Row -->
                            <tr data-kt-element="item-row" class="d-none" id="item_template">
                                <td>
                                    <select class="form-select form-select-sm" data-kt-element="product-select" data-placeholder="Cari Produk...">
                                        <option></option>
                                    </select>
                                    <input type="hidden" name="items[INDEX][product_id]" data-kt-element="product-id" />
                                </td>
                                <td>
                                    <select class="form-select form-select-sm" name="items[INDEX][lot_id]" data-kt-element="lot-select" data-placeholder="Pilih Lot...">
                                        <option></option>
                                    </select>
                                    <div class="text-muted fs-9 mt-1">Tersedia: <span data-kt-element="stock-display">-</span></div>
                                </td>
                                <td>
                                    <div class="input-group input-group-sm">
                                        <input type="number" class="form-control" name="items[INDEX][quantity]" data-kt-element="quantity" step="0.01" value="0" />
                                        <span class="input-group-text fs-9" data-kt-element="unit-display">-</span>
                                    </div>
                                </td>
                                <td>
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text">Rp</span>
                                        <input type="number" class="form-control" name="items[INDEX][unit_price]" data-kt-element="unit-price" step="1" value="0" />
                                    </div>
                                </td>
                                <td class="text-end">
                                    <span class="fw-bold fs-7">Rp <span data-kt-element="row-total">0</span></span>
                                </td>
                                <td class="text-end">
                                    <button type="button" class="btn btn-icon btn-sm btn-light-danger" data-kt-element="remove-item">
                                        <i class="ki-duotone ki-trash fs-3"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span></i>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <button type="button" class="btn btn-light-primary btn-sm mt-3" id="add_item_btn">
                    <i class="ki-duotone ki-plus fs-3"></i> Tambah Item
                </button>
            </div>
        </div>

        <div class="card card-flush py-4">
            <div class="card-header"><div class="card-title"><h2>Catatan</h2></div></div>
            <div class="card-body pt-0">
                <textarea class="form-control" name="notes" rows="3" placeholder="Tambahkan catatan jika ada..."></textarea>
            </div>
        </div>
    </div>
</form>

<!-- Add Customer Modal -->
<div class="modal fade" id="kt_modal_add_customer" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-500px">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="fw-bold">Tambah Pelanggan Baru</h2>
                <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
                    <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
                </div>
            </div>
            <form id="kt_modal_add_customer_form">
                <div class="modal-body py-10 px-lg-17">
                    <div class="fv-row mb-7">
                        <label class="required fs-6 fw-semibold mb-2">Nama Pelanggan</label>
                        <input type="text" class="form-control form-control-solid" name="name" required />
                    </div>
                    <div class="fv-row mb-7">
                        <label class="fs-6 fw-semibold mb-2">No. Telepon / WhatsApp</label>
                        <input type="text" class="form-control form-control-solid" name="phone" />
                    </div>
                </div>
                <div class="modal-footer flex-center">
                    <button type="reset" class="btn btn-light me-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" id="kt_modal_add_customer_submit" class="btn btn-primary">
                        <span class="indicator-label">Simpan Pelanggan</span>
                        <span class="indicator-progress">Mohon tunggu... <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('assets/custom/js/pages/inventory/sales/create.js') }}?v={{ time() }}"></script>
@endpush
