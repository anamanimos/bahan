"use strict";

var TKAppInventoryPurchaseRequisitionList = function () {
    var table;
    var datatable;
    var mobileContainer;
    var mobilePagination;

    var initDataTable = function () {
        table = document.querySelector('#kt_inventory_pr_table');
        mobileContainer = document.querySelector('#kt_inventory_pr_mobile_container');
        mobilePagination = document.querySelector('#kt_inventory_pr_mobile_pagination');

        if (!table) return;

        datatable = $(table).DataTable({
            searchDelay: 500,
            processing: true,
            serverSide: true,
            order: [[1, 'desc']],
            ajax: {
                url: hostUrl + "inventory/ajax/purchase-requisition/list",
                type: "POST",
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
            },
            columns: [
                { data: 'id' },
                { data: 'id' },
                { data: 'date' },
                { data: 'staff_name' },
                { data: 'items_count' },
                { data: 'total_estimation' },
                { data: 'status' },
                { data: null },
            ],
            columnDefs: [
                {
                    targets: 0,
                    orderable: false,
                    render: function (data) {
                        return `<div class="form-check form-check-sm form-check-custom form-check-solid"><input class="form-check-input" type="checkbox" value="${data}" /></div>`;
                    }
                },
                {
                    targets: 1,
                    render: function (data) { return `<span class="text-gray-800 fw-bold">${data}</span>`; }
                },
                {
                    targets: 4,
                    className: 'text-center'
                },
                {
                    targets: 5,
                    render: function (data) { return `<span class="fw-bold text-gray-800">Rp ${parseFloat(data).toLocaleString('id-ID')}</span>`; }
                },
                {
                    targets: 6,
                    render: function (data) {
                        let color = 'primary';
                        if (data === 'Approved') color = 'success';
                        if (data === 'Pending') color = 'warning';
                        return `<span class="badge badge-light-${color} fw-bold">${data}</span>`;
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
                                Aksi <i class="ki-duotone ki-down fs-5 ms-1"></i>
                            </a>
                            <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-150px py-4" data-kt-menu="true">
                                <div class="menu-item px-3"><a href="${hostUrl}inventory/purchase-requisition/${row.id}" class="menu-link px-3">Detail</a></div>
                                <div class="menu-item px-3"><a href="#" class="menu-link px-3 text-danger">Hapus</a></div>
                            </div>`;
                    },
                },
            ],
        });

        datatable.on('draw', function () {
            KTMenu.createInstances();
            renderMobileCards();
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
                <div class="card card-flush border-bottom rounded-0">
                    <div class="card-body p-5">
                        <div class="d-flex flex-stack mb-2">
                            <span class="text-gray-800 fw-bold fs-6">${row.id}</span>
                            <span class="badge badge-light-primary fw-bold">${row.status}</span>
                        </div>
                        <div class="fs-7 text-muted mb-4">${row.date} • ${row.staff_name}</div>
                        <div class="d-flex flex-stack">
                            <div class="fs-7 fw-bold text-gray-700">${row.items_count} Items</div>
                            <div class="fs-6 fw-bold text-success">Rp ${parseFloat(row.total_estimation).toLocaleString('id-ID')}</div>
                        </div>
                    </div>
                </div>`;
            mobileContainer.insertAdjacentHTML('beforeend', card);
        });
    };

    return {
        init: function () {
            initDataTable();
            window.addEventListener('resize', renderMobileCards);
        }
    };
}();

KTUtil.onDOMContentLoaded(function () { TKAppInventoryPurchaseRequisitionList.init(); });
