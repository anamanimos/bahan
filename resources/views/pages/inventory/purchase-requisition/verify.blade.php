@extends('layouts.app')

@section('title', 'Verifikasi Pengajuan Beli - ' . $requisition->identifier)

@section('breadcrumb')
    <li class="breadcrumb-item"><span class="bullet bg-gray-500 w-5px h-2px"></span></li>
    <li class="breadcrumb-item text-muted">Inventori</li>
    <li class="breadcrumb-item"><span class="bullet bg-gray-500 w-5px h-2px"></span></li>
    <li class="breadcrumb-item text-muted"><a href="{{ route('inventory.purchase-requisition.index') }}" class="text-muted text-hover-primary">Daftar PR</a></li>
    <li class="breadcrumb-item"><span class="bullet bg-gray-500 w-5px h-2px"></span></li>
    <li class="breadcrumb-item text-muted">Verifikasi PR</li>
@endsection

@section('content')
<form id="kt_pr_verify_form" method="POST" action="{{ route('inventory.purchase-requisition.update-status', $requisition->identifier) }}">
    @csrf
    <div class="d-flex flex-column gap-7 gap-lg-10">
        <!--begin::Order summary-->
        <div class="card card-flush py-4">
            <div class="card-header">
                <div class="card-title">
                    <h2>Verifikasi Pengajuan (#{{ $requisition->identifier }})</h2>
                </div>
                <div class="card-toolbar">
                    <span class="badge badge-light-primary fw-bold fs-7">Status Saat Ini: {{ $requisition->status }}</span>
                </div>
            </div>
            <div class="card-body pt-0">
                <div class="d-flex flex-wrap gap-10 fs-6">
                    <div class="d-flex flex-column">
                        <span class="text-muted fw-bold">Tanggal Pengajuan</span>
                        <span class="fw-bold text-gray-800">{{ $requisition->created_at->translatedFormat('d F Y, H:i') }}</span>
                    </div>
                    <div class="d-flex flex-column">
                        <span class="text-muted fw-bold">Diajukan Oleh</span>
                        <span class="fw-bold text-gray-800">Admin Gudang</span>
                    </div>
                </div>
            </div>
        </div>

        <!--begin::Items Section-->
        <div class="card card-flush py-4 d-none d-md-block">
            <div class="card-header">
                <div class="card-title">
                    <h2>Daftar Item & Persetujuan</h2>
                </div>
            </div>
            <div class="card-body pt-0">
                <!--begin::Desktop Table-->
                <div class="table-responsive">
                    <table class="table align-middle table-row-dashed fs-6 gy-5">
                        <thead>
                            <tr class="text-start text-gray-400 fw-bold fs-7 text-uppercase gs-0">
                                <th class="min-w-200px">Bahan / Produk</th>
                                <th class="min-w-100px text-end">Kuantitas</th>
                                <th class="min-w-150px">Status Item</th>
                                <th class="min-w-250px">Catatan Verifikasi</th>
                            </tr>
                        </thead>
                        <tbody class="fw-semibold text-gray-800">
                            @foreach($requisition->items as $item)
                                <tr>
                                    <td>
                                        <div class="d-flex flex-column">
                                            <span class="fw-bold text-gray-800 fs-6">{{ $item->product->name }}</span>
                                            <span class="text-muted fs-8">Context: {{ $item->context }} {{ $item->erp_order_reference ? '('.$item->erp_order_reference.')' : '' }}</span>
                                        </div>
                                    </td>
                                    <td class="text-end">
                                        <span class="fw-bold">{{ number_format($item->requested_quantity, 2, ',', '.') }}</span> {{ $item->unit }}
                                    </td>
                                    <td>
                                        <select class="form-select form-select-solid form-select-sm" name="item_status[{{ $item->id }}]" data-control="select2" data-hide-search="true">
                                            <option value="Pending" {{ $item->status == 'Pending' ? 'selected' : '' }}>Pending</option>
                                            <option value="Approved" {{ $item->status == 'Approved' ? 'selected' : '' }}>Setujui</option>
                                            <option value="Rejected" {{ $item->status == 'Rejected' ? 'selected' : '' }}>Tolak</option>
                                        </select>
                                    </td>
                                    <td>
                                        <textarea class="form-control form-control-solid fs-7" name="item_notes[{{ $item->id }}]" rows="1" placeholder="Catatan untuk item ini...">{{ $item->notes }}</textarea>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <!--end::Desktop Table-->
            </div>
        </div>

        <!--begin::Mobile View (Full Width outside card)-->
        <div class="d-md-none">
            <h2 class="fw-bold mb-5 px-1">Daftar Item & Persetujuan</h2>
            @foreach($requisition->items as $index => $item)
                <div class="bg-white rounded shadow-sm mb-4 overflow-hidden border border-gray-200">
                    <!--begin::Item Header (Trigger)-->
                    <div class="p-4 cursor-pointer d-flex align-items-center justify-content-between bg-light-primary bg-opacity-10" 
                         data-bs-toggle="collapse" 
                         data-bs-target="#kt_pr_item_verify_{{ $index }}">
                        <div class="d-flex flex-column">
                            <span class="fw-bold text-gray-800 fs-6">{{ $item->product->name }}</span>
                            <span class="text-muted fs-8">{{ number_format($item->requested_quantity, 2, ',', '.') }} {{ $item->unit }}</span>
                        </div>
                        <div class="d-flex align-items-center gap-3">
                            <span class="badge badge-sm badge-light-warning fw-bold" id="status_badge_{{ $item->id }}">Menunggu</span>
                            <i class="ki-duotone ki-down fs-3 transition-all"></i>
                        </div>
                    </div>
                    
                    <!--begin::Item Content (Collapsible)-->
                    <div id="kt_pr_item_verify_{{ $index }}" class="collapse {{ $index === 0 ? 'show' : '' }}">
                        <div class="p-5 border-top border-gray-100">
                            <div class="mb-5">
                                <label class="form-label fs-8 fw-bold text-uppercase text-muted mb-2">Tindakan Verifikasi</label>
                                <div class="row g-2">
                                    <div class="col-12">
                                        <select class="form-select form-select-solid" name="item_status[{{ $item->id }}]" data-control="select2" data-hide-search="true" onchange="updateMobileBadge({{ $item->id }}, this.value)">
                                            <option value="Pending" {{ $item->status == 'Pending' ? 'selected' : '' }}>Pending</option>
                                            <option value="Approved" {{ $item->status == 'Approved' ? 'selected' : '' }}>Setujui</option>
                                            <option value="Rejected" {{ $item->status == 'Rejected' ? 'selected' : '' }}>Tolak</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            
                            <div>
                                <label class="form-label fs-8 fw-bold text-uppercase text-muted mb-2">Catatan Verifikasi</label>
                                <textarea class="form-control form-control-solid fs-7" name="item_notes[{{ $item->id }}]" rows="3" placeholder="Berikan catatan jika diperlukan...">{{ $item->notes }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        <!--end::Mobile View-->

        <div class="d-flex flex-column flex-md-row justify-content-end gap-3 mb-10 mt-5 sticky-bottom-mobile">
            <a href="{{ route('inventory.purchase-requisition.index') }}" class="btn btn-light px-10 order-2 order-md-1">Batal</a>
            <button type="submit" class="btn btn-primary px-10 order-1 order-md-2" id="kt_pr_verify_submit">
                <span class="indicator-label">Simpan Verifikasi</span>
                <span class="indicator-progress">Memproses... <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
            </button>
        </div>
    </div>
</form>
@endsection

@push('styles')
<style>
    [data-bs-toggle="collapse"][aria-expanded="true"] i {
        transform: rotate(180deg);
    }

    @media (max-width: 767.98px) {
        .sticky-bottom-mobile {
            position: sticky !important;
            bottom: 0;
            z-index: 100;
            background: #fff;
            margin-left: -1.25rem !important;
            margin-right: -1.25rem !important;
            width: calc(100% + 2.5rem) !important;
            padding: 1.25rem !important;
            box-shadow: 0 -10px 30px rgba(0,0,0,0.05) !important;
            border-top: 1px solid #eee;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    function updateMobileBadge(id, value) {
        const badge = document.getElementById('status_badge_' + id);
        if(!badge) return;
        
        badge.classList.remove('badge-light-primary', 'badge-light-success', 'badge-light-danger', 'badge-light-warning', 'badge-light-info');
        
        if(value === 'Approved') {
            badge.innerText = 'Disetujui';
            badge.classList.add('badge-light-success');
        } else if(value === 'Rejected') {
            badge.innerText = 'Ditolak';
            badge.classList.add('badge-light-danger');
        } else if(value === 'Submitted' || value === 'Pending') {
            badge.innerText = 'Menunggu';
            badge.classList.add('badge-light-warning');
        } else {
            badge.innerText = 'Review';
            badge.classList.add('badge-light-primary');
        }
    }

    $(document).ready(function() {
        // Initialize badges for existing values
        @foreach($requisition->items as $item)
            updateMobileBadge({{ $item->id }}, '{{ $item->status }}');
        @endforeach

        // Sync Desktop and Mobile inputs
        $(document).on('change', 'select[name^="item_status"]', function(e) {
            if (e.originalEvent === undefined && e.namespace !== 'select2') return; // Prevent infinite loops
            
            const name = $(this).attr('name');
            const val = $(this).val();
            const itemId = name.match(/\[(\d+)\]/)[1];
            
            // Sync other inputs with same name
            $(`select[name="${name}"]`).not(this).val(val).trigger('change');
            
            // Update mobile badge
            updateMobileBadge(itemId, val);
        });

        $(document).on('input', 'textarea[name^="item_notes"]', function() {
            const name = $(this).attr('name');
            const val = $(this).val();
            $(`textarea[name="${name}"]`).not(this).val(val);
        });

        $('#kt_pr_verify_form').on('submit', function(e) {
            e.preventDefault();
            const form = $(this);
            const submitBtn = $('#kt_pr_verify_submit');

            // Disable one set of inputs to avoid duplicate names in serialization
            // We'll keep the ones that are currently visible
            const isMobile = window.innerWidth < 768;
            if (isMobile) {
                $('.d-none.d-md-block').find('select, textarea').attr('disabled', true);
            } else {
                $('.d-md-none').find('select, textarea').attr('disabled', true);
            }

            submitBtn.attr('data-kt-indicator', 'on').attr('disabled', true);

            $.ajax({
                url: form.attr('action'),
                type: 'POST',
                data: form.serialize(),
                success: function(response) {
                    // Re-enable for next attempt if needed
                    form.find('select, textarea').attr('disabled', false);
                    
                    submitBtn.removeAttr('data-kt-indicator').attr('disabled', false);
                    if (response.success) {
                        Swal.fire({
                            text: response.message,
                            icon: "success",
                            buttonsStyling: false,
                            confirmButtonText: "Ok, mengerti!",
                            customClass: { confirmButton: "btn btn-primary" }
                        }).then(function() {
                            window.location.href = "{{ route('inventory.purchase-requisition.index') }}";
                        });
                    } else {
                        Swal.fire({ text: response.message, icon: "error", buttonsStyling: false, confirmButtonText: "Ok, mengerti!", customClass: { confirmButton: "btn btn-primary" } });
                    }
                },
                error: function() {
                    submitBtn.removeAttr('data-kt-indicator').attr('disabled', false);
                    Swal.fire({ text: "Terjadi kesalahan sistem.", icon: "error", buttonsStyling: false, confirmButtonText: "Ok, mengerti!", customClass: { confirmButton: "btn btn-primary" } });
                }
            });
        });
    });
</script>
@endpush
