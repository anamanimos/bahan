"use strict";

var TKAppMasterProductList = function () {
    var table;
    var datatable;

    var initDataTable = function () {
        if (typeof DataTable !== 'undefined' && DataTable.isDataTable(table)) {
            new DataTable(table).destroy();
        }

        if (typeof DataTable === 'undefined') {
            console.error('DataTable is not defined! Please check if plugins.bundle.js is loaded correctly.');
            return;
        }

        datatable = new DataTable(table, {
            searchDelay: 500,
            processing: true,
            serverSide: true,
            stateSave: false,
            ajax: {
                url: hostUrl + "master/ajax/product/list",
                type: "POST",
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                data: function(d) {
                    d.filters = {
                        categories: $('#filter_categories').val(),
                        colors: $('#filter_colors').val(),
                    };
                },
                error: function (xhr, error, thrown) {
                    console.error('AJAX Error:', error, thrown);
                    $(table).find('tbody').html(`<tr><td colspan="7" class="text-center py-10"><div class="text-danger fw-bold">Gagal menghubungi server (HTTP ${xhr.status}).</div></td></tr>`);
                }
            },
            language: {
                processing: `<div class="d-flex flex-column align-items-center"><div class="spinner-border text-primary" role="status"></div><span class="text-muted fs-7 fw-bold mt-5">Memuat data...</span></div>`,
                zeroRecords: `<div class="d-flex flex-column flex-center">
                    <img src="${hostUrl}assets/vendors/media/illustrations/sigma-1/5.png" class="mw-200px mb-10" />
                    <div class="fs-4 fw-bolder text-gray-800 mb-2">Data Tidak Ditemukan</div>
                    <div class="fs-6 fw-bold text-gray-500">Kami tidak dapat menemukan produk yang Anda cari.</div>
                </div>`,
                emptyTable: `<div class="d-flex flex-column flex-center">
                    <img src="${hostUrl}assets/vendors/media/illustrations/sigma-1/5.png" class="mw-200px mb-10" />
                    <div class="fs-4 fw-bolder text-gray-800 mb-2">Belum Ada Produk</div>
                    <div class="fs-6 fw-bold text-gray-500">Mulai dengan menambahkan produk pertama Anda.</div>
                </div>`,
                info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                infoEmpty: "Menampilkan 0 data",
                infoFiltered: "(disaring dari _MAX_ total data)",
            },
            drawCallback: function() {
                const api = this.api();
                const pageInfo = api.page.info();
                const infoEl = $(api.table().container()).find('.dataTables_info, .dt-info');
                
                if (pageInfo.recordsDisplay === 0) {
                    infoEl.hide();
                } else {
                    infoEl.show();
                }
            },
            columns: [
                { data: 'id' },
                { data: 'name' },
                { data: 'base_unit' },
                { data: 'sku' },
                { data: 'stock' },
                { data: 'category_name' },
                { data: 'color' },
                { data: null },
            ],
            columnDefs: [
                {
                    targets: 0,
                    orderable: false,
                    render: function (data) {
                        return `<div class="form-check form-check-sm form-check-custom form-check-solid"><input class="form-check-input" type="checkbox" value="${data}" /></div>`;
                    }
                },
                {
                    targets: 1,
                    render: function (data, type, row) {
                        return `<div class="d-flex align-items-center">
                            <div class="ms-1">
                                <a href="${hostUrl}master/product/edit/${row.id}" class="text-gray-800 text-hover-primary fs-6 fw-bold" data-kt-product-filter="product_name">${data}</a>
                            </div>
                        </div>`;
                    }
                },
                {
                    targets: 2,
                    render: function (data) {
                        return `<span class="badge badge-light-secondary fw-bold text-gray-800">${data}</span>`;
                    }
                },
                {
                    targets: 4,
                    render: function (data, type, row) {
                        return `<span class="fw-bold text-gray-800">${data} ${row.base_unit}</span>`;
                    }
                },
                {
                    targets: 6,
                    render: function (data, type, row) {
                        if (!data || data === '-') return '-';
                        const hex = row.color_hex || '#e4e6ef';
                        return `<div class="d-flex align-items-center">
                            <span class="bullet bullet-dot w-15px h-15px me-3" style="background-color: ${hex}"></span>
                            <span class="text-gray-800 fw-bold">${data}</span>
                        </div>`;
                    }
                },
                {
                    targets: -1,
                    data: null,
                    orderable: false,
                    className: 'text-end',
                    render: function (data, type, row) {
                        return `
                            <a href="#" class="btn btn-sm btn-light btn-active-light-primary btn-flex btn-center" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                                Aksi <i class="ki-duotone ki-down fs-5 ms-1"></i>
                            </a>
                            <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-125px py-4" data-kt-menu="true">
                                <div class="menu-item px-3"><a href="${hostUrl}master/product/edit/${row.id}" class="menu-link px-3">Edit</a></div>
                                <div class="menu-item px-3"><a href="#" class="menu-link px-3" data-kt-product-filter="duplicate_row">Duplikat</a></div>
                                <div class="menu-item px-3"><a href="#" class="menu-link px-3 text-danger" data-kt-product-filter="delete_row">Hapus</a></div>
                            </div>`;
                    },
                },
            ],
            "stateSaveParams": function (settings, data) {
                var widths = [];
                $(table).find('thead th').each(function() {
                    widths.push($(this).outerWidth());
                });
                data.columnWidths = widths;
            },
            "stateLoadParams": function (settings, data) {
                if (data.columnWidths) {
                    this.api().columns().every(function (i) {
                        if (data.columnWidths[i]) {
                            $(this.header()).css('width', data.columnWidths[i] + 'px');
                        }
                    });
                }
            }
        });

        datatable.on('draw', function () {
            console.log('Table drawn.');
            KTMenu.createInstances();
            handleDeleteRows();
            handleDuplicateRows();
            initColumnResize();
        });

        initColumnResize();
    };

    var initColumnResize = function() {
        const ths = table.querySelectorAll('thead th:not(:last-child)');
        ths.forEach(th => {
            const existingHandle = th.querySelector('.resize-handle');
            if (existingHandle) existingHandle.remove();

            const handle = document.createElement('div');
            handle.classList.add('resize-handle');
            th.appendChild(handle);
            th.style.position = 'relative';

            let startX, startWidth;

            handle.addEventListener('mousedown', function(e) {
                e.preventDefault();
                startX = e.pageX;
                startWidth = th.offsetWidth;
                document.addEventListener('mousemove', onMouseMove);
                document.addEventListener('mouseup', onMouseUp);
                document.body.classList.add('resizing');
                handle.classList.add('resizing');
            });

            function onMouseMove(e) {
                const width = startWidth + (e.pageX - startX);
                if (width >= 60) {
                    $(th).css('width', width + 'px');
                    $(th).css('min-width', width + 'px');
                }
            }

            function onMouseUp() {
                document.removeEventListener('mousemove', onMouseMove);
                document.removeEventListener('mouseup', onMouseUp);
                document.body.classList.remove('resizing');
                handle.classList.remove('resizing');
                datatable.state.save();
            }
        });
    };

    var handleSearchDatatable = () => {
        const filterSearch = document.querySelector('[data-kt-product-filter="search"]');
        if (filterSearch) {
            filterSearch.addEventListener('keyup', function (e) {
                datatable.search(e.target.value).draw();
            });
        }
    };

    var handleDeleteRows = () => {
        const deleteButtons = table.querySelectorAll('[data-kt-product-filter="delete_row"]');
        deleteButtons.forEach(d => {
            d.addEventListener('click', function (e) {
                e.preventDefault();
                const parent = e.target.closest('tr');
                const productName = parent.querySelector('[data-kt-product-filter="product_name"]').innerText;

                Swal.fire({
                    text: "Apakah Anda yakin ingin menghapus " + productName + "?",
                    icon: "warning",
                    showCancelButton: true,
                    buttonsStyling: false,
                    confirmButtonText: "Ya, hapus!",
                    cancelButtonText: "Tidak, batalkan",
                    customClass: {
                        confirmButton: "btn fw-bold btn-danger",
                        cancelButton: "btn fw-bold btn-active-light-primary"
                    }
                }).then(function (result) {
                    if (result.value) {
                        // Show loading
                        Swal.fire({
                            text: "Sedang menghapus produk...",
                            icon: "info",
                            showConfirmButton: false,
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        $.ajax({
                            url: hostUrl + "master/ajax/product/delete",
                            type: "POST",
                            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                            data: { id: datatable.row($(parent)).data().id },
                            success: function (response) {
                                if (response.success) {
                                    Swal.fire({
                                        text: "Anda telah menghapus " + productName + "!.",
                                        icon: "success",
                                        buttonsStyling: false,
                                        confirmButtonText: "Ok, mengerti!",
                                        customClass: { confirmButton: "btn fw-bold btn-primary" }
                                    }).then(function () {
                                        datatable.row($(parent)).remove().draw();
                                    });
                                } else {
                                    Swal.fire({
                                        text: response.message,
                                        icon: "error",
                                        buttonsStyling: false,
                                        confirmButtonText: "Ok, mengerti!",
                                        customClass: { confirmButton: "btn fw-bold btn-primary" }
                                    });
                                }
                            },
                            error: function (xhr) {
                                Swal.fire({
                                    text: "Terjadi kesalahan saat menghapus produk.",
                                    icon: "error",
                                    buttonsStyling: false,
                                    confirmButtonText: "Ok, mengerti!",
                                    customClass: { confirmButton: "btn fw-bold btn-primary" }
                                });
                            }
                        });
                    }
                });
            });
        });
    };

    var handleDuplicateRows = () => {
        const duplicateButtons = table.querySelectorAll('[data-kt-product-filter="duplicate_row"]');
        duplicateButtons.forEach(d => {
            d.addEventListener('click', function (e) {
                e.preventDefault();
                const parent = e.target.closest('tr');
                const rowData = datatable.row($(parent)).data();
                const productName = rowData.name;

                Swal.fire({
                    text: "Apakah Anda yakin ingin menduplikasi produk " + productName + "?",
                    icon: "question",
                    showCancelButton: true,
                    buttonsStyling: false,
                    confirmButtonText: "Ya, duplikasi!",
                    cancelButtonText: "Tidak, batalkan",
                    customClass: {
                        confirmButton: "btn fw-bold btn-primary",
                        cancelButton: "btn fw-bold btn-active-light-primary"
                    }
                }).then(function (result) {
                    if (result.value) {
                        // Show loading
                        Swal.fire({
                            text: "Sedang menduplikasi produk...",
                            icon: "info",
                            showConfirmButton: false,
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        $.ajax({
                            url: hostUrl + "master/ajax/product/duplicate",
                            type: "POST",
                            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                            data: { id: rowData.id },
                            success: function (response) {
                                if (response.success) {
                                    Swal.fire({
                                        text: response.message,
                                        icon: "success",
                                        buttonsStyling: false,
                                        confirmButtonText: "Ok, mengerti!",
                                        customClass: { confirmButton: "btn fw-bold btn-primary" }
                                    }).then(function () {
                                        datatable.draw();
                                    });
                                } else {
                                    Swal.fire({
                                        text: response.message,
                                        icon: "error",
                                        buttonsStyling: false,
                                        confirmButtonText: "Ok, mengerti!",
                                        customClass: { confirmButton: "btn fw-bold btn-primary" }
                                    });
                                }
                            },
                            error: function (xhr) {
                                Swal.fire({
                                    text: "Terjadi kesalahan saat menduplikasi produk.",
                                    icon: "error",
                                    buttonsStyling: false,
                                    confirmButtonText: "Ok, mengerti!",
                                    customClass: { confirmButton: "btn fw-bold btn-primary" }
                                });
                            }
                        });
                    }
                });
            });
        });
    };

    var updateActiveFilters = function() {
        const container = $('#active-filters-container');
        if (!container.length) return;

        container.empty();
        const selectedCategories = ($('#filter_categories').val() || []).filter(v => v !== '');
        const selectedColors = ($('#filter_colors').val() || []).filter(v => v !== '');

        if (selectedCategories.length === 0 && selectedColors.length === 0) {
            container.addClass('d-none');
            return;
        }

        container.removeClass('d-none');
        
        // Categories
        selectedCategories.forEach(cat => {
            container.append(`
                <div class="badge badge-light-primary border border-primary border-dashed px-3 py-2 d-flex align-items-center">
                    <span class="me-2">Kategori: ${cat}</span>
                    <a href="#" class="btn btn-icon btn-sm btn-active-light-primary w-20px h-20px remove-filter-cat" data-value="${cat}"><i class="ki-duotone ki-cross fs-3"><span class="path1"></span><span class="path2"></span></i></a>
                </div>
            `);
        });

        // Colors
        selectedColors.forEach(color => {
            container.append(`
                <div class="badge badge-light-info border border-info border-dashed px-3 py-2 d-flex align-items-center">
                    <span class="me-2">Warna: ${color}</span>
                    <a href="#" class="btn btn-icon btn-sm btn-active-light-info w-20px h-20px remove-filter-color" data-value="${color}"><i class="ki-duotone ki-cross fs-3"><span class="path1"></span><span class="path2"></span></i></a>
                </div>
            `);
        });

        container.append(`<a href="#" class="text-primary fw-bold fs-7 ms-2 align-self-center" id="clear-all-filters">Hapus Semua</a>`);

        $('.remove-filter-cat').on('click', function(e) {
            e.preventDefault();
            const val = $(this).data('value');
            const current = $('#filter_categories').val();
            $('#filter_categories').val(current.filter(item => item !== val)).trigger('change');
        });

        $('.remove-filter-color').on('click', function(e) {
            e.preventDefault();
            const val = $(this).data('value');
            const current = $('#filter_colors').val();
            $('#filter_colors').val(current.filter(item => item !== val)).trigger('change');
        });

        $('#clear-all-filters').on('click', function(e) {
            e.preventDefault();
            $('#filter_categories').val(null);
            $('#filter_colors').val(null).trigger('change');
        });
    };

    var handleFilterDatatable = function() {
        // Initialize Select2 for category filter
        const filterCategories = $('#filter_categories');
        if (filterCategories.length && typeof filterCategories.select2 === 'function') {
            filterCategories.select2({
                minimumResultsForSearch: -1,
                placeholder: "Pilih Kategori"
            });
        }
        
        filterCategories.on('change', function() {
            if (datatable) {
                datatable.ajax.reload();
                updateActiveFilters();
            }
        });

        const filterColors = $('#filter_colors');
        if (filterColors.length && typeof filterColors.select2 === 'function') {
            const format = (item) => {
                if (!item.id) return item.text;
                var color = item.element ? item.element.getAttribute('data-kt-color') : null;
                if (color) {
                    return `<span class="d-flex align-items-center"><span class="w-15px h-15px rounded-circle me-2" style="background-color:${color}; border: 1px solid #ddd;"></span>${item.text}</span>`;
                }
                return item.text;
            };

            filterColors.select2({
                placeholder: "Pilih Warna",
                allowClear: true,
                templateResult: format,
                templateSelection: format,
                escapeMarkup: function(m) { return m; }
            });
        }

        filterColors.on('change', function() {
            if (datatable) {
                datatable.ajax.reload();
                updateActiveFilters();
            }
        });

        const resetButton = document.querySelector('[data-kt_menu_filter="reset"]');
        if (resetButton) {
            resetButton.addEventListener('click', function(e) {
                e.preventDefault();
                filterCategories.val(null);
                filterColors.val(null).trigger('change');
            });
        }
    };

    var handleGroupActions = function() {
        const toolbarBase = document.querySelector('[data-kt-product-table-toolbar="base"]');
        const toolbarSelected = document.querySelector('[data-kt-product-table-toolbar="selected"]');
        const selectedCount = document.querySelector('[data-kt-product-table-select="selected_count"]');
        const mergeButton = document.querySelector('[data-kt-product-table-select="merge_selected"]');

        // Listen for checkbox changes using delegation
        table.addEventListener('change', function(e) {
            if (e.target.classList.contains('form-check-input')) {
                updateToolbar();
            }
        });

        const updateToolbar = () => {
            const allCheckboxes = table.querySelectorAll('tbody [type="checkbox"]:checked');
            const count = allCheckboxes.length;

            if (count > 0) {
                selectedCount.innerHTML = count;
                toolbarBase.classList.add('d-none');
                toolbarSelected.classList.remove('d-none');
            } else {
                toolbarBase.classList.remove('d-none');
                toolbarSelected.classList.add('d-none');
            }
        };

        // Handle Merge Button Click
        mergeButton.addEventListener('click', function() {
            const selectedIds = Array.from(table.querySelectorAll('tbody [type="checkbox"]:checked')).map(cb => cb.value);
            
            if (selectedIds.length < 2) {
                Swal.fire({ text: "Pilih minimal 2 produk untuk digabung.", icon: "warning", buttonsStyling: false, confirmButtonText: "Ok, mengerti", customClass: { confirmButton: "btn btn-primary" } });
                return;
            }

            // Get full data for selected products from datatable
            const selectedProducts = [];
            datatable.rows().every(function() {
                const data = this.data();
                if (selectedIds.includes(data.id.toString())) {
                    selectedProducts.push(data);
                }
            });

            // Populate Modal
            const targetSelect = $('#merge_target_product');
            targetSelect.empty().append('<option></option>');
            
            const sourceList = $('#merge_source_products_list');
            sourceList.empty();

            selectedProducts.forEach(p => {
                targetSelect.append(`<option value="${p.id}">${p.name} (${p.sku || 'No SKU'}) - Stok: ${p.stock}</option>`);
                sourceList.append(`
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-gray-800 fw-bold">${p.name}</span>
                        <span class="badge badge-light-secondary">${p.stock} ${p.base_unit}</span>
                    </div>
                `);
            });

            if (typeof targetSelect.select2 === 'function') {
                targetSelect.select2({
                    dropdownParent: $('#kt_modal_merge_products'),
                    placeholder: "Pilih produk yang akan dipertahankan",
                    minimumResultsForSearch: Infinity
                });
            }

            $('#kt_modal_merge_products').modal('show');
        });

        // Handle Modal Form Submit
        const form = document.querySelector('#kt_modal_merge_products_form');
        const submitButton = document.querySelector('#kt_modal_merge_products_submit');

        form.addEventListener('submit', function(e) {
            e.preventDefault();

            const targetId = $('#merge_target_product').val();
            const selectedIds = Array.from(table.querySelectorAll('tbody [type="checkbox"]:checked')).map(cb => cb.value);
            const sourceIds = selectedIds.filter(id => id !== targetId);

            if (!targetId) {
                Swal.fire({ text: "Pilih produk utama (target) terlebih dahulu.", icon: "error", buttonsStyling: false, confirmButtonText: "Ok, mengerti", customClass: { confirmButton: "btn btn-primary" } });
                return;
            }

            Swal.fire({
                text: "Apakah Anda yakin ingin menggabung produk-produk ini? Produk sumber akan dihapus dan datanya dipindahkan ke produk target.",
                icon: "warning",
                showCancelButton: true,
                buttonsStyling: false,
                confirmButtonText: "Ya, Gabung!",
                cancelButtonText: "Batal",
                customClass: {
                    confirmButton: "btn fw-bold btn-danger",
                    cancelButton: "btn fw-bold btn-active-light-primary"
                }
            }).then(function (result) {
                if (result.value) {
                    submitButton.setAttribute('data-kt-indicator', 'on');
                    submitButton.disabled = true;

                    $.ajax({
                        url: hostUrl + "master/ajax/product/merge",
                        type: "POST",
                        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                        data: {
                            target_id: targetId,
                            source_ids: sourceIds
                        },
                        success: function(response) {
                            submitButton.removeAttribute('data-kt-indicator');
                            submitButton.disabled = false;

                            if (response.success) {
                                Swal.fire({
                                    text: response.message,
                                    icon: "success",
                                    buttonsStyling: false,
                                    confirmButtonText: "Ok, mengerti",
                                    customClass: { confirmButton: "btn btn-primary" }
                                }).then(function() {
                                    $('#kt_modal_merge_products').modal('hide');
                                    datatable.ajax.reload();
                                    toolbarBase.classList.remove('d-none');
                                    toolbarSelected.classList.add('d-none');
                                    
                                    // Reset header checkbox
                                    const headerCheck = table.querySelector('thead [type="checkbox"]');
                                    if (headerCheck) headerCheck.checked = false;
                                });
                            } else {
                                Swal.fire({ text: response.message, icon: "error", buttonsStyling: false, confirmButtonText: "Ok, mengerti", customClass: { confirmButton: "btn btn-primary" } });
                            }
                        },
                        error: function(xhr) {
                            submitButton.removeAttribute('data-kt-indicator');
                            submitButton.disabled = false;
                            const error = xhr.responseJSON ? xhr.responseJSON.message : "Terjadi kesalahan sistem";
                            Swal.fire({ text: error, icon: "error", buttonsStyling: false, confirmButtonText: "Ok, mengerti", customClass: { confirmButton: "btn btn-primary" } });
                        }
                    });
                }
            });
        });
    };

    var handleExport = function() {
        const exportAllBtn = document.querySelector('[data-kt-product-action="export_all"]');
        const exportSelectedBtn = document.querySelector('[data-kt-product-table-select="export_selected"]');

        if (exportAllBtn) {
            exportAllBtn.addEventListener('click', function (e) {
                e.preventDefault();
                submitExportForm([]);
            });
        }

        if (exportSelectedBtn) {
            exportSelectedBtn.addEventListener('click', function (e) {
                e.preventDefault();
                const selectedIds = Array.from(table.querySelectorAll('tbody [type="checkbox"]:checked')).map(cb => cb.value);
                submitExportForm(selectedIds);
            });
        }

        function submitExportForm(ids) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = hostUrl + 'master/product/export';
            form.style.display = 'none';

            const csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = $('meta[name="csrf-token"]').attr('content');
            form.appendChild(csrfToken);

            ids.forEach(id => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'product_ids[]';
                input.value = id;
                form.appendChild(input);
            });

            document.body.appendChild(form);
            form.submit();
            document.body.removeChild(form);
        }
    };

    var handleImport = function() {
        const form = document.getElementById('kt_modal_import_products_form');
        if (!form) return;

        const submitButton = document.getElementById('kt_modal_import_products_submit');

        form.addEventListener('submit', function (e) {
            e.preventDefault();

            const formData = new FormData(form);

            submitButton.setAttribute('data-kt-indicator', 'on');
            submitButton.disabled = true;

            $.ajax({
                url: hostUrl + 'master/product/import',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
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
                            $('#kt_modal_import_products').modal('hide');
                            form.reset();
                            datatable.ajax.reload();
                        });
                    } else {
                        Swal.fire({
                            text: response.message || "Gagal meng-import data.",
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
                    
                    let errorMsg = "Terjadi kesalahan saat import.";
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMsg = xhr.responseJSON.message;
                    }
                    
                    Swal.fire({
                        text: errorMsg,
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
            table = document.querySelector('#kt_products_table');
            
            if (!table) {
                console.error('Table #kt_products_table not found!');
                return;
            }

            initDataTable();
            handleSearchDatatable();
            handleFilterDatatable();
            updateActiveFilters();
            handleGroupActions();
            handleExport();
            handleImport();
        }
    };
}();

// On document ready
KTUtil.onDOMContentLoaded(function () {
    TKAppMasterProductList.init();
});
