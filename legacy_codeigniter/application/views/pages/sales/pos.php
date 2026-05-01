<!--begin::POS App-->
<div class="d-flex flex-column flex-xl-row">
    <!--begin::Content-->
    <div class="d-flex flex-row-fluid me-xl-9 mb-10 mb-xl-0">
        <!--begin::Pos food-->
        <div class="card card-flush card-p-0 bg-transparent border-0">
            <!--begin::Body-->
            <div class="card-body">
                <!--begin::Nav-->
                <ul class="nav nav-pills d-flex justify-content-between nav-pills-custom gap-3 mb-6">
                    <!--begin::Item-->
                    <li class="nav-item mb-3 me-0">
                        <!--begin::Link-->
                        <a class="nav-link nav-link-border-solid btn btn-outline btn-flex btn-active-color-primary flex-column flex-stack pt-9 pb-7 page-bg show active" data-bs-toggle="pill" href="#kt_pos_food_content_1" style="width: 138px;height: 180px">
                            <!--begin::Icon-->
                            <div class="nav-icon mb-3">
                                <i class="ki-duotone ki-Toggle-Right fs-3x">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                            </div>
                            <!--end::Icon-->
                            <!--begin::Info-->
                            <div class="">
                                <span class="text-gray-800 fw-bold fs-2 d-block">Kain Rajut</span>
                                <span class="text-gray-500 fw-semibold fs-7">12 SKU</span>
                            </div>
                            <!--end::Info-->
                        </a>
                        <!--end::Link-->
                    </li>
                    <!--end::Item-->
                    <!--begin::Item-->
                    <li class="nav-item mb-3 me-0">
                        <!--begin::Link-->
                        <a class="nav-link nav-link-border-solid btn btn-outline btn-flex btn-active-color-primary flex-column flex-stack pt-9 pb-7 page-bg" data-bs-toggle="pill" href="#kt_pos_food_content_2" style="width: 138px;height: 180px">
                            <!--begin::Icon-->
                            <div class="nav-icon mb-3">
                                <i class="ki-duotone ki-Toggle-Left fs-3x">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                            </div>
                            <!--end::Icon-->
                            <!--begin::Info-->
                            <div class="">
                                <span class="text-gray-800 fw-bold fs-2 d-block">Kain Tenun</span>
                                <span class="text-gray-500 fw-semibold fs-7">8 SKU</span>
                            </div>
                            <!--end::Info-->
                        </a>
                        <!--end::Link-->
                    </li>
                    <!--end::Item-->
                </ul>
                <!--end::Nav-->
                <!--begin::Tab Content-->
                <div class="tab-content">
                    <!--begin::Tap pane-->
                    <div class="tab-pane fade show active" id="kt_pos_food_content_1">
                        <!--begin::Wrapper-->
                        <div class="d-flex flex-wrap d-grid gap-5 gap-xxl-9">
                            <!--begin::Card Product-->
                            <div class="card card-flush flex-row-fluid p-6 pb-5 mw-100">
                                <!--begin::Body-->
                                <div class="card-body text-center">
                                    <!--begin::Food img-->
                                    <img src="<?= base_url('assets/vendors/media/stock/ecommerce/1.png') ?>" class="rounded-3 mb-4 w-150px h-150px w-xxl-200px h-xxl-200px" alt="" />
                                    <!--end::Food img-->
                                    <!--begin::Info-->
                                    <div class="mb-2">
                                        <div class="text-gray-800 fs-3 fw-bold">Cotton Combed 30s</div>
                                        <div class="text-gray-500 fs-7 fw-bold">SKU: KNB-001</div>
                                    </div>
                                    <!--end::Info-->
                                    <!--begin::Price-->
                                    <span class="text-primary fs-2hx fw-bold">Rp 95rb</span>
                                    <!--end::Price-->
                                </div>
                                <!--end::Body-->
                            </div>
                            <!--end::Card Product-->
                        </div>
                        <!--end::Wrapper-->
                    </div>
                    <!--end::Tap pane-->
                </div>
                <!--end::Tab Content-->
            </div>
            <!--end::Body-->
        </div>
        <!--end::Pos food-->
    </div>
    <!--end::Content-->
    <!--begin::Sidebar-->
    <div class="flex-row-auto w-xl-450px">
        <!--begin::Pos order-->
        <div class="card card-flush bg-body" id="kt_pos_form">
            <!--begin::Header-->
            <div class="card-header pt-5">
                <h3 class="card-title fw-bold text-gray-800 fs-2hx">Pesanan Retail</h3>
                <div class="card-toolbar">
                    <button class="btn btn-light-primary fw-bold" id="kt_pos_details_clear_all">Clear All</button>
                </div>
            </div>
            <!--end::Header-->
            <!--begin::Body-->
            <div class="card-body pt-0">
                <!--begin::Table container-->
                <div class="table-responsive mb-8">
                    <!--begin::Table-->
                    <table class="table align-middle gs-0 gy-4 border-dashed">
                        <!--begin::Table head-->
                        <thead>
                            <tr class="fs-7 fw-bold text-gray-500 border-bottom-0">
                                <th class="p-0 pb-3 min-w-175px text-start">ITEM</th>
                                <th class="p-0 pb-3 min-w-50px text-center">QTY</th>
                                <th class="p-0 pb-3 min-w-70px text-end">TOTAL</th>
                            </tr>
                        </thead>
                        <!--end::Table head-->
                        <!--begin::Table body-->
                        <tbody>
                            <tr data-kt-pos-element="item" data-kt-pos-item-id="1">
                                <td class="pe-0">
                                    <div class="d-flex align-items-center">
                                        <img src="<?= base_url('assets/vendors/media/stock/ecommerce/1.png') ?>" class="w-50px h-50px rounded-3 me-3" alt="" />
                                        <span class="fw-bold text-gray-800 cursor-pointer text-hover-primary fs-6">Cotton Combed 30s</span>
                                    </div>
                                </td>
                                <td class="pe-0">
                                    <div class="position-relative d-flex align-items-center" data-kt-dialer="true" data-kt-dialer-min="1" data-kt-dialer-max="100" data-kt-dialer-step="1" data-kt-dialer-prefix="" data-kt-dialer-decimals="1">
                                        <button type="button" class="btn btn-icon btn-sm btn-light btn-icon-gray-500" data-kt-dialer-control="decrease">
                                            <i class="ki-duotone ki-minus fs-2"></i>
                                        </button>
                                        <input type="text" class="form-control border-0 text-center px-0 fs-3 fw-bold text-gray-800 w-30px" data-kt-dialer-control="input" placeholder="Amount" name="manage_budget" readonly="readonly" value="1.0" />
                                        <button type="button" class="btn btn-icon btn-sm btn-light btn-icon-gray-500" data-kt-dialer-control="increase">
                                            <i class="ki-duotone ki-plus fs-2"></i>
                                        </button>
                                    </div>
                                </td>
                                <td class="text-end">
                                    <span class="fw-bold text-primary fs-2">95.000</span>
                                </td>
                            </tr>
                        </tbody>
                        <!--end::Table body-->
                    </table>
                    <!--end::Table-->
                </div>
                <!--end::Table container-->
                <!--begin::Summary-->
                <div class="d-flex flex-stack bg-success rounded-3 p-6 mb-11">
                    <!--begin::Content-->
                    <div class="fs-6 fw-bold text-white">
                        <span class="d-block lh-1 mb-2">Subtotal</span>
                        <span class="d-block mb-2">Pajak (0%)</span>
                        <span class="d-block fs-2hx lh-1">Total</span>
                    </div>
                    <!--end::Content-->
                    <!--begin::Content-->
                    <div class="fs-6 fw-bold text-white text-end">
                        <span class="d-block lh-1 mb-2">Rp 95.000</span>
                        <span class="d-block mb-2">Rp 0</span>
                        <span class="d-block fs-2hx lh-1">Rp 95.000</span>
                    </div>
                    <!--end::Content-->
                </div>
                <!--end::Summary-->
                <!--begin::Payment Method-->
                <div class="m-0">
                    <h3 class="fw-bold text-gray-800 mb-5">Metode Pembayaran</h3>
                    <div class="d-flex flex-stack gap-5 mb-10">
                        <button class="btn btn-outline btn-outline-dashed btn-active-light-primary d-flex flex-stack px-6 py-3 w-100 active">
                            <i class="ki-duotone ki-bank fs-2hx"><span class="path1"></span><span class="path2"></span></i>
                            <span class="fw-bold text-gray-800 text-start">Tunai</span>
                        </button>
                        <button class="btn btn-outline btn-outline-dashed btn-active-light-primary d-flex flex-stack px-6 py-3 w-100">
                            <i class="ki-duotone ki-credit-cart fs-2hx"><span class="path1"></span><span class="path2"></span></i>
                            <span class="fw-bold text-gray-800 text-start">QRIS</span>
                        </button>
                    </div>
                    <button class="btn btn-primary fs-1 w-100 py-4">Proses Bayar</button>
                </div>
                <!--end::Payment Method-->
            </div>
            <!--end::Body-->
        </div>
        <!--end::Pos order-->
    </div>
    <!--end::Sidebar-->
</div>
<!--end::POS App-->
