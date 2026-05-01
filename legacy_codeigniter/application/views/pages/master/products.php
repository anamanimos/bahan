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
            <!--begin::Filter & Sort Buttons-->
            <div class="d-flex gap-3">
                <!--begin::Filter-->
                <div>
                    <button type="button" class="btn btn-sm btn-light-primary btn-flex" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                        <i class="ki-duotone ki-filter fs-2"><span class="path1"></span><span class="path2"></span></i>
                        Filter
                    </button>
                    <!--begin::Menu-->
                    <div class="menu menu-sub menu-sub-dropdown w-250px w-md-300px" data-kt-menu="true" id="kt_menu_filter">
                        <div class="px-7 py-5">
                            <div class="fs-5 text-gray-900 fw-bold">Opsi Filter</div>
                        </div>
                        <div class="separator border-gray-200"></div>
                        <div class="px-7 py-5">
                            <!--begin::Input group-->
                            <div class="mb-5">
                                <label class="form-label fw-semibold">Kategori:</label>
                                <div>
                                    <select class="form-select form-select-solid" data-kt-select2="true" data-placeholder="Pilih Kategori" data-dropdown-parent="#kt_menu_filter" data-allow-clear="true" multiple="multiple" id="filter_categories">
                                        <option></option>
                                        <option value="Kain Rajut">Kain Rajut</option>
                                        <option value="Kain Tenun">Kain Tenun</option>
                                        <option value="Aksesoris">Aksesoris</option>
                                    </select>
                                </div>
                            </div>
                            <!--end::Input group-->
                            <!--begin::Input group-->
                            <div class="mb-5">
                                <label class="form-label fw-semibold">Warna:</label>
                                <div class="d-flex flex-wrap gap-2">
                                    <div class="form-check form-check-custom form-check-solid">
                                        <input class="form-check-input w-20px h-20px" type="checkbox" name="filter_color" value="Hitam" id="filter_color_hitam" />
                                        <label class="form-check-label" for="filter_color_hitam">Hitam</label>
                                    </div>
                                    <div class="form-check form-check-custom form-check-solid">
                                        <input class="form-check-input w-20px h-20px" type="checkbox" name="filter_color" value="Putih" id="filter_color_putih" />
                                        <label class="form-check-label" for="filter_color_putih">Putih</label>
                                    </div>
                                </div>
                            </div>
                            <!--end::Input group-->
                            <div class="d-flex justify-content-end">
                                <button type="reset" class="btn btn-sm btn-light btn-active-light-primary" data-kt-menu-dismiss="true">Reset</button>
                            </div>
                        </div>
                    </div>
                    <!--end::Menu-->
                </div>
                <!--end::Filter-->
            </div>
            <!--end::Filter & Sort Buttons-->

            <!--begin::Add product-->
            <a href="<?= base_url('master/products/create') ?>" class="btn btn-sm btn-primary">Tambah Produk</a>
            <!--end::Add product-->
        </div>
        <!--end::Card toolbar-->
    </div>
    <!--end::Card header-->

    <!--begin::Active Filters-->
    <div class="px-10 pb-5 d-flex flex-wrap gap-2 d-none" id="active-filters-container">
        <!-- Filter badges will be injected here -->
        <div class="badge badge-light-primary border border-primary border-dashed px-3 py-2 d-flex align-items-center">
            <span class="me-2">Kategori: Kain Rajut</span>
            <a href="#" class="btn btn-icon btn-sm btn-active-light-primary w-15px h-15px"><i class="ki-duotone ki-cross fs-10"></i></a>
        </div>
        <a href="#" class="text-primary fw-bold fs-7 ms-2 align-self-center">Hapus Semua</a>
    </div>
    <!--end::Active Filters-->

    <!--begin::Card body-->
    <div class="card-body pt-0">
        <!--begin::Table wrapper-->
        <div class="table-responsive">
            <!--begin::Table-->
            <table class="table align-middle table-row-dashed fs-6 gy-2 w-100" id="kt_ecommerce_products_table">
                <thead>
                    <tr class="text-start text-gray-500 fw-bold fs-7 text-uppercase gs-0">
                        <th class="w-75px pe-2">
                            <div class="form-check form-check-sm form-check-custom form-check-solid me-3">
                                <input class="form-check-input" type="checkbox" data-kt-check="true" data-kt-check-target="#kt_ecommerce_products_table .form-check-input" value="1" />
                            </div>
                        </th>
                        <th class="min-w-200px">Produk</th>
                        <th class="min-w-100px">SKU</th>
                        <th class="min-w-70px">Stok</th>
                        <th class="min-w-100px">Kategori</th>
                        <th class="min-w-100px">Warna</th>
                        <th class="text-end min-w-70px">Aksi</th>
                    </tr>
                </thead>
                <tbody class="fw-semibold text-gray-600">
                    <!-- Data will be populated by DataTables AJAX -->
                </tbody>
            </table>
            <!--end::Table-->
        </div>
        <!--end::Table wrapper-->
    </div>
    <!--end::Card body-->
</div>
<!--end::Products-->
