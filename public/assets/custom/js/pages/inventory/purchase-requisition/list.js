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
            language: {
                emptyTable: `
                    <div class="d-flex flex-column flex-center text-center p-10">
                        <i class="ki-duotone ki-file-added fs-5x text-muted mb-5"><span class="path1"></span><span class="path2"></span></i>
                        <div class="fw-bold fs-3 text-gray-800 mb-2">Belum ada Pengajuan Beli</div>
                        <div class="text-muted fw-semibold fs-6">Silakan buat pengajuan baru untuk mulai mencatat.</div>
                    </div>
                `
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
                        if (data === 'Submitted') color = 'warning';
                        if (data === 'Rejected') color = 'danger';
                        if (data === 'Partially Approved') color = 'info';
                        return `<span class="badge badge-light-${color} fw-bold">${data}</span>`;
                    }
                },
                {
                    targets: -1,
                    data: null,
                    orderable: false,
                    className: 'text-end',
                    render: function (data, type, row) {
                        let verifyBtn = '';
                        if (row.can_verify && row.status === 'Submitted') {
                            verifyBtn = `<div class="menu-item px-3"><a href="${hostUrl}inventory/purchase-requisition/verify/${row.id}" class="menu-link px-3 text-success">Verifikasi</a></div>`;
                        }
                        return `
                            <a href="#" class="btn btn-sm btn-light btn-active-light-primary btn-flex btn-center" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                                Aksi <i class="ki-duotone ki-down fs-5 ms-1"></i>
                            </a>
                            <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-150px py-4" data-kt-menu="true">
                                <div class="menu-item px-3"><a href="${hostUrl}inventory/purchase-requisition/${row.id}" class="menu-link px-3">Detail</a></div>
                                ${verifyBtn}
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

    var getStatusColor = function(status) {
        let color = 'primary';
        if (status === 'Approved') color = 'success';
        if (status === 'Submitted') color = 'warning';
        if (status === 'Rejected') color = 'danger';
        if (status === 'Partially Approved') color = 'info';
        return color;
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
            let verifyBtn = '';
            if (row.can_verify && row.status === 'Submitted') {
                verifyBtn = `<a href="${hostUrl}inventory/purchase-requisition/verify/${row.id}" class="btn btn-sm btn-light-success py-1 px-3 fs-8">Verifikasi</a>`;
            }

            const card = `
                <div class="card card-flush border-bottom rounded-0">
                    <div class="card-body p-5">
                        <div class="d-flex flex-stack mb-2">
                            <span class="text-gray-800 fw-bold fs-6">${row.id}</span>
                            <span class="badge badge-light-${getStatusColor(row.status)} fw-bold">${row.status}</span>
                        </div>
                        <div class="fs-7 text-muted mb-4">${row.date} • ${row.staff_name}</div>
                        <div class="d-flex flex-stack mb-5">
                            <div class="fs-7 fw-bold text-gray-700">${row.items_count} Items</div>
                            <div class="fs-6 fw-bold text-success">Rp ${parseFloat(row.total_estimation).toLocaleString('id-ID')}</div>
                        </div>
                        <div class="d-flex gap-2">
                            <a href="${hostUrl}inventory/purchase-requisition/${row.id}" class="btn btn-sm btn-light-primary py-1 px-3 fs-8">Detail</a>
                            ${verifyBtn}
                            <a href="#" class="btn btn-sm btn-light-danger py-1 px-3 fs-8">Hapus</a>
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
