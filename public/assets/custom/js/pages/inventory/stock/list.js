"use strict";

var TKAppInventoryStockList = function () {
    var table;
    var datatable;

    var initDataTable = function () {
        datatable = new DataTable(table, {
            searchDelay: 500,
            processing: true,
            serverSide: true,
            ajax: {
                url: hostUrl + "inventory/ajax/stocks/list",
                type: "POST",
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                data: function(d) {
                    d.filters = {
                        categories: $('#filter_categories').val(),
                        stock_status: $('#filter_stock_status').val(),
                    };
                }
            },
            columns: [
                { data: 'name' },
                { data: 'category_name' },
                { data: 'lot_count' },
                { data: 'total_stock' },
                { data: 'status' },
                { data: null },
            ],
            columnDefs: [
                {
                    targets: 0,
                    render: function (data, type, row) {
                        return `<div class="d-flex align-items-center">
                            <div class="symbol symbol-45px me-5">
                                <span class="symbol-label bg-light-primary text-primary fw-bold">${data.charAt(0)}</span>
                            </div>
                            <div class="d-flex justify-content-start flex-column">
                                <span class="text-gray-900 fw-bold fs-6">${data}</span>
                                <span class="text-muted fw-semibold d-block fs-7">Ref: STK-${row.id}</span>
                            </div>
                        </div>`;
                    }
                },
                {
                    targets: 1,
                    className: 'text-center',
                    render: function (data) {
                        return `<span class="badge badge-light-secondary fw-bold text-gray-800">${data}</span>`;
                    }
                },
                {
                    targets: 2,
                    className: 'text-center',
                    render: function (data) {
                        return `<span class="badge badge-light fw-bold fs-7">${data} Lot aktif</span>`;
                    }
                },
                {
                    targets: 3,
                    className: 'text-end',
                    render: function (data, type, row) {
                        return `<span class="fw-bold text-gray-900 fs-6">${parseFloat(data).toLocaleString('id-ID')}</span>
                                <span class="text-muted fs-8">${row.base_unit}</span>`;
                    }
                },
                {
                    targets: 4,
                    className: 'text-center',
                    render: function (data) {
                        if (data === 'out') return '<span class="badge badge-light-danger fw-bold px-4 py-3">Habis</span>';
                        if (data === 'low') return '<span class="badge badge-light-warning fw-bold px-4 py-3">Menipis</span>';
                        return '<span class="badge badge-light-success fw-bold px-4 py-3">Tersedia</span>';
                    }
                },
                {
                    targets: -1,
                    orderable: false,
                    className: 'text-end',
                    render: function (data, type, row) {
                        return `<button class="btn btn-sm btn-icon btn-light-primary toggle-details" data-id="${row.id}">
                                    <i class="ki-duotone ki-down fs-2"></i>
                                </button>`;
                    }
                }
            ],
        });

        datatable.on('draw', function () {
            KTMenu.createInstances();
        });
    };

    var handleDetails = function() {
        $(table).on('click', '.toggle-details', function(e) {
            e.preventDefault();
            var tr = $(this).closest('tr');
            var row = datatable.row(tr);
            var btn = $(this);

            if (row.child.isShown()) {
                row.child.hide();
                tr.removeClass('shown');
                btn.find('i').removeClass('rotate-180');
            } else {
                row.child(formatDetails(row.data())).show();
                tr.addClass('shown');
                btn.find('i').addClass('rotate-180');
            }
        });
    };

    var formatDetails = function(d) {
        var lotsHtml = '';
        if (d.lots && d.lots.length > 0) {
            d.lots.forEach(lot => {
                lotsHtml += `<tr>
                    <td><span class="fw-bold text-gray-800">${lot.identifier}</span></td>
                    <td>${lot.created_at}</td>
                    <td class="text-end">${parseFloat(lot.initial_quantity).toLocaleString('id-ID')}</td>
                    <td class="text-end"><span class="fw-bolder text-primary">${parseFloat(lot.remaining_quantity).toLocaleString('id-ID')}</span></td>
                </tr>`;
            });
        } else {
            lotsHtml = '<tr><td colspan="4" class="text-center text-muted">Tidak ada lot aktif untuk produk ini.</td></tr>';
        }

        return `<div class="p-5 bg-light-primary bg-opacity-5 rounded">
            <h6 class="fw-bold text-gray-700 mb-3 fs-8 text-uppercase">Rincian Per Lot (FIFO)</h6>
            <div class="table-responsive">
                <table class="table table-bordered table-row-gray-300 align-middle gs-4 gy-2 bg-white">
                    <thead>
                        <tr class="fw-bold text-gray-500 fs-9 text-uppercase bg-light">
                            <th>Lot ID / No. Nota</th>
                            <th>Tgl Terima</th>
                            <th class="text-end">Jml Awal</th>
                            <th class="text-end">Sisa</th>
                        </tr>
                    </thead>
                    <tbody class="fs-7">
                        ${lotsHtml}
                    </tbody>
                </table>
            </div>
        </div>`;
    };

    var handleSearch = function() {
        $('[data-kt-stock-filter="search"]').on('keyup', function(e) {
            datatable.search(e.target.value).draw();
        });
    };

    var updateActiveFilters = function() {
        const container = $('#active-filters-container');
        if (!container.length) return;

        container.empty();
        const selectedStatus = $('#filter_stock_status').val();
        const selectedCategories = ($('#filter_categories').val() || []).filter(v => v !== '');

        if (!selectedStatus && selectedCategories.length === 0) {
            container.addClass('d-none');
            return;
        }

        container.removeClass('d-none');
        
        // Status
        if (selectedStatus) {
            const statusText = $('#filter_stock_status option:selected').text();
            container.append(`
                <div class="badge badge-light-primary border border-primary border-dashed px-3 py-2 d-flex align-items-center">
                    <span class="me-2">Status: ${statusText}</span>
                    <a href="#" class="btn btn-icon btn-sm btn-active-light-primary w-20px h-20px remove-filter-status"><i class="ki-duotone ki-cross fs-3"><span class="path1"></span><span class="path2"></span></i></a>
                </div>
            `);
        }

        // Categories
        selectedCategories.forEach(cat => {
            container.append(`
                <div class="badge badge-light-info border border-info border-dashed px-3 py-2 d-flex align-items-center">
                    <span class="me-2">Kategori: ${cat}</span>
                    <a href="#" class="btn btn-icon btn-sm btn-active-light-info w-20px h-20px remove-filter-cat" data-value="${cat}"><i class="ki-duotone ki-cross fs-3"><span class="path1"></span><span class="path2"></span></i></a>
                </div>
            `);
        });

        container.append(`<a href="#" class="text-primary fw-bold fs-7 ms-2 align-self-center" id="clear-all-filters">Hapus Semua</a>`);

        $('.remove-filter-status').on('click', function(e) {
            e.preventDefault();
            $('#filter_stock_status').val(null).trigger('change');
        });

        $('.remove-filter-cat').on('click', function(e) {
            e.preventDefault();
            const val = $(this).data('value');
            const current = $('#filter_categories').val();
            $('#filter_categories').val(current.filter(item => item !== val)).trigger('change');
        });

        $('#clear-all-filters').on('click', function(e) {
            e.preventDefault();
            $('#filter_stock_status').val(null);
            $('#filter_categories').val(null).trigger('change');
        });
    };

    var handleFilters = function() {
        $('#filter_stock_status, #filter_categories').on('change', function() {
            datatable.draw();
            updateActiveFilters();
        });

        $('#filter_reset').on('click', function() {
            $('#filter_stock_status').val(null);
            $('#filter_categories').val(null).trigger('change');
        });
    };

    return {
        init: function () {
            table = document.querySelector('#kt_stocks_table');
            if (!table) return;

            initDataTable();
            handleDetails();
            handleSearch();
            handleFilters();
            updateActiveFilters();
        }
    };
}();

KTUtil.onDOMContentLoaded(function () {
    TKAppInventoryStockList.init();
});
