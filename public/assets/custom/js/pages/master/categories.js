"use strict";

var TKAppMasterCategories = function () {
    var tree;

    var initTree = function () {
        tree = $('#kt_ecommerce_category_tree');

        tree.jstree({
            "core": {
                "themes": {
                    "responsive": false
                },
                "check_callback": true,
                'data': {
                    'url': hostUrl + 'ajax/master/categories/tree',
                    'dataType': 'json'
                }
            },
            "types": {
                "default": {
                    "icon": "ki-outline ki-folder text-warning fs-1"
                },
                "file": {
                    "icon": "ki-outline ki-file text-primary fs-1"
                }
            },
            "state": { "key": "category_tree" },
            "plugins": ["dnd", "types", "state"]
        });

        // Handle Move (Drag & Drop)
        tree.on('move_node.jstree', function (e, data) {
            $.post(hostUrl + 'ajax/master/categories/move', {
                id: data.node.id,
                parent: data.parent,
                position: data.position
            }, function(res) {
                if(res.status === 'success') {
                    toastr.success(res.message);
                } else {
                    toastr.error('Gagal memperbarui hirarki');
                    tree.jstree(true).refresh();
                }
            });
        });

        // Search in tree
        $('#kt_ecommerce_category_tree_search').on('keyup', function() {
            var v = $(this).val();
            tree.jstree(true).search(v);
        });

        // Expand/Collapse All
        $('#kt_ecommerce_category_expand_all').on('click', function() {
            tree.jstree('open_all');
        });

        $('#kt_ecommerce_category_collapse_all').on('click', function() {
            tree.jstree('close_all');
        });
    };

    var initForm = function() {
        const form = document.querySelector('#kt_ecommerce_add_category_form');
        if (!form) return;

        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const submitButton = form.querySelector('#kt_ecommerce_add_category_submit');
            
            // Show loading
            Swal.fire({
                text: "Sedang menyimpan data...",
                icon: "info",
                showConfirmButton: false,
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            submitButton.setAttribute('data-kt-indicator', 'on');
            submitButton.disabled = true;

            const action = form.getAttribute('action');
            const method = form.getAttribute('method') || 'POST';
            const formData = new FormData(form);

            $.ajax({
                url: action,
                type: method,
                data: formData,
                processData: false,
                contentType: false,
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
                        }).then(function (result) {
                            form.reset();
                            const parentSelect = $(form.querySelector('[name="parent_id"]'));
                            if (parentSelect.length) {
                                parentSelect.val(null).trigger('change');
                            }
                            if (tree.jstree(true)) {
                                tree.jstree(true).refresh();
                            }
                        });
                    } else {
                        Swal.fire({
                            text: response.message || "Terjadi kesalahan saat menyimpan data.",
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
                    
                    let message = "Terjadi kesalahan sistem.";
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        message = xhr.responseJSON.message;
                    }

                    Swal.fire({
                        text: message,
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
            initTree();
            initForm();
        }
    };
}();

KTUtil.onDOMContentLoaded(function () {
    TKAppMasterCategories.init();
});
