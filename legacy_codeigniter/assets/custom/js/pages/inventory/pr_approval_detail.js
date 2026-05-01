"use strict";

var TKAppInventoryPRApprovalDetail = function () {
    var approvedTotal = 0;
    var rejectedTotal = 0;

    var updateSummary = function() {
        approvedTotal = 0;
        rejectedTotal = 0;

        // Use desktop table for calculation (or any element with data-kt-value)
        // We ensure we only count each item once, so we filter by visible element element 
        // to avoid double counting between desktop table and mobile cards
        const isMobile = window.innerWidth < 768;
        const items = isMobile 
            ? document.querySelectorAll('.d-md-none [data-kt-element="item"]')
            : document.querySelectorAll('.d-none.d-md-block [data-kt-element="item"]');

        items.forEach(item => {
            const val = parseFloat(item.getAttribute('data-kt-value')) || 0;
            const status = item.querySelector('[data-kt-element="item-status"]').innerText;

            if (status === 'Disetujui') {
                approvedTotal += val;
            } else if (status === 'Ditolak') {
                rejectedTotal += val;
            }
        });

        document.getElementById('pr_total_approved').innerText = `Rp ${approvedTotal.toLocaleString('id-ID')}`;
        document.getElementById('pr_total_rejected').innerText = `Rp ${rejectedTotal.toLocaleString('id-ID')}`;
    };

    var handleItemActions = function () {
        // Sync desktop and mobile actions
        const syncStatus = (prId, status) => {
            // In a real app, you'd use a unique ID for each item row
            // For this mock, we'll just sync based on index or similar
        };

        // Approve Item
        $(document).on('click', '[data-kt-action="approve-item"]', function () {
            const row = $(this).closest('[data-kt-element="item"]');
            const statusBadge = row.find('[data-kt-element="item-status"]');
            
            statusBadge.removeClass('badge-light-warning badge-light-danger').addClass('badge-light-success').text('Disetujui');
            updateSummary();
        });

        // Reject Item
        $(document).on('click', '[data-kt-action="reject-item"]', function () {
            const row = $(this).closest('[data-kt-element="item"]');
            const statusBadge = row.find('[data-kt-element="item-status"]');
            
            statusBadge.removeClass('badge-light-warning badge-light-success').addClass('badge-light-danger').text('Ditolak');
            updateSummary();
        });

        // Finalize
        const finalizeBtn = document.getElementById('kt_pr_finalize_btn');
        if (finalizeBtn) {
            finalizeBtn.addEventListener('click', function () {
                Swal.fire({
                    text: "Simpan semua keputusan persetujuan untuk PR ini?",
                    icon: "question",
                    showCancelButton: true,
                    buttonsStyling: false,
                    confirmButtonText: "Ya, Simpan",
                    cancelButtonText: "Batal",
                    customClass: {
                        confirmButton: "btn btn-primary",
                        cancelButton: "btn btn-active-light"
                    }
                }).then(function (result) {
                    if (result.isConfirmed) {
                        Swal.fire({
                            text: "Keputusan persetujuan telah disimpan dan diteruskan ke bagian pengadaan.",
                            icon: "success",
                            buttonsStyling: false,
                            confirmButtonText: "Ok",
                            customClass: { confirmButton: "btn btn-primary" }
                        }).then(() => {
                            window.location.href = hostUrl + 'inventory/purchase-requisition';
                        });
                    }
                });
            });
        }
    };

    return {
        init: function () {
            handleItemActions();
            updateSummary();
        }
    };
}();

KTUtil.onDOMContentLoaded(function () {
    TKAppInventoryPRApprovalDetail.init();
});
