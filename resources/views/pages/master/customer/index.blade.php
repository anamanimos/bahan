@extends('layouts.app')

@section('title', 'Master Pelanggan')

@section('breadcrumb')
    <li class="breadcrumb-item"><span class="bullet bg-gray-500 w-5px h-2px"></span></li>
    <li class="breadcrumb-item text-muted">Master Data</li>
    <li class="breadcrumb-item"><span class="bullet bg-gray-500 w-5px h-2px"></span></li>
    <li class="breadcrumb-item text-muted">Pelanggan</li>
@endsection

@section('content')
<div class="card card-flush">
    <div class="card-header align-items-center py-5 gap-2 gap-md-5">
        <div class="card-title">
            <div class="d-flex align-items-center position-relative my-1">
                <i class="ki-duotone ki-magnifier fs-3 position-absolute ms-4"><span class="path1"></span><span class="path2"></span></i>
                <input type="text" data-kt-customer-table-filter="search" class="form-control form-control-solid w-250px ps-12" placeholder="Cari Pelanggan..." />
            </div>
        </div>
        <div class="card-toolbar">
            <a href="{{ route('master.customer.create') }}" class="btn btn-primary">
                <i class="ki-duotone ki-plus fs-2"></i> Tambah Pelanggan
            </a>
        </div>
    </div>
    <div class="card-body pt-0">
        <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_customers_table">
            <thead>
                <tr class="text-start text-gray-400 fw-bold fs-7 text-uppercase gs-0">
                    <th class="min-w-125px">Nama</th>
                    <th class="min-w-100px">Tipe</th>
                    <th class="min-w-125px">WhatsApp/Phone</th>
                    <th class="min-w-125px">Email</th>
                    <th class="text-end min-w-70px">Aksi</th>
                </tr>
            </thead>
            <tbody class="fw-semibold text-gray-600">
                @foreach($customers as $customer)
                <tr>
                    <td>
                        <div class="d-flex align-items-center">
                            <div class="symbol symbol-35px symbol-circle me-3">
                                <span class="symbol-label bg-light-primary text-primary fw-bold text-uppercase">
                                    {{ substr($customer->name, 0, 1) }}
                                </span>
                            </div>
                            <div class="d-flex flex-column">
                                <a href="{{ route('master.customer.edit', $customer->id) }}" class="text-gray-800 text-hover-primary mb-1">{{ $customer->name }}</a>
                                <span class="text-muted fs-7">{{ $customer->address ?? '-' }}</span>
                            </div>
                        </div>
                    </td>
                    <td>
                        @if($customer->type == 'internal')
                            <span class="badge badge-light-warning">Internal</span>
                        @else
                            <span class="badge badge-light-primary">External</span>
                        @endif
                    </td>
                    <td>{{ $customer->phone ?? '-' }}</td>
                    <td>{{ $customer->email ?? '-' }}</td>
                    <td class="text-end">
                        <a href="#" class="btn btn-sm btn-light btn-active-light-primary" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                            Aksi <i class="ki-duotone ki-down fs-5 ms-1"></i>
                        </a>
                        <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-125px py-4" data-kt-menu="true">
                            <div class="menu-item px-3">
                                <a href="{{ route('master.customer.edit', $customer->id) }}" class="menu-link px-3">Edit</a>
                            </div>
                            <div class="menu-item px-3">
                                <a href="#" class="menu-link px-3 text-danger delete-customer" data-id="{{ $customer->id }}" data-name="{{ $customer->name }}">Hapus</a>
                            </div>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        var table = $('#kt_customers_table').DataTable({
            info: false,
            order: [],
            pageLength: 10,
        });

        $('[data-kt-customer-table-filter="search"]').on('keyup', function() {
            table.search($(this).val()).draw();
        });

        $(document).on('click', '.delete-customer', function(e) {
            e.preventDefault();
            var id = $(this).data('id');
            var name = $(this).data('name');

            Swal.fire({
                title: 'Hapus Pelanggan?',
                text: "Anda akan menghapus " + name + ". Tindakan ini tidak dapat dibatalkan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal',
                customClass: {
                    confirmButton: "btn fw-bold btn-danger",
                    cancelButton: "btn fw-bold btn-active-light-primary"
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ url('master/customer/delete') }}/" + id,
                        type: 'DELETE',
                        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                        success: function(res) {
                            if (res.success) {
                                Swal.fire('Dihapus!', res.message, 'success').then(() => {
                                    location.reload();
                                });
                            }
                        }
                    });
                }
            });
        });
    });
</script>
@endpush
