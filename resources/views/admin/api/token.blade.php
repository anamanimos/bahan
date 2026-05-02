@extends('layouts.app')

@section('title', 'API Token Management')

@section('breadcrumb')
<li class="breadcrumb-item text-muted">Super Admin</li>
<li class="breadcrumb-item text-muted">
    <a href="{{ url('admin/api') }}" class="text-muted text-hover-primary">API</a>
</li>
<li class="breadcrumb-item text-gray-900">Tokens</li>
@endsection

@section('toolbar_actions')
<a href="{{ url('admin/api') }}" class="btn btn-sm btn-light-primary">
    <i class="ki-duotone ki-arrow-left fs-2"><span class="path1"></span><span class="path2"></span></i>
    Kembali ke Dokumentasi API
</a>
@endsection

@section('content')
<div class="row g-5 g-xl-10">
    <div class="col-xl-4">
        <!-- Create Token Form -->
        <div class="card shadow-sm mb-5 mb-xl-10">
            <div class="card-header border-0 pt-6">
                <h3 class="card-title align-items-start flex-column">
                    <span class="card-label fw-bold fs-3 mb-1">Create New Token</span>
                </h3>
            </div>
            <div class="card-body">
                <form action="{{ url('admin/api/token') }}" method="POST">
                    @csrf
                    <div class="mb-5">
                        <label class="required form-label">Token Name</label>
                        <input type="text" name="name" class="form-control form-control-solid" placeholder="e.g., Mobile App, Postman" required />
                        @error('name')
                            <div class="text-danger mt-2">{{ $message }}</div>
                        @enderror
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Generate Token</button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-xl-8">
        @if (session('plainTextToken'))
            <div class="alert alert-dismissible bg-light-success d-flex flex-column flex-sm-row p-5 mb-10">
                <i class="ki-duotone ki-shield-tick fs-2hx text-success me-4 mb-5 mb-sm-0"><span class="path1"></span><span class="path2"></span></i>
                <div class="d-flex flex-column pe-0 pe-sm-10">
                    <h4 class="fw-semibold">API Token Generated!</h4>
                    <span>Please copy your new API token. For your security, it won't be shown again.</span>
                    <div class="mt-3 bg-white p-3 border border-success rounded text-break user-select-all fw-bold text-gray-800 fs-5">
                        {{ session('plainTextToken') }}
                    </div>
                </div>
                <button type="button" class="position-absolute position-sm-relative m-2 m-sm-0 top-0 end-0 btn btn-icon ms-sm-auto" data-bs-dismiss="alert">
                    <i class="ki-duotone ki-cross fs-1 text-success"><span class="path1"></span><span class="path2"></span></i>
                </button>
            </div>
        @endif

        @if (session('success') && !session('plainTextToken'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Active Tokens -->
        <div class="card shadow-sm">
            <div class="card-header border-0 pt-6">
                <h3 class="card-title align-items-start flex-column">
                    <span class="card-label fw-bold fs-3 mb-1">Active Tokens</span>
                    <span class="text-muted mt-1 fw-semibold fs-7">{{ $tokens->count() }} active tokens</span>
                </h3>
            </div>
            <div class="card-body py-4">
                <div class="table-responsive">
                    <table class="table align-middle table-row-dashed fs-6 gy-5">
                        <thead>
                            <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                                <th class="min-w-125px">Token Name</th>
                                <th class="min-w-125px">Last Used</th>
                                <th class="min-w-125px">Created At</th>
                                <th class="text-end min-w-70px">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-600 fw-semibold">
                            @forelse ($tokens as $token)
                                <tr>
                                    <td>{{ $token->name }}</td>
                                    <td>{{ $token->last_used_at ? $token->last_used_at->diffForHumans() : 'Never' }}</td>
                                    <td>{{ $token->created_at->format('d M Y, H:i') }}</td>
                                    <td class="text-end">
                                        <form action="{{ url('admin/api/token/' . $token->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to revoke this token?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-light-danger btn-sm">Revoke</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted">No API tokens found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
