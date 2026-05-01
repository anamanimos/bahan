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
            
            submitButton.setAttribute('data-kt-indicator', 'on');
            submitButton.disabled = true;

            // Mock success
            setTimeout(function() {
                submitButton.removeAttribute('data-kt-indicator');
                submitButton.disabled = false;
                
                Swal.fire({
                    text: "Kategori berhasil ditambahkan!",
                    icon: "success",
                    buttonsStyling: false,
                    confirmButtonText: "Ok, mengerti!",
                    customClass: {
                        confirmButton: "btn btn-primary"
                    }
                }).then(function() {
                    form.reset();
                    $(form.querySelector('[name="parent_id"]')).val(null).trigger('change');
                    tree.jstree(true).refresh();
                });
            }, 1000);
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
