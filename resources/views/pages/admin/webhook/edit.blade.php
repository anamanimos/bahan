@extends('layouts.app')

@section('title', 'Edit Webhook')

@section('breadcrumb')
    <li class="breadcrumb-item"><span class="bullet bg-gray-500 w-5px h-2px"></span></li>
    <li class="breadcrumb-item text-muted">Super Admin</li>
    <li class="breadcrumb-item"><span class="bullet bg-gray-500 w-5px h-2px"></span></li>
    <li class="breadcrumb-item text-muted"><a href="{{ route('admin.webhook.index') }}" class="text-muted text-hover-primary">Webhook</a></li>
    <li class="breadcrumb-item"><span class="bullet bg-gray-500 w-5px h-2px"></span></li>
    <li class="breadcrumb-item text-muted">Edit</li>
@endsection

@section('content')
<div class="card card-flush">
    <div class="card-header pt-7">
        <h3 class="card-title fw-bold text-gray-800">Edit Endpoint Webhook: {{ $webhook->name }}</h3>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.webhook.update', $webhook->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="mb-10 fv-row">
                <label class="required form-label">Nama Webhook</label>
                <input type="text" name="name" class="form-control mb-2" placeholder="Contoh: ERP Production, Slack Notification" value="{{ old('name', $webhook->name) }}" required />
            </div>

            <div class="mb-10 fv-row">
                <label class="required form-label">URL Endpoint</label>
                <input type="url" name="url" class="form-control mb-2" placeholder="https://api.your-erp.com/webhooks/stock-update" value="{{ old('url', $webhook->url) }}" required />
            </div>

            <div class="mb-10 fv-row">
                <label class="form-label">Secret Token (Optional)</label>
                <input type="text" name="secret" class="form-control mb-2" placeholder="Masukkan token jika diperlukan untuk autentikasi" value="{{ old('secret', $webhook->secret) }}" />
            </div>

            <div class="mb-10 fv-row">
                <label class="form-label">Status</label>
                <div class="form-check form-switch form-check-custom form-check-solid">
                    <input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active" {{ $webhook->is_active ? 'checked' : '' }} />
                    <label class="form-check-label fw-bold" for="is_active">Aktifkan Endpoint ini</label>
                </div>
            </div>

            <div class="mb-10 fv-row">
                <label class="form-label">Deskripsi</label>
                <textarea name="description" class="form-control mb-2" rows="3">{{ old('description', $webhook->description) }}</textarea>
            </div>

            <div class="d-flex justify-content-end">
                <a href="{{ route('admin.webhook.index') }}" class="btn btn-light me-3">Batal</a>
                <button type="submit" class="btn btn-primary">Perbarui Webhook</button>
            </div>
        </form>
    </div>
</div>
@endsection
