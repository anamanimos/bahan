@extends('layouts.app')

@section('title', 'Edit Pelanggan')

@section('breadcrumb')
    <li class="breadcrumb-item"><span class="bullet bg-gray-500 w-5px h-2px"></span></li>
    <li class="breadcrumb-item text-muted">Master Data</li>
    <li class="breadcrumb-item"><span class="bullet bg-gray-500 w-5px h-2px"></span></li>
    <li class="breadcrumb-item text-muted"><a href="{{ route('master.customer.index') }}" class="text-muted text-hover-primary">Pelanggan</a></li>
    <li class="breadcrumb-item"><span class="bullet bg-gray-500 w-5px h-2px"></span></li>
    <li class="breadcrumb-item text-muted">Edit</li>
@endsection

@section('content')
<div class="row g-5 g-xl-10">
    <div class="col-lg-12">
        <div class="card card-flush h-lg-100">
            <div class="card-header pt-7">
                <h3 class="card-title align-items-start flex-column">
                    <span class="card-label fw-bold text-gray-800">Edit Data Pelanggan: {{ $customer->name }}</span>
                    <span class="text-gray-400 mt-1 fw-semibold fs-6">Perbarui detail pelanggan di bawah ini</span>
                </h3>
            </div>
            <div class="card-body">
                <form action="{{ route('master.customer.update', $customer->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="row g-9 mb-8">
                        <div class="col-md-6 fv-row">
                            <label class="required fs-6 fw-semibold mb-2">Nama Pelanggan</label>
                            <input type="text" class="form-control form-control-solid @error('name') is-invalid @enderror" placeholder="Nama Lengkap" name="name" value="{{ old('name', $customer->name) }}" required />
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6 fv-row">
                            <label class="required fs-6 fw-semibold mb-2">Tipe Pelanggan</label>
                            <div class="d-flex align-items-center mt-3">
                                <label class="form-check form-check-custom form-check-solid me-10">
                                    <input class="form-check-input h-20px w-20px" type="radio" name="type" value="external" {{ old('type', $customer->type) == 'external' ? 'checked' : '' }} />
                                    <span class="form-check-label fw-semibold">External (Umum)</span>
                                </label>
                                <label class="form-check form-check-custom form-check-solid">
                                    <input class="form-check-input h-20px w-20px" type="radio" name="type" value="internal" {{ old('type', $customer->type) == 'internal' ? 'checked' : '' }} />
                                    <span class="form-check-label fw-semibold">Internal (Perusahaan)</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="row g-9 mb-8">
                        <div class="col-md-6 fv-row">
                            <label class="fs-6 fw-semibold mb-2">Nomor WhatsApp / HP</label>
                            <input type="text" class="form-control form-control-solid" placeholder="Contoh: 08123456789" name="phone" value="{{ old('phone', $customer->phone) }}" />
                        </div>
                        <div class="col-md-6 fv-row">
                            <label class="fs-6 fw-semibold mb-2">Alamat Email</label>
                            <input type="email" class="form-control form-control-solid @error('email') is-invalid @enderror" placeholder="email@example.com" name="email" value="{{ old('email', $customer->email) }}" />
                            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="fv-row mb-8">
                        <label class="fs-6 fw-semibold mb-2">Alamat Lengkap</label>
                        <textarea class="form-control form-control-solid" rows="3" name="address" placeholder="Alamat pengiriman / rumah">{{ old('address', $customer->address) }}</textarea>
                    </div>

                    <div class="text-end">
                        <a href="{{ route('master.customer.index') }}" class="btn btn-light me-3">Batal</a>
                        <button type="submit" class="btn btn-primary">
                            <span class="indicator-label">Perbarui Pelanggan</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
