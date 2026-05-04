@extends('layouts.app')

@section('title', 'Master Data Warna')

@section('breadcrumb')
    <li class="breadcrumb-item"><span class="bullet bg-gray-500 w-5px h-2px"></span></li>
    <li class="breadcrumb-item text-muted">Master Data</li>
    <li class="breadcrumb-item"><span class="bullet bg-gray-500 w-5px h-2px"></span></li>
    <li class="breadcrumb-item text-muted">Warna</li>
@endsection

@section('toolbar_actions')
    <div class="d-flex align-items-center gap-2 gap-lg-3">
        <a href="{{ route('master.color.export') }}" class="btn btn-sm btn-light-primary">
            <i class="ki-duotone ki-exit-up fs-3"><span class="path1"></span><span class="path2"></span></i> Export CSV
        </a>
        <button type="button" class="btn btn-sm btn-light-success" data-bs-toggle="modal" data-bs-target="#modal_import_color">
            <i class="ki-duotone ki-exit-down fs-3"><span class="path1"></span><span class="path2"></span></i> Import CSV
        </button>
    </div>
@endsection

@section('content')
<div class="row g-5 g-xl-10">
    <!-- Form Tambah/Edit Warna -->
    <div class="col-lg-4">
        <div class="card card-flush h-lg-100">
            <div class="card-header pt-7">
                <div class="card-title">
                    <span class="card-icon"><i class="ki-duotone ki-color-swatch fs-1 text-primary"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span><span class="path6"></span><span class="path7"></span><span class="path8"></span><span class="path9"></span><span class="path10"></span><span class="path11"></span><span class="path12"></span><span class="path13"></span><span class="path14"></span><span class="path15"></span><span class="path16"></span><span class="path17"></span><span class="path18"></span><span class="path19"></span><span class="path20"></span><span class="path21"></span></i></span>
                    <h2 class="fw-bold" id="form_title">Tambah Warna</h2>
                </div>
            </div>
            <div class="card-body pt-5">
                <form id="form_add_color" action="#" method="POST" class="form d-flex flex-column">
                    @csrf
                    <input type="hidden" name="id" id="color_id">
                    <div class="fv-row mb-7">
                        <label class="fs-6 fw-semibold form-label mt-3"><span class="required">Nama Warna</span></label>
                        <input type="text" class="form-control form-control-solid" name="name" id="color_name" placeholder="Contoh: Merah Maroon" required />
                    </div>
                    <div class="fv-row mb-7">
                        <label class="fs-6 fw-semibold form-label mt-3"><span>Kode Hex</span></label>
                        <div class="d-flex gap-3">
                            <input type="text" class="form-control form-control-solid" name="hex_code" id="color_hex" placeholder="#FFFFFF" />
                            <input type="color" class="form-control form-control-color w-100px h-45px" id="color_picker" value="#ffffff" title="Pilih warna">
                        </div>
                    </div>
                    <div class="separator mb-6"></div>
                    <div class="d-flex justify-content-end">
                        <button type="button" id="btn_cancel_edit" class="btn btn-light me-3 d-none">Batal</button>
                        <button type="submit" id="btn_save_color" class="btn btn-primary w-100">
                            <span class="indicator-label">Simpan Warna</span>
                            <span class="indicator-progress">Mohon tunggu... <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Daftar Warna -->
    <div class="col-lg-8">
        <div class="card card-flush h-lg-100">
            <div class="card-header pt-7">
                <div class="card-title">
                    <h2 class="fw-bold">Daftar Warna</h2>
                </div>
                <div class="card-toolbar">
                    <div class="d-flex align-items-center position-relative my-1">
                        <i class="ki-duotone ki-magnifier fs-3 position-absolute ms-4"><span class="path1"></span><span class="path2"></span></i>
                        <input type="text" data-kt-color-table-filter="search" class="form-control form-control-solid w-250px ps-12" placeholder="Cari Warna..." />
                    </div>
                </div>
            </div>
            <div class="card-body pt-5">
                <div class="table-responsive">
                    <table class="table align-middle table-row-dashed fs-6 gy-3" id="kt_color_table">
                        <thead>
                            <tr class="text-start text-gray-500 fw-bold fs-7 text-uppercase gs-0">
                                <th class="w-10px pe-2">
                                    <div class="form-check form-check-sm form-check-custom form-check-solid me-3">
                                        <input class="form-check-input" type="checkbox" data-kt-check="true" data-kt-check-target="#kt_color_table .form-check-input" value="1" />
                                    </div>
                                </th>
                                <th class="min-w-200px">Nama Warna</th>
                                <th class="min-w-100px">Preview</th>
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

<!-- Modal: Import CSV -->
<div class="modal fade" id="modal_import_color" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-650px">
        <div class="modal-content">
            <form class="form" action="#" id="form_import_color" enctype="multipart/form-data">
                <div class="modal-header">
                    <h2 class="fw-bold">Import Warna</h2>
                    <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
                        <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
                    </div>
                </div>
                <div class="modal-body py-10 px-lg-17">
                    <div class="notice d-flex bg-light-primary rounded border-primary border border-dashed mb-9 p-6">
                        <i class="ki-duotone ki-information fs-2tx text-primary me-4"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>
                        <div class="d-flex flex-stack flex-grow-1">
                            <div class="fw-semibold">
                                <h4 class="text-gray-900 fw-bold">Instruksi Import</h4>
                                <div class="fs-6 text-gray-700">Gunakan file CSV dengan format yang benar. Anda dapat mengunduh template di bawah ini.
                                <br><a href="{{ route('master.color.template') }}" class="fw-bold">Unduh Template CSV</a></div>
                            </div>
                        </div>
                    </div>
                    <div class="fv-row mb-7">
                        <label class="required fs-6 fw-semibold mb-2">Pilih File CSV</label>
                        <input type="file" class="form-control form-control-solid" name="file" accept=".csv" required />
                    </div>
                </div>
                <div class="modal-footer flex-center">
                    <button type="reset" class="btn btn-light me-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" id="btn_import_submit" class="btn btn-primary">
                        <span class="indicator-label">Upload & Validasi</span>
                        <span class="indicator-progress">Mohon tunggu... <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal: Conflict Resolution -->
<div class="modal fade" id="modal_conflict_resolution" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered mw-800px">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="fw-bold">Konfirmasi Data Ganda</h2>
            </div>
            <div class="modal-body py-10 px-lg-17">
                <p class="fs-6 text-gray-700 mb-5">Beberapa warna yang Anda import sudah ada di database namun memiliki kode hex yang berbeda. Silakan pilih aksi untuk setiap data:</p>
                <div class="table-responsive">
                    <table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4">
                        <thead>
                            <tr class="fw-bold text-muted">
                                <th>Nama Warna</th>
                                <th>Eksisting</th>
                                <th>Imported</th>
                                <th class="text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="conflict_list">
                            <!-- Conflict rows will be added here -->
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer flex-center">
                <button type="button" id="btn_finalize_import" class="btn btn-primary">Selesaikan Import</button>
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
    <script src="{{ asset('assets/custom/js/pages/master/colors.js') }}?v={{ time() }}"></script>
@endpush
