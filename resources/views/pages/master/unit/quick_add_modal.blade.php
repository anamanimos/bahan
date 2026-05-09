<!-- Modal: Quick Add Unit -->
<div class="modal fade" id="modal_quick_add_unit" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-400px">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="fw-bold">Tambah Satuan Baru</h2>
                <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
                    <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
                </div>
            </div>
            <div class="modal-body py-10 px-lg-17">
                <form id="form_quick_add_unit" action="{{ route('master.ajax.unit.store') }}">
                    @csrf
                    <div class="mb-5">
                        <label class="required form-label">Nama Satuan</label>
                        <input type="text" class="form-control form-control-solid" name="name" placeholder="Contoh: Meter" required />
                    </div>
                    <div class="mb-5">
                        <label class="required form-label">Simbol / Kode</label>
                        <input type="text" class="form-control form-control-solid" name="symbol" placeholder="Contoh: Mtr" required />
                    </div>
                    <div class="mb-5">
                        <label class="form-label">Keterangan</label>
                        <textarea class="form-control form-control-solid" name="description" rows="2" placeholder="Opsional"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer flex-center">
                <button type="button" class="btn btn-light me-3" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="btn_quick_add_unit_submit">
                    <span class="indicator-label">Simpan Satuan</span>
                    <span class="indicator-progress">Menyimpan... <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('modal_quick_add_unit');
    const form = document.getElementById('form_quick_add_unit');
    const submitBtn = document.getElementById('btn_quick_add_unit_submit');
    let activeSelect = null;

    // Global function to attach listener to unit selects
    window.initQuickAddUnit = function(selectElement) {
        $(selectElement).on('change', function() {
            if ($(this).val() === 'ADD_NEW_UNIT') {
                activeSelect = this;
                $(modal).modal('show');
                // Reset select to previous value or empty to avoid keeping ADD_NEW_UNIT selected
                $(this).val('').trigger('change.select2');
            }
        });
    };

    if (submitBtn) {
        submitBtn.addEventListener('click', function(e) {
            e.preventDefault();
            
            if(!form.checkValidity()) {
                form.reportValidity();
                return;
            }

            submitBtn.setAttribute('data-kt-indicator', 'on');
            submitBtn.disabled = true;

            $.ajax({
                url: form.action,
                type: 'POST',
                data: $(form).serialize(),
                success: function(response) {
                    submitBtn.removeAttribute('data-kt-indicator');
                    submitBtn.disabled = false;

                    if(response.success && response.data) {
                        $(modal).modal('hide');
                        form.reset();
                        
                        // Add new option to all unit selects on the page
                        const newOptionText = response.data.name + ' (' + response.data.symbol + ')';
                        const newOptionValue = response.data.symbol;
                        
                        // Select all elements that might be unit selects
                        const allUnitSelects = $('[data-kt-element="unit-select"], #base_unit_select, #quick_product_unit_select');
                        
                        allUnitSelects.each(function() {
                            // Check if option already exists to avoid duplicates
                            if ($(this).find(`option[value="${newOptionValue}"]`).length === 0) {
                                // Insert before the "ADD_NEW_UNIT" option if it exists
                                const addNewOption = $(this).find('option[value="ADD_NEW_UNIT"]');
                                const newOpt = new Option(newOptionText, newOptionValue, false, false);
                                
                                if (addNewOption.length) {
                                    $(newOpt).insertBefore(addNewOption);
                                } else {
                                    $(this).append(newOpt);
                                }
                            }
                        });

                        // Set the active select to the new value
                        if (activeSelect) {
                            $(activeSelect).val(newOptionValue).trigger('change');
                        }
                        
                        Swal.fire({
                            text: response.message,
                            icon: "success",
                            buttonsStyling: false,
                            confirmButtonText: "Ok, mengerti!",
                            customClass: { confirmButton: "btn btn-primary" }
                        });
                    }
                },
                error: function(xhr) {
                    submitBtn.removeAttribute('data-kt-indicator');
                    submitBtn.disabled = false;
                    let msg = "Terjadi kesalahan.";
                    if(xhr.responseJSON && xhr.responseJSON.message) msg = xhr.responseJSON.message;
                    if(xhr.responseJSON && xhr.responseJSON.errors) {
                        msg = Object.values(xhr.responseJSON.errors)[0][0];
                    }
                    Swal.fire({ text: msg, icon: "error", buttonsStyling: false, confirmButtonText: "Ok, mengerti!", customClass: { confirmButton: "btn btn-primary" } });
                }
            });
        });
    }
});
</script>
