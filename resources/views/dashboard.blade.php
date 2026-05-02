@extends('layouts.app')

@section('title', 'Dashboard')

@section('breadcrumb')
<li class="breadcrumb-item text-gray-900">Dashboard</li>
@endsection

@section('content')
<div class="row g-5 g-xl-10 mb-5 mb-xl-10">
    <div class="col-12">
        @if(session('error'))
            <div class="alert alert-danger d-flex align-items-center p-5 mb-10">
                <i class="ki-duotone ki-shield-cross fs-2hx text-danger me-4"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>
                <div class="d-flex flex-column">
                    <h4 class="mb-1 text-danger">Akses Ditolak</h4>
                    <span>{{ session('error') }}</span>
                </div>
            </div>
        @endif
        
        @if(session('success'))
            <div class="alert alert-success d-flex align-items-center p-5 mb-10">
                <i class="ki-duotone ki-shield-tick fs-2hx text-success me-4"><span class="path1"></span><span class="path2"></span></i>
                <div class="d-flex flex-column">
                    <h4 class="mb-1 text-success">Berhasil</h4>
                    <span>{{ session('success') }}</span>
                </div>
            </div>
        @endif

        <div class="card shadow-sm">
            <div class="card-body p-lg-17">
                <div class="mb-18 text-center">
                    <h3 class="fs-2hx text-gray-900 mb-5">Selamat Datang di ERP</h3>
                    <div class="fs-5 text-muted fw-semibold">
                        Akun Anda saat ini belum memiliki hak akses ke modul manapun.
                        <br>Silakan hubungi administrator untuk meminta akses.
                    </div>
                </div>

                <div class="row mt-10 justify-content-center">
                    <div class="col-md-6">
                        <div class="card bg-light border-0">
                            <div class="card-body p-8">
                                <form action="{{ url('claim-admin') }}" method="POST">
                                    @csrf
                                    <div class="mb-3">
                                        <label class="form-label text-muted fs-7">Developer Mode (Secret Claim)</label>
                                        <div class="input-group">
                                            <input type="password" name="secret_code" class="form-control form-control-solid" placeholder="Enter secret code" required>
                                            <button type="submit" class="btn btn-primary">Claim</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
