"use strict";

// Class definition
var TKAppInventorySaveProduct = function () {
    // Private variables
    var quill;

    // Private functions
    var initQuill = function () {
        // Initialize Quill editor
        var element = document.querySelector('#kt_ecommerce_add_product_description');
        var input = document.querySelector('#kt_ecommerce_add_product_description_input');
        
        if (!element || !input) {
            return;
        }

        // Check if Quill is loaded
        if (typeof Quill === 'undefined') {
            console.error('Quill is not defined. Please ensure Quill is included in your bundle.');
            // Fallback: make the div editable if Quill fails
            element.setAttribute('contenteditable', 'true');
            element.classList.add('form-control');
            element.addEventListener('input', function() {
                input.value = element.innerHTML;
            });
            return;
        }

        quill = new Quill(element, {
            modules: {
                toolbar: [
                    [{
                        header: [1, 2, false]
                    }],
                    ['bold', 'italic', 'underline'],
                    ['image', 'code-block']
                ]
            },
            placeholder: 'Type your text here...',
            theme: 'snow'
        });

        // Sync quill content to hidden input
        quill.on('text-change', function() {
            input.value = quill.root.innerHTML;
        });
    }

    var initTagify = function () {
        // Initialize Tagify
        var element = document.querySelector('#kt_ecommerce_add_product_tags');
        if (!element) {
            return;
        }
        new Tagify(element, {
            whitelist: ["Cotton", "Polyester", "Spandex", "Drill", "Oxford"],
            dropdown: {
                maxItems: 20,           // <- miximum allowed rendered suggestions
                classname: "tags-look", // <- custom classname for this dropdown, so it could be targeted
                enabled: 0,             // <- show suggestions on focus
                closeOnSelect: false    // <- do not hide the suggestions dropdown once an item has been selected
            }
        });
    }

    var initColorSelect2 = function() {
        // Format options with color preview
        const format = (item) => {
            if (!item.id) {
                return item.text;
            }

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
            escapeMarkup: function(m) {
                return m;
            }
        });

        // Handle "Add New Color" selection
        element.on('select2:select', function (e) {
            const data = e.params.data;
            
            if (data.id === 'ADD_NEW_COLOR') {
                // Clear selection
                element.val(null).trigger('change');
                
                // Show modal
                $('#kt_modal_add_color').modal('show');
            }
        });

        // Handle Color Picker changes
        const colorPicker = document.getElementById('kt_modal_add_color_picker');
        const colorHex = document.getElementById('kt_modal_add_color_hex');
        if (colorPicker && colorHex) {
            colorPicker.addEventListener('input', (e) => {
                colorHex.innerText = e.target.value.toUpperCase();
            });
        }

        // Handle Submit New Color
        const submitBtn = document.getElementById('kt_modal_add_color_submit');
        if (submitBtn) {
            submitBtn.addEventListener('click', function() {
                const name = document.getElementById('kt_modal_add_color_name').value;
                const color = document.getElementById('kt_modal_add_color_picker').value;

                if (!name) {
                    Swal.fire({ text: "Nama warna wajib diisi!", icon: "error", buttonsStyling: false, confirmButtonText: "Ok, mengerti", customClass: { confirmButton: "btn btn-primary" } });
                    return;
                }

                // Create new option
                const newOption = new Option(name, name, true, true);
                newOption.setAttribute('data-kt-color', color);
                
                // Add before the "ADD_NEW_COLOR" option
                element.find('option[value="ADD_NEW_COLOR"]').before(newOption);
                
                // Trigger change
                element.trigger('change');

                // Hide modal and clear inputs
                $('#kt_modal_add_color').modal('hide');
                document.getElementById('kt_modal_add_color_name').value = '';
            });
        }
    }

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

        const submitBtn = document.getElementById('kt_modal_add_category_submit');
        if (submitBtn) {
            submitBtn.addEventListener('click', function() {
                const nameInput = document.getElementById('kt_modal_add_category_name');
                const parentId = document.getElementById('kt_modal_add_category_parent').value;
                const parentText = $('#kt_modal_add_category_parent option:selected').text();
                const name = nameInput.value;

                if (!name) {
                    Swal.fire({ text: "Nama kategori wajib diisi!", icon: "error", buttonsStyling: false, confirmButtonText: "Ok, mengerti", customClass: { confirmButton: "btn btn-primary" } });
                    return;
                }

                let displayName = name;
                if (parentId) {
                    // Check parent indentation level
                    const parentPrefix = parentText.match(/^(--)+/);
                    const prefix = parentPrefix ? parentPrefix[0] + '-- ' : '-- ';
                    displayName = prefix + name;
                }

                const newOption = new Option(displayName, 'NEW_' + name, true, true);
                
                // Add before "ADD_NEW_CATEGORY"
                element.find('option[value="ADD_NEW_CATEGORY"]').before(newOption);
                
                element.trigger('change');
                $('#kt_modal_add_category').modal('hide');
                nameInput.value = '';
                $('#kt_modal_add_category_parent').val(null).trigger('change');
            });
        }
    }

    var initImageGallery = function() {
        const input = document.getElementById('kt_ecommerce_add_product_media_input');
        const container = document.getElementById('kt_ecommerce_add_product_media_items');
        
        if (!input || !container) return;

        input.addEventListener('change', function(e) {
            const files = e.target.files;
            
            // In a real app, you might want to limit the number of files
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
                    // Prepend before the "Add new item" label
                    $(container).find('label').before(html);
                };
                reader.readAsDataURL(file);
            }
        });
    }

    var handleSubmit = function() {
        const form = document.getElementById('kt_ecommerce_add_product_form');
        const submitButton = document.getElementById('kt_ecommerce_add_product_submit');

        if (!form || !submitButton) return;

        const validator = FormValidation.formValidation(form, {
            fields: {
                'name': { validators: { notEmpty: { message: 'Nama produk wajib diisi' } } },
                'base_unit': { validators: { notEmpty: { message: 'Satuan utama wajib diisi' } } },
                'rop': { validators: { notEmpty: { message: 'Ambang batas (ROP) wajib diisi' } } },
            },
            plugins: {
                trigger: new FormValidation.plugins.Trigger(),
                bootstrap: new FormValidation.plugins.Bootstrap5({ rowSelector: '.fv-row', eleInvalidClass: '', eleValidClass: '' })
            }
        });

        submitButton.addEventListener('click', e => {
            e.preventDefault();
            if (validator) {
                validator.validate().then(function(status) {
                    if (status == 'Valid') {
                        submitButton.setAttribute('data-kt-indicator', 'on');
                        submitButton.disabled = true;
                        
                        // Sync Quill
                        if (quill) {
                            document.getElementById('kt_ecommerce_add_product_description_input').value = quill.root.innerHTML;
                        }

                        setTimeout(function() {
                            submitButton.removeAttribute('data-kt-indicator');
                            submitButton.disabled = false;
                            Swal.fire({
                                text: "Produk berhasil disimpan!",
                                icon: "success",
                                buttonsStyling: false,
                                confirmButtonText: "Ok, mengerti!",
                                customClass: { confirmButton: "btn btn-primary" }
                            }).then(function(result) {
                                if (result.isConfirmed) {
                                    form.submit();
                                }
                            });
                        }, 2000);
                    } else {
                        Swal.fire({
                            text: "Maaf, sepertinya ada kesalahan yang terdeteksi, silakan coba lagi.",
                            icon: "error",
                            buttonsStyling: false,
                            confirmButtonText: "Ok, mengerti",
                            customClass: { confirmButton: "btn btn-primary" }
                        });
                    }
                });
            }
        });
    }

    // Public methods
    return {
        init: function () {
            // Use try-catch to ensure one failure doesn't stop the whole initialization
            try { initQuill(); } catch(e) { console.error('Quill init failed', e); }
            try { initTagify(); } catch(e) { console.error('Tagify init failed', e); }
            try { initColorSelect2(); } catch(e) { console.error('ColorSelect2 init failed', e); }
            try { initCategorySelect2(); } catch(e) { console.error('CategorySelect2 init failed', e); }
            try { initImageGallery(); } catch(e) { console.error('ImageGallery init failed', e); }
            try { handleSubmit(); } catch(e) { console.error('SubmitHandler failed', e); }
        }
    };
}();

// On document ready
KTUtil.onDOMContentLoaded(function () {
    TKAppInventorySaveProduct.init();
});
