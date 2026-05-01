"use strict";

var TKAppInventoryGRForm = function () {
    var form;
    var submitButton;

    var handleInvoiceUpload = function() {
        const input = $('#kt_gr_invoice_input');
        const preview = $('#kt_gr_invoice_preview');
        const previewContainer = $('#kt_gr_invoice_preview_container');
        const placeholder = $('#kt_gr_invoice_placeholder');

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
        const element = document.querySelector('#kt_gr_date');
        if (!element) return;
        
        flatpickr(element, {
            altInput: true,
            altFormat: "d M Y",
            dateFormat: "Y-m-d",
            defaultDate: "today"
        });
    };

    var handleAddByPR = function() {
        const selectors = ['#kt_gr_add_by_pr', '#kt_gr_add_by_pr_mobile'];
        
        selectors.forEach(selector => {
            $(selector).on('change', function() {
                const val = $(this).val();
                const container = $('#kt_gr_items_container');
                const emptyState = $('#gr_items_empty');

                if (!val) return;

                emptyState.addClass('d-none');
                
                // Simulate loading items from PR
                const mockItems = [
                    { id: 1, name: 'Cotton Combed 30s Putih', order: 'ORD-2026-001', qty_pr: 10, unit: 'Mtr' },
                    { id: 2, name: 'Toyobo Fodu Navy', order: 'ORD-2026-002', qty_pr: 50, unit: 'Mtr' }
                ];

                mockItems.forEach((item, index) => {
                    const template = container.find('.gr-item.d-none').clone().removeClass('d-none');
                    const itemId = `kt_gr_item_${Date.now()}_${index}`;
                    
                    // Set collapse IDs and ARIA attributes
                    template.find('[data-kt-element="collapse-trigger"]').attr({
                        'data-bs-target': `#${itemId}`,
                        'aria-controls': itemId,
                        'aria-expanded': 'true'
                    });
                    template.find('[data-kt-element="collapse-content"]').attr('id', itemId);

                    template.find('[data-kt-element="item-source"]').html(`<span class="badge badge-light-primary">PR: ${val}</span>`);
                    template.find('[data-kt-element="item-name"]').text(item.name);
                    template.find('[data-kt-element="order-ref"]').text(item.order);
                    template.find('[data-kt-element="qty-pr"]').text(item.qty_pr);
                    template.find('[data-kt-element="unit"]').text(item.unit);
                    template.find('[data-kt-element="unit-label"]').text(item.unit);
                    template.find('input[name="quantity[]"]').val(item.qty_pr);
                    
                    // Trigger total calculation on change
                    template.find('input[name="quantity[]"], input[name="price[]"]').on('input', updateTotalCount);

                    template.find('[data-kt-element="remove-item"]').on('click', function() {
                        template.remove();
                        updateTotalCount();
                    });

                    container.append(template);
                });

                $(this).val(null).trigger('change');
                updateTotalCount();
            });
        });
    };

    var handleAddManual = function() {
        $('#kt_gr_add_manual').on('click', function() {
            const container = $('#kt_gr_items_container');
            const emptyState = $('#gr_items_empty');
            const template = container.find('.gr-item.d-none').clone().removeClass('d-none');
            const itemId = `kt_gr_item_manual_${Date.now()}`;
            
            // Set collapse IDs and ARIA attributes
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
            template.find('[data-kt-element="qty-pr-container"]').addClass('d-none');
            
            // Adjust column widths for manual (Non-PR) to fill space (3+2+1+2+4 = 12)
            // Original: Name 3, QtyPR 2, Jml 1, Price 2, Notes 4
            // Manual: Name 5, Jml 1, Price 2, Notes 4
            template.find('[data-kt-element="product-col"]').removeClass('col-md-3').addClass('col-md-5');
            template.find('[data-kt-element="notes-col"]').removeClass('col-md-4').addClass('col-md-4');
            
            // Product select
            template.find('select[name="product_id[]"]').select2({
                placeholder: "Cari Bahan...",
                ajax: {
                    url: hostUrl + "ajax/inventory/stocks/search_products",
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
                    // Init order select2 if not already
                    if (!orderSelect.hasClass("select2-hidden-accessible")) {
                        orderSelect.select2({
                            width: '100%',
                            placeholder: "Pilih Order...",
                            ajax: {
                                url: hostUrl + "ajax/inventory/stocks/search_orders",
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

            // Trigger total calculation on change
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
        const items = $('#kt_gr_items_container .gr-item:not(.d-none)');
        const count = items.length;
        $('#kt_gr_total_count').text(count);
        $('#kt_gr_total_items_label').text(`${count} Item terdaftar`);
        
        // Calculate Total Estimation
        let totalEstimation = 0;
        items.each(function() {
            const qty = parseFloat($(this).find('input[name="quantity[]"]').val()) || 0;
            const price = parseFloat($(this).find('input[name="price[]"]').val()) || 0;
            totalEstimation += (qty * price);
        });
        $('#kt_gr_total_estimation').text(totalEstimation.toLocaleString('id-ID'));

        const emptyState = $('#gr_items_empty');
        if (count === 0) {
            emptyState.removeClass('d-none');
            $('#kt_gr_submit').attr('disabled', true);
        } else {
            emptyState.addClass('d-none');
            $('#kt_gr_submit').attr('disabled', false);
        }
    };

    var handleSubmit = function() {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            submitButton.setAttribute('data-kt-indicator', 'on');
            submitButton.disabled = true;

            setTimeout(function() {
                submitButton.removeAttribute('data-kt-indicator');
                submitButton.disabled = false;

                Swal.fire({
                    text: "Nota Penerimaan Barang berhasil disimpan dan stok telah diperbarui!",
                    icon: "success",
                    buttonsStyling: false,
                    confirmButtonText: "Selesai",
                    customClass: { confirmButton: "btn btn-primary" }
                }).then(function (result) {
                    if (result.isConfirmed) {
                        window.location.href = hostUrl + 'inventory/goods-receipt';
                    }
                });
            }, 1500);
        });
    };

    return {
        init: function () {
            form = document.querySelector('#kt_gr_form');
            submitButton = document.querySelector('#kt_gr_submit');
            if (!form) return;

            handleInvoiceUpload();
            initFlatpickr();
            handleAddByPR();
            handleAddManual();
            handleSubmit();
            
            $('#kt_gr_add_by_pr, #gr_supplier_id').select2();
        }
    };
}();

KTUtil.onDOMContentLoaded(function () { TKAppInventoryGRForm.init(); });
