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
            stateSave: true,
            ajax: {
                url: hostUrl + "master/ajax/product/list",
                type: "POST",
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                data: function(d) {
                    d.filters = {
                        categories: $('#filter_categories').val(),
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
                        Swal.fire({
                            text: "Anda telah menghapus " + productName + "!.",
                            icon: "success",
                            buttonsStyling: false,
                            confirmButtonText: "Ok, mengerti!",
                            customClass: { confirmButton: "btn fw-bold btn-primary" }
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
        if (!container.length) return;

        container.empty();
        const selectedCategories = ($('#filter_categories').val() || []).filter(v => v !== '');

        if (selectedCategories.length === 0) {
            container.addClass('d-none');
            return;
        }

        container.removeClass('d-none');
        selectedCategories.forEach(cat => {
            container.append(`
                <div class="badge badge-light-primary border border-primary border-dashed px-3 py-2 d-flex align-items-center">
                    <span class="me-2">Kategori: ${cat}</span>
                    <a href="#" class="btn btn-icon btn-sm btn-active-light-primary w-20px h-20px remove-filter-cat" data-value="${cat}"><i class="ki-duotone ki-cross fs-3"><span class="path1"></span><span class="path2"></span></i></a>
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

        $('#clear-all-filters').on('click', function(e) {
            e.preventDefault();
            $('#filter_categories').val(null).trigger('change');
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

        const resetButton = document.querySelector('[data-kt_menu_filter="reset"]');
        if (resetButton) {
            resetButton.addEventListener('click', function(e) {
                e.preventDefault();
                filterCategories.val(null).trigger('change');
            });
        }
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
        }
    };
}();

// On document ready
KTUtil.onDOMContentLoaded(function () {
    TKAppMasterProductList.init();
});
