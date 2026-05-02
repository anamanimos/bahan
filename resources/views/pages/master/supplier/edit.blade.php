@extends('layouts.app')

@section('title', 'Edit Supplier: ' . $supplier->name)

@section('breadcrumb')
    <li class="breadcrumb-item"><span class="bullet bg-gray-500 w-5px h-2px"></span></li>
    <li class="breadcrumb-item text-muted">Master Data</li>
    <li class="breadcrumb-item"><span class="bullet bg-gray-500 w-5px h-2px"></span></li>
    <li class="breadcrumb-item text-muted">Supplier</li>
    <li class="breadcrumb-item"><span class="bullet bg-gray-500 w-5px h-2px"></span></li>
    <li class="breadcrumb-item text-muted">Edit</li>
@endsection

@section('content')
<div class="card card-flush">
    <div class="card-header">
        <div class="card-title">
            <h2>Informasi Supplier</h2>
        </div>
    </div>
    <div class="card-body pt-0">
        <form id="kt_ecommerce_add_supplier_form" class="form" action="{{ route('master.ajax.supplier.update', $supplier->id) }}" method="POST">
            @csrf
            <div class="row g-9 mb-7">
                <div class="col-md-6 fv-row">
                    <label class="required form-label">Nama Supplier (Toko)</label>
                    <input type="text" name="name" class="form-control mb-2" placeholder="Masukkan nama supplier" value="{{ $supplier->name }}" required />
                </div>
                <div class="col-md-6 fv-row">
                    <label class="form-label">PIC / Kontak Person</label>
                    <input type="text" name="contact_person" class="form-control mb-2" placeholder="Nama orang yang bisa dihubungi" value="{{ $supplier->contact_person }}" />
                </div>
            </div>

            <div class="row g-9 mb-7">
                <div class="col-md-6 fv-row">
                    <label class="form-label">Nomor Telepon / WA</label>
                    <input type="text" name="phone_number" class="form-control mb-2" placeholder="Contoh: 08123456789" value="{{ $supplier->phone_number }}" />
                </div>
                <div class="col-md-6 fv-row">
                    <label class="form-label">Alamat Lengkap</label>
                    <textarea name="address" class="form-control mb-2" rows="3" placeholder="Alamat fisik supplier">{{ $supplier->address }}</textarea>
                </div>
            </div>

            <div class="d-flex justify-content-end mt-10">
                <a href="{{ route('master.supplier.index') }}" class="btn btn-light me-3">Batal</a>
                <button type="submit" id="kt_ecommerce_add_supplier_submit" class="btn btn-primary">
                    <span class="indicator-label">Perbarui Supplier</span>
                    <span class="indicator-progress">Mohon tunggu... <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
    <script src="{{ asset('assets/custom/js/pages/master/supplier/form.js') }}?v={{ time() }}"></script>
@endpush
