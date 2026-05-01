@extends('layouts.app')

@section('title', 'Kelola Kategori Produk')

@section('breadcrumb')
    <li class="breadcrumb-item"><span class="bullet bg-gray-500 w-5px h-2px"></span></li>
    <li class="breadcrumb-item text-muted">Master Data</li>
    <li class="breadcrumb-item"><span class="bullet bg-gray-500 w-5px h-2px"></span></li>
    <li class="breadcrumb-item text-muted">Kategori</li>
@endsection

@section('toolbar_actions')
    <a href="{{ route('master.product.index') }}" class="btn btn-sm btn-secondary">
        <i class="ki-duotone ki-arrow-left fs-3"><span class="path1"></span><span class="path2"></span></i> Kembali
    </a>
@endsection

@section('content')
<div class="row g-5 g-xl-10">
    <div class="col-lg-4">
        <div class="card card-flush h-lg-100">
            <div class="card-header pt-7">
                <div class="card-title">
                    <span class="card-icon"><i class="ki-duotone ki-category fs-1 text-primary"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span></i></span>
                    <h2 class="fw-bold">Tambah Kategori</h2>
                </div>
            </div>
            <div class="card-body pt-5">
                <form action="{{ route('master.category.store') }}" method="POST" class="form d-flex flex-column">
                    @csrf
                    <div class="fv-row mb-7">
                        <label class="fs-6 fw-semibold form-label mt-3"><span class="required">Nama Kategori</span></label>
                        <input type="text" class="form-control form-control-solid" name="name" placeholder="Contoh: Kain Rajut" required />
                    </div>
                    <div class="fv-row mb-7">
                        <label class="fs-6 fw-semibold form-label mt-3"><span>Induk Kategori</span></label>
                        <select class="form-select form-select-solid" name="parent_id" data-control="select2" data-placeholder="Pilih Induk (Opsional)">
                            <option></option>
                            @foreach($rootCategories as $root)
                                <option value="{{ $root->id }}">{{ $root->name }}</option>
                            @endforeach
                        </select>
                        <div class="text-muted fs-7 mt-2">Biarkan kosong untuk kategori tingkat utama.</div>
                    </div>
                    <div class="separator mb-6"></div>
                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary w-100">
                            <span class="indicator-label">Simpan Kategori</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card card-flush h-lg-100">
            <div class="card-header pt-7">
                <div class="card-title">
                    <h2 class="fw-bold">Daftar Kategori</h2>
                </div>
            </div>
            <div class="card-body pt-5">
                <div class="table-responsive">
                    <table class="table align-middle table-row-dashed fs-6 gy-3">
                        <thead>
                            <tr class="text-start text-gray-500 fw-bold fs-7 text-uppercase gs-0">
                                <th class="min-w-200px">Nama Kategori</th>
                                <th class="min-w-150px">Induk</th>
                                <th class="min-w-100px">Jumlah Produk</th>
                                <th class="text-end min-w-70px">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="fw-semibold text-gray-600">
                            @foreach($categories as $category)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        @if($category->parent_id)
                                            <span class="text-muted me-2">--</span>
                                        @endif
                                        <span class="text-gray-800 text-hover-primary fw-bold">{{ $category->name }}</span>
                                    </div>
                                </td>
                                <td>{{ $category->parent->name ?? '-' }}</td>
                                <td>0</td> {{-- Future: count products --}}
                                <td class="text-end">
                                    <button class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1"><i class="ki-duotone ki-pencil fs-2"><span class="path1"></span><span class="path2"></span></i></button>
                                    <button class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm"><i class="ki-duotone ki-trash fs-2"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span></i></button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
