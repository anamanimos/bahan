"use strict";

var TKAppMasterProductForm = function () {
    var quill;
    var tagify;

    var initQuill = function () {
        var element = document.querySelector('#kt_ecommerce_add_product_description');
        if (!element) return;

        quill = new Quill('#kt_ecommerce_add_product_description', {
            modules: {
                toolbar: [
                    [{ header: [1, 2, false] }],
                    ['bold', 'italic', 'underline'],
                    ['image', 'code-block']
                ]
            },
            placeholder: 'Masukkan deskripsi produk...',
            theme: 'snow'
        });

        // Sync quill content to hidden input
        var input = document.querySelector('#kt_ecommerce_add_product_description_input');
        quill.on('text-change', function() {
            input.value = quill.root.innerHTML;
        });
    };

    var initTagify = function () {
        var element = document.querySelector('#kt_ecommerce_add_product_tags');
        if (!element) return;

        tagify = new Tagify(element, {
            whitelist: ["Cotton", "Polyester", "Spandex", "Drill", "Oxford"],
            dropdown: {
                maxItems: 20,
                enabled: 0,
                closeOnSelect: false
            }
        });
    };

    var initColorSelect2 = function() {
        const format = (item) => {
            if (!item.id) return item.text;
            var color = item.element ? item.element.getAttribute('data-kt-color') : null;
            if (color) {
                return `<span class="d-flex align-items-center"><span class="w-15px h-15px rounded-circle me-2" style="background-color:${color}; border: 1px solid #ddd;"></span>${item.text}</span>`;
            }
            return item.text;
        };

        const element = $('#kt_ecommerce_add_product_color');
        if (!element.length) return;

        element.select2({
            placeholder: "Pilih Warna",
            allowClear: true,
            templateResult: format,
            templateSelection: format,
            escapeMarkup: function(m) { return m; }
        });

        element.on('select2:select', function (e) {
            if (e.params.data.id === 'ADD_NEW_COLOR') {
                element.val(null).trigger('change');
                $('#kt_modal_add_color').modal('show');
            }
        });

        $('#kt_modal_add_color_picker').on('input', function() {
            $('#kt_modal_add_color_hex').text($(this).val().toUpperCase());
        });

        $('#kt_modal_add_color_submit').on('click', function() {
            const name = $('#kt_modal_add_color_name').val();
            const color = $('#kt_modal_add_color_picker').val();

            if (!name) {
                Swal.fire({ text: "Nama warna wajib diisi!", icon: "error", buttonsStyling: false, confirmButtonText: "Ok, mengerti", customClass: { confirmButton: "btn btn-primary" } });
                return;
            }

            // Show loading
            Swal.fire({
                text: "Sedang menyimpan warna...",
                icon: "info",
                showConfirmButton: false,
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            $.ajax({
                url: hostUrl + "master/ajax/color/store",
                type: "POST",
                data: {
                    name: name,
                    hex_code: color,
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        const newOption = new Option(response.data.name, response.data.name, true, true);
                        newOption.setAttribute('data-kt-color', response.data.hex_code);
                        element.find('option[value="ADD_NEW_COLOR"]').before(newOption);
                        element.trigger('change');
                        $('#kt_modal_add_color').modal('hide');
                        $('#kt_modal_add_color_name').val('');
                        
                        Swal.fire({ text: "Warna berhasil ditambahkan!", icon: "success", buttonsStyling: false, confirmButtonText: "Ok, mengerti", customClass: { confirmButton: "btn btn-primary" } });
                    }
                },
                error: function(xhr) {
                    const error = xhr.responseJSON ? xhr.responseJSON.message : "Terjadi kesalahan";
                    Swal.fire({ text: error, icon: "error", buttonsStyling: false, confirmButtonText: "Ok, mengerti", customClass: { confirmButton: "btn btn-primary" } });
                }
            });
        });
    };

    var initCategorySelect2 = function() {
        const element = $('#kt_ecommerce_add_product_category');
        if (!element.length) return;

        element.select2({
            placeholder: "Pilih Kategori",
            allowClear: true
        });

        element.on('select2:select', function (e) {
            if (e.params.data.id === 'ADD_NEW_CATEGORY') {
                element.val(null).trigger('change');
                $('#kt_modal_add_category').modal('show');
            }
        });

        $('#kt_modal_add_category_submit').on('click', function() {
            const name = $('#kt_modal_add_category_name').val();
            const parentId = $('#kt_modal_add_category_parent').val();

            if (!name) {
                Swal.fire({ text: "Nama kategori wajib diisi!", icon: "error", buttonsStyling: false, confirmButtonText: "Ok, mengerti", customClass: { confirmButton: "btn btn-primary" } });
                return;
            }

            // Show loading
            Swal.fire({
                text: "Sedang menyimpan kategori...",
                icon: "info",
                showConfirmButton: false,
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            $.ajax({
                url: hostUrl + "master/ajax/category/store",
                type: "POST",
                data: {
                    name: name,
                    parent_id: parentId,
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        let displayName = response.data.name;
                        if (parentId) {
                            displayName = '-- ' + response.data.name;
                        }

                        const newOption = new Option(displayName, response.data.id, true, true);
                        element.find('option[value="ADD_NEW_CATEGORY"]').before(newOption);
                        element.trigger('change');
                        $('#kt_modal_add_category').modal('hide');
                        $('#kt_modal_add_category_name').val('');
                        $('#kt_modal_add_category_parent').val(null).trigger('change');

                        Swal.fire({ text: "Kategori berhasil ditambahkan!", icon: "success", buttonsStyling: false, confirmButtonText: "Ok, mengerti", customClass: { confirmButton: "btn btn-primary" } });
                    }
                },
                error: function(xhr) {
                    const error = xhr.responseJSON ? xhr.responseJSON.message : "Terjadi kesalahan";
                    Swal.fire({ text: error, icon: "error", buttonsStyling: false, confirmButtonText: "Ok, mengerti", customClass: { confirmButton: "btn btn-primary" } });
                }
            });
        });
    };

    var initImageGallery = function() {
        const input = document.getElementById('kt_ecommerce_add_product_media_input');
        const container = document.getElementById('kt_ecommerce_add_product_media_items');
        if (!input || !container) return;

        input.addEventListener('change', function(e) {
            const files = e.target.files;
            for (let i = 0; i < files.length; i++) {
                const file = files[i];
                if (!file.type.match('image.*')) continue;

                const reader = new FileReader();
                reader.onload = function(event) {
                    const html = `
                        <div class="image-input image-input-outline border border-gray-300 rounded p-2">
                            <div class="image-input-wrapper w-125px h-125px" style="background-image: url(${event.target.result})"></div>
                            <span class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow position-absolute translate-middle top-0 start-100" onclick="this.parentElement.remove()">
                                <i class="ki-duotone ki-cross fs-2"><span class="path1"></span><span class="path2"></span></i>
                            </span>
                        </div>
                    `;
                    $(container).find('label').before(html);
                };
                reader.readAsDataURL(file);
            }
        });
    };

    var handleSubmit = function() {
        const form = document.getElementById('kt_ecommerce_add_product_form');
        const submitButton = document.getElementById('kt_ecommerce_add_product_submit');
        if (!form || !submitButton) return;

        submitButton.addEventListener('click', e => {
            e.preventDefault();
            
            // Manual validation check for required fields
            const name = form.querySelector('[name="name"]').value;
            const category = form.querySelector('[name="category_id"]').value;
            const unit = form.querySelector('[name="base_unit"]').value;

            if (!name || !category || !unit) {
                Swal.fire({
                    text: "Mohon lengkapi semua bidang yang wajib diisi (*)",
                    icon: "error",
                    buttonsStyling: false,
                    confirmButtonText: "Ok, mengerti",
                    customClass: { confirmButton: "btn btn-primary" }
                });
                return;
            }

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

            const formData = new FormData(form);
            const action = form.getAttribute('action');
            const method = form.getAttribute('method') || 'POST';

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
                                window.location.href = hostUrl + "master/product";
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
                    } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                        // Handle Laravel validation errors
                        const errors = xhr.responseJSON.errors;
                        message = Object.values(errors).flat().join('<br>');
                    }

                    Swal.fire({
                        html: message,
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
            initQuill();
            initTagify();
            initColorSelect2();
            initCategorySelect2();
            initImageGallery();
            handleSubmit();
        }
    };
}();

$(document).ready(function () {
    TKAppMasterProductForm.init();
});
