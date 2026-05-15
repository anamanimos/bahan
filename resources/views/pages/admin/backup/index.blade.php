@extends('layouts.app')

@section('title', 'Database Backup')

@section('content')
<div class="card card-flush shadow-sm">
    <div class="card-header">
        <h3 class="card-title">Database Backup Management</h3>
        <div class="card-toolbar gap-3">
            <button type="button" class="btn btn-primary" id="btn_run_backup">
                <i class="ki-duotone ki-cloud-change fs-2"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>
                Backup ke Telegram Sekarang
            </button>
        </div>
    </div>
    <div class="card-body">
        <!-- Alert Info -->
        <div class="alert alert-dismissible bg-light-primary d-flex flex-column flex-sm-row p-5 mb-10">
            <i class="ki-duotone ki-information-5 fs-2hx text-primary me-4 mb-5 mb-sm-0"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>
            <div class="d-flex flex-column pe-0 pe-sm-10">
                <h4 class="fw-bold">Informasi Backup Otomatis</h4>
                <span>Sistem telah dijadwalkan untuk melakukan backup otomatis setiap hari pada jam <b>03:00 Pagi</b> dan dikirim langsung ke Telegram.</span>
            </div>
            <button type="button" class="position-absolute position-sm-relative m-2 m-sm-0 top-0 end-0 btn btn-icon ms-sm-auto" data-bs-dismiss="alert">
                <i class="ki-duotone ki-cross fs-1 text-primary"><span class="path1"></span><span class="path2"></span></i>
            </button>
        </div>

        <div class="table-responsive">
            <table class="table align-middle table-row-dashed fs-6 gy-5">
                <thead>
                    <tr class="text-start text-gray-400 fw-bold fs-7 text-uppercase gs-0">
                        <th>Nama File</th>
                        <th>Ukuran</th>
                        <th>Tanggal Dibuat</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody class="fw-semibold text-gray-600">
                    @forelse($files as $file)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <i class="ki-duotone ki-file fs-2x text-primary me-3"><span class="path1"></span><span class="path2"></span></i>
                                <span class="text-gray-800">{{ $file['name'] }}</span>
                            </div>
                        </td>
                        <td>{{ $file['size'] }}</td>
                        <td>{{ $file['date'] }}</td>
                        <td class="text-end">
                            <a href="{{ route('admin.backup.download', $file['name']) }}" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1" title="Download">
                                <i class="ki-duotone ki-download fs-3"><span class="path1"></span><span class="path2"></span></i>
                            </a>
                            <form action="{{ route('admin.backup.delete', $file['name']) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus file backup ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-icon btn-bg-light btn-active-color-danger btn-sm" title="Hapus">
                                    <i class="ki-duotone ki-trash fs-3"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center text-muted py-10">Belum ada file backup yang tersimpan di server.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('#btn_run_backup').on('click', function() {
        const btn = $(this);
        
        Swal.fire({
            title: 'Memproses Backup...',
            text: 'Harap tunggu, sistem sedang mem-backup database dan mengirimkannya ke Telegram.',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        $.ajax({
            url: "{{ route('admin.backup.run') }}",
            type: "POST",
            data: { _token: "{{ csrf_token() }}" },
            success: function(response) {
                Swal.fire({
                    title: 'Berhasil!',
                    text: response.message,
                    icon: 'success'
                }).then(() => window.location.reload());
            },
            error: function(xhr) {
                Swal.fire({
                    title: 'Gagal!',
                    text: xhr.responseJSON?.message || 'Terjadi kesalahan saat memproses backup.',
                    icon: 'error'
                });
            }
        });
    });
});
</script>
@endpush
