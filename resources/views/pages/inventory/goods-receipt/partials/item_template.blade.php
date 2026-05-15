                                <!-- Header: Click to collapse -->
                                <div class="p-3 bg-light-primary bg-opacity-10 d-flex justify-content-between align-items-center rounded-top border-bottom border-gray-200 cursor-pointer collapsible" 
                                     data-bs-toggle="collapse" data-kt-element="collapse-trigger">
                                    <div class="d-flex align-items-center">
                                        <i class="ki-duotone ki-down fs-4 me-2 collapsible-active-rotate-180"></i>
                                        <span class="fw-bold fs-7 text-gray-800" data-kt-element="item-source">Item Baru</span>
                                    </div>
                                    <button type="button" class="btn btn-icon btn-sm btn-active-light-danger h-25px w-25px" data-kt-element="remove-item">
                                        <i class="ki-duotone ki-cross fs-3"><span class="path1"></span><span class="path2"></span></i>
                                    </button>
                                </div>
                                
                                <!-- Hidden Inputs -->
                                <input type="hidden" data-kt-element="input-product-id" value="" />
                                <input type="hidden" data-kt-element="input-order-reference" value="" />
                                <input type="hidden" data-kt-element="input-pr-item-id" value="" />
                                <input type="hidden" data-kt-element="input-unit" value="" />

                                <div class="collapse show" data-kt-element="collapse-content">
                                    <div class="p-5">
                                        <div class="row g-5 align-items-start">
                                            <!-- Col Barang -->
                                            <div class="col-12 col-md-3" data-kt-element="product-col">
                                                <label class="form-label fw-bold fs-8 text-gray-700 mb-1">Nama Barang</label>
                                                <div class="d-flex flex-column" data-kt-element="product-display">
                                                    <div class="fw-bold text-gray-800 fs-7 lh-1 mb-1" data-kt-element="item-name">Cotton Combed 30s Putih</div>
                                                    <div class="text-muted fs-9">Ref: <span data-kt-element="order-ref">-</span></div>
                                                </div>
                                                <div class="d-none" data-kt-element="product-select-container">
                                                    <select class="form-select form-select-solid mb-2" data-placeholder="Cari Bahan..." data-kt-element="product-select">
                                                        <option></option>
                                                    </select>
                                                    <div class="row g-2">
                                                        <div class="col-4">
                                                            <select class="form-select form-select-solid fs-9 px-3" data-kt-element="context-type">
                                                                <option value="Stok">Stok</option>
                                                                <option value="Order">Order</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-8 d-none" data-kt-element="order-container">
                                                            <select class="form-select form-select-solid fs-9" data-placeholder="Pilih Order..." data-kt-element="order-select" data-ajax-url="{{ route('inventory.ajax.purchase-requisition.search-orders') }}">
                                                                <option></option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <!-- Col Qty -->
                                            <div class="col-6 col-md-2" data-kt-element="qty-purchase-requisition-container">
                                                <label class="form-label fw-bold fs-8 text-gray-700 mb-1 text-center d-block">Sisa PR</label>
                                                <div class="fs-7 fw-bold text-gray-600 text-center"><span data-kt-element="qty-purchase-requisition">-</span> <span class="fs-9" data-kt-element="unit">-</span></div>
                                            </div>
                                            
                                            <div class="col-6 col-md-1" data-kt-element="qty-receive-col">
                                                <label class="form-label fw-bold fs-8 text-primary mb-1">Jml</label>
                                                <input type="number" class="form-control form-control-solid px-3" placeholder="0.00" step="0.01" data-kt-element="input-quantity" />
                                            </div>

                                            <div class="col-6 col-md-1 d-none" data-kt-element="unit-col">
                                                <label class="form-label fw-bold fs-8 text-gray-700 mb-1">Satuan</label>
                                                <select class="form-select form-select-solid px-2 fs-9" data-kt-element="unit-select">
                                                    @foreach($units as $unit)
                                                        <option value="{{ $unit->symbol }}">{{ $unit->name }} ({{ $unit->symbol }})</option>
                                                    @endforeach
                                                    <option value="ADD_NEW_UNIT" class="fw-bold text-primary">-- + Baru --</option>
                                                </select>
                                            </div>

                                            <!-- Col Price -->
                                            <div class="col-6 col-md-2" data-kt-element="price-col">
                                                <label class="form-label fw-bold fs-8 text-success mb-1">Harga Satuan</label>
                                                <div class="input-group input-group-solid">
                                                    <span class="input-group-text fs-9 px-2">Rp</span>
                                                    <input type="number" class="form-control form-control-solid ps-2" placeholder="0" data-kt-element="input-price" />
                                                </div>
                                            </div>

                                            <!-- Col Notes -->
                                            <div class="col-12 col-md-4" data-kt-element="notes-col">
                                                <label class="form-label fw-bold fs-8 text-gray-700 mb-1">Catatan / Keterangan</label>
                                                <textarea class="form-control form-control-solid fs-8" rows="1" placeholder="Tulis keterangan di sini..." data-kt-element="input-notes"></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
