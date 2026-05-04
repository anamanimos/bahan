@extends('layouts.app')

@section('title', 'Manajemen Webhook')

@section('breadcrumb')
    <li class="breadcrumb-item"><span class="bullet bg-gray-500 w-5px h-2px"></span></li>
    <li class="breadcrumb-item text-muted">Super Admin</li>
    <li class="breadcrumb-item"><span class="bullet bg-gray-500 w-5px h-2px"></span></li>
    <li class="breadcrumb-item text-muted">Webhook</li>
@endsection

@section('content')
<div class="card card-flush">
    <div class="card-header align-items-center py-5 gap-2 gap-md-5">
        <div class="card-title">
            <h3 class="card-label fw-bold text-gray-800">Daftar Webhook</h3>
        </div>
        <div class="card-toolbar gap-3">
            <a href="{{ route('admin.webhook.documentation') }}" class="btn btn-light-info">
                <i class="ki-duotone ki-book-open fs-2"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span></i> Dokumentasi
            </a>
            <a href="{{ route('admin.webhook.create') }}" class="btn btn-primary">
                <i class="ki-duotone ki-plus fs-2"></i> Tambah Webhook
            </a>
        </div>
    </div>
    <div class="card-body pt-0">
        <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_webhooks_table">
            <thead>
                <tr class="text-start text-gray-400 fw-bold fs-7 text-uppercase gs-0">
                    <th class="min-w-125px">Nama</th>
                    <th class="min-w-250px">URL Endpoint</th>
                    <th class="min-w-100px">Status</th>
                    <th class="text-end min-w-70px">Aksi</th>
                </tr>
            </thead>
            <tbody class="fw-semibold text-gray-600">
                @forelse($webhooks as $webhook)
                <tr>
                    <td>
                        <div class="d-flex flex-column">
                            <span class="text-gray-800 fw-bold">{{ $webhook->name }}</span>
                            <span class="text-muted fs-7">{{ Str::limit($webhook->description, 50) }}</span>
                        </div>
                    </td>
                    <td>
                        <code class="text-primary">{{ $webhook->url }}</code>
                    </td>
                    <td>
                        @if($webhook->is_active)
                            <span class="badge badge-light-success">Aktif</span>
                        @else
                            <span class="badge badge-light-danger">Non-Aktif</span>
                        @endif
                    </td>
                    <td class="text-end">
                        <a href="{{ route('admin.webhook.edit', $webhook->id) }}" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1">
                            <i class="ki-duotone ki-pencil fs-2"><span class="path1"></span><span class="path2"></span></i>
                        </a>
                        <button class="btn btn-icon btn-bg-light btn-active-color-danger btn-sm delete-webhook" data-id="{{ $webhook->id }}" data-name="{{ $webhook->name }}">
                            <i class="ki-duotone ki-trash fs-2"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span></i>
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="text-center py-10 text-muted">Belum ada webhook yang terdaftar</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $(document).on('click', '.delete-webhook', function(e) {
            e.preventDefault();
            var id = $(this).data('id');
            var name = $(this).data('name');

            Swal.fire({
                title: 'Hapus Webhook?',
                text: "Anda akan menghapus endpoint " + name,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ url('admin/webhook/delete') }}/" + id,
                        type: 'DELETE',
                        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                        success: function(res) {
                            if (res.success) {
                                location.reload();
                            }
                        }
                    });
                }
            });
        });
    });
</script>
@endpush
