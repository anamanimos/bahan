<div class="card card-flush">
    <!--begin::Card header-->
    <div class="card-header align-items-center py-5 gap-2 gap-md-5">
        <!--begin::Card title-->
        <div class="card-title">
            <!--begin::Search-->
            <div class="d-flex align-items-center position-relative my-1">
                <i class="ki-duotone ki-magnifier fs-3 position-absolute ms-4">
                    <span class="path1"></span>
                    <span class="path2"></span>
                </i>
                <input type="text" data-kt-inventory-stock-filter="search" class="form-control form-control-solid w-250px ps-12" placeholder="Cari Stok..." />
            </div>
            <!--end::Search-->
        </div>
        <!--end::Card title-->
        <!--begin::Card toolbar-->
        <div class="card-toolbar flex-row-fluid justify-content-end gap-5">
            <div class="d-flex gap-3">
                <a href="<?= base_url('inventory/stocks/pr') ?>" class="btn btn-sm btn-light-primary btn-flex">
                    <i class="ki-duotone ki-notepad fs-2"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span></i>
                    Buat PR
                </a>
                <a href="<?= base_url('inventory/stocks/gr') ?>" class="btn btn-sm btn-primary btn-flex">
                    <i class="ki-duotone ki-plus fs-2"></i>
                    Input Nota
                </a>
            </div>
        </div>
        <!--end::Card toolbar-->
    </div>
    <!--end::Card header-->
    <!--begin::Card body-->
    <div class="card-body pt-0">
        <!--begin::Table-->
        <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_inventory_stock_table">
            <thead>
                <tr class="text-start text-gray-500 fw-bold fs-7 text-uppercase gs-0">
                    <th class="min-w-100px">SKU</th>
                    <th class="min-w-200px">Produk</th>
                    <th class="min-w-100px text-start">Kategori</th>
                    <th class="min-w-100px text-start">Stok Aktual</th>
                    <th class="min-w-100px text-start">Lokasi</th>
                    <th class="min-w-100px text-start">Status</th>
                    <th class="text-end min-w-70px">Aksi</th>
                </tr>
            </thead>
            <tbody class="fw-semibold text-gray-600">
            </tbody>
        </table>
        <!--end::Table-->
    </div>
    <!--end::Card body-->
</div>
