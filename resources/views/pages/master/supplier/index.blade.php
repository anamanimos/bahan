@extends('layouts.app')

@section('title', 'Master Data Supplier')

@section('breadcrumb')
    <li class="breadcrumb-item"><span class="bullet bg-gray-500 w-5px h-2px"></span></li>
    <li class="breadcrumb-item text-muted">Master Data</li>
    <li class="breadcrumb-item"><span class="bullet bg-gray-500 w-5px h-2px"></span></li>
    <li class="breadcrumb-item text-muted">Supplier</li>
@endsection

@section('content')
<div class="row g-5 g-xl-10">
    <!--begin::Col-->
    <div class="col-lg-3">
        <div class="d-flex flex-column gap-5">
            <!--begin::Stats Widget-->
            <div class="card card-flush py-4">
                <div class="card-header">
                    <div class="card-title">
                        <h2>Ringkasan</h2>
                    </div>
                </div>
                <div class="card-body pt-0">
                    <div class="d-flex flex-column gap-5">
                        <div class="d-flex flex-stack">
                            <div class="fw-semibold text-gray-400">Total Supplier:</div>
                            <div class="fw-bold text-gray-800 fs-6">{{ $stats['total'] }}</div>
                        </div>
                        <div class="separator separator-dashed"></div>
                        <div class="d-flex flex-stack">
                            <div class="fw-semibold text-gray-400">Baru Bulan Ini:</div>
                            <div class="fw-bold text-success fs-6">{{ $stats['total_this_month'] }}</div>
                        </div>
                        <div class="separator separator-dashed"></div>
                        <div class="d-flex flex-stack">
                            <div class="fw-semibold text-gray-400">Terhapus:</div>
                            <div class="fw-bold text-danger fs-6">{{ $stats['total_deleted'] }}</div>
                        </div>
                    </div>
                </div>
            </div>
            <!--end::Stats Widget-->

            <!--begin::Info Widget-->
            <div class="card card-flush py-4 bg-light-primary border-primary border-dashed">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <i class="ki-duotone ki-information-5 fs-2x text-primary me-3"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>
                        <div class="fw-semibold">
                            <span class="fs-6 text-gray-800 fw-bold d-block">Tips Cepat</span>
                            <span class="text-gray-600 fs-7">Gunakan fitur <b>Merge</b> untuk merapikan data supplier ganda.</span>
                        </div>
                    </div>
                </div>
            </div>
            <!--end::Info Widget-->
        </div>
    </div>
    <!--end::Col-->

    <!--begin::Col-->
    <div class="col-lg-9">
        <div class="card card-flush">
            <div class="card-header align-items-center py-5 gap-2 gap-md-5">
                <div class="card-title">
                    <div class="d-flex align-items-center position-relative my-1">
                        <i class="ki-duotone ki-magnifier fs-3 position-absolute ms-4"><span class="path1"></span><span class="path2"></span></i>
                        <input type="text" data-kt-supplier-filter="search" class="form-control form-control-solid w-250px ps-12" placeholder="Cari Supplier..." />
                    </div>
                </div>
                
                <div class="card-toolbar">
                    <!--begin::Toolbar-->
                    <div class="d-flex justify-content-end" data-kt-supplier-table-toolbar="base">
                        <a href="{{ route('master.supplier.create') }}" class="btn btn-sm btn-primary">Tambah Supplier</a>
                    </div>
                    <!--end::Toolbar-->

                    <!--begin::Group actions-->
                    <div class="d-flex justify-content-end align-items-center d-none" data-kt-supplier-table-toolbar="selected">
                        <div class="fw-bold me-5">
                            <span class="me-2" data-kt-supplier-table-select="selected_count"></span>Terpilih
                        </div>
                        <button type="button" class="btn btn-sm btn-info" data-kt-supplier-table-select="merge_selected">Gabungkan (Merge)</button>
                    </div>
                    <!--end::Group actions-->
                </div>
            </div>

            <div class="card-body pt-0">
                <div class="table-responsive">
                    <table class="table align-middle table-row-dashed fs-6 gy-2 w-100" id="kt_suppliers_table">
                        <thead>
                            <tr class="text-start text-gray-500 fw-bold fs-7 text-uppercase gs-0">
                                <th class="w-10px pe-2">
                                    <div class="form-check form-check-sm form-check-custom form-check-solid me-3">
                                        <input class="form-check-input" type="checkbox" data-kt-check="true" data-kt-check-target="#kt_suppliers_table .form-check-input" value="1" />
                                    </div>
                                </th>
                                <th class="min-w-150px">Nama Supplier</th>
                                <th class="min-w-150px">PIC / Kontak</th>
                                <th class="min-w-150px">Telepon</th>
                                <th class="min-w-200px">Alamat</th>
                                <th class="text-end min-w-70px">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="fw-semibold text-gray-600"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <!--end::Col-->
</div>

<!--begin::Modal - Merge Supplier-->
<div class="modal fade" id="kt_modal_merge_supplier" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-650px">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="fw-bold">Gabungkan Supplier</h2>
                <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
                    <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
                </div>
            </div>
            <div class="modal-body scroll-y mx-5 mx-xl-15 my-7">
                <form id="kt_modal_merge_supplier_form" class="form" action="#">
                    <div class="fv-row mb-10">
                        <label class="required fs-6 fw-semibold mb-2">Pilih Supplier Target</label>
                        <select class="form-select form-select-solid" data-control="select2" data-placeholder="Pilih supplier tujuan penggabungan" data-dropdown-parent="#kt_modal_merge_supplier" name="target_supplier" id="kt_merge_target_supplier">
                            <option></option>
                        </select>
                        <div class="text-muted fs-7 mt-2">Seluruh riwayat transaksi dari supplier lain yang dipilih akan dipindahkan ke supplier ini. Supplier lain tersebut akan dihapus.</div>
                    </div>
                    <div class="text-center">
                        <button type="reset" id="kt_modal_merge_supplier_cancel" class="btn btn-light me-3">Batal</button>
                        <button type="submit" id="kt_modal_merge_supplier_submit" class="btn btn-primary">
                            <span class="indicator-label">Gabungkan Sekarang</span>
                            <span class="indicator-progress">Mohon tunggu... <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
    <link href="{{ asset('assets/vendors/plugins/custom/datatables/datatables.bundle.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/custom/css/pages/master/suppliers.css') }}?v={{ time() }}" rel="stylesheet" type="text/css" />
@endpush

@push('scripts')
    <script src="{{ asset('assets/vendors/plugins/custom/datatables/datatables.bundle.js') }}"></script>
    <script src="{{ asset('assets/custom/js/pages/master/supplier/list.js') }}?v={{ time() }}"></script>
@endpush
