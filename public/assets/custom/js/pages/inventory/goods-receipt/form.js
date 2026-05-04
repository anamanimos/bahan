"use strict";

var TKAppInventoryGoodsReceiptForm = function () {
    var form;
    var submitButton;

    // --- Companion Camera State ---
    var companionToken = null;
    var companionPollInterval = null;
    var companionPhotoUrl = null; // URL of photo received from companion

    // --- UI Helper: Show only one photo panel ---
    var showPanel = function(panelId) {
        // Hide all panels
        $('#kt_goods_receipt_photo_options').addClass('d-none');
        $('#kt_goods_receipt_companion_panel').addClass('d-none');
        $('#kt_goods_receipt_companion_connected').addClass('d-none');
        $('#kt_goods_receipt_invoice_preview_container').addClass('d-none');
        // Show the requested one
        if (panelId) {
            $(panelId).removeClass('d-none');
        }
    };

    // --- Check for existing companion session on page load ---
    var checkExistingSession = function() {
        $.ajax({
            url: hostUrl + "companion/check-session",
            type: "GET",
            success: function(response) {
                if (response.success && response.has_session) {
                    companionToken = response.token;

                    if (response.phone_connected) {
                        // HP is already connected! Show connected panel
                        // Clear old photo for new nota
                        $.ajax({
                            url: hostUrl + "companion/clear-photo/" + companionToken,
                            type: "POST",
                            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
                        });

                        showPanel('#kt_goods_receipt_companion_connected');
                        startCompanionPolling();
                    }
                    // If session exists but phone not connected, just show normal options
                }
            }
        });
    };

    var handleInvoiceUpload = function() {
        const input = $('#kt_goods_receipt_invoice_input');
        const preview = $('#kt_goods_receipt_invoice_preview');

        // Option 1: Pick from gallery
        $('#kt_goods_receipt_option_gallery').on('click', function() {
            input.trigger('click');
        });

        // Handle file selection
        input.on('change', function() {
            const file = this.files[0];
            if (file) {
                stopCompanionPolling();
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.attr('src', e.target.result);
                    showPanel('#kt_goods_receipt_invoice_preview_container');
                    companionPhotoUrl = null; // Using file input, not companion
                }
                reader.readAsDataURL(file);
            }
        });

        // Option 2: Companion camera
        $('#kt_goods_receipt_option_companion').on('click', function() {
            startCompanionSession();
        });

        // Cancel companion (go back to options)
        $('#companion_cancel_btn').on('click', function() {
            stopCompanionPolling();
            showPanel('#kt_goods_receipt_photo_options');
        });

        // Switch to gallery from connected panel
        $('#companion_switch_gallery_btn').on('click', function() {
            input.trigger('click');
        });

        // Disconnect companion
        $('#companion_disconnect_btn').on('click', function() {
            stopCompanionPolling();
            companionToken = null;
            showPanel('#kt_goods_receipt_photo_options');
        });

        // Change photo (go back to options or connected panel)
        $('#kt_goods_receipt_change_photo').on('click', function() {
            preview.attr('src', '');
            input.val('');
            companionPhotoUrl = null;

            // If companion is still active, go to connected panel
            if (companionToken) {
                // Clear old photo on server for new one
                $.ajax({
                    url: hostUrl + "companion/clear-photo/" + companionToken,
                    type: "POST",
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
                });
                showPanel('#kt_goods_receipt_companion_connected');
                startCompanionPolling();
            } else {
                showPanel('#kt_goods_receipt_photo_options');
            }
        });
    };

    // --- Companion Camera Functions ---

    var startCompanionSession = function() {
        const qrLoading = $('#companion_qr_loading');
        const qrDisplay = $('#companion_qr_display');
        const qrCodeContainer = $('#companion_qr_code');

        // Show QR panel with loading
        showPanel('#kt_goods_receipt_companion_panel');
        qrLoading.removeClass('d-none');
        qrDisplay.addClass('d-none');

        // Request session (reuses existing if available)
        $.ajax({
            url: hostUrl + "companion/session",
            type: "POST",
            data: { context_type: 'goods_receipt' },
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            success: function(response) {
                if (response.success) {
                    companionToken = response.token;
                    
                    qrCodeContainer.empty();
                    
                    if (typeof QRCode !== 'undefined') {
                        new QRCode(qrCodeContainer[0], {
                            text: response.url,
                            width: 180,
                            height: 180,
                            colorDark: '#1B1B28',
                            colorLight: '#FFFFFF',
                            correctLevel: QRCode.CorrectLevel.M
                        });
                        
                        qrLoading.addClass('d-none');
                        qrDisplay.removeClass('d-none');
                    } else {
                        // Fallback: show URL as text
                        qrCodeContainer.html(
                            '<div class="p-3 bg-light rounded">' +
                            '<div class="fs-9 text-muted mb-1">Buka URL ini di HP:</div>' +
                            '<input type="text" class="form-control form-control-sm fs-9" value="' + response.url + '" readonly onclick="this.select()" />' +
                            '</div>'
                        );
                        qrLoading.addClass('d-none');
                        qrDisplay.removeClass('d-none');
                    }

                    startCompanionPolling();
                }
            },
            error: function() {
                showPanel('#kt_goods_receipt_photo_options');
                Swal.fire({
                    text: "Gagal membuat sesi companion. Silakan coba lagi.",
                    icon: "error",
                    buttonsStyling: false,
                    confirmButtonText: "Ok",
                    customClass: { confirmButton: "btn btn-primary" }
                });
            }
        });
    };

    var startCompanionPolling = function() {
        if (companionPollInterval) {
            clearInterval(companionPollInterval);
        }

        companionPollInterval = setInterval(function() {
            if (!companionToken) return;

            $.ajax({
                url: hostUrl + "companion/check-photo/" + companionToken,
                type: "GET",
                success: function(response) {
                    if (response.success) {
                        // Update HP connection status in QR panel
                        if (response.phone_connected) {
                            $('#companion_hp_status').html(
                                '<span class="badge badge-light-success fs-9">' +
                                '<i class="ki-duotone ki-check-circle fs-8 me-1"><span class="path1"></span><span class="path2"></span></i>' +
                                'HP Terhubung' +
                                '</span>'
                            );

                            // If we're showing QR panel but phone just connected,
                            // switch to connected panel
                            if (!$('#kt_goods_receipt_companion_panel').hasClass('d-none')) {
                                showPanel('#kt_goods_receipt_companion_connected');
                            }
                        }

                        // Photo received!
                        if (response.has_photo && response.photo_url) {
                            stopCompanionPolling();
                            handleCompanionPhotoReceived(response.photo_url);
                        }
                    }
                },
                error: function(xhr) {
                    if (xhr.status === 410) {
                        stopCompanionPolling();
                        companionToken = null;
                        $('#companion_hp_status').html(
                            '<span class="badge badge-light-danger fs-9">Sesi expired</span>'
                        );
                    }
                }
            });
        }, 3000); // Poll every 3 seconds
    };

    var stopCompanionPolling = function() {
        if (companionPollInterval) {
            clearInterval(companionPollInterval);
            companionPollInterval = null;
        }
    };

    var handleCompanionPhotoReceived = function(photoUrl) {
        const preview = $('#kt_goods_receipt_invoice_preview');

        companionPhotoUrl = photoUrl;
        preview.attr('src', photoUrl);
        showPanel('#kt_goods_receipt_invoice_preview_container');

        Swal.fire({
            text: "Foto berhasil diterima dari HP! 📸",
            icon: "success",
            buttonsStyling: false,
            confirmButtonText: "Ok",
            customClass: { confirmButton: "btn btn-primary" },
            timer: 3000,
            timerProgressBar: true
        });
    };

    var initFlatpickr = function() {
        const element = document.querySelector('#kt_goods_receipt_date');
        if (!element) return;
        
        flatpickr(element, {
            altInput: true,
            altFormat: "d M Y",
            dateFormat: "Y-m-d",
            defaultDate: "today"
        });
    };

    var handleAddByPurchaseRequisition = function() {
        const selectors = ['#kt_goods_receipt_add_by_purchase_requisition', '#kt_goods_receipt_add_by_purchase_requisition_mobile'];
        
        selectors.forEach(selector => {
            $(selector).on('change', function() {
                const identifier = $(this).val();
                const container = $('#kt_goods_receipt_items_container');
                const emptyState = $('#goods_receipt_items_empty');

                if (!identifier) return;

                $.ajax({
                    url: hostUrl + "inventory/ajax/goods-receipt/get-purchase-requisition",
                    type: "GET",
                    data: { identifier: identifier },
                    success: function(response) {
                        emptyState.addClass('d-none');
                        
                        if (!$('#goods_receipt_supplier_id').val() && response.supplier_id) {
                            $('#goods_receipt_supplier_id').val(response.supplier_id).trigger('change');
                        }

                        response.items.forEach((item, index) => {
                            const template = container.find('.goods-receipt-item.d-none').clone().removeClass('d-none');
                            const itemId = `kt_goods_receipt_item_${Date.now()}_${index}`;
                            
                            template.find('[data-kt-element="collapse-trigger"]').attr({
                                'data-bs-target': `#${itemId}`,
                                'aria-controls': itemId,
                                'aria-expanded': 'true'
                            });
                            template.find('[data-kt-element="collapse-content"]').attr('id', itemId);

                            template.find('[data-kt-element="item-source"]').html(`<span class="badge badge-light-primary">PR: ${identifier}</span>`);
                            template.find('[data-kt-element="item-name"]').text(item.product_name);
                            template.find('[data-kt-element="order-ref"]').text(item.order_reference || '-');
                            template.find('[data-kt-element="qty-purchase-requisition"]').text(item.requested_quantity);
                            template.find('[data-kt-element="unit"]').text(item.unit);
                            template.find('[data-kt-element="input-quantity"]').attr('name', 'quantity[]').val(item.requested_quantity);
                            template.find('[data-kt-element="input-price"]').attr('name', 'price[]').val(item.estimated_unit_price);
                            template.find('[data-kt-element="input-notes"]').attr('name', 'notes[]').val('');

                            template.find('[data-kt-element="product-display"]').removeClass('d-none');
                            template.find('[data-kt-element="product-select-container"]').addClass('d-none');
                            template.find('[data-kt-element="qty-purchase-requisition-container"]').removeClass('d-none');
                            
                            template.find('[data-kt-element="input-product-id"]').val(item.product_id);
                            template.find('[data-kt-element="input-order-reference"]').val(item.order_reference || '');
                            template.find('[data-kt-element="input-pr-item-id"]').val(item.id);
                            
                            template.find('input[name="quantity[]"], input[name="price[]"]').on('input', updateTotalCount);

                            template.find('[data-kt-element="remove-item"]').on('click', function() {
                                template.remove();
                                updateTotalCount();
                            });

                            container.append(template);
                        });

                        updateTotalCount();
                    },
                    error: function() {
                        Swal.fire({ text: "Gagal mengambil data PR.", icon: "error", buttonsStyling: false, confirmButtonText: "Ok", customClass: { confirmButton: "btn btn-primary" } });
                    }
                });

                $(this).val(null).trigger('change');
            });
        });
    };

    var handleAddManual = function() {
        $('#kt_goods_receipt_add_manual').on('click', function() {
            const container = $('#kt_goods_receipt_items_container');
            const emptyState = $('#goods_receipt_items_empty');
            const template = container.find('.goods-receipt-item.d-none').clone().removeClass('d-none');
            const itemId = `kt_goods_receipt_item_manual_${Date.now()}`;
            
            template.find('[data-kt-element="collapse-trigger"]').attr({
                'data-bs-target': `#${itemId}`,
                'aria-controls': itemId,
                'aria-expanded': 'true'
            });
            template.find('[data-kt-element="collapse-content"]').attr('id', itemId);

            emptyState.addClass('d-none');

            template.find('[data-kt-element="item-source"]').html(`<span class="badge badge-light-warning">Manual (Non-PR)</span>`);
            template.find('[data-kt-element="product-display"]').addClass('d-none');
            template.find('[data-kt-element="product-select-container"]').removeClass('d-none');
            template.find('[data-kt-element="qty-purchase-requisition-container"]').addClass('d-none');
            
            template.find('[data-kt-element="product-col"]').removeClass('col-md-3').addClass('col-md-5');
            
            template.find('[data-kt-element="product-select"]').select2({
                placeholder: "Cari Bahan...",
                ajax: {
                    url: hostUrl + "inventory/ajax/goods-receipt/search-products",
                    dataType: 'json',
                    delay: 250,
                    data: params => ({ q: params.term }),
                    processResults: data => ({ results: data.results })
                }
            }).on('select2:select', function(e) {
                template.find('[data-kt-element="input-product-id"]').val(e.params.data.id);
            });

            template.find('[data-kt-element="context-type"]').attr('name', 'context_type[]');
            template.find('[data-kt-element="input-quantity"]').attr('name', 'quantity[]').val('');
            template.find('[data-kt-element="input-price"]').attr('name', 'price[]').val('');
            template.find('[data-kt-element="input-notes"]').attr('name', 'notes[]').val('');

            const contextSelect = template.find('[data-kt-element="context-type"]');
            const orderContainer = template.find('[data-kt-element="order-container"]');
            const orderSelect = template.find('[data-kt-element="order-select"]');

            contextSelect.on('change', function() {
                const val = $(this).val();
                if (val === 'Order') {
                    orderContainer.removeClass('d-none');
                    if (!orderSelect.hasClass("select2-hidden-accessible")) {
                        orderSelect.select2({
                            width: '100%',
                            placeholder: "Pilih Order...",
                            ajax: {
                                url: orderSelect.data('ajax-url'),
                                dataType: 'json',
                                delay: 250,
                                data: params => ({ q: params.term }),
                                processResults: data => ({ results: data.results })
                            }
                        }).on('select2:select', function(e) {
                            template.find('[data-kt-element="order-ref"]').text(e.params.data.text);
                            template.find('[data-kt-element="input-order-reference"]').val(e.params.data.id); // ID is the order_number
                        });
                    }
                } else {
                    orderContainer.addClass('d-none');
                    template.find('[data-kt-element="input-order-reference"]').val('');
                }
            });

            template.find('input[name="quantity[]"], input[name="price[]"]').on('input', updateTotalCount);

            template.find('[data-kt-element="remove-item"]').on('click', function() {
                template.remove();
                updateTotalCount();
            });

            container.append(template);
            updateTotalCount();
        });
    };

    var updateTotalCount = function() {
        const items = $('#kt_goods_receipt_items_container .goods-receipt-item:not(.d-none)');
        const count = items.length;
        $('#kt_goods_receipt_total_count').text(count);
        $('#kt_goods_receipt_total_items_label').text(`${count} Item terdaftar`);
        
        let totalEstimation = 0;
        items.each(function() {
            const qty = parseFloat($(this).find('input[name="quantity[]"]').val()) || 0;
            const price = parseFloat($(this).find('input[name="price[]"]').val()) || 0;
            totalEstimation += (qty * price);
        });
        $('#kt_goods_receipt_total_estimation').text(totalEstimation.toLocaleString('id-ID'));

        const emptyState = $('#goods_receipt_items_empty');
        if (count === 0) {
            emptyState.removeClass('d-none');
            $('#kt_goods_receipt_submit').attr('disabled', true);
        } else {
            emptyState.addClass('d-none');
            $('#kt_goods_receipt_submit').attr('disabled', false);
        }
    };

    var handleQuickAddSupplier = function() {
        const modal = $('#modal_quick_add_supplier');
        const form = document.querySelector('#form_quick_add_supplier');
        const submitButton = document.querySelector('#btn_quick_add_supplier_submit');
        const supplierSelect = $('#goods_receipt_supplier_id');

        if (!form) return;

        supplierSelect.on('change', function() {
            if ($(this).val() === 'add_new') {
                $(this).val(null).trigger('change');
                modal.modal('show');
            }
        });

        form.addEventListener('submit', function(e) {
            e.preventDefault();

            submitButton.setAttribute('data-kt-indicator', 'on');
            submitButton.disabled = true;

            $.ajax({
                url: hostUrl + "master/ajax/supplier/store",
                type: "POST",
                data: $(form).serialize(),
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                success: function(response) {
                    submitButton.removeAttribute('data-kt-indicator');
                    submitButton.disabled = false;

                    if (response.success) {
                        const newOption = new Option(response.data.name, response.data.id, true, true);
                        supplierSelect.append(newOption).trigger('change');
                        
                        modal.modal('hide');
                        form.reset();

                        Swal.fire({
                            text: "Supplier baru berhasil ditambahkan!",
                            icon: "success",
                            buttonsStyling: false,
                            confirmButtonText: "Ok",
                            customClass: { confirmButton: "btn btn-primary" }
                        });
                    }
                },
                error: function(xhr) {
                    submitButton.removeAttribute('data-kt-indicator');
                    submitButton.disabled = false;
                    const errorMsg = xhr.responseJSON ? xhr.responseJSON.message : "Gagal menambahkan supplier.";
                    Swal.fire({ text: errorMsg, icon: "error", buttonsStyling: false, confirmButtonText: "Ok", customClass: { confirmButton: "btn btn-primary" } });
                }
            });
        });
    };

    var handleSubmit = function() {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Validate all quantities are > 0 (only for visible items)
            let valid = true;
            $('#kt_goods_receipt_items_container .goods-receipt-item:not(.d-none) input[name="quantity[]"]').each(function() {
                const val = parseFloat($(this).val());
                if (isNaN(val) || val <= 0) {
                    valid = false;
                    $(this).addClass('is-invalid');
                } else {
                    $(this).removeClass('is-invalid');
                }
            });

            if (!valid) {
                Swal.fire({
                    text: "Mohon pastikan semua jumlah barang telah diisi dengan benar (minimal 0.01).",
                    icon: "warning",
                    buttonsStyling: false,
                    confirmButtonText: "Ok, saya perbaiki",
                    customClass: { confirmButton: "btn btn-primary" }
                });
                return;
            }

            submitButton.setAttribute('data-kt-indicator', 'on');
            submitButton.disabled = true;

            const formData = new FormData(form);

            // If photo came from companion (not file input), add the URL
            if (companionPhotoUrl) {
                formData.append('companion_photo_url', companionPhotoUrl);
                if (!$('#kt_goods_receipt_invoice_input')[0].files.length) {
                    formData.delete('invoice_photo');
                }
            }

            $.ajax({
                url: hostUrl + "inventory/goods-receipt/store",
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    submitButton.removeAttribute('data-kt-indicator');
                    submitButton.disabled = false;

                    stopCompanionPolling();

                    Swal.fire({
                        text: response.message || "Nota Penerimaan Barang berhasil disimpan!",
                        icon: "success",
                        buttonsStyling: false,
                        confirmButtonText: "Selesai",
                        customClass: { confirmButton: "btn btn-primary" }
                    }).then(function (result) {
                        if (result.isConfirmed) {
                            window.location.href = hostUrl + 'inventory/goods-receipt';
                        }
                    });
                },
                error: function(xhr) {
                    submitButton.removeAttribute('data-kt-indicator');
                    submitButton.disabled = false;
                    const errorMsg = xhr.responseJSON ? xhr.responseJSON.message : "Terjadi kesalahan sistem.";
                    Swal.fire({ text: errorMsg, icon: "error", buttonsStyling: false, confirmButtonText: "Ok", customClass: { confirmButton: "btn btn-primary" } });
                }
            });
        });
    };

    return {
        init: function () {
            form = document.querySelector('#kt_goods_receipt_form');
            submitButton = document.querySelector('#kt_goods_receipt_submit');
            if (!form) return;

            handleInvoiceUpload();
            initFlatpickr();
            handleAddByPurchaseRequisition();
            handleAddManual();
            handleQuickAddSupplier();
            handleSubmit();

            // Check for existing companion session on page load
            checkExistingSession();
            
            const formatSupplier = (item) => {
                if (!item.id) return item.text;
                if (item.element && item.element.getAttribute('data-kt-select2-template') === 'add_new') {
                    return $(`<span class="text-primary fw-bold"><i class="ki-duotone ki-plus fs-4 me-2 text-primary"></i>${item.text}</span>`);
                }
                return item.text;
            };

            $('#kt_goods_receipt_add_by_purchase_requisition').select2();
            $('#goods_receipt_supplier_id').select2({
                templateResult: formatSupplier,
                templateSelection: formatSupplier
            });
        }
    };
}();

KTUtil.onDOMContentLoaded(function () { TKAppInventoryGoodsReceiptForm.init(); });
