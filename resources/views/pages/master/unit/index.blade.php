@extends('layouts.app')

@section('title', 'Master Data Satuan')

@section('breadcrumb')
    <li class="breadcrumb-item"><span class="bullet bg-gray-500 w-5px h-2px"></span></li>
    <li class="breadcrumb-item text-muted">Master Data</li>
    <li class="breadcrumb-item"><span class="bullet bg-gray-500 w-5px h-2px"></span></li>
    <li class="breadcrumb-item text-muted">Satuan</li>
@endsection

@section('content')
<div class="row g-5 g-xl-10">
    <!-- Form Tambah/Edit Satuan -->
    <div class="col-lg-4">
        <div class="card card-flush h-lg-100">
            <div class="card-header pt-7">
                <div class="card-title">
                    <span class="card-icon"><i class="ki-duotone ki-delivery-2 fs-1 text-primary"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span><span class="path6"></span><span class="path7"></span><span class="path8"></span><span class="path9"></span></i></span>
                    <h2 class="fw-bold" id="form_title">Tambah Satuan</h2>
                </div>
            </div>
            <div class="card-body pt-5">
                <form id="form_add_unit" action="#" method="POST" class="form d-flex flex-column">
                    @csrf
                    <input type="hidden" name="id" id="unit_id">
                    <div class="fv-row mb-7">
                        <label class="fs-6 fw-semibold form-label mt-3"><span class="required">Nama Satuan</span></label>
                        <input type="text" class="form-control form-control-solid" name="name" id="unit_name" placeholder="Contoh: Meter" required />
                    </div>
                    <div class="fv-row mb-7">
                        <label class="fs-6 fw-semibold form-label mt-3"><span class="required">Simbol / Kode</span></label>
                        <input type="text" class="form-control form-control-solid" name="symbol" id="unit_symbol" placeholder="Contoh: Mtr" required />
                    </div>
                    <div class="fv-row mb-7">
                        <label class="fs-6 fw-semibold form-label mt-3"><span>Keterangan</span></label>
                        <textarea class="form-control form-control-solid" name="description" id="unit_description" rows="3" placeholder="Opsional"></textarea>
                    </div>
                    <div class="separator mb-6"></div>
                    <div class="d-flex justify-content-end">
                        <button type="button" id="btn_cancel_edit" class="btn btn-light me-3 d-none">Batal</button>
                        <button type="submit" id="btn_save_unit" class="btn btn-primary w-100">
                            <span class="indicator-label">Simpan Satuan</span>
                            <span class="indicator-progress">Mohon tunggu... <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Daftar Satuan -->
    <div class="col-lg-8">
        <div class="card card-flush h-lg-100">
            <div class="card-header pt-7">
                <div class="card-title">
                    <h2 class="fw-bold">Daftar Satuan</h2>
                </div>
                <div class="card-toolbar">
                    <div class="d-flex align-items-center position-relative my-1">
                        <i class="ki-duotone ki-magnifier fs-3 position-absolute ms-4"><span class="path1"></span><span class="path2"></span></i>
                        <input type="text" data-kt-unit-table-filter="search" class="form-control form-control-solid w-250px ps-12" placeholder="Cari Satuan..." />
                    </div>
                </div>
            </div>
            <div class="card-body pt-5">
                <div class="table-responsive">
                    <table class="table align-middle table-row-dashed fs-6 gy-3" id="kt_unit_table">
                        <thead>
                            <tr class="text-start text-gray-500 fw-bold fs-7 text-uppercase gs-0">
                                <th class="w-10px pe-2">
                                    <div class="form-check form-check-sm form-check-custom form-check-solid me-3">
                                        <input class="form-check-input" type="checkbox" data-kt-check="true" data-kt-check-target="#kt_unit_table .form-check-input" value="1" />
                                    </div>
                                </th>
                                <th class="min-w-150px">Nama Satuan</th>
                                <th class="min-w-100px">Simbol</th>
                                <th class="min-w-200px">Keterangan</th>
                                <th class="text-end min-w-70px">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="fw-semibold text-gray-600">
                            <!-- DataTables will populate this -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
    <link href="{{ asset('assets/vendors/plugins/custom/datatables/datatables.bundle.css') }}" rel="stylesheet" type="text/css" />
@endpush

@push('scripts')
    <script src="{{ asset('assets/vendors/plugins/custom/datatables/datatables.bundle.js') }}"></script>
    <script src="{{ asset('assets/custom/js/pages/master/units.js') }}?v={{ time() }}"></script>
@endpush
