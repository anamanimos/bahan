<div class="card card-flush">
    <div class="card-header pt-7">
        <div class="card-title">
            <span class="card-icon">
                <i class="ki-duotone ki-delivery-2 fs-1 text-primary"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span><span class="path6"></span><span class="path7"></span><span class="path8"></span><span class="path9"></span></i>
            </span>
            <h2 class="fw-bold">Input Nota / Goods Receipt (GR)</h2>
        </div>
    </div>
    <div class="card-body">
        <form id="kt_gr_form" class="form">
            <div class="row mb-8">
                <div class="col-md-4">
                    <label class="fs-6 fw-semibold form-label mt-3">
                        <span class="required">Tanggal Nota</span>
                    </label>
                    <input type="date" class="form-control form-control-solid" name="gr_date" value="<?= date('Y-m-d') ?>" />
                </div>
                <div class="col-md-4">
                    <label class="fs-6 fw-semibold form-label mt-3">
                        <span class="required">Nama Toko / Supplier</span>
                    </label>
                    <input type="text" class="form-control form-control-solid" name="supplier_name" placeholder="Input nama toko..." />
                    <div class="text-muted fs-7 mt-2">Nama toko baru otomatis terdaftar.</div>
                </div>
                <div class="col-md-4">
                    <label class="fs-6 fw-semibold form-label mt-3">
                        <span>Nomor Nota Fisik</span>
                    </label>
                    <input type="text" class="form-control form-control-solid" name="nota_number" placeholder="Contoh: INV/001/X" />
                </div>
            </div>

            <div class="separator separator-dashed mb-8"></div>

            <div class="table-responsive mb-10">
                <table class="table g-5 gs-0 mb-0 fw-bold text-gray-700" id="kt_gr_items_table">
                    <thead>
                        <tr class="border-bottom fs-7 fw-bold text-gray-700 text-uppercase">
                            <th class="min-w-300px">Bahan / Produk</th>
                            <th class="min-w-100px">Qty Beli</th>
                            <th class="min-w-150px">Harga Satuan</th>
                            <th class="min-w-150px">Total Harga</th>
                            <th class="min-w-100px text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="border-bottom border-bottom-dashed">
                            <td>
                                <select class="form-select form-select-solid" data-control="select2" data-placeholder="Pilih Produk">
                                    <option></option>
                                    <option value="1">Cotton Combed 30s - Putih</option>
                                    <option value="2">Toyobo Fodu - Navy</option>
                                </select>
                            </td>
                            <td>
                                <input type="number" class="form-control form-control-solid" placeholder="0" />
                            </td>
                            <td>
                                <input type="text" class="form-control form-control-solid text-end" placeholder="0.00" />
                            </td>
                            <td class="pt-8 text-end">Rp 0.00</td>
                            <td class="pt-5 text-end">
                                <button type="button" class="btn btn-sm btn-icon btn-active-color-primary">
                                    <i class="ki-duotone ki-trash fs-3"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span></i>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="3" class="text-end fs-4 fw-bold pt-8">Grand Total Nota</th>
                            <th class="text-end fs-4 fw-bold pt-8">Rp 0.00</th>
                            <th></th>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <div class="d-flex justify-content-end">
                <button type="reset" class="btn btn-light me-3">Batal</button>
                <button type="submit" class="btn btn-primary" id="kt_gr_submit">
                    <span class="indicator-label">Simpan Stok & FIFO Lot</span>
                    <span class="indicator-progress">Mohon tunggu... 
                    <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                </button>
            </div>
        </form>
    </div>
</div>
