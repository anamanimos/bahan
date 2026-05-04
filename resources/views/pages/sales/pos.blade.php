@extends('layouts.app')

@section('title', 'Point of Sales')

@section('content')
<div class="d-flex flex-column flex-lg-row">
    <!-- Main Content: Product Selection -->
    <div class="flex-row-fluid me-lg-10 mb-10 mb-lg-0">
        <!-- Search & Filter Card -->
        <div class="card card-flush mb-5 sticky-top pos-sticky-header">
            <div class="card-body d-flex flex-column flex-md-row align-items-md-center justify-content-md-between gap-5 py-5">
                <div class="d-flex align-items-center position-relative">
                    <i class="ki-duotone ki-magnifier fs-2 position-absolute ms-4"><span class="path1"></span><span class="path2"></span></i>
                    <input type="text" id="pos_product_search" class="form-control form-control-solid w-100 w-md-300px ps-12" placeholder="Cari Produk atau Scan Barcode..." autofocus />
                </div>
                <div class="d-flex flex-row flex-md-nowrap gap-3 gap-md-5 w-100 w-md-auto">
                    <div class="w-50 w-md-200px">
                        <select class="form-select form-select-solid" id="pos_category_filter" data-control="select2" data-placeholder="Kategori" data-hide-search="true">
                            <option value="all">Semua</option>
                            @foreach(\App\Models\Category::all() as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="w-50 w-md-300px">
                        <select class="form-select form-select-solid" id="pos_tags_filter" data-control="select2" data-placeholder="Tag" data-allow-clear="true" multiple="multiple">
                            @foreach($tags as $tag)
                                <option value="{{ $tag }}">{{ $tag }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- Product Grid -->
        <div class="row g-5" id="product_grid">
                    @foreach($products as $product)
                    @php 
                        $totalStock = $product->lots_sum_remaining_quantity ?? 0;
                        $productTags = data_get($product->specifications, 'tags', []);
                    @endphp
                    <div class="col-6 col-md-3 col-lg-2 product-card" 
                         data-category-id="{{ $product->category_id }}" 
                         data-tags="{{ json_encode($productTags) }}"
                         data-name="{{ strtolower($product->name) }}" 
                         data-sku="{{ strtolower($product->sku) }}">
                        <div class="card border-0 shadow-sm card-flush h-100 product-item cursor-pointer text-hover-primary {{ $totalStock <= 0 ? 'opacity-50' : '' }}" 
                             data-id="{{ $product->id }}" 
                             data-name="{{ $product->name }}"
                             data-unit="{{ $product->base_unit }}"
                             data-lots="{{ json_encode($product->lots->map(function($l){ return ['id'=>$l->id, 'identifier'=>$l->identifier, 'stock'=>$l->remaining_quantity]; })) }}">
                            <div class="card-body p-3">
                                <div class="overlay-wrapper mb-3 text-center">
                                    @if($product->image_path)
                                        <img src="{{ asset('storage/'.$product->image_path) }}" alt="{{ $product->name }}" class="aspect-ratio-1 object-fit-cover rounded" />
                                    @else
                                        <div class="aspect-ratio-1 bg-light rounded d-flex align-items-center justify-content-center">
                                            <img src="{{ asset('assets/media/svg/files/blank-image.svg') }}" class="w-50px" alt="No Image" />
                                        </div>
                                    @endif
                                </div>
                                <div class="d-flex flex-column">
                                    <div class="fs-7 fw-bold text-gray-800 name-target mb-1 line-clamp-2" title="{{ $product->name }}" style="height: 2.8rem; line-height: 1.4rem;">
                                        {{ $product->name }}
                                    </div>
                                    <div class="d-flex flex-stack mt-auto">
                                        <span class="fs-9 text-muted text-truncate">SKU: {{ $product->sku ?? '-' }}</span>
                                        @if($totalStock > 0)
                                            <span class="badge badge-light-success fs-9 px-1 py-0">{{ number_format($totalStock, 0) }} {{ $product->base_unit }}</span>
                                        @else
                                            <span class="badge badge-light-danger fs-9 px-1 py-0">Habis</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
        </div>
    </div>

    <!-- Sidebar: Order Summary -->
    <div class="flex-column flex-lg-row-auto w-100 w-lg-400px">
        <!-- Mobile Toggle Button -->
        <button class="btn btn-primary btn-lg shadow-lg d-lg-none position-fixed bottom-0 end-0 m-5 z-index-3 fw-bold px-8 py-4 rounded-pill" id="kt_pos_mobile_cart_toggle">
            <i class="ki-duotone ki-cart fs-2 me-2"><span class="path1"></span><span class="path2"></span></i>
            Keranjang (<span id="mobile_cart_count">0</span>)
        </button>

        <div class="card card-flush bg-body border-0 shadow-sm sticky-lg-top pos-sticky-sidebar mobile-hidden" id="kt_pos_order">
            <div class="d-lg-none w-40px h-5px bg-gray-300 rounded-pill mx-auto mt-3"></div>
            <div class="card-header pt-5">
                <div class="card-title d-flex flex-column">
                    <span class="fs-2hx fw-bold text-gray-900 me-2 lh-1 ls-n2">Keranjang</span>
                    <span class="text-gray-500 pt-1 fw-semibold fs-6">Detail pesanan pelanggan</span>
                </div>
                <div class="card-toolbar gap-2">
                    <button class="btn btn-icon btn-sm btn-light-danger" id="clear_cart" title="Kosongkan Keranjang"><i class="ki-duotone ki-trash fs-2"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span></i></button>
                    <button class="btn btn-icon btn-sm btn-light-primary d-lg-none" id="kt_pos_mobile_cart_close"><i class="ki-duotone ki-cross fs-2"><span class="path1"></span><span class="path2"></span></i></button>
                </div>
            </div>
            <div class="card-body pt-5">
                <div class="mb-7">
                    <label class="form-label fw-bold">Pelanggan</label>
                    <div class="d-flex gap-2">
                        <select class="form-select form-select-solid" id="pos_customer_select" data-control="select2" data-placeholder="Pilih Pelanggan">
                            <option></option>
                            @foreach($customers as $customer)
                                <option value="{{ $customer->id }}" data-type="{{ $customer->type }}">{{ $customer->name }}</option>
                            @endforeach
                        </select>
                        <button type="button" class="btn btn-icon btn-light-primary btn-sm w-40px h-40px flex-shrink-0" id="btn_add_customer">
                            <i class="ki-duotone ki-plus fs-2"></i>
                        </button>
                    </div>
                </div>

                <div class="mb-5">
                    <label class="form-label fw-bold">Tanggal Transaksi</label>
                    <input class="form-control form-control-solid" id="pos_sale_date" placeholder="Pilih Tanggal" />
                </div>

                <div class="separator separator-dashed mb-5"></div>

                <div class="mh-400px scroll-y mb-5" id="cart_items">
                    <!-- Cart items inject here -->
                    <div class="text-center py-10 text-muted" id="empty_cart_msg">
                        <i class="ki-duotone ki-cart fs-3x opacity-25 mb-5"><span class="path1"></span><span class="path2"></span></i>
                        <p>Belum ada item terpilih</p>
                    </div>
                </div>

                <div class="separator separator-dashed mb-5"></div>

                <div class="d-flex flex-stack mb-5">
                    <span class="fw-semibold fs-4 text-gray-800">Total Tagihan</span>
                    <span class="fw-bold fs-2 text-primary">Rp <span id="pos_total">0</span></span>
                </div>

                <button class="btn btn-primary w-100 py-4 fs-3 fw-bold" id="process_payment">Bayar Sekarang</button>
            </div>
        </div>
    </div>
</div>

<!-- Lot Selection Modal -->
<div class="modal fade" id="kt_modal_select_lot" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-500px">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="fw-bold" id="modal_product_name">Pilih Lot</h2>
                <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
                    <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
                </div>
            </div>
            <div class="modal-body">
                <div class="mb-5">
                    <label class="form-label required">Pilih Lot (FIFO)</label>
                    <select class="form-select" id="lot_select_dropdown"></select>
                </div>
                <div class="mb-5">
                    <label class="form-label required">Jumlah Jual</label>
                    <div class="input-group">
                        <input type="number" class="form-control" id="lot_qty_input" step="0.01" value="1" />
                        <span class="input-group-text" id="modal_unit_text">-</span>
                    </div>
                </div>
                <div class="mb-5 d-none" id="modal_order_container">
                    <label class="form-label required">Referensi Order (ERP)</label>
                    <select class="form-select" id="modal_order_select" data-placeholder="Cari Order..." data-ajax-url="{{ route('inventory.ajax.purchase-requisition.search-orders') }}">
                        <option></option>
                    </select>
                </div>

                <div class="mb-5">
                    <label class="form-label required">Harga Satuan</label>
                    <div class="input-group">
                        <span class="input-group-text">Rp</span>
                        <input type="number" class="form-control" id="lot_price_input" value="0" />
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="confirm_add_to_cart">Tambahkan</button>
            </div>
        </div>
    </div>
</div>

<!-- Add Customer Modal (Reused) -->
<div class="modal fade" id="kt_modal_add_customer" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-400px">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="fw-bold">Tambah Pelanggan</h2>
                <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
                    <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
                </div>
            </div>
            <form id="kt_modal_add_customer_form">
                <div class="modal-body">
                    <div class="fv-row mb-7">
                        <label class="required fs-6 fw-semibold mb-2">Nama</label>
                        <input type="text" class="form-control form-control-solid" name="name" required />
                    </div>
                    <div class="fv-row mb-7">
                        <label class="required fs-6 fw-semibold mb-2">Tipe Pelanggan</label>
                        <div class="d-flex align-items-center mt-3">
                            <label class="form-check form-check-custom form-check-solid me-10">
                                <input class="form-check-input h-20px w-20px" type="radio" name="type" value="external" checked />
                                <span class="form-check-label fw-semibold">External (Umum)</span>
                            </label>
                            <label class="form-check form-check-custom form-check-solid">
                                <input class="form-check-input h-20px w-20px" type="radio" name="type" value="internal" />
                                <span class="form-check-label fw-semibold">Internal (Perusahaan)</span>
                            </label>
                        </div>
                    </div>
                    <div class="fv-row mb-7">
                        <label class="fs-6 fw-semibold mb-2">WhatsApp</label>
                        <input type="text" class="form-control form-control-solid" name="phone" />
                    </div>
                </div>
                <div class="modal-footer flex-center">
                    <button type="submit" class="btn btn-primary w-100">Simpan Pelanggan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    /* Responsive Sticky Header */
    .pos-sticky-header {
        top: 25px !important; 
        z-index: 105;
    }
    
    @media (min-width: 992px) {
        .pos-sticky-header {
            top: 80px !important;
        }
    }

    /* Responsive Sticky Sidebar */
    .pos-sticky-sidebar {
        z-index: 100;
    }

    @media (max-width: 991px) {
        .pos-sticky-sidebar {
            position: fixed !important;
            bottom: 0 !important;
            left: 0 !important;
            right: 0 !important;
            width: 100% !important;
            z-index: 1000 !important;
            margin: 0 !important;
            border-radius: 1.5rem 1.5rem 0 0 !important;
            box-shadow: 0 -10px 30px rgba(0,0,0,0.1) !important;
            background-color: var(--bs-body-bg) !important;
            border-top: 1px solid var(--bs-gray-200) !important;
            transition: transform 0.3s ease-in-out !important;
            max-height: 90vh;
        }

        .pos-sticky-sidebar.mobile-hidden {
            transform: translateY(100%);
        }

        .pos-sticky-sidebar.mobile-visible {
            transform: translateY(0);
        }

        .pos-sticky-sidebar .card-header {
            padding-top: 1rem !important;
            padding-bottom: 0.5rem !important;
            min-height: auto !important;
        }

        .pos-sticky-sidebar .card-body {
            padding-top: 0.5rem !important;
        }

        #cart_items {
            max-height: 150px !important;
            margin-bottom: 0.5rem !important;
        }

        .pos-sticky-sidebar .separator {
            margin-top: 0.5rem !important;
            margin-bottom: 0.5rem !important;
        }
    }

    @media (min-width: 992px) {
        .pos-sticky-sidebar {
            top: 80px !important;
        }
    }

    .product-item:hover { background-color: var(--bs-primary-light); }
    .cart-item { border-bottom: 1px dashed var(--bs-gray-300); }
    .cart-item:last-child { border-bottom: 0; }
    .object-fit-cover { object-fit: cover; }
    .text-truncate { overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .aspect-ratio-1 { aspect-ratio: 1 / 1; width: 100%; height: auto; }
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
</style>
@endpush

@push('scripts')
<script src="{{ asset('assets/custom/js/pages/inventory/sales/pos.js') }}?v={{ time() }}"></script>
@endpush
