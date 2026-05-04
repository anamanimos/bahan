@extends('layouts.app')

@section('title', 'Daftar Penjualan')

@section('breadcrumb')
    <li class="breadcrumb-item"><span class="bullet bg-gray-500 w-5px h-2px"></span></li>
    <li class="breadcrumb-item text-muted">Penjualan</li>
@endsection

@section('content')
<div class="row g-5 g-xl-10">
    <!--begin::Col (Statistics)-->
    <div class="col-xl-3">
        <div class="row row-cols-2 row-cols-xl-1 g-3 g-xl-5 mb-5 mb-xl-0">
            <div class="col">
                <div class="card card-flush h-100 py-4 bg-light-primary border-0 shadow-none">
                    <div class="card-header pt-2 px-3 px-md-9">
                        <div class="card-title d-flex flex-column">
                            <span class="fs-2 fs-md-2hx fw-bold text-gray-900 me-2 lh-1 ls-n2">{{ number_format($stats['count_today']) }}</span>
                            <span class="text-gray-600 pt-1 fw-semibold fs-9 fs-md-6">Penjualan Hari Ini</span>
                        </div>
                    </div>
                    <div class="card-body d-none d-md-flex align-items-end pt-0 px-9">
                        <i class="ki-duotone ki-cart fs-2hx text-primary"><span class="path1"></span><span class="path2"></span></i>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card card-flush h-100 py-4 bg-light-success border-0 shadow-none">
                    <div class="card-header pt-2 px-3 px-md-9">
                        <div class="card-title d-flex flex-column">
                            <span class="fs-4 fs-md-2hx fw-bold text-gray-900 me-2 lh-1 ls-n2 text-nowrap">{{ $stats['amount_today_formatted'] }}</span>
                            <span class="text-gray-600 pt-1 fw-semibold fs-9 fs-md-6">Omset Hari Ini</span>
                        </div>
                    </div>
                    <div class="card-body d-none d-md-flex align-items-end pt-0 px-9">
                        <i class="ki-duotone ki-chart-line-star fs-2hx text-success"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>
                    </div>
                </div>
            </div>
            <div class="col d-md-none">
                <div class="card card-flush h-100 py-4 bg-light-info border-0 shadow-none">
                    <div class="card-header pt-2 px-3 px-md-9">
                        <div class="card-title d-flex flex-column">
                            <span class="fs-4 fw-bold text-gray-900 me-2 lh-1 ls-n2 text-nowrap">{{ $stats['total_month_formatted'] }}</span>
                            <span class="text-gray-600 pt-1 fw-semibold fs-9">Bulan Ini</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="card card-flush h-auto mt-5 d-none d-xl-flex bg-light-info border-0 shadow-none">
            <div class="card-header pt-5 px-9">
                <div class="card-title d-flex flex-column">
                    <span class="fs-2hx fw-bold text-gray-900 me-2 lh-1 ls-n2">{{ $stats['total_month_formatted'] }}</span>
                    <span class="text-gray-600 pt-1 fw-semibold fs-6">Total Penjualan Bulan Ini</span>
                </div>
            </div>
            <div class="card-body d-flex align-items-end pt-0 px-9 pb-7">
                <i class="ki-duotone ki-wallet fs-2hx text-info"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span></i>
            </div>
        </div>
    </div>

    <!--begin::Col (Table/Cards)-->
    <div class="col-xl-9">
        <div class="card card-flush border-0 shadow-sm card-mobile-full">
            <div class="card-header align-items-center py-5 gap-2 gap-md-5 px-5 px-md-9 sticky-top-mobile">
                <div class="card-title w-100 w-md-auto">
                    <div class="d-flex align-items-center position-relative my-1 w-100">
                        <i class="ki-duotone ki-magnifier fs-3 position-absolute ms-4"><span class="path1"></span><span class="path2"></span></i>
                        <input type="text" data-kt-sale-filter="search" class="form-control form-control-solid w-100 w-md-250px ps-12" placeholder="Cari Nota / Pelanggan..." />
                    </div>
                </div>
                <div class="card-toolbar w-100 w-md-auto justify-content-end gap-2">
                    <a href="{{ route('sales.pos') }}" class="btn btn-primary btn-sm flex-grow-1 flex-md-grow-0">
                        <i class="ki-duotone ki-screen fs-2"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span></i> Buka POS
                    </a>
                </div>
            </div>
            <div class="card-body pt-0 px-0 px-md-9 card-body-mobile-full">
                <!-- Desktop View -->
                <div class="d-none d-md-block overflow-hidden">
                    <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_sales_table">
                        <thead>
                            <tr class="text-start text-gray-400 fw-bold fs-7 text-uppercase gs-0">
                                <th class="min-w-100px">Invoice</th>
                                <th class="min-w-120px">Pelanggan</th>
                                <th class="min-w-100px">Tanggal</th>
                                <th class="min-w-100px text-end">Total</th>
                                <th class="min-w-80px text-center">Status</th>
                                <th class="text-end min-w-70px">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="fw-semibold text-gray-600"></tbody>
                    </table>
                </div>

                <!-- Mobile View -->
                <div id="kt_sales_mobile_container" class="d-md-none d-flex flex-column border-top border-gray-200">
                    <div class="text-center py-10 text-muted fs-7">Memuat data penjualan...</div>
                </div>

                <div id="kt_sales_mobile_pagination" class="d-md-none mt-5 px-5"></div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
    <link href="{{ asset('assets/vendors/plugins/custom/datatables/datatables.bundle.css') }}" rel="stylesheet" type="text/css" />
    <style>
        @media (max-width: 767.98px) {
            .sticky-top-mobile {
                position: sticky !important;
                top: -1px;
                z-index: 100;
                background: #fff;
                border-radius: 0 !important;
                box-shadow: 0 10px 30px rgba(0,0,0,0.05) !important;
                margin-left: -1rem !important;
                margin-right: -1rem !important;
                width: calc(100% + 2rem) !important;
                padding-top: 1rem !important;
                padding-bottom: 1rem !important;
                margin-bottom: 1.5rem !important;
            }
            .card-mobile-full {
                background: transparent !important;
                border: 0 !important;
                box-shadow: none !important;
            }
            .card-body-mobile-full {
                padding-left: 0 !important;
                padding-right: 0 !important;
            }
            #kt_sales_mobile_container {
                margin-left: -1rem;
                margin-right: -1rem;
            }
        }
    </style>
@endpush

@push('scripts')
    <script src="{{ asset('assets/vendors/plugins/custom/datatables/datatables.bundle.js') }}"></script>
    <script src="{{ asset('assets/custom/js/pages/inventory/sales/list.js') }}?v={{ time() }}"></script>
@endpush
