"use strict";

var TKAppSalesList = function () {
    var table;
    var datatable;
    var mobileContainer;
    var mobilePagination;

    var initDataTable = function () {
        if (!table) return;

        datatable = $(table).DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: hostUrl + "ajax/sales/list",
                type: "POST",
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                dataSrc: function(json) {
                    renderMobileCards(json.data);
                    renderMobilePagination(json);
                    return json.data;
                }
            },
            columns: [
                { data: 'invoice_number' },
                { data: 'customer_name' },
                { data: 'sale_date' },
                { data: 'total_amount' },
                { data: 'status' },
                { data: 'uuid' },
            ],
            columnDefs: [
                {
                    targets: 0,
                    render: function (data) {
                        return `<span class="text-gray-800 fw-bold">${data}</span>`;
                    }
                },
                {
                    targets: 2,
                    className: 'd-none d-sm-table-cell'
                },
                {
                    targets: 3,
                    className: 'text-end',
                    render: function (data) {
                        return `<span class="fw-bold text-gray-900">Rp ${parseFloat(data).toLocaleString('id-ID')}</span>`;
                    }
                },
                {
                    targets: 4,
                    className: 'text-center d-none d-md-table-cell',
                    render: function (data) {
                        return `<span class="badge badge-light-success fw-bold">${data}</span>`;
                    }
                },
                {
                    targets: -1,
                    orderable: false,
                    className: 'text-end',
                    render: function (data, type, row) {
                        return `
                            <a href="#" class="btn btn-sm btn-light btn-active-light-primary" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                                Aksi <i class="ki-duotone ki-down fs-5 ms-1"></i>
                            </a>
                            <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-150px py-4" data-kt-menu="true">
                                <div class="menu-item px-3">
                                    <a href="${hostUrl}sales/${data}" class="menu-link px-3">Detail</a>
                                </div>
                                <div class="menu-item px-3">
                                    <a href="${hostUrl}sales/print/${data}" target="_blank" class="menu-link px-3">Cetak Nota</a>
                                </div>
                                <div class="separator mt-3 opacity-75"></div>
                                <div class="menu-item px-3">
                                    <a href="#" class="menu-link px-3 text-danger delete-sale" data-id="${data}" data-invoice="${row.invoice_number}">Hapus</a>
                                </div>
                            </div>`;
                    },
                }
            ],
        });

        datatable.on('draw', function () {
            KTMenu.createInstances();
            handleDelete();
        });
    };

    var renderMobileCards = function(data) {
        if (!mobileContainer) return;
        
        mobileContainer.empty();
        
        if (data.length === 0) {
            mobileContainer.append('<div class="text-center py-10 text-muted fs-7">Tidak ada data penjualan.</div>');
            return;
        }

        data.forEach(function(item) {
            var card = `
                <div class="p-5 border-bottom border-gray-200 bg-white">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div class="d-flex flex-column">
                            <a href="${hostUrl}sales/${item.uuid}" class="text-gray-900 fw-bold text-hover-primary fs-6">${item.invoice_number}</a>
                            <span class="text-muted fs-8">${item.sale_date}</span>
                        </div>
                        <span class="badge badge-light-success fw-bold fs-8">${item.status}</span>
                    </div>
                    
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex flex-column">
                            <span class="text-gray-600 fs-7 fw-semibold">${item.customer_name}</span>
                            <span class="text-gray-400 fs-9">${item.payment_method}</span>
                        </div>
                        <div class="text-end">
                            <div class="text-gray-900 fs-6 fw-bold">Rp ${parseFloat(item.total_amount).toLocaleString('id-ID')}</div>
                            <div class="mt-1">
                                <a href="${hostUrl}sales/${item.uuid}" class="btn btn-sm btn-icon btn-light-primary w-30px h-30px me-1" title="Detail">
                                    <i class="ki-duotone ki-eye fs-5"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>
                                </a>
                                <a href="${hostUrl}sales/print/${item.uuid}" target="_blank" class="btn btn-sm btn-icon btn-light-success w-30px h-30px me-1" title="Cetak">
                                    <i class="ki-duotone ki-printer fs-5"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span></i>
                                </a>
                                <button class="btn btn-sm btn-icon btn-light-danger w-30px h-30px delete-sale" data-id="${item.uuid}" data-invoice="${item.invoice_number}" title="Hapus">
                                    <i class="ki-duotone ki-trash fs-5"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            mobileContainer.append(card);
        });
    };

    var renderMobilePagination = function(json) {
        if (!mobilePagination) return;
        mobilePagination.empty();

        var pageInfo = datatable.page.info();
        var totalPages = pageInfo.pages;
        var currentPage = pageInfo.page;

        if (totalPages <= 1) return;

        var pagination = `<ul class="pagination pagination-outline">`;
        
        // Prev
        pagination += `<li class="page-item previous ${currentPage === 0 ? 'disabled' : ''}">
            <a href="#" class="page-link" data-page="${currentPage - 1}"><i class="ki-duotone ki-left fs-2"></i></a>
        </li>`;

        // Pages (limited)
        for (var i = 0; i < totalPages; i++) {
            if (i === 0 || i === totalPages - 1 || (i >= currentPage - 1 && i <= currentPage + 1)) {
                pagination += `<li class="page-item ${i === currentPage ? 'active' : ''}">
                    <a href="#" class="page-link" data-page="${i}">${i + 1}</a>
                </li>`;
            } else if (i === currentPage - 2 || i === currentPage + 2) {
                pagination += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
            }
        }

        // Next
        pagination += `<li class="page-item next ${currentPage === totalPages - 1 ? 'disabled' : ''}">
            <a href="#" class="page-link" data-page="${currentPage + 1}"><i class="ki-duotone ki-right fs-2"></i></a>
        </li>`;

        pagination += `</ul>`;
        mobilePagination.append(pagination);

        mobilePagination.find('.page-link').on('click', function(e) {
            e.preventDefault();
            var page = $(this).data('page');
            if (page !== undefined && page >= 0 && page < totalPages) {
                datatable.page(page).draw('page');
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }
        });
    };

    var handleDelete = function() {
        $('.delete-sale').on('click', function(e) {
            e.preventDefault();
            var id = $(this).data('id');
            var invoice = $(this).data('invoice');

            Swal.fire({
                title: 'Konfirmasi Hapus',
                text: `Apakah Anda yakin ingin menghapus transaksi ${invoice}? Stok barang akan otomatis dikembalikan ke Lot masing-masing.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Hapus & Kembalikan Stok',
                cancelButtonText: 'Batal',
                customClass: {
                    confirmButton: "btn btn-danger",
                    cancelButton: "btn btn-light"
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: hostUrl + "sales/" + id,
                        type: 'DELETE',
                        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                        success: function(res) {
                            if (res.success) {
                                Swal.fire('Terhapus!', res.message, 'success');
                                datatable.ajax.reload();
                                // Optional: Update stats via another AJAX or reload
                                setTimeout(() => window.location.reload(), 1500);
                            }
                        },
                        error: function(xhr) {
                            Swal.fire('Error', 'Gagal menghapus transaksi.', 'error');
                        }
                    });
                }
            });
        });
    };

    return {
        init: function () {
            table = document.querySelector('#kt_sales_table');
            mobileContainer = $('#kt_sales_mobile_container');
            mobilePagination = $('#kt_sales_mobile_pagination');

            if (!table) return;
            initDataTable();
            
            $('[data-kt-sale-filter="search"]').on('keyup', function(e) {
                datatable.search(e.target.value).draw();
            });
        }
    };
}();

KTUtil.onDOMContentLoaded(function () {
    TKAppSalesList.init();
});
