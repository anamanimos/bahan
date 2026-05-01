<!--begin::Products-->
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
                <input type="text" data-kt-ecommerce-product-filter="search" class="form-control form-control-solid w-250px ps-12" placeholder="Cari Produk..." />
            </div>
            <!--end::Search-->
        </div>
        <!--end::Card title-->
        <!--begin::Card toolbar-->
        <div class="card-toolbar flex-row-fluid justify-content-end gap-5">
            <div class="w-100 mw-150px">
                <!--begin::Select2-->
                <select class="form-select form-select-solid" data-control="select2" data-hide-search="true" data-placeholder="Status" data-kt-ecommerce-product-filter="status">
                    <option></option>
                    <option value="all">Semua</option>
                    <option value="published">Aktif</option>
                    <option value="scheduled">Stok Menipis</option>
                    <option value="inactive">Non-Aktif</option>
                </select>
                <!--end::Select2-->
            </div>
            <!--begin::Add product-->
            <a href="#" class="btn btn-primary">Tambah Produk</a>
            <!--end::Add product-->
        </div>
        <!--end::Card toolbar-->
    </div>
    <!--end::Card header-->
    <!--begin::Card body-->
    <div class="card-body pt-0">
        <!--begin::Table-->
        <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_ecommerce_products_table">
            <thead>
                <tr class="text-start text-gray-500 fw-bold fs-7 text-uppercase gs-0">
                    <th class="w-10px pe-2">
                        <div class="form-check form-check-sm form-check-custom form-check-solid me-3">
                            <input class="form-check-input" type="checkbox" data-kt-check="true" data-kt-check-target="#kt_ecommerce_products_table .form-check-input" value="1" />
                        </div>
                    </th>
                    <th class="min-w-200px">Produk</th>
                    <th class="text-end min-w-100px">SKU</th>
                    <th class="text-end min-w-70px">Stok</th>
                    <th class="text-end min-w-100px">Harga Retail</th>
                    <th class="text-end min-w-100px">Status</th>
                    <th class="text-end min-w-70px">Aksi</th>
                </tr>
            </thead>
            <tbody class="fw-semibold text-gray-600">
                <!--begin::Table row-->
                <tr>
                    <td>
                        <div class="form-check form-check-sm form-check-custom form-check-solid">
                            <input class="form-check-input" type="checkbox" value="1" />
                        </div>
                    </td>
                    <td>
                        <div class="d-flex align-items-center">
                            <!--begin::Thumbnail-->
                            <a href="#" class="symbol symbol-50px">
                                <span class="symbol-label" style="background-image:url(<?= base_url('assets/vendors/media/stock/ecommerce/1.png') ?>);"></span>
                            </a>
                            <!--end::Thumbnail-->
                            <div class="ms-5">
                                <!--begin::Title-->
                                <a href="#" class="text-gray-800 text-hover-primary fs-5 fw-bold" data-kt-ecommerce-product-filter="product_name">Cotton Combed 30s</a>
                                <!--end::Title-->
                            </div>
                        </div>
                    </td>
                    <td class="text-end pe-0">
                        <span class="fw-bold">KNB-001</span>
                    </td>
                    <td class="text-end pe-0" data-order="25">
                        <span class="fw-bold ms-3">25 Roll</span>
                    </td>
                    <td class="text-end pe-0">Rp 95,000</td>
                    <td class="text-end pe-0" data-order="Published">
                        <!--begin::Badges-->
                        <div class="badge badge-light-success">Aktif</div>
                        <!--end::Badges-->
                    </td>
                    <td class="text-end">
                        <a href="#" class="btn btn-sm btn-light btn-flex btn-center btn-active-light-primary" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">Aksi 
                        <i class="ki-duotone ki-down fs-5 ms-1"></i></a>
                        <!--begin::Menu-->
                        <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-125px py-4" data-kt-menu="true">
                            <div class="menu-item px-3">
                                <a href="#" class="menu-link px-3">Edit</a>
                            </div>
                            <div class="menu-item px-3">
                                <a href="#" class="menu-link px-3" data-kt-ecommerce-product-filter="delete_row">Delete</a>
                            </div>
                        </div>
                        <!--end::Menu-->
                    </td>
                </tr>
                <!--end::Table row-->
            </tbody>
        </table>
        <!--end::Table-->
    </div>
    <!--end::Card body-->
</div>
<!--end::Products-->
