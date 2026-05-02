@extends('layouts.app')

@section('title', 'Daftar Penerimaan Barang (Goods Receipt)')

@section('breadcrumb')
    <li class="breadcrumb-item"><span class="bullet bg-gray-500 w-5px h-2px"></span></li>
    <li class="breadcrumb-item text-muted">Inventori</li>
    <li class="breadcrumb-item"><span class="bullet bg-gray-500 w-5px h-2px"></span></li>
    <li class="breadcrumb-item text-muted">Daftar Penerimaan</li>
@endsection

@section('content')
<div class="row g-5 g-xl-10">
    <!--begin::Col (Statistics)-->
    <div class="col-xl-3">
        <div class="row row-cols-3 row-cols-xl-1 g-3 g-xl-5 mb-5 mb-xl-0">
            <div class="col">
                <div class="card card-flush h-100 py-4 bg-light-info border-0 shadow-none">
                    <div class="card-header pt-2 px-3 px-md-9">
                        <div class="card-title d-flex flex-column">
                            <span class="fs-2 fs-md-2hx fw-bold text-gray-900 me-2 lh-1 ls-n2">{{ number_format($stats['in_progress']) }}</span>
                            <span class="text-gray-600 pt-1 fw-semibold fs-9 fs-md-6">PR Approved</span>
                        </div>
                    </div>
                    <div class="card-body d-none d-md-flex align-items-end pt-0 px-9">
                        <i class="ki-duotone ki-delivery-3 fs-2hx text-info"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card card-flush h-100 py-4 bg-light-success border-0 shadow-none">
                    <div class="card-header pt-2 px-3 px-md-9">
                        <div class="card-title d-flex flex-column">
                            <span class="fs-2 fs-md-2hx fw-bold text-gray-900 me-2 lh-1 ls-n2">{{ number_format($stats['received']) }}</span>
                            <span class="text-gray-600 pt-1 fw-semibold fs-9 fs-md-6">GR Received</span>
                        </div>
                    </div>
                    <div class="card-body d-none d-md-flex align-items-end pt-0 px-9">
                        <i class="ki-duotone ki-package fs-2hx text-success"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card card-flush h-100 py-4 bg-light-primary border-0 shadow-none">
                    <div class="card-header pt-2 px-3 px-md-9">
                        <div class="card-title d-flex flex-column">
                            <span class="fs-4 fs-md-2hx fw-bold text-gray-900 me-2 lh-1 ls-n2 text-nowrap">{{ $stats['total_items_formatted'] }}</span>
                            <span class="text-gray-600 pt-1 fw-semibold fs-9 fs-md-6">Total Qty (Pcs/Mtr)</span>
                        </div>
                    </div>
                    <div class="card-body d-none d-md-flex align-items-end pt-0 px-9">
                        <i class="ki-duotone ki-barcode fs-2hx text-primary"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span><span class="path6"></span><span class="path7"></span><span class="path8"></span></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!--begin::Col (Table/Cards)-->
    <div class="col-xl-9">
        <div class="card card-flush border-0 shadow-sm">
            <!--begin::Card header-->
            <div class="card-header align-items-center py-5 gap-2 gap-md-5 px-5 px-md-9">
                <!--begin::Card title (Search)-->
                <div class="card-title w-100 w-md-auto">
                    <div class="d-flex align-items-center position-relative my-1 w-100">
                        <i class="ki-duotone ki-magnifier fs-3 position-absolute ms-4"><span class="path1"></span><span class="path2"></span></i>
                        <input type="text" data-kt-goods-receipt-filter="search" class="form-control form-control-solid w-100 w-md-250px ps-12" placeholder="Cari No. Goods Receipt..." />
                    </div>
                </div>
                
                <!--begin::Card toolbar-->
                <div class="card-toolbar flex-row-fluid justify-content-end gap-3 w-100 w-md-auto">
                    <!--begin::Filter Trigger-->
                    <button type="button" class="btn btn-sm btn-light-primary btn-flex btn-center" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                        <i class="ki-duotone ki-filter fs-2"><span class="path1"></span><span class="path2"></span></i>
                        <span class="d-none d-md-inline ms-2">Filter</span>
                    </button>
                    
                    <!--begin::Filter Menu-->
                    <div class="menu menu-sub menu-sub-dropdown w-250px w-md-300px" data-kt-menu="true" id="kt_menu_filter">
                        <div class="px-7 py-5"><div class="fs-5 text-gray-900 fw-bold">Opsi Filter</div></div>
                        <div class="separator border-gray-200"></div>
                        <div class="px-7 py-5">
                            <div class="mb-5">
                                <label class="form-label fw-semibold">Rentang Tanggal:</label>
                                <input class="form-control form-control-solid" placeholder="Pilih rentang tanggal" id="filter_date_range" />
                            </div>
                            <div class="mb-10">
                                <label class="form-label fw-semibold">Supplier:</label>
                                <div>
                                    <select class="form-select form-select-solid" id="filter_supplier" data-placeholder="Pilih Supplier" data-allow-clear="true" multiple="multiple">
                                        <option value="Supplier A">Supplier A</option>
                                        <option value="Supplier B">Supplier B</option>
                                        <option value="CV Maju Jaya">CV Maju Jaya</option>
                                    </select>
                                </div>
                            </div>
                            <div class="d-flex justify-content-end">
                                <button type="reset" class="btn btn-sm btn-light btn-active-light-primary" data-kt-menu-dismiss="true">Reset</button>
                            </div>
                        </div>
                    </div>

                    <a href="{{ route('inventory.goods-receipt.create') }}" class="btn btn-primary btn-sm px-4 flex-grow-1 flex-md-grow-0">
                        <i class="ki-duotone ki-plus fs-3"></i> Baru
                    </a>
                </div>
            </div>

            <!--begin::Active Filters-->
            <div class="px-5 px-md-10 pb-5 d-flex flex-wrap gap-2 d-none" id="active-filters-container"></div>

            <!--begin::Card body-->
            <div class="card-body pt-0 px-0 px-md-9">
                <!-- Desktop Table -->
                <div class="d-none d-md-block overflow-hidden">
                    <table class="table align-middle table-row-dashed fs-6 gy-2 w-100" id="kt_goods_receipt_table">
                        <thead>
                            <tr class="text-start text-gray-500 fw-bold fs-7 text-uppercase gs-0">
                                <th class="w-10px pe-2"><div class="form-check form-check-sm form-check-custom form-check-solid me-3"><input class="form-check-input" type="checkbox" data-kt-check="true" data-kt-check-target="#kt_goods_receipt_table .form-check-input" value="1" /></div></th>
                                <th class="min-w-100px border-start border-gray-300 ps-3 text-nowrap">No. Goods Receipt</th>
                                <th class="min-w-100px border-start border-gray-300 ps-3 text-nowrap">No. Purchase Requisition</th>
                                <th class="min-w-125px border-start border-gray-300 ps-3">Tanggal</th>
                                <th class="min-w-150px border-start border-gray-300 ps-3">Supplier</th>
                                <th class="min-w-100px border-start border-gray-300 ps-3 text-center">Item</th>
                                <th class="text-end min-w-100px border-start border-gray-300 ps-3">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="fw-semibold text-gray-600"></tbody>
                    </table>
                </div>

                <!-- Mobile Cards Container -->
                <div id="kt_goods_receipt_mobile_container" class="d-md-none d-flex flex-column border-top border-gray-200">
                    <div class="text-center py-10 text-muted fs-7">Memuat data penerimaan...</div>
                </div>

                <!-- Mobile Pagination Placeholder -->
                <div id="kt_goods_receipt_mobile_pagination" class="d-md-none mt-5 px-5"></div>
            </div>
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
.collapsible-active-rotate-180 { transition: transform 0.3s ease; }
.collapsed .collapsible-active-rotate-180 { transform: rotate(-90deg); }
</style>
@endpush

@push('scripts')
    <script src="{{ asset('assets/vendors/plugins/custom/datatables/datatables.bundle.js') }}"></script>
    <script src="{{ asset('assets/custom/js/pages/inventory/goods-receipt/list.js') }}?v={{ time() }}"></script>
@endpush
