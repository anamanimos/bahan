"use strict";

var KTUnitsList = function () {
    var table;
    var datatable;

    var initDataTable = function () {
        if (typeof DataTable !== 'undefined' && DataTable.isDataTable(table)) {
            new DataTable(table).destroy();
        }

        datatable = new DataTable(table, {
            searchDelay: 500,
            processing: true,
            serverSide: true,
            order: [[1, 'asc']],
            stateSave: true,
            ajax: {
                url: hostUrl + 'master/ajax/unit/list',
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
                { data: 'symbol' },
                { data: 'description' },
                {
                    data: null,
                    className: 'text-end',
                    render: function (data, type, row) {
                        return `
                            <button class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1 btn-edit" 
                                data-id="${row.id}" 
                                data-name="${row.name}" 
                                data-symbol="${row.symbol}"
                                data-description="${row.description || ''}">
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
                { orderable: false, targets: 4 },
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
        const filterSearch = document.querySelector('[data-kt-unit-table-filter="search"]');
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
                const symbol = this.getAttribute('data-symbol');
                const description = this.getAttribute('data-description');

                $('#unit_id').val(id);
                $('#unit_name').val(name).focus();
                $('#unit_symbol').val(symbol);
                $('#unit_description').val(description);
                
                $('#form_title').text('Edit Satuan');
                $('#btn_cancel_edit').removeClass('d-none');
                
                if (window.innerWidth < 992) {
                    document.getElementById('form_add_unit').scrollIntoView({ behavior: 'smooth' });
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
                    text: "Apakah Anda yakin ingin menghapus satuan ini?",
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
                        $.post(hostUrl + 'master/ajax/unit/delete', { id: id }, function (res) {
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

    var handleAddUnit = function () {
        const form = document.getElementById('form_add_unit');
        if (!form) return;
        
        const submitButton = document.getElementById('btn_save_unit');
        const cancelButton = document.getElementById('btn_cancel_edit');

        cancelButton.addEventListener('click', function() {
            form.reset();
            $('#unit_id').val('');
            $('#form_title').text('Tambah Satuan');
            cancelButton.classList.add('d-none');
        });

        form.addEventListener('submit', function (e) {
            e.preventDefault();
            const id = $('#unit_id').val();
            const url = id ? hostUrl + 'master/ajax/unit/update/' + id : hostUrl + 'master/ajax/unit/store';

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
                        $('#unit_id').val('');
                        $('#form_title').text('Tambah Satuan');
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

    return {
        init: function () {
            table = document.querySelector('#kt_unit_table');
            if (!table) return;

            initDataTable();
            handleSearchDatatable();
            handleAddUnit();
        }
    };
}();

KTUtil.onDOMContentLoaded(function () {
    KTUnitsList.init();
});
