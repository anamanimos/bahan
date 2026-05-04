"use strict";

var TKAppPurchaseRequisitionCreate = function () {
    var form;
    var submitButton;
    var container;
    var template;
    var activeSelectElement = null;

    window.openQuickAddProduct = function(btn) {
        $('select[data-kt-element="item-name"]').select2('close');
        $('#kt_modal_add_product_quick').modal('show');
    };

    window.openQuickAddSupplier = function(btn) {
        $('select[data-kt-element="item-supplier"]').select2('close');
        $('#modal_quick_add_supplier').modal('show');
    };

    var initSelect2 = function (element) {
        // Initialize Product Select2
        $(element).find('[data-kt-element="item-name"]').select2({
            ajax: {
                url: hostUrl + "inventory/ajax/goods-receipt/search-products",
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
            language: {
                noResults: function() {
                    return `<a href="#" class="btn btn-sm btn-light-primary w-100" onclick="window.openQuickAddProduct(this)">+ Tambah Produk Baru</a>`;
                }
            },
            escapeMarkup: function (markup) {
                return markup;
            }
        }).on('select2:select', function (e) {
            var data = e.params.data;
            $(element).find('[data-kt-element="unit-label"]').text(data.base_unit || 'Pcs');
            updateSummary(element);
        }).on('select2:open', function() {
            activeSelectElement = this;
        });

        // Initialize Supplier Select2
        $(element).find('[data-kt-element="item-supplier"]').select2({
            ajax: {
                url: hostUrl + "inventory/ajax/goods-receipt/search-suppliers",
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return { q: params.term };
                },
                processResults: function (data) {
                    var results = data.map(function(item) {
                        return { id: item.id, text: item.name };
                    });
                    return { results: results };
                },
                cache: true
            },
            minimumInputLength: 1,
            language: {
                noResults: function() {
                    return `<a href="#" class="btn btn-sm btn-light-primary w-100" onclick="window.openQuickAddSupplier(this)">+ Tambah Toko Baru</a>`;
                }
            },
            escapeMarkup: function (markup) {
                return markup;
            }
        }).on('select2:open', function() {
            activeSelectElement = this;
        });

        // Context type change
        $(element).find('[data-kt-element="context-type"]').select2({
            minimumResultsForSearch: -1
        }).on('change', function() {
            var val = $(this).val();
            var orderContainer = $(element).find('[data-kt-element="order-container"]');
            var orderSelect = $(element).find('[data-kt-element="order-ref"]');
            if(val === 'Order') {
                orderContainer.removeClass('d-none');
                orderSelect.attr('required', true);
            } else {
                orderContainer.addClass('d-none');
                orderSelect.removeAttr('required');
                orderSelect.val(null).trigger('change');
            }
        });

        // Initialize Order Select2
        $(element).find('[data-kt-element="order-ref"]').select2({
            ajax: {
                url: $(element).find('[data-kt-element="order-ref"]').data('ajax-url'),
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
            minimumInputLength: 1
        });
    };

    var updateSummary = function(element) {
        var productName = $(element).find('[data-kt-element="item-name"] option:selected').text();
        if(!productName) productName = 'Pilih Bahan...';
        $(element).find('[data-kt-element="item-summary"]').text(productName);
    };

    var calculateTotal = function() {
        var grandTotal = 0;
        var items = container.querySelectorAll('[data-kt-element="item"]');
        
        items.forEach(function(item, index) {
            $(item).find('[data-kt-element="item-index"]').text(index + 1);
            
            var qty = parseFloat($(item).find('[data-kt-element="quantity"]').val()) || 0;
            var price = parseFloat($(item).find('[data-kt-element="price"]').val()) || 0;
            var total = qty * price;
            
            $(item).find('[data-kt-element="total"]').text(total.toLocaleString('id-ID'));
            $(item).find('[data-kt-element="total-desktop"]').text(total.toLocaleString('id-ID'));
            
            grandTotal += total;
        });
        
        document.getElementById('kt_pr_grand_total').innerText = grandTotal.toLocaleString('id-ID');
    };

    var handleRepeater = function () {
        container = document.getElementById('kt_pr_items_container');
        var firstItem = container.querySelector('[data-kt-element="item"]');
        template = firstItem.cloneNode(true);
        
        // Remove select2 from template
        $(template).find('.select2-container').remove();
        $(template).find('select').removeClass('select2-hidden-accessible').removeAttr('data-select2-id tabindex aria-hidden');
        $(template).find('option').removeAttr('data-select2-id');

        initSelect2(firstItem);

        // Add Item
        document.getElementById('kt_pr_add_item').addEventListener('click', function(e) {
            e.preventDefault();
            var newItem = template.cloneNode(true);
            
            // Clear inputs
            var inputs = newItem.querySelectorAll('input, select');
            inputs.forEach(function(input) {
                if(input.tagName === 'INPUT') input.value = '';
                if(input.tagName === 'SELECT') {
                    $(input).val(null).empty().append('<option></option>');
                }
            });
            
            // Re-add options for context_type
            $(newItem).find('[data-kt-element="context-type"]').html('<option value="Stock">Stok</option><option value="Order">Order</option>');
            $(newItem).find('[data-kt-element="order-container"]').addClass('d-none');
            $(newItem).find('[data-kt-element="order-ref"]').removeAttr('required');

            var index = container.querySelectorAll('[data-kt-element="item"]').length;
            var collapseId = 'kt_pr_item_collapse_' + index;
            $(newItem).find('[data-bs-toggle="collapse"]').attr('data-bs-target', '#' + collapseId);
            $(newItem).find('.collapse').attr('id', collapseId).addClass('show');

            container.appendChild(newItem);
            initSelect2(newItem);
            calculateTotal();
        });

        // Remove Item & Input changes
        container.addEventListener('click', function(e) {
            var removeBtn = e.target.closest('[data-kt-element="remove-item"]');
            if (removeBtn) {
                e.preventDefault();
                var item = removeBtn.closest('[data-kt-element="item"]');
                if (container.querySelectorAll('[data-kt-element="item"]').length > 1) {
                    Swal.fire({
                        text: "Yakin ingin menghapus item ini?",
                        icon: "warning",
                        showCancelButton: true,
                        buttonsStyling: false,
                        confirmButtonText: "Ya, hapus!",
                        cancelButtonText: "Tidak",
                        customClass: {
                            confirmButton: "btn btn-primary",
                            cancelButton: "btn btn-active-light"
                        }
                    }).then(function (result) {
                        if (result.value) {
                            item.remove();
                            calculateTotal();
                        }
                    });
                } else {
                    Swal.fire({ text: "Minimal harus ada 1 item.", icon: "info", buttonsStyling: false, confirmButtonText: "Ok, mengerti!", customClass: { confirmButton: "btn btn-primary" } });
                }
            }
        });

        $(container).on('input', 'input', function() {
            calculateTotal();
        });
    };

    var handleFormSubmit = function () {
        form = document.getElementById('kt_pr_form');
        submitButton = document.getElementById('kt_pr_submit');

        form.addEventListener('submit', function (e) {
            e.preventDefault();

            // validation
            var items = container.querySelectorAll('[data-kt-element="item"]');
            var valid = true;
            items.forEach(function(item) {
                var product = $(item).find('[name="product_id[]"]').val();
                var supplier = $(item).find('[name="supplier_id[]"]').val();
                var qty = $(item).find('[name="quantity[]"]').val();
                
                if (!product || !supplier || !qty || parseFloat(qty) <= 0) {
                    valid = false;
                }
            });

            if(!valid) {
                Swal.fire({ text: "Mohon lengkapi Produk, Toko, dan Jumlah di semua baris.", icon: "error", buttonsStyling: false, confirmButtonText: "Ok, mengerti!", customClass: { confirmButton: "btn btn-primary" } });
                return;
            }

            submitButton.setAttribute('data-kt-indicator', 'on');
            submitButton.disabled = true;

            $.ajax({
                url: form.action,
                type: 'POST',
                data: $(form).serialize(),
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
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
                        }).then(function () {
                            window.location.href = hostUrl + 'inventory/purchase-requisition';
                        });
                    } else {
                        Swal.fire({ text: response.message || "Gagal menyimpan pengajuan.", icon: "error", buttonsStyling: false, confirmButtonText: "Ok, mengerti!", customClass: { confirmButton: "btn btn-primary" } });
                    }
                },
                error: function (xhr) {
                    submitButton.removeAttribute('data-kt-indicator');
                    submitButton.disabled = false;
                    let errorMsg = "Terjadi kesalahan pada server.";
                    if (xhr.responseJSON && xhr.responseJSON.message) errorMsg = xhr.responseJSON.message;
                    Swal.fire({ text: errorMsg, icon: "error", buttonsStyling: false, confirmButtonText: "Ok, mengerti!", customClass: { confirmButton: "btn btn-primary" } });
                }
            });
        });
    };

    var handleQuickAddProduct = function() {
        var form = document.getElementById('kt_modal_add_product_quick_form');
        var submitBtn = document.getElementById('kt_modal_add_product_quick_submit');

        if(!form || !submitBtn) return;

        submitBtn.addEventListener('click', function(e) {
            e.preventDefault();
            
            if(!form.checkValidity()) {
                form.reportValidity();
                return;
            }

            submitBtn.setAttribute('data-kt-indicator', 'on');
            submitBtn.disabled = true;

            $.ajax({
                url: form.action,
                type: 'POST',
                data: $(form).serialize(),
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                success: function(response) {
                    submitBtn.removeAttribute('data-kt-indicator');
                    submitBtn.disabled = false;

                    if(response.success && response.product) {
                        $('#kt_modal_add_product_quick').modal('hide');
                        form.reset();
                        
                        if (activeSelectElement) {
                            var newOption = new Option(response.product.name, response.product.id, true, true);
                            $(activeSelectElement).append(newOption).trigger('change');
                            var row = $(activeSelectElement).closest('[data-kt-element="item"]');
                            row.find('[data-kt-element="unit-label"]').text(response.product.base_unit || 'Pcs');
                            updateSummary(row);
                        }
                    } else {
                        Swal.fire({ text: response.message || "Gagal menyimpan produk.", icon: "error", buttonsStyling: false, confirmButtonText: "Ok, mengerti!", customClass: { confirmButton: "btn btn-primary" } });
                    }
                },
                error: function(xhr) {
                    submitBtn.removeAttribute('data-kt-indicator');
                    submitBtn.disabled = false;
                    var msg = "Terjadi kesalahan.";
                    if(xhr.responseJSON && xhr.responseJSON.message) msg = xhr.responseJSON.message;
                    Swal.fire({ text: msg, icon: "error", buttonsStyling: false, confirmButtonText: "Ok, mengerti!", customClass: { confirmButton: "btn btn-primary" } });
                }
            });
        });
    };

    var handleQuickAddSupplier = function() {
        var form = document.getElementById('form_quick_add_supplier');
        var submitBtn = document.getElementById('btn_quick_add_supplier_submit');

        if(!form || !submitBtn) return;

        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            if(!form.checkValidity()) {
                form.reportValidity();
                return;
            }

            submitBtn.setAttribute('data-kt-indicator', 'on');
            submitBtn.disabled = true;

            $.ajax({
                url: form.action,
                type: 'POST',
                data: $(form).serialize(),
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                success: function(response) {
                    submitBtn.removeAttribute('data-kt-indicator');
                    submitBtn.disabled = false;

                    if(response.success && response.data) {
                        $('#modal_quick_add_supplier').modal('hide');
                        form.reset();
                        
                        if (activeSelectElement && $(activeSelectElement).attr('data-kt-element') === 'item-supplier') {
                            var newOption = new Option(response.data.name, response.data.id, true, true);
                            $(activeSelectElement).append(newOption).trigger('change');
                        }
                    } else {
                        Swal.fire({ text: response.message || "Gagal menyimpan supplier.", icon: "error", buttonsStyling: false, confirmButtonText: "Ok, mengerti!", customClass: { confirmButton: "btn btn-primary" } });
                    }
                },
                error: function(xhr) {
                    submitBtn.removeAttribute('data-kt-indicator');
                    submitBtn.disabled = false;
                    var msg = "Terjadi kesalahan.";
                    if(xhr.responseJSON && xhr.responseJSON.message) msg = xhr.responseJSON.message;
                    Swal.fire({ text: msg, icon: "error", buttonsStyling: false, confirmButtonText: "Ok, mengerti!", customClass: { confirmButton: "btn btn-primary" } });
                }
            });
        });
    };

    return {
        init: function () {
            handleRepeater();
            handleFormSubmit();
            handleQuickAddProduct();
            handleQuickAddSupplier();
        }
    };
}();

KTUtil.onDOMContentLoaded(function () { TKAppPurchaseRequisitionCreate.init(); });
