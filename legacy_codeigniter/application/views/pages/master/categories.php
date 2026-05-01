<div class="row g-5 g-xl-10">
    <!--begin::Col-->
    <div class="col-lg-4">
        <!--begin::Card-->
        <div class="card card-flush h-lg-100">
            <!--begin::Card header-->
            <div class="card-header pt-7">
                <!--begin::Card title-->
                <div class="card-title">
                    <span class="card-icon">
                        <i class="ki-duotone ki-category fs-1 text-primary">
                            <span class="path1"></span>
                            <span class="path2"></span>
                            <span class="path3"></span>
                            <span class="path4"></span>
                        </i>
                    </span>
                    <h2 class="fw-bold">Tambah Kategori</h2>
                </div>
                <!--end::Card title-->
            </div>
            <!--end::Card header-->
            <!--begin::Card body-->
            <div class="card-body pt-5">
                <form id="kt_ecommerce_add_category_form" class="form d-flex flex-column" action="#">
                    <!--begin::Input group-->
                    <div class="fv-row mb-7">
                        <label class="fs-6 fw-semibold form-label mt-3">
                            <span class="required">Nama Kategori</span>
                        </label>
                        <input type="text" class="form-control form-control-solid" name="category_name" value="" placeholder="Contoh: Kain Rajut" />
                    </div>
                    <!--end::Input group-->
                    <!--begin::Input group-->
                    <div class="fv-row mb-7">
                        <label class="fs-6 fw-semibold form-label mt-3">
                            <span>Induk Kategori</span>
                        </label>
                        <select class="form-select form-select-solid" name="parent_id" data-control="select2" data-placeholder="Pilih Induk (Opsional)">
                            <option></option>
                            <option value="0">Kategori Utama (Root)</option>
                            <option value="1">Kain Rajut</option>
                            <option value="2">Kain Tenun</option>
                        </select>
                        <div class="text-muted fs-7 mt-2">Biarkan kosong jika ingin membuat kategori utama.</div>
                    </div>
                    <!--end::Input group-->
                    <!--begin::Separator-->
                    <div class="separator mb-6"></div>
                    <!--end::Separator-->
                    <!--begin::Action buttons-->
                    <div class="d-flex justify-content-end">
                        <button type="reset" class="btn btn-light me-3">Batal</button>
                        <button type="submit" class="btn btn-primary" id="kt_ecommerce_add_category_submit">
                            <span class="indicator-label">Simpan</span>
                            <span class="indicator-progress">Mohon tunggu... 
                            <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                        </button>
                    </div>
                    <!--end::Action buttons-->
                </form>
            </div>
            <!--end::Card body-->
        </div>
        <!--end::Card-->
    </div>
    <!--end::Col-->
    <!--begin::Col-->
    <div class="col-lg-8">
        <!--begin::Card-->
        <div class="card card-flush h-lg-100">
            <!--begin::Card header-->
            <div class="card-header pt-7">
                <!--begin::Card title-->
                <div class="card-title">
                    <h2 class="fw-bold">Hirarki Kategori</h2>
                </div>
                <!--end::Card title-->
                <!--begin::Card toolbar-->
                <div class="card-toolbar">
                    <button class="btn btn-sm btn-light-primary" id="kt_ecommerce_category_expand_all">Expand All</button>
                    <button class="btn btn-sm btn-light-primary ms-2" id="kt_ecommerce_category_collapse_all">Collapse All</button>
                </div>
                <!--end::Card toolbar-->
            </div>
            <!--end::Card header-->
            <!--begin::Card body-->
            <div class="card-body pt-5">
                <div class="text-muted fs-7 mb-5">Gunakan drag & drop untuk mengatur urutan dan hirarki kategori secara langsung.</div>
                <div id="kt_ecommerce_category_tree" class="tree-demo"></div>
            </div>
            <!--end::Card body-->
        </div>
        <!--end::Card-->
    </div>
    <!--end::Col-->
</div>
