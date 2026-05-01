"use strict";

var TKAppInventoryGR = function () {
    var initForm = function () {
        const form = document.getElementById('kt_gr_form');
        if (!form) return;

        $(form).on('submit', function(e) {
            e.preventDefault();
            const submitButton = document.getElementById('kt_gr_submit');
            
            submitButton.setAttribute('data-kt-indicator', 'on');
            submitButton.disabled = true;

            $.post(hostUrl + 'ajax/inventory/stocks/submit_gr', $(form).serialize(), function(res) {
                submitButton.removeAttribute('data-kt-indicator');
                submitButton.disabled = false;

                if(res.status === 'success') {
                    Swal.fire({
                        text: res.message,
                        icon: "success",
                        buttonsStyling: false,
                        confirmButtonText: "Ok, mengerti!",
                        customClass: {
                            confirmButton: "btn btn-primary"
                        }
                    }).then(function() {
                        window.location.href = hostUrl + 'inventory/stocks';
                    });
                }
            });
        });
    };

    return {
        init: function () {
            initForm();
        }
    };
}();

KTUtil.onDOMContentLoaded(function () {
    TKAppInventoryGR.init();
});
