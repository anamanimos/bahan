"use strict";

var TKAppInventoryPRApproval = function () {
    var table;
    var datatable;

    var initDataTable = function () {
        table = document.querySelector('#kt_inventory_pr_approval_table');
        if (!table) return;

        datatable = $(table).DataTable({
            searchDelay: 500,
            processing: true,
            serverSide: true,
            ajax: {
                url: hostUrl + "ajax/inventory/stocks/pr_approval_list",
            },
            columns: [
                { data: 'id' },
                { data: 'date' },
                { data: 'staff' },
                { data: 'total_est' },
                { data: 'status' },
                { data: null },
            ],
            columnDefs: [
                {
                    targets: 0,
                    render: function (data) { return `<span class="text-gray-800 fw-bold">${data}</span>`; }
                },
                {
                    targets: 3,
                    render: function (data) { return `Rp ${parseFloat(data).toLocaleString('id-ID')}`; }
                },
                {
                    targets: 4,
                    render: function (data) {
                        return `<span class="badge badge-light-warning fw-bold">${data}</span>`;
                    }
                },
                {
                    targets: -1,
                    data: null,
                    orderable: false,
                    className: 'text-end',
                    render: function (data, type, row) {
                        return `
                            <a href="${hostUrl}inventory/purchase-requisition/approval/${row.id}" class="btn btn-sm btn-light-primary btn-active-primary me-2">
                                Review & Setujui
                            </a>
                        `;
                    },
                },
            ],
        });
    };

    var handleReviewAction = function () {
        $(table).on('click', '[data-kt-action="review"]', function () {
            const prId = $(this).data('id');
            $('#pr_detail_title').text(`Review Pengajuan: ${prId}`);
            
            // Mock loading items
            const itemsHtml = `
                <tr>
                    <td>
                        <div class="fw-bold">Cotton Combed 30s Putih</div>
                        <div class="text-muted fs-7">Konteks: Stok Gudang</div>
                    </td>
                    <td>Toko Subur Makmur</td>
                    <td class="text-center">10 Meter</td>
                    <td class="text-end">Rp 35.000</td>
                    <td class="text-end fw-bold">Rp 350.000</td>
                </tr>
                <tr>
                    <td>
                        <div class="fw-bold">Toyobo Fodu Navy</div>
                        <div class="text-muted fs-7">Konteks: Order ORD-2026-001</div>
                    </td>
                    <td>CV. Tekstil Jaya</td>
                    <td class="text-center">5 Meter</td>
                    <td class="text-end">Rp 45.000</td>
                    <td class="text-end fw-bold">Rp 225.000</td>
                </tr>
            `;
            $('#pr_detail_items').html(itemsHtml);
            $('#pr_detail_grand_total').text('Rp 575.000');
            
            $('#kt_modal_pr_approval_detail').modal('show');
        });

        $('#kt_pr_approve_btn').on('click', function() {
            Swal.fire({
                text: "Apakah Anda yakin ingin menyetujui pengajuan beli ini?",
                icon: "question",
                showCancelButton: true,
                buttonsStyling: false,
                confirmButtonText: "Ya, Setujui",
                cancelButtonText: "Batal",
                customClass: {
                    confirmButton: "btn btn-primary",
                    cancelButton: "btn btn-active-light"
                }
            }).then(function (result) {
                if (result.isConfirmed) {
                    $('#kt_modal_pr_approval_detail').modal('hide');
                    Swal.fire({
                        text: "PR telah berhasil disetujui dan diterbitkan.",
                        icon: "success",
                        buttonsStyling: false,
                        confirmButtonText: "Ok",
                        customClass: { confirmButton: "btn btn-primary" }
                    }).then(() => datatable.draw());
                }
            });
        });
    };

    return {
        init: function () {
            initDataTable();
            handleReviewAction();
        }
    };
}();

KTUtil.onDOMContentLoaded(function () {
    TKAppInventoryPRApproval.init();
});
