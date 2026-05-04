"use strict";

var KTColorsList = function () {
    var table;
    var datatable;
    var importData = {
        new_items: [],
        conflicts: [],
        resolutions: []
    };

    var initDataTable = function () {
        if (typeof DataTable !== 'undefined' && DataTable.isDataTable(table)) {
            new DataTable(table).destroy();
        }

        if (typeof DataTable === 'undefined') {
            console.error('DataTable is not defined!');
            return;
        }

        datatable = new DataTable(table, {
            searchDelay: 500,
            processing: true,
            serverSide: true,
            order: [[1, 'asc']],
            stateSave: true,
            ajax: {
                url: hostUrl + 'master/ajax/color/list',
                type: 'POST',
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
            },
            columns: [
                {
                    data: 'id',
                    render: function (data) {
                        return `<div class="form-check form-check-sm form-check-custom form-check-solid">
                                    <input class="form-check-input" type="checkbox" value="${data}" />
                                </div>`;
                    }
                },
                { data: 'name' },
                { 
                    data: 'hex_code',
                    render: function (data) {
                        return `<div class="d-flex align-items-center">
                                    <div class="w-30px h-20px rounded me-2 border" style="background-color: ${data || '#ffffff'}"></div>
                                    <span class="text-gray-600 fw-bold">${data || '-'}</span>
                                </div>`;
                    }
                },
                {
                    data: null,
                    className: 'text-end',
                    render: function (data, type, row) {
                        return `
                            <button class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1 btn-edit" data-id="${row.id}" data-name="${row.name}" data-hex="${row.hex_code}">
                                <i class="ki-duotone ki-pencil fs-2"><span class="path1"></span><span class="path2"></span></i>
                            </button>
                            <button class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm btn-delete" data-id="${row.id}">
                                <i class="ki-duotone ki-trash fs-2"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span></i>
                            </button>
                        `;
                    }
                },
            ],
            columnDefs: [
                { orderable: false, targets: 0 },
                { orderable: false, targets: 2 },
                { orderable: false, targets: 3 },
            ],
            language: {
                processing: `<div class="d-flex flex-column align-items-center"><div class="spinner-border text-primary" role="status"></div><span class="text-muted fs-7 fw-bold mt-5">Memuat data...</span></div>`,
            }
        });

        datatable.on('draw', function () {
            handleEditRows();
            handleDeleteRows();
        });
    };

    var handleSearchDatatable = function () {
        const filterSearch = document.querySelector('[data-kt-color-table-filter="search"]');
        if (filterSearch) {
            filterSearch.addEventListener('keyup', function (e) {
                datatable.search(e.target.value).draw();
            });
        }
    };

    var handleEditRows = function () {
        const editButtons = table.querySelectorAll('.btn-edit');
        editButtons.forEach(d => {
            d.addEventListener('click', function (e) {
                e.preventDefault();
                const id = this.getAttribute('data-id');
                const name = this.getAttribute('data-name');
                const hex = this.getAttribute('data-hex');

                $('#color_id').val(id);
                $('#color_name').val(name).focus();
                $('#color_hex').val(hex);
                $('#color_picker').val(hex || '#ffffff');
                
                $('#form_title').text('Edit Warna');
                $('#btn_cancel_edit').removeClass('d-none');
                
                // Scroll to form on mobile
                if (window.innerWidth < 992) {
                    document.getElementById('form_add_color').scrollIntoView({ behavior: 'smooth' });
                }
            });
        });
    };

    var handleDeleteRows = function () {
        const deleteButtons = table.querySelectorAll('.btn-delete');
        deleteButtons.forEach(d => {
            d.addEventListener('click', function (e) {
                e.preventDefault();
                const id = this.getAttribute('data-id');

                Swal.fire({
                    text: "Apakah Anda yakin ingin menghapus warna ini?",
                    icon: "warning",
                    showCancelButton: true,
                    buttonsStyling: false,
                    confirmButtonText: "Ya, hapus!",
                    cancelButtonText: "Tidak, batal",
                    customClass: {
                        confirmButton: "btn fw-bold btn-danger",
                        cancelButton: "btn fw-bold btn-active-light-primary"
                    }
                }).then(function (result) {
                    if (result.value) {
                        $.post(hostUrl + 'master/ajax/color/delete', { id: id }, function (res) {
                            if (res.success) {
                                Swal.fire({
                                    text: res.message,
                                    icon: "success",
                                    buttonsStyling: false,
                                    confirmButtonText: "Ok, mengerti!",
                                    customClass: {
                                        confirmButton: "btn fw-bold btn-primary",
                                    }
                                }).then(function () {
                                    datatable.draw();
                                });
                            }
                        });
                    }
                });
            });
        });
    };

    var handleAddColor = function () {
        const form = document.getElementById('form_add_color');
        if (!form) return;
        
        const submitButton = document.getElementById('btn_save_color');
        const cancelButton = document.getElementById('btn_cancel_edit');
        const colorPicker = document.getElementById('color_picker');
        const colorHexInput = document.getElementById('color_hex');

        colorPicker.addEventListener('input', function() {
            colorHexInput.value = this.value.toUpperCase();
        });

        colorHexInput.addEventListener('input', function() {
            if (/^#[0-9A-F]{6}$/i.test(this.value)) {
                colorPicker.value = this.value;
            }
        });

        cancelButton.addEventListener('click', function() {
            form.reset();
            $('#color_id').val('');
            $('#form_title').text('Tambah Warna');
            cancelButton.classList.add('d-none');
        });

        form.addEventListener('submit', function (e) {
            e.preventDefault();
            const id = $('#color_id').val();
            const url = id ? hostUrl + 'master/ajax/color/update/' + id : hostUrl + 'master/ajax/color/store';

            submitButton.setAttribute('data-kt-indicator', 'on');
            submitButton.disabled = true;

            $.post(url, $(form).serialize(), function (res) {
                if (res.success) {
                    Swal.fire({
                        text: res.message,
                        icon: "success",
                        buttonsStyling: false,
                        confirmButtonText: "Ok, mengerti!",
                        customClass: {
                            confirmButton: "btn fw-bold btn-primary",
                        }
                    }).then(function () {
                        datatable.draw();
                        form.reset();
                        $('#color_id').val('');
                        $('#form_title').text('Tambah Warna');
                        cancelButton.classList.add('d-none');
                    });
                }
            }).fail(function (err) {
                Swal.fire({
                    text: err.responseJSON.message || "Terjadi kesalahan.",
                    icon: "error",
                    buttonsStyling: false,
                    confirmButtonText: "Ok, mengerti!",
                    customClass: {
                        confirmButton: "btn fw-bold btn-primary",
                    }
                });
            }).always(function () {
                submitButton.removeAttribute('data-kt-indicator');
                submitButton.disabled = false;
            });
        });
    };

    var handleImport = function() {
        const form = document.getElementById('form_import_color');
        if (!form) return;
        
        const submitButton = document.getElementById('btn_import_submit');

        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(form);

            submitButton.setAttribute('data-kt-indicator', 'on');
            submitButton.disabled = true;

            $.ajax({
                url: hostUrl + 'master/ajax/color/import/validate',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(res) {
                    $('#modal_import_color').modal('hide');
                    importData.new_items = res.new_data;
                    importData.conflicts = res.conflicts;
                    importData.resolutions = [];

                    if (importData.conflicts.length > 0) {
                        showConflictModal();
                    } else if (importData.new_items.length > 0) {
                        finalizeImport([]);
                    } else {
                        Swal.fire({
                            text: "Tidak ada data baru untuk di-import.",
                            icon: "info",
                            buttonsStyling: false,
                            confirmButtonText: "Ok",
                            customClass: { confirmButton: "btn btn-primary" }
                        });
                    }
                },
                error: function(err) {
                    Swal.fire({
                        text: err.responseJSON.message || "Gagal memproses file.",
                        icon: "error",
                        buttonsStyling: false,
                        confirmButtonText: "Ok",
                        customClass: { confirmButton: "btn btn-primary" }
                    });
                },
                complete: function() {
                    submitButton.removeAttribute('data-kt-indicator');
                    submitButton.disabled = false;
                }
            });
        });
    };

    var showConflictModal = function() {
        const list = document.getElementById('conflict_list');
        list.innerHTML = '';

        importData.conflicts.forEach((c, index) => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td><span class="fw-bold">${c.name}</span></td>
                <td><div class="d-flex align-items-center"><div class="w-20px h-15px rounded me-2 border" style="background-color: ${c.existing_hex || '#fff'}"></div><span>${c.existing_hex || '-'}</span></div></td>
                <td><div class="d-flex align-items-center"><div class="w-20px h-15px rounded me-2 border" style="background-color: ${c.imported_hex || '#fff'}"></div><span>${c.imported_hex || '-'}</span></div></td>
                <td class="text-end">
                    <div class="btn-group" role="group">
                        <input type="radio" class="btn-check" name="res_${index}" id="update_${index}" value="update" autocomplete="off" checked>
                        <label class="btn btn-outline btn-outline-dashed btn-outline-success btn-active-light-success p-2 fs-8" for="update_${index}">Update</label>

                        <input type="radio" class="btn-check" name="res_${index}" id="skip_${index}" value="skip" autocomplete="off">
                        <label class="btn btn-outline btn-outline-dashed btn-outline-danger btn-active-light-danger p-2 fs-8" for="skip_${index}">Skip</label>
                    </div>
                </td>
            `;
            list.appendChild(row);
        });

        $('#modal_conflict_resolution').modal('show');

        document.getElementById('btn_finalize_import').onclick = function() {
            const resolutions = importData.conflicts.map((c, index) => {
                const action = document.querySelector(`input[name="res_${index}"]:checked`).value;
                return {
                    name: c.name,
                    hex_code: c.imported_hex,
                    action: action
                };
            });
            finalizeImport(resolutions);
        };
    };

    var finalizeImport = function(resolutions) {
        const finalizeButton = document.getElementById('btn_finalize_import');
        if (finalizeButton) {
            finalizeButton.disabled = true;
            finalizeButton.innerText = 'Mohon tunggu...';
        }

        const items = [
            ...importData.new_items.map(i => ({ ...i, action: 'create' })),
            ...resolutions
        ];

        $.post(hostUrl + 'master/ajax/color/import/confirm', { items: items }, function(res) {
            $('#modal_conflict_resolution').modal('hide');
            Swal.fire({
                text: res.message,
                icon: "success",
                buttonsStyling: false,
                confirmButtonText: "Ok",
                customClass: { confirmButton: "btn btn-primary" }
            }).then(() => {
                datatable.draw();
            });
        }).fail(function(err) {
            Swal.fire({
                text: "Gagal menyelesaikan import.",
                icon: "error",
                buttonsStyling: false,
                confirmButtonText: "Ok",
                customClass: { confirmButton: "btn btn-primary" }
            });
        }).always(() => {
            if (finalizeButton) {
                finalizeButton.disabled = false;
                finalizeButton.innerText = 'Selesaikan Import';
            }
        });
    };

    return {
        init: function () {
            table = document.querySelector('#kt_color_table');
            if (!table) return;

            initDataTable();
            handleSearchDatatable();
            handleAddColor();
            handleImport();
        }
    };
}();

KTUtil.onDOMContentLoaded(function () {
    KTColorsList.init();
});
