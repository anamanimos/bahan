"use strict";

var TKAppInventoryGoodsReceiptForm = function () {
    var form;
    var submitButton;

    var handleInvoiceUpload = function() {
        const input = $('#kt_goods_receipt_invoice_input');
        const preview = $('#kt_goods_receipt_invoice_preview');
        const previewContainer = $('#kt_goods_receipt_invoice_preview_container');
        const placeholder = $('#kt_goods_receipt_invoice_placeholder');

        input.on('change', function() {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.attr('src', e.target.result);
                    previewContainer.removeClass('d-none');
                    placeholder.addClass('d-none');
                }
                reader.readAsDataURL(file);
            }
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

                // Fetch real data from server
                $.ajax({
                    url: hostUrl + "inventory/ajax/goods-receipt/get-purchase-requisition",
                    type: "GET",
                    data: { identifier: identifier },
                    success: function(response) {
                        emptyState.addClass('d-none');
                        
                        // Auto-select supplier if not set
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
                            template.find('input[name="quantity[]"]').val(item.requested_quantity);
                            template.find('input[name="price[]"]').val(item.estimated_unit_price);
                            
                            // Hidden fields for submission
                            template.append(`<input type="hidden" name="item_product_id[]" value="${item.product_id}">`);
                            template.append(`<input type="hidden" name="item_purchase_requisition_item_id[]" value="${item.id}">`);
                            
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
            
            // Product select
            const productSelect = template.find('select[name="product_id[]"]');
            productSelect.select2({
                placeholder: "Cari Bahan...",
                ajax: {
                    url: hostUrl + "inventory/ajax/goods-receipt/search-products",
                    dataType: 'json',
                    delay: 250,
                    data: params => ({ q: params.term }),
                    processResults: data => ({ results: data.results })
                }
            });

            // Context toggle
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
                                url: hostUrl + "ajax/sales/orders/search", // Future implementation
                                dataType: 'json',
                                delay: 250,
                                data: params => ({ q: params.term }),
                                processResults: data => ({ results: data.results })
                            }
                        });
                    }
                } else {
                    orderContainer.addClass('d-none');
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

    var handleSubmit = function() {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            submitButton.setAttribute('data-kt-indicator', 'on');
            submitButton.disabled = true;

            const formData = new FormData(form);

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
            handleSubmit();
            
            $('#kt_goods_receipt_add_by_purchase_requisition, #goods_receipt_supplier_id').select2();
        }
    };
}();

KTUtil.onDOMContentLoaded(function () { TKAppInventoryGoodsReceiptForm.init(); });
