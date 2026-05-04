"use strict";

var TKAppSalesPOS = function () {
    var cart = [];
    var activeProduct = null;
    var editCartIndex = null;

    var saveCart = function() {
        const data = {
            cart: cart,
            customer_id: $('#pos_customer_select').val(),
            sale_date: $('#pos_sale_date').val()
        };
        localStorage.setItem('pos_cart_data', JSON.stringify(data));
    };

    var loadCart = function() {
        const savedData = localStorage.getItem('pos_cart_data');
        if (savedData) {
            try {
                const data = JSON.parse(savedData);
                cart = data.cart || [];
                
                if (data.customer_id) {
                    $('#pos_customer_select').val(data.customer_id).trigger('change');
                }
                
                if (data.sale_date) {
                    $('#pos_sale_date').val(data.sale_date);
                }
                
                renderCart();
            } catch (e) {
                console.error("Error loading cart from localStorage", e);
            }
        }
    };

    var initPOS = function () {
        var activeCategory = 'all';
        var activeTags = [];

        // Initialize Flatpickr
        $("#pos_sale_date").flatpickr({
            dateFormat: "Y-m-d",
            defaultDate: "today",
            onChange: function() { saveCart(); }
        });

        // Mobile Cart Toggles
        $('#kt_pos_mobile_cart_toggle').on('click', function() {
            $('#kt_pos_order').removeClass('mobile-hidden').addClass('mobile-visible');
            $(this).fadeOut();
        });

        $('#kt_pos_mobile_cart_close').on('click', function() {
            $('#kt_pos_order').removeClass('mobile-visible').addClass('mobile-hidden');
            $('#kt_pos_mobile_cart_toggle').fadeIn();
        });

        var filterProducts = function() {
            var searchVal = $('#pos_product_search').val().toLowerCase();
            
            $('.product-card').each(function() {
                var name = $(this).data('name');
                var sku = $(this).data('sku');
                var catId = $(this).data('category-id').toString();
                var tags = $(this).data('tags') || [];
                
                var matchesSearch = name.includes(searchVal) || sku.includes(searchVal);
                var matchesCategory = activeCategory === 'all' || catId === activeCategory;
                
                var matchesTags = true;
                if (activeTags.length > 0) {
                    matchesTags = activeTags.every(tag => tags.includes(tag));
                }
                
                if (matchesSearch && matchesCategory && matchesTags) {
                    $(this).removeClass('d-none');
                } else {
                    $(this).addClass('d-none');
                }
            });
        };

        // Search Logic
        $('#pos_product_search').on('keyup', function() {
            filterProducts();
        });

        // Category Filter Logic
        $('#pos_category_filter').on('change', function() {
            activeCategory = $(this).val().toString();
            filterProducts();
        });

        // Tags Filter Logic
        $('#pos_tags_filter').on('change', function() {
            activeTags = $(this).val() || [];
            filterProducts();
        });

        // Product Selection
        $('.product-item').on('click', function() {
            var data = $(this).data();
            editCartIndex = null; // Reset edit mode
            openLotModal(data);
        });

        // Customer Selection Logic - Re-render cart to update warnings
        $('#pos_customer_select').on('change', function() {
            renderCart();
            saveCart();
        });

        $('#confirm_add_to_cart').on('click', function() {
            var lotId = $('#lot_select_dropdown').val();
            var lotName = $('#lot_select_dropdown option:selected').text().split(' (')[0];
            var qty = parseFloat($('#lot_qty_input').val());
            var price = parseFloat($('#lot_price_input').val());
            var orderRef = $('#modal_order_select').val();
            var orderText = $('#modal_order_select option:selected').text();

            if (qty <= 0) return Swal.fire('Error', 'Jumlah harus lebih dari 0', 'error');

            var itemData = {
                product_id: activeProduct.id,
                product_name: activeProduct.name,
                lot_id: lotId,
                lot_name: lotName,
                quantity: qty,
                unit_price: price,
                unit: activeProduct.unit,
                order_reference: orderRef,
                order_text: orderRef ? orderText : null
            };

            if (editCartIndex !== null) {
                cart[editCartIndex] = itemData;
                editCartIndex = null;
            } else {
                addToCart(itemData);
            }

            $('#kt_modal_select_lot').modal('hide');
            renderCart();
            saveCart();
        });

        $('#clear_cart').on('click', function() {
            cart = [];
            renderCart();
            saveCart();
        });

        $('#process_payment').on('click', function() {
            handlePayment();
        });

        loadCart();
    };

    var openLotModal = function(data, existingItem = null) {
        if (!data.lots || data.lots.length === 0) {
            return Swal.fire('Stok Kosong', 'Produk ini tidak memiliki stok yang tersedia (Lot aktif).', 'warning');
        }

        activeProduct = data;
        
        $('#modal_product_name').text(data.name);
        $('#modal_unit_text').text(data.unit);
        
        var lotDropdown = $('#lot_select_dropdown');
        lotDropdown.empty();
        data.lots.forEach(lot => {
            lotDropdown.append(`<option value="${lot.id}" data-stock="${lot.stock}">${lot.identifier} (Sisa: ${lot.stock})</option>`);
        });

        if (existingItem) {
            lotDropdown.val(existingItem.lot_id);
            $('#lot_qty_input').val(existingItem.quantity);
            $('#lot_price_input').val(existingItem.unit_price);
            
            if (existingItem.order_reference) {
                var newOption = new Option(existingItem.order_text, existingItem.order_reference, true, true);
                $('#modal_order_select').append(newOption).trigger('change');
            } else {
                $('#modal_order_select').val(null).trigger('change');
            }
        } else {
            $('#lot_qty_input').val(1).attr('max', data.lots[0].stock);
            $('#lot_price_input').val(0);
            $('#modal_order_select').val(null).trigger('change');
        }

        // Order Selection in Modal
        if (!$('#modal_order_select').hasClass("select2-hidden-accessible")) {
            $('#modal_order_select').select2({
                dropdownParent: $('#kt_modal_select_lot'),
                width: '100%',
                ajax: {
                    url: $('#modal_order_select').data('ajax-url'),
                    dataType: 'json',
                    delay: 250,
                    data: params => ({ q: params.term }),
                    processResults: data => ({ results: data.results })
                }
            });
        }

        $('#modal_order_container').removeClass('d-none');
        $('#kt_modal_select_lot').modal('show');
    };

    var addToCart = function(item) {
        // Check if same product & lot & order already in cart
        var existing = cart.find(i => i.product_id == item.product_id && i.lot_id == item.lot_id && i.order_reference == item.order_reference);
        if (existing) {
            existing.quantity += item.quantity;
        } else {
            cart.push(item);
        }
        renderCart();
    };

    var renderCart = function() {
        var container = $('#cart_items');
        var mobileCountEl = $('#mobile_cart_count');
        container.empty();
        
        const customerType = $('#pos_customer_select').find('option:selected').data('type');

        if (cart.length === 0) {
            $('#empty_cart_msg').show().removeClass('d-none');
            $('#pos_total').text('0');
            mobileCountEl.text('0');
            return;
        }

        $('#empty_cart_msg').hide().addClass('d-none');
        var total = 0;
        var totalQty = 0;

        cart.forEach((item, index) => {
            var subtotal = item.quantity * item.unit_price;
            total += subtotal;
            totalQty += item.quantity;
            
            let badges = `<span class="badge badge-light fw-semibold fs-9">Lot: ${item.lot_name}</span>`;
            if (item.order_text) {
                badges += `<span class="badge badge-light-primary fw-semibold fs-9">Ref: ${item.order_text}</span>`;
            } else if (customerType === 'internal') {
                badges += `<span class="badge badge-light-danger fw-semibold fs-9 animation-blink">⚠️ Order Belum Dipilih</span>`;
            }

            container.append(`
                <div class="d-flex flex-stack py-4 cart-item ${(!item.order_text && customerType === 'internal') ? 'bg-light-danger bg-opacity-10 px-2 rounded' : ''}">
                    <div class="d-flex flex-column me-3">
                        <span class="text-gray-800 fw-bold fs-6">${item.product_name}</span>
                        <div class="d-flex flex-wrap gap-2 mt-1">
                            ${badges}
                        </div>
                    </div>
                    <div class="d-flex align-items-center">
                        <div class="text-end me-5">
                            <div class="fw-bold fs-6 text-gray-800">${item.quantity} ${item.unit}</div>
                            <div class="text-muted fs-8">@ Rp ${item.unit_price.toLocaleString('id-ID')}</div>
                        </div>
                        <div class="d-flex gap-1">
                            <button class="btn btn-icon btn-sm btn-light-primary edit-cart-item" data-index="${index}"><i class="ki-duotone ki-pencil fs-3"><span class="path1"></span><span class="path2"></span></i></button>
                            <button class="btn btn-icon btn-sm btn-light-danger remove-from-cart" data-index="${index}"><i class="ki-duotone ki-trash fs-3"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span></i></button>
                        </div>
                    </div>
                </div>
            `);
        });

        $('#pos_total').text(total.toLocaleString('id-ID'));
        mobileCountEl.text(totalQty);

        $('.remove-from-cart').on('click', function() {
            var idx = $(this).data('index');
            cart.splice(idx, 1);
            renderCart();
            saveCart();
        });

        $('.edit-cart-item').on('click', function() {
            var idx = $(this).data('index');
            var item = cart[idx];
            editCartIndex = idx;
            
            // Find product data from the grid
            var productEl = $(`.product-item[data-id="${item.product_id}"]`);
            if (productEl.length > 0) {
                openLotModal(productEl.data(), item);
            } else {
                Swal.fire('Error', 'Data produk tidak ditemukan di halaman ini.', 'error');
            }
        });
    };

    var handlePayment = function() {
        if (cart.length === 0) return Swal.fire('Oops', 'Keranjang masih kosong.', 'warning');
        
        const customerSelect = $('#pos_customer_select');
        const customerId = customerSelect.val();
        if (!customerId) return Swal.fire('Oops', 'Silakan pilih pelanggan.', 'warning');

        const customerType = customerSelect.find('option:selected').data('type');
        
        // VALIDATION: If internal, all items MUST have order_reference
        if (customerType === 'internal') {
            const missingRefs = cart.filter(item => !item.order_reference);
            if (missingRefs.length > 0) {
                return Swal.fire({
                    title: 'Data Belum Lengkap',
                    text: `Terdapat ${missingRefs.length} item yang belum memiliki Referensi Order. Pelanggan internal mewajibkan referensi order untuk setiap item.`,
                    icon: 'warning',
                    confirmButtonText: 'Saya Lengkapi'
                });
            }
        }

        Swal.fire({
            title: 'Konfirmasi Pembayaran',
            text: "Pastikan jumlah dan barang sudah benar.",
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, Bayar Sekarang',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                submitSale();
            }
        });
    };

    var submitSale = function() {
        var btn = $('#process_payment');
        btn.attr('data-kt-indicator', 'on').prop('disabled', true);

        var data = {
            customer_id: $('#pos_customer_select').val(),
            sale_date: $('#pos_sale_date').val(),
            payment_method: 'Cash',
            items: cart
        };

        $.ajax({
            url: hostUrl + 'sales/store',
            type: 'POST',
            data: data,
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            success: function(res) {
                if (res.success) {
                    localStorage.removeItem('pos_cart_data'); // Clear storage
                    
                    Swal.fire({
                        title: 'Berhasil!',
                        text: 'Penjualan telah disimpan. Apakah Anda ingin mencetak nota?',
                        icon: 'success',
                        showCancelButton: true,
                        confirmButtonText: '<i class="ki-duotone ki-printer fs-2 me-2"></i> Cetak Nota',
                        cancelButtonText: 'Tutup (Selesai)',
                        customClass: {
                            confirmButton: "btn btn-primary",
                            cancelButton: "btn btn-light"
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Open print window
                            window.open(hostUrl + 'sales/print/' + res.sale_id, '_blank');
                        }
                        window.location.reload();
                    });
                } else {
                    Swal.fire('Gagal', res.message, 'error');
                    btn.removeAttr('data-kt-indicator').prop('disabled', false);
                }
            },
            error: function(xhr) {
                var msg = xhr.responseJSON ? xhr.responseJSON.message : 'Terjadi kesalahan.';
                Swal.fire('Error', msg, 'error');
                btn.removeAttr('data-kt-indicator').prop('disabled', false);
            }
        });
    };

    var handleCustomerModal = function() {
        $('#kt_modal_add_customer_form').on('submit', function(e) {
            e.preventDefault();
            $.ajax({
                url: hostUrl + 'ajax/sales/customer/store',
                type: 'POST',
                data: $(this).serialize(),
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                success: function(res) {
                    if (res.success) {
                        var newOption = new Option(res.customer.name, res.customer.id, true, true);
                        $('#pos_customer_select').append(newOption).trigger('change');
                        $('#kt_modal_add_customer').modal('hide');
                        $('#kt_modal_add_customer_form')[0].reset();
                    }
                }
            });
        });
    };

    return {
        init: function () {
            initPOS();
            handleCustomerModal();
        }
    };
}();

KTUtil.onDOMContentLoaded(function () {
    TKAppSalesPOS.init();
});
