"use strict";

var TKAppSalesCreate = function () {
    var form;
    var itemsContainer;
    var itemTemplate;
    var itemIndex = 0;

    var initForm = function () {
        $('#sale_date_picker').flatpickr({
            altInput: true,
            altFormat: "d F Y",
            dateFormat: "Y-m-d",
        });

        $('#add_item_btn').on('click', function() {
            addItemRow();
        });

        addItemRow(); // Add one initial row
    };

    var addItemRow = function() {
        var newRow = itemTemplate.clone();
        newRow.removeClass('d-none');
        newRow.removeAttr('id');
        
        var html = newRow.html().replace(/INDEX/g, itemIndex);
        newRow.html(html);
        
        itemsContainer.append(newRow);

        initRowPlugins(newRow);
        itemIndex++;
        calculateTotal();
    };

    var initRowPlugins = function(row) {
        var productSelect = row.find('[data-kt-element="product-select"]');
        var lotSelect = row.find('[data-kt-element="lot-select"]');
        var productIdInput = row.find('[data-kt-element="product-id"]');
        var quantityInput = row.find('[data-kt-element="quantity"]');
        var priceInput = row.find('[data-kt-element="unit-price"]');
        var unitDisplay = row.find('[data-kt-element="unit-display"]');
        var stockDisplay = row.find('[data-kt-element="stock-display"]');

        productSelect.select2({
            ajax: {
                url: hostUrl + 'inventory/ajax/sales/search-products',
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return { q: params.term };
                },
                processResults: function (data) {
                    return { results: data.results };
                },
                cache: true
            },
            minimumInputLength: 1,
            dropdownParent: row
        }).on('select2:select', function(e) {
            var data = e.params.data;
            productIdInput.val(data.id);
            unitDisplay.text(data.base_unit);
            
            // Clear and populate Lots
            lotSelect.empty().append('<option></option>');
            data.lots.forEach(lot => {
                lotSelect.append(`<option value="${lot.id}" data-stock="${lot.remaining_quantity}">${lot.identifier} (Sisa: ${lot.remaining_quantity})</option>`);
            });
            lotSelect.trigger('change');
            
            // Default price from last lot? Or just keep 0
            if (data.lots.length > 0) {
                // priceInput.val(data.lots[0].unit_cost); 
            }
        });

        lotSelect.on('change', function() {
            var selected = lotSelect.find('option:selected');
            var stock = selected.data('stock') || 0;
            stockDisplay.text(stock + ' ' + unitDisplay.text());
            quantityInput.attr('max', stock);
        });

        quantityInput.add(priceInput).on('input', function() {
            calculateRowTotal(row);
        });

        row.find('[data-kt-element="remove-item"]').on('click', function() {
            row.remove();
            calculateTotal();
        });
    };

    var calculateRowTotal = function(row) {
        var qty = parseFloat(row.find('[data-kt-element="quantity"]').val()) || 0;
        var price = parseFloat(row.find('[data-kt-element="unit-price"]').val()) || 0;
        var total = qty * price;
        row.find('[data-kt-element="row-total"]').text(total.toLocaleString('id-ID'));
        calculateTotal();
    };

    var calculateTotal = function() {
        var total = 0;
        $('[data-kt-element="row-total"]').each(function() {
            var rowTotal = parseFloat($(this).text().replace(/\./g, '').replace(/,/g, '.')) || 0;
            total += rowTotal;
        });

        $('#summary_subtotal').text(total.toLocaleString('id-ID'));
        $('#summary_total').text(total.toLocaleString('id-ID'));
    };

    var handleCustomerModal = function() {
        $('#kt_modal_add_customer_form').on('submit', function(e) {
            e.preventDefault();
            var submitBtn = $('#kt_modal_add_customer_submit');
            submitBtn.attr('data-kt-indicator', 'on');

            $.ajax({
                url: hostUrl + 'inventory/ajax/sales/customer/store',
                type: 'POST',
                data: $(this).serialize(),
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                success: function(res) {
                    submitBtn.removeAttr('data-kt-indicator');
                    if (res.success) {
                        var newOption = new Option(res.customer.name, res.customer.id, true, true);
                        $('#customer_select').append(newOption).trigger('change');
                        $('#kt_modal_add_customer').modal('hide');
                        $('#kt_modal_add_customer_form')[0].reset();
                    }
                },
                error: function() {
                    submitBtn.removeAttr('data-kt-indicator');
                }
            });
        });
    };

    var handleSubmit = function() {
        form.on('submit', function(e) {
            e.preventDefault();
            var submitBtn = $('#kt_sales_submit');
            
            // Basic validation
            if (!$('#customer_select').val()) {
                Swal.fire('Error', 'Silakan pilih pelanggan.', 'error');
                return;
            }

            submitBtn.attr('data-kt-indicator', 'on');
            submitBtn.prop('disabled', true);

            $.ajax({
                url: hostUrl + 'inventory/sales/store',
                type: 'POST',
                data: form.serialize(),
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                success: function(res) {
                    if (res.success) {
                        Swal.fire({
                            text: res.message,
                            icon: "success",
                            buttonsStyling: false,
                            confirmButtonText: "Ok, mengerti!",
                            customClass: { confirmButton: "btn btn-primary" }
                        }).then(function() {
                            window.location.href = res.redirect;
                        });
                    } else {
                        Swal.fire('Gagal', res.message, 'error');
                        submitBtn.removeAttr('data-kt-indicator');
                        submitBtn.prop('disabled', false);
                    }
                },
                error: function(xhr) {
                    var msg = xhr.responseJSON ? xhr.responseJSON.message : 'Terjadi kesalahan.';
                    Swal.fire('Error', msg, 'error');
                    submitBtn.removeAttr('data-kt-indicator');
                    submitBtn.prop('disabled', false);
                }
            });
        });
    };

    return {
        init: function () {
            form = $('#kt_sales_create_form');
            itemsContainer = $('#items_container');
            itemTemplate = $('#item_template');

            initForm();
            handleCustomerModal();
            handleSubmit();
        }
    };
}();

KTUtil.onDOMContentLoaded(function () {
    TKAppSalesCreate.init();
});
