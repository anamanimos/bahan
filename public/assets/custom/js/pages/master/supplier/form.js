"use strict";

var TKAppMasterSupplierForm = function () {
    var form;
    var submitButton;

    var handleForm = function () {
        form.addEventListener('submit', function (e) {
            e.preventDefault();

            // Show loading
            Swal.fire({
                text: "Sedang menyimpan data...",
                icon: "info",
                showConfirmButton: false,
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            submitButton.setAttribute('data-kt-indicator', 'on');
            submitButton.disabled = true;

            const action = form.getAttribute('action');
            const method = form.getAttribute('method');
            const formData = new FormData(form);

            $.ajax({
                url: action,
                type: method,
                data: formData,
                processData: false,
                contentType: false,
                success: function (response) {
                    submitButton.removeAttribute('data-kt-indicator');
                    submitButton.disabled = false;

                    if (response.success) {
                        Swal.fire({
                            text: response.message,
                            icon: "success",
                            buttonsStyling: false,
                            confirmButtonText: "Ok, mengerti!",
                            customClass: { confirmButton: "btn btn-primary" }
                        }).then(function (result) {
                            if (result.isConfirmed) {
                                window.location.href = hostUrl + "master/supplier";
                            }
                        });
                    } else {
                        Swal.fire({
                            text: response.message || "Terjadi kesalahan saat menyimpan data.",
                            icon: "error",
                            buttonsStyling: false,
                            confirmButtonText: "Ok, mengerti!",
                            customClass: { confirmButton: "btn btn-primary" }
                        });
                    }
                },
                error: function (xhr) {
                    submitButton.removeAttribute('data-kt-indicator');
                    submitButton.disabled = false;
                    
                    let message = "Terjadi kesalahan sistem.";
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        message = xhr.responseJSON.message;
                    }

                    Swal.fire({
                        text: message,
                        icon: "error",
                        buttonsStyling: false,
                        confirmButtonText: "Ok, mengerti!",
                        customClass: { confirmButton: "btn btn-primary" }
                    });
                }
            });
        });
    };

    return {
        init: function () {
            form = document.querySelector('#kt_ecommerce_add_supplier_form');
            submitButton = document.querySelector('#kt_ecommerce_add_supplier_submit');

            if (!form) return;

            handleForm();
        }
    };
}();

KTUtil.onDOMContentLoaded(function () {
    TKAppMasterSupplierForm.init();
});
