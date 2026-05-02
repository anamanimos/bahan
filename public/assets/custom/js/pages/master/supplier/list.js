"use strict";

var TKAppMasterSupplierList = function () {
    var table;
    var datatable;

    var initDataTable = function () {
        datatable = new DataTable(table, {
            searchDelay: 500,
            processing: true,
            serverSide: true,
            stateSave: true,
            autoWidth: false,
            colReorder: false, // Explicitly disable reordering
            ajax: {
                url: hostUrl + "master/ajax/supplier/list",
                type: "POST",
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            },
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
            },
            columns: [
                { data: 'id' },
                { data: 'name' },
                { data: 'contact_person' },
                { data: 'phone_number' },
                { data: 'address' },
                { data: null },
            ],
            columnDefs: [
                {
                    targets: 0,
                    orderable: false,
                    render: function (data) {
                        return `
                            <div class="form-check form-check-sm form-check-custom form-check-solid">
                                <input class="form-check-input" type="checkbox" value="${data}" />
                            </div>`;
                    }
                },
                {
                    targets: 1,
                    render: function (data, type, row) {
                        return `<a href="${hostUrl}master/supplier/edit/${row.id}" class="text-gray-800 text-hover-primary fw-bold">${data}</a>`;
                    }
                },
                {
                    targets: 2,
                    render: function (data) {
                        return data || '<span class="text-muted fs-7">Tidak ada</span>';
                    }
                },
                {
                    targets: 3,
                    className: 'text-start',
                    render: function (data) {
                        return data || '<span class="text-muted fs-7">-</span>';
                    }
                },
                {
                    targets: 4,
                    render: function (data) {
                        if (!data) return '<span class="text-muted fs-7">-</span>';
                        return `<span class="text-truncate d-inline-block" style="max-width: 200px;">${data}</span>`;
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
                                <div class="menu-item px-3"><a href="${hostUrl}master/supplier/edit/${row.id}" class="menu-link px-3">Edit</a></div>
                                <div class="menu-item px-3"><a href="#" class="menu-link px-3 text-danger" data-kt-supplier-filter="delete_row">Hapus</a></div>
                            </div>`;
                    },
                },
            ],
        });

        datatable.on('draw', function () {
            KTMenu.createInstances();
            handleDeleteRows();
            toggleToolbars();
            initColumnResize();
        });

        initColumnResize();
    };

    var initColumnResize = function() {
        console.log('Initializing column resize for', table.id);
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
        const filterSearch = document.querySelector('[data-kt-supplier-filter="search"]');
        if (filterSearch) {
            filterSearch.addEventListener('keyup', function (e) {
                datatable.search(e.target.value).draw();
            });
        }
    };

    var toggleToolbars = () => {
        const container = document.querySelector('#kt_suppliers_table');
        const toolbarBase = document.querySelector('[data-kt-supplier-table-toolbar="base"]');
        const toolbarSelected = document.querySelector('[data-kt-supplier-table-toolbar="selected"]');
        const selectedCount = document.querySelector('[data-kt-supplier-table-select="selected_count"]');
        const allCheckboxes = container.querySelectorAll('tbody [type="checkbox"]');

        let checkedState = false;
        let count = 0;

        allCheckboxes.forEach(c => {
            if (c.checked) {
                checkedState = true;
                count++;
            }
        });

        if (checkedState) {
            selectedCount.innerHTML = count;
            toolbarBase.classList.add('d-none');
            toolbarSelected.classList.remove('d-none');
        } else {
            toolbarBase.classList.remove('d-none');
            toolbarSelected.classList.add('d-none');
        }
    };

    var handleGroupActions = () => {
        const container = document.querySelector('#kt_suppliers_table');
        const checkboxes = container.querySelectorAll('[type="checkbox"]');
        const mergeButton = document.querySelector('[data-kt-supplier-table-select="merge_selected"]');

        container.addEventListener('change', function(e) {
            if (e.target.type === 'checkbox') {
                toggleToolbars();
            }
        });

        mergeButton.addEventListener('click', function() {
            const selectedIds = [];
            const selectedNames = [];
            container.querySelectorAll('tbody [type="checkbox"]:checked').forEach(c => {
                const rowData = datatable.row($(c).closest('tr')).data();
                selectedIds.push(rowData.id);
                selectedNames.push(rowData.name);
            });

            if (selectedIds.length < 2) {
                Swal.fire({ text: "Pilih minimal 2 supplier untuk digabungkan.", icon: "warning", buttonsStyling: false, confirmButtonText: "Ok, mengerti", customClass: { confirmButton: "btn btn-primary" } });
                return;
            }

            // Populate target dropdown
            const targetSelect = $('#kt_merge_target_supplier');
            targetSelect.empty().append('<option></option>');
            container.querySelectorAll('tbody [type="checkbox"]:checked').forEach(c => {
                const rowData = datatable.row($(c).closest('tr')).data();
                targetSelect.append(`<option value="${rowData.id}">${rowData.name}</option>`);
            });

            $('#kt_modal_merge_supplier').modal('show');

            $('#kt_modal_merge_supplier_form').off('submit').on('submit', function(e) {
                e.preventDefault();
                const targetId = targetSelect.val();
                if (!targetId) {
                    Swal.fire({ text: "Pilih supplier target!", icon: "error", buttonsStyling: false, confirmButtonText: "Ok, mengerti", customClass: { confirmButton: "btn btn-primary" } });
                    return;
                }

                Swal.fire({
                    text: "Apakah Anda yakin ingin menggabungkan supplier terpilih? Tindakan ini tidak dapat dibatalkan.",
                    icon: "warning",
                    showCancelButton: true,
                    buttonsStyling: false,
                    confirmButtonText: "Ya, gabungkan!",
                    cancelButtonText: "Batal",
                    customClass: { confirmButton: "btn btn-danger", cancelButton: "btn btn-active-light-primary" }
                }).then(function(result) {
                    if (result.value) {
                        // Show loading
                        Swal.fire({
                            text: "Sedang menggabungkan data...",
                            icon: "info",
                            showConfirmButton: false,
                            allowOutsideClick: false,
                            didOpen: () => { Swal.showLoading(); }
                        });

                        $.ajax({
                            url: hostUrl + "master/ajax/supplier/merge",
                            type: "POST",
                            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                            data: {
                                source_ids: selectedIds,
                                target_id: targetId
                            },
                            success: function(response) {
                                if (response.success) {
                                    Swal.fire({
                                        text: response.message,
                                        icon: "success",
                                        buttonsStyling: false,
                                        confirmButtonText: "Ok, mengerti!",
                                        customClass: { confirmButton: "btn btn-primary" }
                                    }).then(function() {
                                        $('#kt_modal_merge_supplier').modal('hide');
                                        datatable.draw();
                                    });
                                } else {
                                    Swal.fire({ text: response.message, icon: "error", buttonsStyling: false, confirmButtonText: "Ok, mengerti!", customClass: { confirmButton: "btn btn-primary" } });
                                }
                            },
                            error: function(xhr) {
                                Swal.fire({ text: "Terjadi kesalahan sistem", icon: "error", buttonsStyling: false, confirmButtonText: "Ok, mengerti!", customClass: { confirmButton: "btn btn-primary" } });
                            }
                        });
                    }
                });
            });
        });
    };

    var handleDeleteRows = () => {
        const deleteButtons = table.querySelectorAll('[data-kt-supplier-filter="delete_row"]');
        deleteButtons.forEach(d => {
            d.addEventListener('click', function (e) {
                e.preventDefault();
                const parent = e.target.closest('tr');
                const rowData = datatable.row($(parent)).data();
                const supplierName = rowData.name;

                Swal.fire({
                    text: "Apakah Anda yakin ingin menghapus " + supplierName + "?",
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
                            text: "Sedang menghapus data...",
                            icon: "info",
                            showConfirmButton: false,
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        $.ajax({
                            url: hostUrl + "master/ajax/supplier/delete",
                            type: "POST",
                            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                            data: { id: rowData.id },
                            success: function (response) {
                                if (response.success) {
                                    Swal.fire({
                                        text: "Anda telah menghapus " + supplierName + "!.",
                                        icon: "success",
                                        buttonsStyling: false,
                                        confirmButtonText: "Ok, mengerti!",
                                        customClass: { confirmButton: "btn fw-bold btn-primary" }
                                    }).then(function () {
                                        datatable.row($(parent)).remove().draw();
                                    });
                                } else {
                                    Swal.fire({ text: response.message, icon: "error", buttonsStyling: false, confirmButtonText: "Ok, mengerti!", customClass: { confirmButton: "btn fw-bold btn-primary" } });
                                }
                            },
                            error: function (xhr) {
                                Swal.fire({ text: "Terjadi kesalahan sistem", icon: "error", buttonsStyling: false, confirmButtonText: "Ok, mengerti!", customClass: { confirmButton: "btn fw-bold btn-primary" } });
                            }
                        });
                    }
                });
            });
        });
    };

    return {
        init: function () {
            table = document.querySelector('#kt_suppliers_table');
            if (!table) return;

            initDataTable();
            handleSearchDatatable();
            handleGroupActions();
        }
    };
}();

KTUtil.onDOMContentLoaded(function () {
    TKAppMasterSupplierList.init();
});
