<div class="card card-flush">
    <!--begin::Card header-->
    <div class="card-header align-items-center py-5 gap-2 gap-md-5">
        <!--begin::Card title-->
        <div class="card-title">
            <div class="d-flex align-items-center position-relative my-1">
                <i class="ki-duotone ki-magnifier fs-3 position-absolute ms-4">
                    <span class="path1"></span>
                    <span class="path2"></span>
                </i>
                <input type="text" data-kt-inventory-pr-approval-filter="search" class="form-control form-control-solid w-250px ps-12" placeholder="Cari PR Pending..." />
            </div>
        </div>
        <!--end::Card title-->
    </div>
    <!--end::Card header-->
    <!--begin::Card body-->
    <div class="card-body pt-0">
        <!--begin::Table-->
        <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_inventory_pr_approval_table">
            <thead>
                <tr class="text-start text-gray-500 fw-bold fs-7 text-uppercase gs-0">
                    <th class="min-w-100px">No. PR</th>
                    <th class="min-w-150px">Tanggal</th>
                    <th class="min-w-150px">Staff Pengaju</th>
                    <th class="min-w-100px">Total Est.</th>
                    <th class="min-w-100px">Status</th>
                    <th class="text-end min-w-150px">Aksi</th>
                </tr>
            </thead>
            <tbody class="fw-semibold text-gray-600">
            </tbody>
        </table>
        <!--end::Table-->
    </div>
    <!--end::Card body-->
</div>

<!--begin::Modal - PR Details & Approval-->
<div class="modal fade" id="kt_modal_pr_approval_detail" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-800px">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="fw-bold" id="pr_detail_title">Detail Pengajuan: PR-20260501-001</h2>
                <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
                    <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
                </div>
            </div>
            <div class="modal-body py-10 px-lg-17">
                <div class="table-responsive">
                    <table class="table table-row-bordered table-row-gray-300 align-middle g-4">
                        <thead>
                            <tr class="fw-bold fs-7 text-gray-800 text-uppercase">
                                <th>Bahan / Produk</th>
                                <th>Toko</th>
                                <th class="text-center">Jumlah</th>
                                <th class="text-end">Est. Harga</th>
                                <th class="text-end">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody id="pr_detail_items">
                            <!-- Items will be loaded here via AJAX -->
                        </tbody>
                        <tfoot>
                            <tr class="fw-bold fs-6">
                                <td colspan="4" class="text-end">Total Estimasi:</td>
                                <td class="text-end text-primary" id="pr_detail_grand_total">Rp 0</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                
                <div class="mt-8">
                    <label class="form-label fw-bold text-gray-700">Catatan Admin (Opsional)</label>
                    <textarea class="form-control form-control-solid" rows="3" id="pr_approval_note" placeholder="Tambahkan catatan persetujuan atau alasan penolakan..."></textarea>
                </div>
            </div>
            <div class="modal-footer flex-center">
                <button type="button" class="btn btn-light-danger me-5" id="kt_pr_reject_btn">Tolak Pengajuan</button>
                <button type="button" class="btn btn-primary" id="kt_pr_approve_btn">Setujui & Terbitkan PR</button>
            </div>
        </div>
    </div>
</div>
<!--end::Modal-->
