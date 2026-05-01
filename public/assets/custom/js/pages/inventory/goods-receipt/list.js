"use strict";

var TKAppInventoryGoodsReceiptList = function () {
    var table;
    var datatable;
    var mobileContainer;
    var mobilePagination;
    var flatpickrInstance;

    var initDataTable = function () {
        table = document.querySelector('#kt_goods_receipt_table');
        mobileContainer = document.querySelector('#kt_goods_receipt_mobile_container');
        mobilePagination = document.querySelector('#kt_goods_receipt_mobile_pagination');

        if (!table) return;

        datatable = $(table).DataTable({
            searchDelay: 500,
            processing: true,
            serverSide: true,
            order: [[1, 'desc']],
            stateSave: true,
            autoWidth: false,
            ajax: {
                url: hostUrl + "ajax/inventory/goods-receipt/list",
                type: "POST",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: function(d) {
                    d.filters = {
                        supplier: $('#filter_supplier').val(),
                        date_range: $('#filter_date_range').val()
                    };
                }
            },
            columns: [
                { data: 'id' },
                { data: 'id' },
                { data: 'purchase_requisition_identifier' },
                { data: 'date' },
                { data: 'supplier_name' },
                { data: 'items_count' },
                { data: null },
            ],
            columnDefs: [
                {
                    targets: 0,
                    orderable: false,
                    render: function (data) {
                        return `<div class="form-check form-check-sm form-check-custom form-check-solid">
                                    <input class="form-check-input" type="checkbox" value="${data}" />
                                </div>`;
                    }
                },
                {
                    targets: 1,
                    render: function (data) { return `<span class="text-gray-800 fw-bold">${data}</span>`; }
                },
                {
                    targets: 2,
                    render: function (data) { 
                        return data ? `<a href="${hostUrl}inventory/purchase-requisition/detail/${data}" class="text-primary fw-bold">${data}</a>` : '<span class="text-muted fs-8">-</span>'; 
                    }
                },
                {
                    targets: 5,
                    className: 'text-center'
                },
                {
                    targets: -1,
                    data: null,
                    orderable: false,
                    className: 'text-end',
                    render: function (data, type, row) {
                        return `
                            <a href="#" class="btn btn-sm btn-light btn-active-light-primary btn-flex btn-center" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                                Aksi
                                <i class="ki-duotone ki-down fs-5 ms-1"></i>
                            </a>
                            <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-150px py-4" data-kt-menu="true">
                                <div class="menu-item px-3">
                                    <a href="#" class="menu-link px-3">Detail Lengkap</a>
                                </div>
                                <div class="menu-item px-3">
                                    <a href="#" class="menu-link px-3">Cetak Label</a>
                                </div>
                                <div class="separator border-gray-200 my-2"></div>
                                <div class="menu-item px-3">
                                    <a href="#" class="menu-link px-3 text-danger">Hapus</a>
                                </div>
                            </div>
                        `;
                    },
                },
            ],
            "stateSaveParams": function (settings, data) {
                var widths = [];
                $(table).find('thead th').each(function() { widths.push($(this).outerWidth()); });
                data.columnWidths = widths;
            },
            "stateLoadParams": function (settings, data) {
                if (data.columnWidths) {
                    this.api().columns().every(function (i) {
                        if (data.columnWidths[i]) $(this.header()).css('width', data.columnWidths[i] + 'px');
                    });
                }
            }
        });

        datatable.on('draw', function () {
            KTMenu.createInstances();
            renderMobileCards();
            renderMobilePagination();
            initColumnResize();
        });

        initColumnResize();
    };

    var initColumnResize = function() {
        const ths = table.querySelectorAll('thead th:not(:last-child):not(:first-child)'); 
        ths.forEach(th => {
            const existingHandle = th.querySelector('.resize-handle');
            if (existingHandle) existingHandle.remove();
            const handle = document.createElement('div');
            handle.classList.add('resize-handle');
            th.appendChild(handle);
            th.style.position = 'relative';
            let startX, startWidth;
            handle.addEventListener('mousedown', function(e) {
                e.preventDefault();
                startX = e.pageX;
                startWidth = th.offsetWidth;
                document.addEventListener('mousemove', onMouseMove);
                document.addEventListener('mouseup', onMouseUp);
                document.body.classList.add('resizing');
                handle.classList.add('resizing');
            });
            function onMouseMove(e) {
                const width = startWidth + (e.pageX - startX);
                if (width >= 80) { $(th).css('width', width + 'px'); $(th).css('min-width', width + 'px'); }
            }
            function onMouseUp() {
                document.removeEventListener('mousemove', onMouseMove);
                document.removeEventListener('mouseup', onMouseUp);
                document.body.classList.remove('resizing');
                handle.classList.remove('resizing');
                datatable.state.save();
            }
        });
    };

    var initFlatpickr = function() {
        const element = document.querySelector('#filter_date_range');
        if (!element) return;
        flatpickrInstance = $(element).flatpickr({
            altInput: true,
            altFormat: "F j, Y",
            dateFormat: "Y-m-d",
            mode: "range",
            onChange: function() {
                datatable.ajax.reload();
                updateActiveFilters();
            }
        });
    };

    var initFilters = function() {
        $('#filter_supplier').select2({
            dropdownParent: $('#kt_menu_filter'),
            minimumResultsForSearch: -1,
            placeholder: "Pilih opsi"
        });
    };

    var renderMobileCards = function () {
        if (!mobileContainer || window.innerWidth >= 768) return;
        const data = datatable.rows({ page: 'current' }).data();
        mobileContainer.innerHTML = '';
        if (data.length === 0) {
            mobileContainer.innerHTML = '<div class="text-center py-10 text-muted fs-7">Data tidak ditemukan</div>';
            return;
        }
        data.each(function (row, index) {
            const card = `
                <div class="card card-flush border-bottom rounded-0" data-kt-element="item">
                    <div class="card-header min-h-auto py-4 px-5 collapsible cursor-pointer collapsed" data-bs-toggle="collapse" data-bs-target="#kt_goods_receipt_list_item_${index}">
                        <div class="card-title m-0">
                            <div class="d-flex flex-column">
                                <span class="text-gray-800 fw-bold fs-6">${row.id}</span>
                                <span class="text-muted fs-8">${row.date} • ${row.supplier_name}</span>
                            </div>
                        </div>
                        <div class="card-toolbar">
                            <i class="ki-duotone ki-down fs-4 collapsible-active-rotate-180"></i>
                        </div>
                    </div>
                    <div id="kt_goods_receipt_list_item_${index}" class="collapse">
                        <div class="card-body p-5 pt-0">
                            <div class="separator separator-dashed my-3"></div>
                            <div class="d-flex flex-stack fs-7 mb-1"><span class="text-gray-500">No. Purchase Requisition:</span><span class="text-primary fw-bold">${row.purchase_requisition_identifier || '-'}</span></div>
                            <div class="d-flex flex-stack fs-7 mb-4"><span class="text-gray-500">Item:</span><span class="text-gray-800 fw-bold">${row.items_count} Bahan</span></div>
                            <div class="d-flex justify-content-end">
                                <button class="btn btn-sm btn-light-primary btn-flex btn-center px-6" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                                    Aksi <i class="ki-duotone ki-down fs-5 ms-2"></i>
                                </button>
                                <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-150px py-4" data-kt-menu="true">
                                    <div class="menu-item px-3"><a href="#" class="menu-link px-3">Detail Lengkap</a></div>
                                    <div class="menu-item px-3"><a href="#" class="menu-link px-3">Cetak Label</a></div>
                                    <div class="separator border-gray-200 my-2"></div>
                                    <div class="menu-item px-3"><a href="#" class="menu-link px-3 text-danger">Hapus</a></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>`;
            mobileContainer.insertAdjacentHTML('beforeend', card);
        });
        KTMenu.createInstances();
    };

    var renderMobilePagination = function() {
        if (!mobilePagination || window.innerWidth >= 768) return;
        const info = datatable.page.info();
        if (info.pages <= 1) { mobilePagination.innerHTML = ''; return; }
        let html = `
            <div class="d-flex flex-stack flex-wrap gap-3 fs-7">
                <div class="text-gray-700">Hal ${info.page + 1} dari ${info.pages}</div>
                <div class="d-flex gap-2">
                    <button class="btn btn-sm btn-light ${info.page === 0 ? 'disabled' : ''}" onclick="TKAppInventoryGoodsReceiptList.setPage('prev')">Sebelumnya</button>
                    <button class="btn btn-sm btn-light ${info.page === info.pages - 1 ? 'disabled' : ''}" onclick="TKAppInventoryGoodsReceiptList.setPage('next')">Selanjutnya</button>
                </div>
            </div>`;
        mobilePagination.innerHTML = html;
    };

    var handleSearchDatatable = () => {
        const filterSearch = document.querySelector('[data-kt-goods-receipt-filter="search"]');
        if (!filterSearch) return;
        filterSearch.addEventListener('keyup', function (e) { datatable.search(e.target.value).draw(); });
    }

    var updateActiveFilters = function() {
        const container = $('#active-filters-container');
        container.empty();
        const selectedSupplier = ($('#filter_supplier').val() || []).filter(v => v !== '');
        const selectedDate = $('#filter_date_range').val();
        if (selectedSupplier.length === 0 && !selectedDate) { container.addClass('d-none'); return; }
        container.removeClass('d-none');
        if (selectedDate) {
            container.append(`
                <div class="badge badge-light-danger border border-danger border-dashed px-3 py-2 d-flex align-items-center">
                    <span class="me-2">Tanggal: ${selectedDate}</span>
                    <a href="#" class="btn btn-icon btn-sm btn-active-light-danger w-20px h-20px remove-filter-date"><i class="ki-duotone ki-cross fs-3"><span class="path1"></span><span class="path2"></span></i></a>
                </div>`);
        }
        selectedSupplier.forEach(v => {
            container.append(`
                <div class="badge badge-light-primary border border-primary border-dashed px-3 py-2 d-flex align-items-center">
                    <span class="me-2">Supplier: ${v}</span>
                    <a href="#" class="btn btn-icon btn-sm btn-active-light-primary w-20px h-20px remove-filter-supplier" data-value="${v}"><i class="ki-duotone ki-cross fs-3"><span class="path1"></span><span class="path2"></span></i></a>
                </div>`);
        });
        container.append(`<a href="#" class="text-primary fw-bold fs-7 ms-2 align-self-center" id="clear-all-filters">Hapus Semua</a>`);
        $('.remove-filter-date').on('click', function(e) { e.preventDefault(); flatpickrInstance.clear(); });
        $('.remove-filter-supplier').on('click', function(e) { e.preventDefault(); const val = $(this).data('value'); const current = $('#filter_supplier').val(); $('#filter_supplier').val(current.filter(item => item !== val)).trigger('change'); });
        $('#clear-all-filters').on('click', function(e) { e.preventDefault(); flatpickrInstance.clear(); $('#filter_supplier').val(null).trigger('change'); });
    };

    var handleFilterDatatable = function() {
        $('#filter_supplier').on('change', function() { datatable.ajax.reload(); updateActiveFilters(); });
        const resetButton = document.querySelector('[data-kt-menu-dismiss="true"][type="reset"]');
        if (resetButton) {
            resetButton.addEventListener('click', function(e) {
                e.preventDefault();
                flatpickrInstance.clear();
                $('#filter_supplier').val(null).trigger('change');
            });
        }
    };

    return {
        init: function () {
            KTMenu.createInstances();
            initDataTable();
            initFlatpickr();
            initFilters();
            handleSearchDatatable();
            handleFilterDatatable();
            updateActiveFilters();
            window.addEventListener('resize', function() { renderMobileCards(); renderMobilePagination(); KTMenu.createInstances(); });
        },
        setPage: function(dir) { if (dir === 'next') datatable.page('next').draw('page'); else datatable.page('previous').draw('page'); }
    };
}();

KTUtil.onDOMContentLoaded(function () { TKAppInventoryGoodsReceiptList.init(); });
