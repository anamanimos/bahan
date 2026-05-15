@extends('layouts.app')

@section('title', 'Media Sync (R2 & MinIO)')

@section('breadcrumb')
    <li class="breadcrumb-item text-muted">Admin</li>
    <li class="breadcrumb-item"><span class="bullet bg-gray-500 w-5px h-2px"></span></li>
    <li class="breadcrumb-item text-muted">Media Synchronization</li>
@endsection

@section('content')
<div class="card card-flush">
    <div class="card-header pt-7">
        <div class="card-title">
            <h2>Media Synchronization</h2>
        </div>
        <div class="card-toolbar gap-3">
            <button type="button" class="btn btn-light-danger" id="btn_delete_local_all">
                <i class="ki-duotone ki-trash fs-2"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span></i>
                Bersihkan Semua Lokal
            </button>
            <button type="button" class="btn btn-primary" id="btn_sync_all">
                <i class="ki-duotone ki-arrows-loop fs-2"><span class="path1"></span><span class="path2"></span></i>
                Sinkronkan Semua ke Cloud
            </button>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_media_sync_table">
                <thead>
                    <tr class="text-start text-gray-400 fw-bold fs-7 text-uppercase gs-0">
                        <th class="min-w-200px">File Path</th>
                        <th class="text-center">Local</th>
                        <th class="text-center">Cloud (Primary)</th>
                        <th class="text-center">MinIO (Secondary)</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody class="fw-semibold text-gray-600">
                    @foreach($mediaList as $media)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="symbol symbol-40px me-3">
                                    <img src="{{ \App\Services\MediaSyncService::url($media['path']) }}" alt="preview" class="rounded" onerror="this.src='{{ asset('assets/vendors/media/svg/files/blank-image.svg') }}'" />
                                </div>
                                <span class="text-gray-800 fs-7">{{ $media['path'] }}</span>
                            </div>
                        </td>
                        <td class="text-center">
                            @if($media['local'])
                                <span class="badge badge-light-success">Exists</span>
                            @else
                                <span class="badge badge-light-danger">Deleted (Cloud Only)</span>
                            @endif
                        </td>
                        <td class="text-center" data-kt-element="primary-status">
                            @if($media['primary'])
                                <div class="d-flex flex-column align-items-center">
                                    <span class="badge badge-light-success">Synced</span>
                                    @if($media['url_primary'])
                                        <a href="{{ $media['url_primary'] }}" target="_blank" class="text-primary fs-9 mt-1"><i class="ki-duotone ki-external-link fs-9 me-1"><span class="path1"></span><span class="path2"></span></i> Lihat di R2</a>
                                    @endif
                                </div>
                            @else
                                <span class="badge badge-light-warning">Not Synced</span>
                            @endif
                        </td>
                        <td class="text-center" data-kt-element="secondary-status">
                            @if($media['secondary'])
                                <div class="d-flex flex-column align-items-center">
                                    <span class="badge badge-light-success">Synced</span>
                                    @if($media['url_secondary'])
                                        <a href="{{ $media['url_secondary'] }}" target="_blank" class="text-primary fs-9 mt-1"><i class="ki-duotone ki-external-link fs-9 me-1"><span class="path1"></span><span class="path2"></span></i> Lihat di MinIO</a>
                                    @endif
                                </div>
                            @else
                                <span class="badge badge-light-warning">Not Synced</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <button type="button" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1 btn-sync-one" data-path="{{ $media['path'] }}" title="Sinkronkan ke Cloud">
                                <i class="ki-duotone ki-arrows-loop fs-3"><span class="path1"></span><span class="path2"></span></i>
                            </button>
                            <button type="button" class="btn btn-icon btn-bg-light btn-active-color-danger btn-sm me-1 btn-delete-local {{ !$media['local'] || !$media['primary'] ? 'disabled' : '' }}" data-path="{{ $media['path'] }}" title="Hapus dari Lokal">
                                <i class="ki-duotone ki-trash fs-3"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span></i>
                            </button>
                            <a href="{{ \App\Services\MediaSyncService::url($media['path']) }}" target="_blank" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm">
                                <i class="ki-duotone ki-eye fs-3"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Single Sync
    $('.btn-sync-one').on('click', function() {
        const btn = $(this);
        const path = btn.data('path');
        const row = btn.closest('tr');
        
        btn.addClass('btn-loading disabled');
        
        $.ajax({
            url: "{{ route('admin.media-sync.sync') }}",
            type: "POST",
            data: { path: path },
            success: function(response) {
                if (response.status === 'success') {
                    const data = response.data;
                    
                    // Update Status Badges
                    if (data.primary) {
                        let primaryHtml = `<div class="d-flex flex-column align-items-center">
                                            <span class="badge badge-light-success">Synced</span>`;
                        if (data.url_primary) {
                            primaryHtml += `<a href="${data.url_primary}" target="_blank" class="text-primary fs-9 mt-1"><i class="ki-duotone ki-external-link fs-9 me-1"><span class="path1"></span><span class="path2"></span></i> Lihat di R2</a>`;
                        }
                        primaryHtml += `</div>`;
                        row.find('[data-kt-element="primary-status"]').html(primaryHtml);
                        
                        // Enable delete local button
                        row.find('.btn-delete-local').removeClass('disabled');
                    }

                    if (data.secondary) {
                        let secondaryHtml = `<div class="d-flex flex-column align-items-center">
                                            <span class="badge badge-light-success">Synced</span>`;
                        if (data.url_secondary) {
                            secondaryHtml += `<a href="${data.url_secondary}" target="_blank" class="text-primary fs-9 mt-1"><i class="ki-duotone ki-external-link fs-9 me-1"><span class="path1"></span><span class="path2"></span></i> Lihat di MinIO</a>`;
                        }
                        secondaryHtml += `</div>`;
                        row.find('[data-kt-element="secondary-status"]').html(secondaryHtml);
                    }

                    toastr.success(response.message);
                }
            },
            error: function(xhr) {
                toastr.error(xhr.responseJSON?.message || "Terjadi kesalahan.");
            },
            complete: function() {
                btn.removeClass('btn-loading disabled');
            }
        });
    });

    // Single Delete Local
    $('.btn-delete-local').on('click', function() {
        const btn = $(this);
        const path = btn.data('path');
        const row = btn.closest('tr');

        Swal.fire({
            title: "Hapus file lokal?",
            text: "File akan tetap tersedia di Cloudflare R2.",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Ya, Hapus!",
            cancelButtonText: "Batal"
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "{{ route('admin.media-sync.delete-local') }}",
                    type: "POST",
                    data: { path: path },
                    success: function(response) {
                        row.find('td:nth-child(2)').html('<span class="badge badge-light-danger">Deleted (Cloud Only)</span>');
                        btn.addClass('disabled');
                        toastr.success(response.message);
                    },
                    error: function(xhr) {
                        toastr.error(xhr.responseJSON?.message || "Gagal menghapus file.");
                    }
                });
            }
        });
    });

    // Sync All (with SweetAlert Progress)
    $('#btn_sync_all').on('click', function() {
        const btn = $(this);
        
        Swal.fire({
            title: 'Mencari File...',
            text: 'Harap tunggu sebentar.',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        // Step 1: Get list of files
        $.ajax({
            url: "{{ route('admin.media-sync.index') }}",
            data: { json: 1 },
            type: "GET",
            success: function(response) {
                const files = response.files;
                if (!files || files.length === 0) {
                    Swal.fire('Info', 'Tidak ada file untuk disinkronkan.', 'info');
                    return;
                }

                let current = 0;
                const total = files.length;

                Swal.fire({
                    title: 'Sinkronisasi Media',
                    html: `Memproses file <b id="sync_current">0</b> dari <b>${total}</b>...<br/><br/>
                           <div class="progress h-20px w-100 mt-5">
                               <div id="sync_progress" class="progress-bar progress-bar-striped progress-bar-animated bg-primary" role="progressbar" style="width: 0%"></div>
                           </div>`,
                    allowOutsideClick: false,
                    showConfirmButton: false,
                    didOpen: () => {
                        const processNext = () => {
                            if (current >= total) {
                                Swal.fire({
                                    title: 'Selesai!',
                                    text: `${total} file telah diproses.`,
                                    icon: 'success'
                                }).then(() => window.location.reload());
                                return;
                            }

                            const path = files[current];
                            $.ajax({
                                url: "{{ route('admin.media-sync.sync') }}",
                                type: "POST",
                                data: { path: path },
                                success: function() {
                                    current++;
                                    const percent = Math.round((current / total) * 100);
                                    $('#sync_current').text(current);
                                    $('#sync_progress').css('width', percent + '%');
                                    processNext();
                                },
                                error: function() {
                                    // Continue to next even if error
                                    current++;
                                    processNext();
                                }
                            });
                        };
                        processNext();
                    }
                });
            },
            error: function() {
                Swal.fire('Error', 'Gagal mengambil daftar file.', 'error');
            }
        });
    });

    // Delete All Local
    $('#btn_delete_local_all').on('click', function() {
        Swal.fire({
            title: "Bersihkan Semua File Lokal?",
            text: "Pastikan seluruh file sudah tersinkronisasi ke Cloudflare R2 sebelum melakukan ini.",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#f1416c",
            confirmButtonText: "Ya, Bersihkan Lokal!",
            cancelButtonText: "Batal"
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Membersihkan...',
                    allowOutsideClick: false,
                    didOpen: () => { Swal.showLoading(); }
                });

                $.ajax({
                    url: "{{ route('admin.media-sync.delete-local-all') }}",
                    type: "POST",
                    success: function(response) {
                        Swal.fire('Selesai!', response.message, 'success').then(() => window.location.reload());
                    },
                    error: function() {
                        Swal.fire('Error', 'Gagal membersihkan file lokal.', 'error');
                    }
                });
            }
        });
    });
});
</script>
@endpush
