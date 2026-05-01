<div class="d-flex flex-column gap-7 gap-lg-10">
    <!--begin::PR Header-->
    <div class="card card-flush py-4">
        <div class="card-header">
            <div class="card-title">
                <h2>Informasi Pengajuan: <?= $pr_id ?></h2>
            </div>
            <div class="card-toolbar">
                <span class="badge badge-light-warning fw-bold px-4 py-3">Pending Approval</span>
            </div>
        </div>
        <div class="card-body pt-0">
            <div class="row g-5">
                <div class="col-6 col-md">
                    <div class="fs-7 text-gray-500 fw-bold text-uppercase mb-2">Tanggal Pengajuan</div>
                    <div class="fs-6 text-gray-800 fw-bold text-nowrap">01 May 2026, 10:30</div>
                </div>
                <div class="col-6 col-md border-start border-md-none border-gray-300">
                    <div class="fs-7 text-gray-500 fw-bold text-uppercase mb-2">Staff Pengaju</div>
                    <div class="fs-6 text-gray-800 fw-bold">Staff Gudang A</div>
                </div>
                <div class="col-4 col-md border-md-start border-gray-300 ps-md-8">
                    <div class="fs-7 text-gray-500 fw-bold text-uppercase mb-2">Total Estimasi</div>
                    <div class="fs-5 text-gray-800 fw-bolder" id="pr_total_est">Rp 575.000</div>
                </div>
                <div class="col-4 col-md border-start border-gray-300">
                    <div class="fs-7 text-success fw-bold text-uppercase mb-2">Disetujui</div>
                    <div class="fs-5 text-success fw-bolder" id="pr_total_approved">Rp 0</div>
                </div>
                <div class="col-4 col-md border-start border-gray-300">
                    <div class="fs-7 text-danger fw-bold text-uppercase mb-2">Ditolak</div>
                    <div class="fs-5 text-danger fw-bolder" id="pr_total_rejected">Rp 0</div>
                </div>
            </div>
        </div>
    </div>
    <!--end::PR Header-->

    <!--begin::PR Items-->
    <div class="card card-flush py-4">
        <div class="card-header">
            <div class="card-title">
                <h2>Rincian Item & Persetujuan</h2>
            </div>
        </div>
        <div class="card-body pt-0">
            <!--begin::Desktop Table-->
            <div class="table-responsive d-none d-md-block">
                <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_pr_approval_items_table">
                    <thead>
                        <tr class="text-start text-gray-500 fw-bold fs-7 text-uppercase gs-0">
                            <th class="min-w-200px">Bahan / Produk</th>
                            <th class="min-w-150px">Toko / Supplier</th>
                            <th class="text-center min-w-100px">Jumlah</th>
                            <th class="text-end min-w-125px">Est. Harga</th>
                            <th class="text-end min-w-125px">Subtotal</th>
                            <th class="text-end min-w-120px">Status</th>
                            <th class="min-w-200px">Catatan Admin</th>
                            <th class="text-end min-w-100px">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="fw-semibold text-gray-600">
                        <!-- Mock Item 1 -->
                        <tr data-kt-element="item" data-kt-value="350000">
                            <td>
                                <div class="text-gray-800 fs-6 fw-bold">Cotton Combed 30s Putih</div>
                                <div class="text-muted fs-7">Konteks: Stok Gudang</div>
                            </td>
                            <td>Toko Subur Makmur</td>
                            <td class="text-center">10 Meter</td>
                            <td class="text-end">Rp 35.000</td>
                            <td class="text-end fw-bold text-gray-800">Rp 350.000</td>
                            <td class="text-end">
                                <span class="badge badge-light-warning" data-kt-element="item-status">Pending</span>
                            </td>
                            <td>
                                <input type="text" class="form-control form-control-sm form-control-solid" placeholder="Alasan/Catatan..." />
                            </td>
                            <td class="text-end">
                                <div class="d-flex justify-content-end gap-2">
                                    <button type="button" class="btn btn-icon btn-sm btn-light-danger" data-kt-action="reject-item" title="Tolak Item">
                                        <i class="ki-duotone ki-cross fs-2"><span class="path1"></span><span class="path2"></span></i>
                                    </button>
                                    <button type="button" class="btn btn-icon btn-sm btn-light-success" data-kt-action="approve-item" title="Setujui Item">
                                        <i class="ki-duotone ki-check fs-2"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <!-- Mock Item 2 -->
                        <tr data-kt-element="item" data-kt-value="225000">
                            <td>
                                <div class="text-gray-800 fs-6 fw-bold">Toyobo Fodu Navy</div>
                                <div class="text-muted fs-7">Konteks: Order ORD-2026-001</div>
                            </td>
                            <td>CV. Tekstil Jaya</td>
                            <td class="text-center">5 Meter</td>
                            <td class="text-end">Rp 45.000</td>
                            <td class="text-end fw-bold text-gray-800">Rp 225.000</td>
                            <td class="text-end">
                                <span class="badge badge-light-warning" data-kt-element="item-status">Pending</span>
                            </td>
                            <td>
                                <input type="text" class="form-control form-control-sm form-control-solid" placeholder="Alasan/Catatan..." />
                            </td>
                            <td class="text-end">
                                <div class="d-flex justify-content-end gap-2">
                                    <button type="button" class="btn btn-icon btn-sm btn-light-danger" data-kt-action="reject-item" title="Tolak Item">
                                        <i class="ki-duotone ki-cross fs-2"><span class="path1"></span><span class="path2"></span></i>
                                    </button>
                                    <button type="button" class="btn btn-icon btn-sm btn-light-success" data-kt-action="approve-item" title="Setujui Item">
                                        <i class="ki-duotone ki-check fs-2"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <!--end::Desktop Table-->

            <!--begin::Mobile Cards (Collapsible)-->
            <div class="d-md-none d-flex flex-column gap-5">
                <!-- Mobile Item 1 -->
                <div class="card card-bordered border-gray-300 shadow-sm" data-kt-element="item" data-kt-value="350000">
                    <!--begin::Header-->
                    <div class="card-header min-h-auto py-4 px-5 collapsible cursor-pointer" data-bs-toggle="collapse" data-bs-target="#kt_pr_approval_item_0">
                        <div class="card-title m-0">
                            <div class="d-flex flex-column">
                                <div class="text-gray-800 fs-6 fw-bold">Cotton Combed 30s Putih</div>
                                <div class="text-muted fs-8">Stok Gudang • Rp 350.000</div>
                            </div>
                        </div>
                        <div class="card-toolbar">
                            <span class="badge badge-light-warning fs-9 me-2" data-kt-element="item-status">Pending</span>
                            <i class="ki-duotone ki-down fs-4 collapsible-active-rotate-180"></i>
                        </div>
                    </div>
                    <!--end::Header-->
                    <!--begin::Body-->
                    <div id="kt_pr_approval_item_0" class="collapse show">
                        <div class="card-body p-5 pt-0">
                            <div class="separator separator-dashed my-3"></div>
                            <div class="d-flex flex-stack fs-7 mb-1">
                                <span class="text-gray-500">Supplier:</span>
                                <span class="text-gray-800 fw-bold">Toko Subur Makmur</span>
                            </div>
                            <div class="d-flex flex-stack fs-7 mb-4">
                                <span class="text-gray-500">Rincian:</span>
                                <span class="text-gray-800 fw-bold">10 Meter @ Rp 35.000</span>
                            </div>
                            <div class="mb-4">
                                <label class="form-label fs-8 text-gray-500 mb-1">Catatan Admin</label>
                                <input type="text" class="form-control form-control-solid fs-7" placeholder="Alasan/Catatan..." />
                            </div>
                            <div class="d-grid gap-2">
                                <button type="button" class="btn btn-sm btn-light-success" data-kt-action="approve-item">
                                    <i class="ki-duotone ki-check fs-4"></i> Setujui Item
                                </button>
                                <button type="button" class="btn btn-sm btn-light-danger" data-kt-action="reject-item">
                                    <i class="ki-duotone ki-cross fs-4"><span class="path1"></span><span class="path2"></span></i> Tolak Item
                                </button>
                            </div>
                        </div>
                    </div>
                    <!--end::Body-->
                </div>

                <!-- Mobile Item 2 -->
                <div class="card card-bordered border-gray-300 shadow-sm" data-kt-element="item" data-kt-value="225000">
                    <!--begin::Header-->
                    <div class="card-header min-h-auto py-4 px-5 collapsible cursor-pointer collapsed" data-bs-toggle="collapse" data-bs-target="#kt_pr_approval_item_1">
                        <div class="card-title m-0">
                            <div class="d-flex flex-column">
                                <div class="text-gray-800 fs-6 fw-bold">Toyobo Fodu Navy</div>
                                <div class="text-muted fs-8">Order ORD-2026-001 • Rp 225.000</div>
                            </div>
                        </div>
                        <div class="card-toolbar">
                            <span class="badge badge-light-warning fs-9 me-2" data-kt-element="item-status">Pending</span>
                            <i class="ki-duotone ki-down fs-4 collapsible-active-rotate-180"></i>
                        </div>
                    </div>
                    <!--end::Header-->
                    <!--begin::Body-->
                    <div id="kt_pr_approval_item_1" class="collapse">
                        <div class="card-body p-5 pt-0">
                            <div class="separator separator-dashed my-3"></div>
                            <div class="d-flex flex-stack fs-7 mb-1">
                                <span class="text-gray-500">Supplier:</span>
                                <span class="text-gray-800 fw-bold">CV. Tekstil Jaya</span>
                            </div>
                            <div class="d-flex flex-stack fs-7 mb-4">
                                <span class="text-gray-500">Rincian:</span>
                                <span class="text-gray-800 fw-bold">5 Meter @ Rp 45.000</span>
                            </div>
                            <div class="mb-4">
                                <label class="form-label fs-8 text-gray-500 mb-1">Catatan Admin</label>
                                <input type="text" class="form-control form-control-solid fs-7" placeholder="Alasan/Catatan..." />
                            </div>
                            <div class="d-grid gap-2">
                                <button type="button" class="btn btn-sm btn-light-success" data-kt-action="approve-item">
                                    <i class="ki-duotone ki-check fs-4"></i> Setujui Item
                                </button>
                                <button type="button" class="btn btn-sm btn-light-danger" data-kt-action="reject-item">
                                    <i class="ki-duotone ki-cross fs-4"><span class="path1"></span><span class="path2"></span></i> Tolak Item
                                </button>
                            </div>
                        </div>
                    </div>
                    <!--end::Body-->
                </div>
            </div>
            <!--end::Mobile Cards-->

            <div class="separator separator-dashed my-8"></div>

            <div class="row g-5">
                <div class="col-12">
                    <label class="form-label fw-bold text-gray-700">Catatan Internal Admin (Global)</label>
                    <textarea class="form-control form-control-solid" rows="3" placeholder="Tambahkan catatan untuk riwayat pengajuan ini..."></textarea>
                </div>
            </div>
        </div>
        <div class="card-footer d-flex justify-content-end py-6 px-9">
            <a href="<?= base_url('inventory/purchase-requisition') ?>" class="btn btn-light me-3">Kembali</a>
            <button type="button" class="btn btn-primary" id="kt_pr_finalize_btn">Simpan Keputusan Final</button>
        </div>
    </div>
</div>

<style>
.collapsible-active-rotate-180 {
    transition: transform 0.3s ease;
}
.collapsed .collapsible-active-rotate-180 {
    transform: rotate(-90deg);
}
</style>
