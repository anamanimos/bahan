"use strict";

var TKAppInventoryPRForm = function () {
    var form;
    var container;
    var addButton;
    var grandTotalEl;
    var itemCount = 1;
    var activeSelect;

    var initRepeater = function () {
        container = document.getElementById('kt_pr_items_container');
        addButton = document.getElementById('kt_pr_add_item');
        grandTotalEl = document.getElementById('kt_pr_grand_total');

        if (!container || !addButton) return;

        addButton.addEventListener('click', function () {
            itemCount++;
            const items = container.querySelectorAll('[data-kt-element="item"]');
            const newItem = items[0].cloneNode(true);
            
            const collapseTrigger = newItem.querySelector('[data-bs-toggle="collapse"]');
            const collapseBody = newItem.querySelector('.collapse');
            const collapseId = `kt_pr_item_collapse_${itemCount}`;
            
            if(collapseTrigger && collapseBody) {
                collapseTrigger.setAttribute('data-bs-target', `#${collapseId}`);
                collapseBody.setAttribute('id', collapseId);
                if(window.innerWidth < 768) {
                    container.querySelectorAll('.collapse.show').forEach(el => $(el).collapse('hide'));
                    $(collapseBody).collapse('show');
                }
            }

            const indexBadge = newItem.querySelector('.badge-circle');
            if(indexBadge) indexBadge.innerText = itemCount;

            // Clear Product Select2
            const clonedProductSelect = newItem.querySelector('[data-kt-element="item-name"]');
            $(clonedProductSelect).next('.select2-container').remove();
            $(clonedProductSelect).removeClass('select2-hidden-accessible').removeAttr('data-select2-id').empty();
            
            // Clear Supplier Select2
            const clonedSupplierSelect = newItem.querySelector('[data-kt-element="item-supplier"]');
            $(clonedSupplierSelect).next('.select2-container').remove();
            $(clonedSupplierSelect).removeClass('select2-hidden-accessible').removeAttr('data-select2-id').empty();

            // Clear Order Select2
            const clonedOrderSelect = newItem.querySelector('[data-kt-element="order-select"]');
            $(clonedOrderSelect).next('.select2-container').remove();
            $(clonedOrderSelect).removeClass('select2-hidden-accessible').removeAttr('data-select2-id').empty();
            const orderContainer = newItem.querySelector('[data-kt-element="order-container"]');
            orderContainer.classList.add('d-none');
            
            newItem.querySelectorAll('input').forEach(input => input.value = '');
            newItem.querySelector('[data-kt-element="item-summary"]').innerText = 'Bahan Baru';
            newItem.querySelector('[data-kt-element="total"]').innerText = '0';
            
            container.appendChild(newItem);
            initProductSelect2(clonedProductSelect);
            initSupplierSelect2(clonedSupplierSelect);
            initOrderSelect2(clonedOrderSelect);
            reinitializeEvents();
        });

        container.querySelectorAll('[data-kt-element="item-name"]').forEach(select => initProductSelect2(select));
        container.querySelectorAll('[data-kt-element="item-supplier"]').forEach(select => initSupplierSelect2(select));
        container.querySelectorAll('[data-kt-element="order-select"]').forEach(select => initOrderSelect2(select));

        reinitializeEvents();
    };

    var initProductSelect2 = function (element) {
        $(element).select2({
            ajax: {
                url: hostUrl + "ajax/inventory/stocks/search_products",
                dataType: 'json',
                delay: 250,
                data: function (params) { return { q: params.term }; },
                processResults: function (data, params) {
                    if (params.term) data.results.unshift({ id: 'ADD_NEW_PRODUCT', text: params.term, isNew: true });
                    return { results: data.results };
                },
                cache: true
            },
            templateResult: function(data) {
                if (data.isNew) return $(`<div class="d-flex align-items-center justify-content-between"><span><span class="text-muted fs-8">Tambah:</span> <strong>${data.text}</strong></span><span class="badge badge-light-primary fs-9">Baru</span></div>`);
                return data.text;
            },
            placeholder: "Cari Bahan...",
            allowClear: true
        }).on('select2:select', function (e) {
            const data = e.params.data;
            if (data.id === 'ADD_NEW_PRODUCT') {
                activeSelect = element;
                document.getElementById('quick_add_product_name').value = data.text;
                $('#kt_modal_add_product_quick').modal('show');
                $(element).val(null).trigger('change');
            } else {
                const summary = $(this).closest('[data-kt-element="item"]').find('[data-kt-element="item-summary"]');
                summary.text(data.text);
            }
        });
    };

    var initSupplierSelect2 = function (element) {
        $(element).select2({
            tags: true,
            ajax: {
                url: hostUrl + "ajax/inventory/stocks/search_suppliers",
                dataType: 'json',
                delay: 250,
                data: function (params) { return { q: params.term }; },
                processResults: function (data) { return { results: data.results }; },
                cache: true
            },
            placeholder: "Cari Toko...",
            allowClear: true
        });
    };

    var initOrderSelect2 = function (element) {
        $(element).select2({
            ajax: {
                url: hostUrl + "ajax/inventory/stocks/search_orders",
                dataType: 'json',
                delay: 250,
                data: function (params) { return { q: params.term }; },
                processResults: function (data) { return { results: data.results }; },
                cache: true
            },
            placeholder: "Pilih Order...",
            allowClear: true
        });
    };

    var initQuickAddModal = function() {
        const submitBtn = document.getElementById('kt_modal_add_product_quick_submit');
        const modal = document.getElementById('kt_modal_add_product_quick');
        if(!submitBtn) return;
        submitBtn.addEventListener('click', function() {
            const name = document.getElementById('quick_add_product_name').value;
            const category = document.getElementById('quick_add_product_category').value;
            if(!name) {
                Swal.fire({ text: "Nama produk wajib diisi", icon: "error", buttonsStyling: false, confirmButtonText: "Ok", customClass: { confirmButton: "btn btn-primary" }});
                return;
            }
            submitBtn.setAttribute('data-kt-indicator', 'on');
            setTimeout(() => {
                submitBtn.removeAttribute('data-kt-indicator');
                const newOption = new Option(name, "new_" + name, true, true);
                $(activeSelect).append(newOption).trigger('change');
                $(activeSelect).trigger({ type: 'select2:select', params: { data: { id: "new_" + name, text: name } } });
                $(modal).modal('hide');
                document.getElementById('quick_add_product_name').value = '';
                Swal.fire({ text: "Produk berhasil ditambahkan ke daftar pengajuan", icon: "success", buttonsStyling: false, confirmButtonText: "Ok", customClass: { confirmButton: "btn btn-primary" }});
            }, 1000);
        });
    };

    var reinitializeEvents = function () {
        // Handle Context Type Change
        container.querySelectorAll('[data-kt-element="context-type"]').forEach(select => {
            select.onchange = function() {
                const item = select.closest('[data-kt-element="item"]');
                const orderContainer = item.querySelector('[data-kt-element="order-container"]');
                if (select.value === 'Order') {
                    orderContainer.classList.remove('d-none');
                } else {
                    orderContainer.classList.add('d-none');
                }
            };
        });

        // Handle Remove Item
        container.querySelectorAll('[data-kt-element="remove-item"]').forEach(button => {
            button.onclick = function () {
                const items = container.querySelectorAll('[data-kt-element="item"]');
                if(items.length > 1) {
                    button.closest('[data-kt-element="item"]').remove();
                    calculateGrandTotal();
                } else {
                    Swal.fire({ text: "Minimal harus ada satu item dalam pengajuan.", icon: "warning", buttonsStyling: false, confirmButtonText: "Ok", customClass: { confirmButton: "btn btn-primary" } });
                }
            };
        });

        // Handle Calculations
        container.querySelectorAll('[data-kt-element="item"]').forEach(item => {
            const qtyInput = item.querySelector('[data-kt-element="quantity"]');
            const priceInput = item.querySelector('[data-kt-element="price"]');
            const totalEl = item.querySelector('[data-kt-element="total"]');
            const calculate = () => {
                const qty = parseFloat(qtyInput.value) || 0;
                const price = parseFloat(priceInput.value.replace(/,/g, '')) || 0;
                const total = qty * price;
                totalEl.innerText = total.toLocaleString('id-ID');
                calculateGrandTotal();
            };
            qtyInput.oninput = calculate;
            priceInput.oninput = calculate;
        });
    };

    var calculateGrandTotal = function () {
        let grandTotal = 0;
        container.querySelectorAll('[data-kt-element="total"]').forEach(el => {
            grandTotal += parseFloat(el.innerText.replace(/\./g, '').replace(/,/g, '')) || 0;
        });
        grandTotalEl.innerText = grandTotal.toLocaleString('id-ID');
    };

    var initFormSubmit = function () {
        form = document.getElementById('kt_pr_form');
        if (!form) return;
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            const submitButton = document.getElementById('kt_pr_submit');
            submitButton.setAttribute('data-kt-indicator', 'on');
            submitButton.disabled = true;
            $.post(hostUrl + 'ajax/inventory/stocks/submit_pr', $(form).serialize(), function (res) {
                submitButton.removeAttribute('data-kt-indicator');
                submitButton.disabled = false;
                if (res.status === 'success') {
                    Swal.fire({ text: res.message, icon: "success", buttonsStyling: false, confirmButtonText: "Ok, mengerti!", customClass: { confirmButton: "btn btn-primary" } }).then(function () {
                        window.location.href = hostUrl + 'inventory/purchase-requisition';
                    });
                }
            });
        });
    };

    return { init: function () { initRepeater(); initQuickAddModal(); initFormSubmit(); } };
}();

KTUtil.onDOMContentLoaded(function () { TKAppInventoryPRForm.init(); });
