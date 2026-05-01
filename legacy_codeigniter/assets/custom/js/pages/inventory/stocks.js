"use strict";

var TKAppInventoryStocks = function () {
    var table;
    var datatable;

    var initDataTable = function () {
        table = document.querySelector('#kt_inventory_stock_table');

        if (!table) {
            return;
        }

        datatable = $(table).DataTable({
            searchDelay: 500,
            processing: true,
            serverSide: true,
            order: [[1, 'asc']],
            stateSave: true,
            ajax: {
                url: hostUrl + "ajax/inventory/stocks/list",
            },
            columns: [
                { data: 'sku' },
                { data: 'name' },
                { data: 'category' },
                { data: 'stock' },
                { data: 'location' },
                { data: 'status' },
                { data: null },
            ],
            columnDefs: [
                {
                    targets: 1,
                    render: function (data, type, row) {
                        return `<span class="text-gray-800 fw-bold">${data}</span>`;
                    }
                },
                {
                    targets: 3,
                    render: function (data, type, row) {
                        return `<span class="fw-bold">${data} ${row.unit}</span>`;
                    }
                },
                {
                    targets: 5,
                    render: function (data, type, row) {
                        let statusClass = 'badge-light-success';
                        if (data === 'Low Stock') statusClass = 'badge-light-warning';
                        if (data === 'Out of Stock') statusClass = 'badge-light-danger';
                        return `<span class="badge ${statusClass}">${data}</span>`;
                    }
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
                            <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-125px py-4" data-kt-menu="true">
                                <div class="menu-item px-3">
                                    <a href="#" class="menu-link px-3">Detail</a>
                                </div>
                                <div class="menu-item px-3">
                                    <a href="#" class="menu-link px-3">Kartu Stok</a>
                                </div>
                            </div>
                        `;
                    },
                },
            ],
        });

        datatable.on('draw', function () {
            KTMenu.createInstances();
        });
    };

    var handleSearchDatatable = () => {
        const filterSearch = document.querySelector('[data-kt-inventory-stock-filter="search"]');
        filterSearch.addEventListener('keyup', function (e) {
            datatable.search(e.target.value).draw();
        });
    }

    return {
        init: function () {
            initDataTable();
            handleSearchDatatable();
        }
    };
}();

KTUtil.onDOMContentLoaded(function () {
    TKAppInventoryStocks.init();
});
