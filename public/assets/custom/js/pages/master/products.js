"use strict";

var TKAppMasterProducts = function () {
    var table;
    var datatable;

    var initDatatable = function () {
        table = document.querySelector('#kt_ecommerce_products_table');
        
        if (!table) return;

        datatable = $(table).DataTable({
            "info": true,
            "serverSide": true,
            "processing": true,
            "ajax": {
                "url": hostUrl + "ajax/master/products/list",
                "type": "POST",
                "data": function(d) {
                    d.filters = {
                        categories: $('#filter_categories').val(),
                        colors: $('input[name="filter_color"]:checked').map(function(){ return $(this).val(); }).get()
                    };
                }
            },
            'order': [],
            'pageLength': 10,
            'stateSave': true,
            'autoWidth': false,
            'colReorder': false, // DISABLED as requested
            'columns': [
                { 
                    data: 'id',
                    orderable: false,
                    render: function(data) {
                        return `<div class="form-check form-check-sm form-check-custom form-check-solid">
                                    <input class="form-check-input" type="checkbox" value="${data}" />
                                </div>`;
                    }
                },
                { 
                    data: 'name',
                    render: function(data, type, row) {
                        return `<div class="d-flex align-items-center">
                                    <a href="#" class="symbol symbol-35px">
                                        <span class="symbol-label" style="background-image:url(${hostUrl}assets/vendors/media/stock/ecommerce/${row.image});"></span>
                                    </a>
                                    <div class="ms-5">
                                        <a href="${hostUrl}master/products/edit/${row.id}" class="text-gray-800 text-hover-primary fs-5 fw-bold" data-kt-ecommerce-product-filter="product_name">${data}</a>
                                    </div>
                                </div>`;
                    }
                },
                { data: 'sku' },
                { 
                    data: 'stock', 
                    render: function(data) {
                        return `<span class="fw-bold ms-3">${data} Roll</span>`;
                    }
                },
                { data: 'category' },
                { 
                    data: 'color', 
                    render: function(data, type, row) {
                        return `<div class="d-flex align-items-center">
                                    <span class="badge badge-circle w-15px h-15px me-2" style="background-color:${row.color_hex}"></span>
                                    ${data}
                                </div>`;
                    }
                },
                { 
                    data: null,
                    orderable: false,
                    className: 'text-end',
                    render: function(data, type, row) {
                        return `<a href="#" class="btn btn-sm btn-light btn-flex btn-center btn-active-light-primary" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">Aksi 
                                <i class="ki-duotone ki-down fs-5 ms-1"></i></a>
                                <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-125px py-4" data-kt-menu="true">
                                    <div class="menu-item px-3">
                                        <a href="${hostUrl}master/products/edit/${row.id}" class="menu-link px-3">Edit</a>
                                    </div>
                                    <div class="menu-item px-3">
                                        <a href="#" class="menu-link px-3" data-kt-ecommerce-product-filter="delete_row">Delete</a>
                                    </div>
                                </div>`;
                    }
                }
            ],
            // Persistent column width logic
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
            KTMenu.createInstances();
            handleDeleteRows();
            initColumnResize(); // Re-init resize handles after draw
        });
        
        initColumnResize();
    };

    var initColumnResize = function() {
        const ths = table.querySelectorAll('thead th:not(:last-child)'); // Exclude actions column
        ths.forEach(th => {
            // Remove existing handles if any
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

    var handleSearchDatatable = function () {
        const filterSearch = document.querySelector('[data-kt-ecommerce-product-filter="search"]');
        filterSearch.addEventListener('keyup', function (e) {
            datatable.search(e.target.value).draw();
        });
    };

    var handleDeleteRows = function () {
        const deleteButtons = table.querySelectorAll('[data-kt-ecommerce-product-filter="delete_row"]');
        deleteButtons.forEach(d => {
            d.addEventListener('click', function (e) {
                e.preventDefault();
                const parent = e.target.closest('tr');
                const productName = parent.querySelector('[data-kt-ecommerce-product-filter="product_name"]').innerText;

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
                        Swal.fire({
                            text: "Anda telah menghapus " + productName + "!.",
                            icon: "success",
                            buttonsStyling: false,
                            confirmButtonText: "Ok, mengerti!",
                            customClass: {
                                confirmButton: "btn fw-bold btn-primary"
                            }
                        }).then(function () {
                            datatable.row($(parent)).remove().draw();
                        });
                    }
                });
            });
        });
    };

    var updateActiveFilters = function() {
        const container = $('#active-filters-container');
        container.empty();

        const selectedCategories = ($('#filter_categories').val() || []).filter(v => v !== '');
        const selectedColors = $('input[name="filter_color"]:checked').map(function(){ return $(this).val(); }).get();

        if (selectedCategories.length === 0 && selectedColors.length === 0) {
            container.addClass('d-none');
            return;
        }

        container.removeClass('d-none');

        // Add Categories
        selectedCategories.forEach(cat => {
            container.append(`
                <div class="badge badge-light-primary border border-primary border-dashed px-3 py-2 d-flex align-items-center">
                    <span class="me-2">Kategori: ${cat}</span>
                    <a href="#" class="btn btn-icon btn-sm btn-active-light-primary w-20px h-20px remove-filter-cat" data-value="${cat}"><i class="ki-duotone ki-cross fs-3"><span class="path1"></span><span class="path2"></span></i></a>
                </div>
            `);
        });

        // Add Colors
        selectedColors.forEach(color => {
            container.append(`
                <div class="badge badge-light-info border border-info border-dashed px-3 py-2 d-flex align-items-center">
                    <span class="me-2">Warna: ${color}</span>
                    <a href="#" class="btn btn-icon btn-sm btn-active-light-info w-20px h-20px remove-filter-color" data-value="${color}"><i class="ki-duotone ki-cross fs-3"><span class="path1"></span><span class="path2"></span></i></a>
                </div>
            `);
        });

        // Add "Hapus Semua"
        container.append(`<a href="#" class="text-primary fw-bold fs-7 ms-2 align-self-center" id="clear-all-filters">Hapus Semua</a>`);

        // Bind events for removal
        $('.remove-filter-cat').on('click', function(e) {
            e.preventDefault();
            const val = $(this).data('value');
            const current = $('#filter_categories').val();
            $('#filter_categories').val(current.filter(item => item !== val)).trigger('change');
            datatable.ajax.reload();
        });

        $('.remove-filter-color').on('click', function(e) {
            e.preventDefault();
            const val = $(this).data('value');
            $(`input[name="filter_color"][value="${val}"]`).prop('checked', false);
            datatable.ajax.reload();
        });

        $('#clear-all-filters').on('click', function(e) {
            e.preventDefault();
            // Clear category select2
            $('#filter_categories').val(null).trigger('change');
            // Uncheck all colors
            $('input[name="filter_color"]').prop('checked', false);
            // Reload table & update UI
            datatable.ajax.reload();
            updateActiveFilters();
        });
    };

    var handleFilterDatatable = function() {
        // Instant filter on Category change
        $('#filter_categories').on('change', function() {
            datatable.ajax.reload();
            updateActiveFilters();
        });

        // Instant filter on Color change
        $('input[name="filter_color"]').on('change', function() {
            datatable.ajax.reload();
            updateActiveFilters();
        });

        // Reset button still useful for clearing everything at once
        const resetButton = document.querySelector('[data-kt-menu-dismiss="true"][type="reset"]');
        if (resetButton) {
            resetButton.addEventListener('click', function(e) {
                e.preventDefault();
                $('#filter_categories').val(null).trigger('change');
                $('input[name="filter_color"]').prop('checked', false);
                datatable.ajax.reload();
                updateActiveFilters();
            });
        }
    };

    return {
        init: function () {
            initDatatable();
            handleSearchDatatable();
            handleDeleteRows();
            handleFilterDatatable();
            updateActiveFilters();
        }
    };
}();

KTUtil.onDOMContentLoaded(function () {
    TKAppMasterProducts.init();
});
